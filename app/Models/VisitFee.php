<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitFee extends Model
{
    use HasFactory;
     protected $fillable = [
        'visit_info_id',
        'fee_type',
        'amount',
    ];

    public const TREATMENT_AND_OTHERS = 'Treatment and others';

    public function visit()
    {
        return $this->belongsTo(VisitInfo::class, 'visit_info_id');
    }

    /**
     * Visits created from Sales portal sales took the admin income type name
     * "Treatment & others" (or the category name "Treatment"), while visit
     * targets and manual entries use "Treatment and others". Treat them as one.
     */
    public static function normalizeType(?string $type): string
    {
        $type = trim(preg_replace('/\s+/', ' ', (string) $type));
        $key = strtolower(preg_replace('/\s+/', ' ', str_replace('&', ' and ', $type)));

        return in_array($key, ['treatment and others', 'treatment and other', 'treatment'], true)
            ? self::TREATMENT_AND_OTHERS
            : $type;
    }

    /** Sum of amounts per fee type, with fee type spellings normalized. */
    public static function summarize($fees)
    {
        return collect($fees)
            ->groupBy(fn ($fee) => self::normalizeType($fee->fee_type))
            ->map(fn ($group) => (float) $group->sum(fn ($fee) => (float) ($fee->amount ?? $fee->total_amount ?? 0)));
    }
}
