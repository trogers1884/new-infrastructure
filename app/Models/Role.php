<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'auth.tbl_roles';
    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'auth.tbl_user_roles',
            'role_id',
            'user_id'
        )->withTimestamps()
            ->withPivot('description');
    }

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'auth.role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to search roles by name.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->whereRaw('LOWER(name) LIKE ?', [$search]);
        });
    }
}
