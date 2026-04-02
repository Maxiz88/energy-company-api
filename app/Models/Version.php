<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'versionable_id',
        'versionable_type',
        'version_number',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
