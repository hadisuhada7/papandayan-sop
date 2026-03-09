<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditTrail extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    protected $table = 'audit_trails';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'datetime_stamp',
        'action',
        'description',
    ];

    protected $casts = [
        'datetime_stamp' => 'datetime', // format method...
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
