<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'email',
        'category_user',
        'company_id',
        'user_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_user_detail');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Model events to cascade delete/restore related User and detach categories.
     */
    protected static function booted()
    {
        static::deleting(function (UserDetail $userDetail) {
            if (method_exists($userDetail, 'categories')) {
                $userDetail->categories()->detach();
            }

            // Soft-delete related user if exists
            $user = $userDetail->user();
            if ($user->exists()) {
                $related = $user->first();
                if ($related) {
                    $related->delete();
                }
            }
        });

        static::restored(function (UserDetail $userDetail) {
            // Restore related user if it was soft-deleted
            $userQuery = $userDetail->user()->withTrashed();
            if ($userQuery->exists()) {
                $related = $userQuery->first();
                if (method_exists($related, 'restore')) {
                    $related->restore();
                }
            }
        });
    }
}
