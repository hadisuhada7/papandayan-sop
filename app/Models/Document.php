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
        'type',
        'standard_operational_id',
        'policy_letter_id',
        'internal_memo_id',
        'meeting_memo_id',
    ];

    // Constants
    const TYPE_SOP = 'sop';
    const TYPE_FORM = 'form';

    public function standardOperational()
    {
        return $this->belongsTo(StandardOperational::class, 'standard_operational_id');
    }

    public function policyLetter()
    {
        return $this->belongsTo(PolicyLetter::class, 'policy_letter_id');
    }

    public function internalMemo()
    {
        return $this->belongsTo(InternalMemo::class, 'internal_memo_id');
    }

    public function meetingMemo()
    {
        return $this->belongsTo(MeetingMemo::class, 'meeting_memo_id');
    }

    // Scopes
    public function scopeForStandardOperational($query) {
        return $query->whereNotNull('standard_operational_id');
    }

    public function scopeSop($query) {
        return $query->where('type', self::TYPE_SOP);
    }

    public function scopeForm($query) {
        return $query->where('type', self::TYPE_FORM);
    }
}
