<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, AuthenticationLoggable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'nick_name',
        'email',
        'mobile',
        'email_verified_at',
        'password',
        'image',
        'role',
        'email_verification_status',
        'phone_verification_status',
        'google_auth_status',
        'google2fa_secret',
        'google_id',
        'facebook_id',
        'verify_token',
        'otp',
        'otp_expiry',
        'show_email_in_public',
        'show_phone_in_public',
        'last_seen',
        'created_by',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen' => 'datetime'
    ];

    public function alumni(){
        return $this->hasOne(Alumni::class);
    }

    public function institutions(){
        return $this->hasMany(UserInstitution::class, 'user_id');
    }

    public function currentMembership(){
        return $this->hasOne(UserMembershipPlan::class, 'user_id')->where('expired_date', '>=', now())->latest();
    }
   
    public function unseen_message()
    {
        return $this->hasMany(Chat::class, 'sender_id')->where(['is_seen' => STATUS_PENDING]);
    }
   
    public function messages()
    {
        return $this->hasMany(Chat::class, 'receiver_id')->where('sender_id' , auth()->id());
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid = Str::uuid()->toString();
        });
    }
}
