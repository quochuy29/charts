<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    protected $table = 'm_roles';
    protected $primaryKey = 'role_code';
    protected $fillable = ['role_code', 'role_name'];
}