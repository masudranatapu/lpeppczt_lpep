<?php

namespace App\Models;

use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AppCustomer extends Model
{
    use HasFactory, UserLog, HasApiTokens;

    protected $guarded = [];

    protected $casts = [
        'imported_at' => 'datetime',
    ];

    /** The beneficiary this record was copied from by the Excel import. */
    public function importedFrom()
    {
        return $this->belongsTo(self::class, 'imported_from_id');
    }

    /** Tooltip text for the "Imported" badge, or null for beneficiaries that were not imported. */
    public function importNote(): ?string
    {
        if (!$this->imported_at) {
            return null;
        }

        $note = 'Imported';
        if ($this->imported_from_id) {
            $agent = $this->importedFrom?->agent;
            $owner = $agent ? $agent->name . ($agent->employee_name && $agent->employee_name !== $agent->name ? " / {$agent->employee_name}" : '') : null;
            $note .= " from ID {$this->imported_from_id}" . ($owner ? " ({$owner})" : '');
        }

        return $note . ' on ' . $this->imported_at->format('d M Y h:i A');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    public function sales()
    {
        return $this->hasMany(AgentSale::class);
    }

    public function agentCustomerTransactions()
    {
        return $this->hasMany(AgentCustomerTransaction::class);
    }

    public function agentSales()
    {
        return $this->hasMany(AgentSale::class, 'app_customer_id');
    }
}
