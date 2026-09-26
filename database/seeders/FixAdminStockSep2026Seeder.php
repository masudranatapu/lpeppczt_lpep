<?php

namespace Database\Seeders;

use App\Service\WarehouseInventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * One-off correction for the stock count reported by the client on 13-09-2026.
 *
 * Small Admin Purchases were entered for stock that never arrived and were
 * transferred to the Rajbari Area Office straight away, so the admin purchase
 * total and the office stock were both higher than the physical stock:
 *
 *   Ruchi Max 100 gm                    60 -> 57
 *   Hepamin Forte 1 ltr                 32 -> 31
 *   Magvet Plus 500ml                   16 -> 15
 *   Bovi care 125gm                     73 -> 71
 *   Bonacal P oral suspension 1 liter   67 -> 66
 *
 * The purchase screen refuses to reduce a purchase below what was already
 * transferred, so this seeder removes exactly those purchases (with their
 * payments) and the matching transfers. LSP assignments, sales and returns are
 * not touched.
 *
 * Safety: every target row is verified first and nothing changes if the data
 * differs; the plan is shown and must be confirmed; a JSON backup of the
 * removed rows is written to storage/app/stock-fix/; all changes run in one
 * transaction and are re-checked before commit; running it again is a no-op.
 *
 * Run: php artisan db:seed --class=FixAdminStockSep2026Seeder
 */
class FixAdminStockSep2026Seeder extends Seeder
{
    private const WAREHOUSE_ID = 1; // Rajbari Area Office

    private const PRODUCTS = [
        221 => 'Ruchi Max 100 gm',
        206 => 'Hepamin Forte 1 ltr',
        215 => 'Magvet Plus 500ml',
        128 => 'Bovi care 125gm',
        238 => 'Bonacal P oral suspension 1 liter',
    ];

    // Admin purchases to delete: id => [invoice_no, [product_id => quantity], total_amount]
    private const PURCHASES = [
        52 => ['WP-260800051', [206 => 1], 640.00],
        53 => ['WP-260800052', [221 => 2], 200.00],
        57 => ['WP-260800056', [215 => 1, 128 => 1], 330.00],
        64 => ['WP-260900004', [128 => 1], 146.55],
        65 => ['WP-260900005', [238 => 1], 194.91],
        66 => ['WP-260900006', [221 => 1], 100.00],
    ];

    // Transfers to the Rajbari office to delete: id => [invoice_no, product_id, quantity]
    private const TRANSFERS = [
        50 => ['WT-260800050', 206, 1],
        51 => ['WT-260800051', 221, 1],
        59 => ['WT-260800059', 215, 1],
        60 => ['WT-260800060', 128, 1],
        66 => ['WT-260900003', 238, 1],
        67 => ['WT-260900004', 221, 1],
        76 => ['WT-260900013', 221, 1],
    ];

    // This transfer carried 58 real + 1 extra Bovi care: [id, invoice_no, product_id, from, to]
    private const REDUCED_TRANSFER = [68, 'WT-260900005', 128, 59, 58];

