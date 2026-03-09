<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'documents';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'attachment',
        'standard_operational_id',
    ];

    public function standardOperational()
    {
        return $this->belongsTo(StandardOperational::class, 'standard_operational_id');
    }
}
