<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingMemo extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'meeting_memos';

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
        return $this->belongsToMany(Category::class, 'category_meeting_memo');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