    public function run(WarehouseInventoryService $inventory)
    {
        $this->command->info('Stock correction: ' . implode(', ', self::PRODUCTS));

        $problems = $this->pendingProblems();
        if ($problems) {
            if ($this->isAlreadyApplied()) {
                $this->command->info('Already applied. Nothing to do.');
                return;
            }
            $this->command->error('The database does not match the expected rows. NOTHING was changed:');
            foreach ($problems as $problem) {
                $this->command->line('  - ' . $problem);
            }
            return;
        }

        $removed = $this->removedQuantities();
        $before = $this->snapshot($inventory);

        foreach ($removed as $productId => $quantity) {
            if ($before[$productId]['office'] < $quantity) {
                $this->command->error(self::PRODUCTS[$productId] . " has only {$before[$productId]['office']} unassigned in the Rajbari office, {$quantity} must be removed. NOTHING was changed.");
                return;
            }
        }

        $this->printPlan($before, $removed);

        if (!$this->command->confirm('Apply these changes to the database?', false)) {
            $this->command->warn('Cancelled. Nothing was changed.');
            return;
        }

        $backupPath = $this->writeBackup();
        $this->command->info("Backup of the rows being removed: storage/app/{$backupPath}");

        DB::transaction(function () use ($inventory, $before, $removed) {
            DB::table('lpep_warehouse_purchases')->whereIn('id', array_keys(self::PURCHASES))->lockForUpdate()->get();
            DB::table('lpep_warehouse_stock_transfers')->whereIn('id', $this->transferIds())->lockForUpdate()->get();

            if ($problems = $this->pendingProblems()) {
                throw new RuntimeException('Data changed while running: ' . implode(' | ', $problems));
            }

            $purchaseIds = array_keys(self::PURCHASES);
            DB::table('warehouse_purchase_payments')->whereIn('warehouse_purchase_id', $purchaseIds)->delete();
            DB::table('lpep_warehouse_purchase_items')->whereIn('warehouse_purchase_id', $purchaseIds)->delete();
            DB::table('lpep_warehouse_purchases')->whereIn('id', $purchaseIds)->delete();

            $transferIds = array_keys(self::TRANSFERS);
            DB::table('lpep_warehouse_stock_transfer_items')->whereIn('warehouse_stock_transfer_id', $transferIds)->delete();
            DB::table('lpep_warehouse_stock_transfers')->whereIn('id', $transferIds)->delete();

            [$transferId, , $productId, , $to] = self::REDUCED_TRANSFER;
            DB::table('lpep_warehouse_stock_transfer_items')
                ->where('warehouse_stock_transfer_id', $transferId)
                ->where('product_id', $productId)
                ->update(['quantity' => $to, 'updated_at' => now()]);
            DB::table('lpep_warehouse_stock_transfers')
                ->where('id', $transferId)
                ->update(['total_quantity' => $to, 'updated_at' => now()]);

            $this->verifyResult($before, $this->snapshot($inventory), $removed);
        });

        $this->printResult($before, $this->snapshot($inventory));
        $this->command->info('Done. Purchase and Rajbari office stock now match the physical count.');
    }

    /** Problems that stop the correction; empty when every target row is exactly as expected. */
    private function pendingProblems(): array
    {
        $problems = [];

        foreach (self::PURCHASES as $id => [$invoiceNo, $items, $total]) {
            $purchase = DB::table('lpep_warehouse_purchases')->find($id);
            if (!$purchase) {
                $problems[] = "Purchase #{$id} ({$invoiceNo}) not found.";
                continue;
            }
            if ($purchase->invoice_no !== $invoiceNo || $purchase->warehouse_id !== null || !$this->same($purchase->total_amount, $total)) {
                $problems[] = "Purchase #{$id} is not {$invoiceNo} admin purchase of {$total} (found {$purchase->invoice_no}, total {$purchase->total_amount}).";
            }
            $found = DB::table('lpep_warehouse_purchase_items')->where('warehouse_purchase_id', $id)
                ->selectRaw('product_id, SUM(quantity) as quantity')->groupBy('product_id')->pluck('quantity', 'product_id')->all();
            if (!$this->sameItems($found, $items)) {
                $problems[] = "Purchase #{$id} ({$invoiceNo}) items differ: expected " . json_encode($items) . ', found ' . json_encode($found) . '.';
            }
        }

        foreach (self::TRANSFERS as $id => [$invoiceNo, $productId, $quantity]) {
            $problems = array_merge($problems, $this->transferProblems($id, $invoiceNo, $productId, $quantity));
        }

        [$id, $invoiceNo, $productId, $from] = self::REDUCED_TRANSFER;
        return array_merge($problems, $this->transferProblems($id, $invoiceNo, $productId, $from));
    }

