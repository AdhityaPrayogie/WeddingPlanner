<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerGuest extends Model
{
    protected $fillable = ['name', 'side', 'phone', 'pax', 'rsvp'];
}
