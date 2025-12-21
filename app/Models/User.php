<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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

   public function roles(): BelongsToMany
    {
        // Cú pháp: belongsToMany(RelatedModel, pivot_table, foreignPivotKey, relatedPivotKey, parentKey, relatedKey)
        // Tham số thứ 5 ('user_id') là quan trọng nhất để sửa lỗi này.
        return $this->belongsToMany(
            Role::class,      // Model liên kết
            't_user_roles',   // Bảng trung gian
            'user_id',        // Tên cột khóa ngoại trong bảng pivot trỏ về User (t_user_roles.user_id)
            'role_code',        // Tên cột khóa ngoại trong bảng pivot trỏ về Role (t_user_roles.role_id)
            'user_id',        // <--- QUAN TRỌNG: Tên cột khóa chính thực tế trên bảng Users (t_users.user_id - String)
            'role_code'              // Tên cột khóa chính trên bảng Roles (m_roles.id - Integer)
        );
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