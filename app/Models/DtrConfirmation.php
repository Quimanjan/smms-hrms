<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtrConfirmation extends Model
{
    protected $fillable = ['employee_id', 'period', 'cutoff', 'confirmed_at'];

    protected $casts = ['confirmed_at' => 'datetime'];
}
