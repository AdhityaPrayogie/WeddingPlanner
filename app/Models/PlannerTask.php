<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerTask extends Model
{
    protected $fillable = ['title', 'note', 'phase', 'due_date', 'done_at'];

    protected $casts = ['due_date' => 'date', 'done_at' => 'datetime'];

    public function isOverdue(): bool
    {
        return !$this->done_at && $this->due_date && $this->due_date->isBefore(today());
    }
}
