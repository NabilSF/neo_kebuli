<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    /**
     * Check if user has a specific role (supports hierarchical access).
     * super-admin inherits all roles below it.
     */
    public function hasRole($roleSlug)
    {
        // Define role hierarchy: higher roles inherit lower ones
        $hierarchy = [
            'super-admin' => ['super-admin', 'superadmin', 'admin', 'operator'],
            'superadmin'  => ['super-admin', 'superadmin', 'admin', 'operator'],
            'admin'       => ['admin', 'operator'],
            'operator'    => ['operator'],
        ];

        $userRoles = $this->roles->pluck('slug')->toArray();

        foreach ($userRoles as $userRole) {
            // Direct match
            if ($userRole === $roleSlug) {
                return true;
            }
            // Hierarchical match: does the user's role inherit the requested role?
            if (isset($hierarchy[$userRole]) && in_array($roleSlug, $hierarchy[$userRole])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get role names attached to user.
     */
    public function getRoleNames()
    {
        return $this->roles->pluck('name');
    }
}
