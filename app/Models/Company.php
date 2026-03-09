<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function standardOperationals()
    {
        return $this->hasMany(StandardOperational::class);
    }

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class);
    }
}
