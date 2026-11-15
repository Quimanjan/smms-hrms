<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** An exam proctoring day: a normal working day, marked purple on the DTR. */
class ProctoringDay extends Model
{
    protected $fillable = ['date', 'name'];

    protected $casts = ['date' => 'date:Y-m-d'];
}
