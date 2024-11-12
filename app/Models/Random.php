<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Random extends Model
{
    use HasFactory;

    protected $table = 'randoms';

    protected $fillable = [
        'rand_date',
        'rand_value',
    ];
}
