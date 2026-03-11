<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardOperational extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'standard_operationals';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'document_number',
        'effective_date',
        'expired_date',
        'rules',
        'company_id',
    ];

    protected $casts = [
        'effective_date' => 'date', // format method...
        'expired_date' => 'date', // format method...
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_standard_operational');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function sopDocuments() {
        return $this->hasMany(Document::class)->where('type', 'sop');
    }

    public function formDocuments() {
        return $this->hasMany(Document::class)->where('type', 'form');
    }
}
