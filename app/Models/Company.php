<?php

namespace App\Models;

use App\Traits\HasVersions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, HasVersions;

    protected $fillable = ['name', 'edrpou', 'address'];
}

