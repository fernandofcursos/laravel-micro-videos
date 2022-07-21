<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RulesGeneric;

class Genre extends Model
{
    use HasFactory;

    use SoftDeletes, Uuid;
    

    protected $fillable = [
        'name',  'is_active'
    ];
    protected $dates=['delete_at'];
}
