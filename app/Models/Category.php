<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function userDetails()
    {
        return $this->belongsToMany(UserDetail::class, 'category_user_detail');
    }

    public function standardOperationals()
    {
        return $this->belongsToMany(StandardOperational::class, 'category_standard_operational');
    }

    public function policyLetters()
    {
        return $this->belongsToMany(PolicyLetter::class, 'category_policy_letter');
    }

    public function internalMemos()
    {
        return $this->belongsToMany(InternalMemo::class, 'category_internal_memo');
    }

    public function meetingMemos()
    {
        return $this->belongsToMany(MeetingMemo::class, 'category_meeting_memo');
    }
}
