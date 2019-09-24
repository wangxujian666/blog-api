<?php

namespace App\Models;

use App\Observers\UserObserver;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string $password
 * @property string $we_chat_openid
 * @property mixed|null $user_info
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $is_admin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Favorite[] $favorites
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reply[] $replys
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUserInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereWeChatOpenid($value)
 * @mixin \Eloquent
 * @property mixed|null $settings
 * @property-read int|null $comments_count
 * @property-read int|null $favorites_count
 * @property-read int|null $likes_count
 * @property-read int|null $notifications_count
 * @property-read int|null $permissions_count
 * @property-read int|null $replys_count
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSettings($value)
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token', 'we_chat_openid', 'settings'];

    protected $guard_name = 'api';

    /**
     * 接收用户的频道广播通知.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'notification.' . $this->id;
    }

    public static function boot()
    {
        parent::boot();
        self::observe(UserObserver::class);
    }

    public function getNameAttribute($value)
    {
        if (!$value) {
            return Arr::get($this->user_info, 'nickName', '');
        }

        return $value;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setUserInfoAttribute($userInfo)
    {
        $this->attributes['user_info'] = is_array($userInfo) ? json_encode($userInfo) : $userInfo;
    }

    public function getUserInfoAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSettingsAttribute($settings)
    {
        $this->attributes['settings'] = is_array($settings) ? json_encode($settings) : $settings;
    }

    public function getSettingsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function replys()
    {
        return $this->hasMany(Reply::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