    private function transferProblems(int $id, string $invoiceNo, int $productId, float $quantity): array
    {
        $transfer = DB::table('lpep_warehouse_stock_transfers')->find($id);
        if (!$transfer) {
            return ["Transfer #{$id} ({$invoiceNo}) not found."];
        }

        $problems = [];
        if ($transfer->invoice_no !== $invoiceNo || (int) $transfer->warehouse_id !== self::WAREHOUSE_ID || !$this->same($transfer->total_quantity, $quantity)) {
            $problems[] = "Transfer #{$id} is not {$invoiceNo} of {$quantity} to the Rajbari office (found {$transfer->invoice_no}, warehouse {$transfer->warehouse_id}, total {$transfer->total_quantity}).";
        }
        $found = DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', $id)
            ->selectRaw('product_id, SUM(quantity) as quantity')->groupBy('product_id')->pluck('quantity', 'product_id')->all();
        if (!$this->sameItems($found, [$productId => $quantity])) {
            $problems[] = "Transfer #{$id} ({$invoiceNo}) items differ: expected " . json_encode([$productId => $quantity]) . ', found ' . json_encode($found) . '.';
        }

        return $problems;
    }

    private function isAlreadyApplied(): bool
    {
        [$id, , $productId, , $to] = self::REDUCED_TRANSFER;

        return !DB::table('lpep_warehouse_purchases')->whereIn('id', array_keys(self::PURCHASES))->exists()
            && !DB::table('lpep_warehouse_stock_transfers')->whereIn('id', array_keys(self::TRANSFERS))->exists()
            && !DB::table('lpep_warehouse_stock_transfer_items')->whereIn('warehouse_stock_transfer_id', array_keys(self::TRANSFERS))->exists()
            && $this->same(DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', $id)->where('product_id', $productId)->sum('quantity'), $to);
    }

    /** Units removed per product; purchases and transfers must remove the same amount. */
    private function removedQuantities(): array
    {
        $purchased = array_fill_keys(array_keys(self::PRODUCTS), 0.0);
        foreach (self::PURCHASES as [, $items]) {
            foreach ($items as $productId => $quantity) {
                $purchased[$productId] += $quantity;
            }
        }

        $transferred = array_fill_keys(array_keys(self::PRODUCTS), 0.0);
        foreach (self::TRANSFERS as [, $productId, $quantity]) {
            $transferred[$productId] += $quantity;
        }
        [, , $productId, $from, $to] = self::REDUCED_TRANSFER;
        $transferred[$productId] += $from - $to;

        if ($purchased !== $transferred) {
            throw new RuntimeException('Seeder configuration error: purchases and transfers do not remove the same quantities.');
        }

        return $purchased;
    }

    private function snapshot(WarehouseInventoryService $inventory): array
    {
        $snapshot = [];
        foreach (array_keys(self::PRODUCTS) as $productId) {
            $lspSold = $inventory->warehouseSold(self::WAREHOUSE_ID, $productId) - $inventory->warehouseDirectSold(self::WAREHOUSE_ID, $productId);
            $snapshot[$productId] = [
                'purchased' => $inventory->adminPurchased($productId),
                'transferred' => $inventory->adminTransferred($productId),
                'admin' => $inventory->adminAvailable($productId),
                'office' => $inventory->warehouseUnassignedAvailable(self::WAREHOUSE_ID, $productId),
                'lsp' => round($inventory->warehouseAssigned(self::WAREHOUSE_ID, $productId) - $inventory->warehouseSalesmanReturned(self::WAREHOUSE_ID, $productId) - $lspSold, 2),
            ];
        }

        return $snapshot;
    }

    private function verifyResult(array $before, array $after, array $removed): void
    {
        foreach ($removed as $productId => $quantity) {
            $b = $before[$productId];
            $a = $after[$productId];
            $ok = $this->same($a['purchased'], $b['purchased'] - $quantity)
                && $this->same($a['transferred'], $b['transferred'] - $quantity)
                && $this->same($a['admin'], $b['admin'])
                && $this->same($a['office'], $b['office'] - $quantity)
                && $this->same($a['lsp'], $b['lsp'])
                && $a['admin'] >= 0 && $a['office'] >= 0;

            if (!$ok) {
                throw new RuntimeException('Result check failed for ' . self::PRODUCTS[$productId] . ', all changes were rolled back. Before ' . json_encode($b) . ', after ' . json_encode($a));
            }
        }
    }

    private function writeBackup(): string
    {
        $purchaseIds = array_keys(self::PURCHASES);
        $transferIds = $this->transferIds();

        $backup = [
            'seeder' => static::class,
            'created_at' => now()->toDateTimeString(),
            'lpep_warehouse_purchases' => DB::table('lpep_warehouse_purchases')->whereIn('id', $purchaseIds)->get(),
            'lpep_warehouse_purchase_items' => DB::table('lpep_warehouse_purchase_items')->whereIn('warehouse_purchase_id', $purchaseIds)->get(),
            'warehouse_purchase_payments' => DB::table('warehouse_purchase_payments')->whereIn('warehouse_purchase_id', $purchaseIds)->get(),
            'lpep_warehouse_stock_transfers' => DB::table('lpep_warehouse_stock_transfers')->whereIn('id', $transferIds)->get(),
            'lpep_warehouse_stock_transfer_items' => DB::table('lpep_warehouse_stock_transfer_items')->whereIn('warehouse_stock_transfer_id', $transferIds)->get(),
        ];

        $path = 'stock-fix/FixAdminStockSep2026-backup-' . now()->format('Ymd-His') . '.json';
        Storage::disk('local')->put($path, json_encode($backup, JSON_PRETTY_PRINT));

        return $path;
    }

    private function printPlan(array $before, array $removed): void
    {
        $rows = [];
        foreach (self::PURCHASES as $id => [$invoiceNo, $items, $total]) {
            $payments = DB::table('warehouse_purchase_payments')->where('warehouse_purchase_id', $id)->sum('amount');
            $rows[] = ['Delete purchase', "#{$id} {$invoiceNo}", $this->describeItems($items), "total {$total}, payments {$payments}"];
        }
        foreach (self::TRANSFERS as $id => [$invoiceNo, $productId, $quantity]) {
            $rows[] = ['Delete transfer', "#{$id} {$invoiceNo}", $this->describeItems([$productId => $quantity]), 'to Rajbari'];
        }
        [$id, $invoiceNo, $productId, $from, $to] = self::REDUCED_TRANSFER;
        $rows[] = ['Reduce transfer', "#{$id} {$invoiceNo}", self::PRODUCTS[$productId], "{$from} -> {$to}"];
        $this->command->table(['Action', 'Row', 'Product', 'Detail'], $rows);

        $rows = [];
        foreach ($removed as $productId => $quantity) {
            $b = $before[$productId];
            $rows[] = [
                self::PRODUCTS[$productId],
                $b['purchased'] . ' -> ' . ($b['purchased'] - $quantity),
                $b['transferred'] . ' -> ' . ($b['transferred'] - $quantity),
                $b['admin'],
                $b['office'] . ' -> ' . ($b['office'] - $quantity),
                $b['lsp'],
            ];
        }
        $this->command->table(['Product', 'Purchased', 'Transferred', 'Admin stock', 'Rajbari office', 'LSP stock'], $rows);
    }

    private function printResult(array $before, array $after): void
    {
        $rows = [];
        foreach ($after as $productId => $a) {
            $rows[] = [self::PRODUCTS[$productId], $a['purchased'], $a['transferred'], $a['admin'], $a['office'], $a['lsp']];
        }
        $this->command->table(['Product (after)', 'Purchased', 'Transferred', 'Admin stock', 'Rajbari office', 'LSP stock'], $rows);
    }

    private function describeItems(array $items): string
    {
        return collect($items)->map(fn($quantity, $productId) => self::PRODUCTS[$productId] . " x {$quantity}")->implode(', ');
    }

    private function transferIds(): array
    {
        return array_merge(array_keys(self::TRANSFERS), [self::REDUCED_TRANSFER[0]]);
    }

    private function sameItems(array $found, array $expected): bool
    {
        if (count($found) !== count($expected)) {
            return false;
        }
        foreach ($expected as $productId => $quantity) {
            if (!array_key_exists($productId, $found) || !$this->same($found[$productId], $quantity)) {
                return false;
            }
        }

        return true;
    }

    private function same($a, $b): bool
    {
        return abs((float) $a - (float) $b) < 0.001;
    }
}
