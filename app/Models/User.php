<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string name
 * @property string email
 * @property string password
 * @property Status[] statuses
 * @property mixed followers
 * @property mixed follows
 * @property bool isFollowing
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['isFollowing'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    public function externalOAuthService(): HasMany
    {
        return $this->hasMany(ExternalOAuthService::class);
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'followee_id');
    }

    public function getIsFollowingAttribute(): bool
    {
        return $this->followers->contains('follower_id', auth()->id());
    }
}
