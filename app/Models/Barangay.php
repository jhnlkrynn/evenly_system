<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Lookup list of Nasugbu's barangays for location pickers. */
class Barangay extends Model
{
    protected $fillable = [
        'name',
    ];
}
