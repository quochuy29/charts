<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Xác định tên bảng mới
     */
    protected $table = 't_users';

    protected $fillable = [
        'user_id',
        'display_name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User có nhiều Role thông qua bảng t_user_roles
     */
    public function roles(): BelongsToMany
    {
        // belongsToMany(RelatedModel, pivot_table, foreign_key, related_key)
        return $this->belongsToMany(Role::class, 't_user_roles', 'user_id', 'role_code');
    }

    /**
     * Helper: Kiểm tra xem User có phải là Display User (Persistent) không
     * Role ID 3 = display_user
     */
    public function isPersistent(): bool
    {
        return $this->roles()->where('m_roles.role_code', 3)->exists();
    }
}