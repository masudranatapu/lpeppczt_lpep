<?php

namespace Database\Seeders;

use App\Service\WarehouseInventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * One-off correction for "A mectin plus 30ml" (product #213).
 *
 * Admin purchase WP-260900012 (20-09-2026, ACI LIMITED, 1 pc at 10 tk, no
 * invoice) was entered for stock that never arrived. Two days later its unit
 * went to the Rajbari office inside transfer WT-260900021 as 21 pcs, while the
 * real purchase WP-260900013 was only 20 pcs. So admin purchase and transfer
 * show one unit more than the physical stock (Rajbari office 16 instead of 15).
 *
 * This seeder deletes WP-260900012 (with its 5 tk payment) and reduces the
 * A mectin plus line of WT-260900021 from 21 to 20 (transfer total 318 -> 317).
 * Nothing else in that transfer, and no LSP assignment or sale, is touched.
 *
 * Safety: rows are verified first and nothing changes if the data differs;
 * the plan is shown and must be confirmed; a JSON backup goes to
 * storage/app/stock-fix/; one transaction, re-checked before commit; running
 * it again is a no-op.
 *
 * Run: php artisan db:seed --class=FixAMectinPlusStockSeeder
 */
class FixAMectinPlusStockSeeder extends Seeder
{
    private const PRODUCT_ID = 213;
    private const PRODUCT_NAME = 'A mectin plus 30ml';
    private const WAREHOUSE_ID = 1; // Rajbari Area Office

    // [id, invoice_no, quantity, total_amount]
    private const PURCHASE = [72, 'WP-260900012', 1, 10.00];
    // [id, invoice_no, line quantity from -> to, transfer total from -> to]
    private const TRANSFER = [84, 'WT-260900021', 21, 20, 318, 317];

    public function run(WarehouseInventoryService $inventory)
    {
        $this->command->info('Stock correction: ' . self::PRODUCT_NAME . ' (1 pc)');

        if ($problems = $this->pendingProblems()) {
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

        $before = $this->snapshot($inventory);
        if ($before['office'] < 1) {
            $this->command->error("The Rajbari office has {$before['office']} unassigned " . self::PRODUCT_NAME . ', 1 must be removed. NOTHING was changed.');
            return;
        }

        [$purchaseId, $purchaseInvoice, , $amount] = self::PURCHASE;
        [$transferId, $transferInvoice, $lineFrom, $lineTo, $totalFrom, $totalTo] = self::TRANSFER;
        $payments = DB::table('warehouse_purchase_payments')->where('warehouse_purchase_id', $purchaseId)->sum('amount');
        $this->command->table(['Action', 'Row', 'Detail'], [
            ['Delete purchase', "#{$purchaseId} {$purchaseInvoice}", self::PRODUCT_NAME . " x 1, total {$amount}, payments {$payments}"],
            ['Reduce transfer line', "#{$transferId} {$transferInvoice}", self::PRODUCT_NAME . " {$lineFrom} -> {$lineTo} (transfer total {$totalFrom} -> {$totalTo})"],
        ]);
        $this->command->table(['', 'Purchased', 'Transferred', 'Admin stock', 'Rajbari office', 'LSP stock'], [
            [
                self::PRODUCT_NAME,
                $before['purchased'] . ' -> ' . ($before['purchased'] - 1),
                $before['transferred'] . ' -> ' . ($before['transferred'] - 1),
                $before['admin'],
                $before['office'] . ' -> ' . ($before['office'] - 1),
                $before['lsp'],
            ]
        ]);

        if (!$this->command->confirm('Apply these changes to the database?', false)) {
            $this->command->warn('Cancelled. Nothing was changed.');
            return;
        }

        $backup = 'stock-fix/FixAMectinPlusStock-backup-' . now()->format('Ymd-His') . '.json';
        Storage::disk('local')->put($backup, json_encode([
            'seeder' => static::class,
            'created_at' => now()->toDateTimeString(),
            'lpep_warehouse_purchases' => DB::table('lpep_warehouse_purchases')->where('id', $purchaseId)->get(),
            'lpep_warehouse_purchase_items' => DB::table('lpep_warehouse_purchase_items')->where('warehouse_purchase_id', $purchaseId)->get(),
            'warehouse_purchase_payments' => DB::table('warehouse_purchase_payments')->where('warehouse_purchase_id', $purchaseId)->get(),
            'lpep_warehouse_stock_transfers' => DB::table('lpep_warehouse_stock_transfers')->where('id', $transferId)->get(),
            'lpep_warehouse_stock_transfer_items' => DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', $transferId)->where('product_id', self::PRODUCT_ID)->get(),
        ], JSON_PRETTY_PRINT));
        $this->command->info("Backup of the rows being changed: storage/app/{$backup}");

        DB::transaction(function () use ($inventory, $before, $purchaseId, $transferId, $lineTo, $totalTo) {
            DB::table('lpep_warehouse_purchases')->where('id', $purchaseId)->lockForUpdate()->first();
            DB::table('lpep_warehouse_stock_transfers')->where('id', $transferId)->lockForUpdate()->first();
            if ($problems = $this->pendingProblems()) {
                throw new RuntimeException('Data changed while running: ' . implode(' | ', $problems));
            }

            DB::table('warehouse_purchase_payments')->where('warehouse_purchase_id', $purchaseId)->delete();
            DB::table('lpep_warehouse_purchase_items')->where('warehouse_purchase_id', $purchaseId)->delete();
            DB::table('lpep_warehouse_purchases')->where('id', $purchaseId)->delete();

            DB::table('lpep_warehouse_stock_transfer_items')
                ->where('warehouse_stock_transfer_id', $transferId)->where('product_id', self::PRODUCT_ID)
                ->update(['quantity' => $lineTo, 'updated_at' => now()]);
            DB::table('lpep_warehouse_stock_transfers')->where('id', $transferId)
                ->update(['total_quantity' => $totalTo, 'updated_at' => now()]);

            $after = $this->snapshot($inventory);
            $ok = $this->same($after['purchased'], $before['purchased'] - 1)
                && $this->same($after['transferred'], $before['transferred'] - 1)
                && $this->same($after['admin'], $before['admin'])
                && $this->same($after['office'], $before['office'] - 1)
                && $this->same($after['lsp'], $before['lsp'])
                && $after['admin'] >= 0 && $after['office'] >= 0;
            if (!$ok) {
                throw new RuntimeException('Result check failed, all changes were rolled back. Before ' . json_encode($before) . ', after ' . json_encode($after));
            }
        });

        $after = $this->snapshot($inventory);
        $this->command->table(['After', 'Purchased', 'Transferred', 'Admin stock', 'Rajbari office', 'LSP stock'], [
            [
                self::PRODUCT_NAME,
                $after['purchased'],
                $after['transferred'],
                $after['admin'],
                $after['office'],
                $after['lsp'],
            ]
        ]);
        $this->command->info('Done.');
    }

    private function pendingProblems(): array
    {
        $problems = [];
        [$purchaseId, $purchaseInvoice, $quantity, $amount] = self::PURCHASE;
        [$transferId, $transferInvoice, $lineFrom, , $totalFrom] = self::TRANSFER;

        $purchase = DB::table('lpep_warehouse_purchases')->find($purchaseId);
        if (!$purchase) {
            $problems[] = "Purchase #{$purchaseId} ({$purchaseInvoice}) not found.";
        } else {
            $items = DB::table('lpep_warehouse_purchase_items')->where('warehouse_purchase_id', $purchaseId)->get(['product_id', 'quantity']);
            if (
                $purchase->invoice_no !== $purchaseInvoice || $purchase->warehouse_id !== null || !$this->same($purchase->total_amount, $amount)
                || $items->count() !== 1 || (int) $items[0]->product_id !== self::PRODUCT_ID || !$this->same($items[0]->quantity, $quantity)
            ) {
                $problems[] = "Purchase #{$purchaseId} is not {$purchaseInvoice} with only {$quantity} x " . self::PRODUCT_NAME . " for {$amount}.";
            }
        }

        $transfer = DB::table('lpep_warehouse_stock_transfers')->find($transferId);
        if (!$transfer) {
            $problems[] = "Transfer #{$transferId} ({$transferInvoice}) not found.";
        } else {
            $line = DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', $transferId)->where('product_id', self::PRODUCT_ID)->sum('quantity');
            if (
                $transfer->invoice_no !== $transferInvoice || (int) $transfer->warehouse_id !== self::WAREHOUSE_ID
                || !$this->same($transfer->total_quantity, $totalFrom) || !$this->same($line, $lineFrom)
            ) {
                $problems[] = "Transfer #{$transferId} is not {$transferInvoice} to Rajbari with {$lineFrom} x " . self::PRODUCT_NAME . " and total {$totalFrom} (found total {$transfer->total_quantity}, line {$line}).";
            }
        }

        return $problems;
    }

    private function isAlreadyApplied(): bool
    {
        [$purchaseId] = self::PURCHASE;
        [$transferId, , , $lineTo, , $totalTo] = self::TRANSFER;
        $transfer = DB::table('lpep_warehouse_stock_transfers')->find($transferId);

        return !DB::table('lpep_warehouse_purchases')->where('id', $purchaseId)->exists()
            && $transfer
            && $this->same($transfer->total_quantity, $totalTo)
            && $this->same(DB::table('lpep_warehouse_stock_transfer_items')->where('warehouse_stock_transfer_id', $transferId)->where('product_id', self::PRODUCT_ID)->sum('quantity'), $lineTo);
    }

    private function snapshot(WarehouseInventoryService $inventory): array
    {
        $lspSold = $inventory->warehouseSold(self::WAREHOUSE_ID, self::PRODUCT_ID) - $inventory->warehouseDirectSold(self::WAREHOUSE_ID, self::PRODUCT_ID);

        return [
            'purchased' => $inventory->adminPurchased(self::PRODUCT_ID),
            'transferred' => $inventory->adminTransferred(self::PRODUCT_ID),
            'admin' => $inventory->adminAvailable(self::PRODUCT_ID),
            'office' => $inventory->warehouseUnassignedAvailable(self::WAREHOUSE_ID, self::PRODUCT_ID),
            'lsp' => round($inventory->warehouseAssigned(self::WAREHOUSE_ID, self::PRODUCT_ID) - $inventory->warehouseSalesmanReturned(self::WAREHOUSE_ID, self::PRODUCT_ID) - $lspSold, 2),
        ];
    }

    private function same($a, $b): bool
    {
        return abs((float) $a - (float) $b) < 0.001;
    }
}
