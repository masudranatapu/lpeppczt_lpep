<?php

namespace App\Traits;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserLog
{
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdByCustomer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'created_by')->where('type', 'customer');
    }

    public function updatedByCustomer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'updated_by')->where('type', 'customer');
    }

    public function log(): string
    {
        if ($this->created_by_type && $this->created_by_type == 'customer' && $this->createdByCustomer) {
            $content = 'Created By: ' . $this->createdByCustomer->name . ' (Customer)<br>' . 'Created At: ' . $this->created_at;
        } else if ($this->createdBy) {
            $content = 'Created By: ' . $this->createdBy->name . '<br>' . 'Created At: ' . $this->created_at;
        }

        if ($this->updatedBy) {
            $content .= '<hr>';

            if ($this->updated_by_type && $this->updated_by_type == 'customer' && $this->updatedByCustomer) {
                $content .= 'Updated By: ' . $this->updatedByCustomer->name . ' (Customer)<br>' . 'Updated At: ' . $this->updated_at;
            } else if ($this->updatedBy) {
                $content .= 'Updated By: ' . $this->updatedBy->name . '<br>' . 'Updated At: ' . $this->updated_at;
            }
        }

        return '<button type="button" class="btn btn-info btn-sm mr-1" style="cursor:pointer" data-container="body"
                    data-toggle="popover" data-placement="top" data-trigger="hover"
                    data-html="true" data-content="' . $content . '">
                    <i class="fa fa-info"></i>
                </button>';
    }
}
