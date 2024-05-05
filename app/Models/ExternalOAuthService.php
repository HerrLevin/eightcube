<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ExternalOAuthService extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'token',
        'refresh_token',
    ];

    protected $hidden = [
        'token',
        'refresh_token',
    ];


    public function setTokenAttribute($value): void
    {
        $this->attributes['token'] = Crypt::encrypt($value);
    }

    public function getTokenAttribute($value): string
    {
        return Crypt::decrypt($value);
    }

    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = Crypt::encrypt($value);
    }

    public function getRefreshTokenAttribute($value): string
    {
        return Crypt::decrypt($value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
