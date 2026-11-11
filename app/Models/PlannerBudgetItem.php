<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerBudgetItem extends Model
{
    protected $fillable = ['category', 'name', 'estimate', 'paid', 'note'];

    protected $casts = ['estimate' => 'decimal:2', 'paid' => 'decimal:2'];
}
