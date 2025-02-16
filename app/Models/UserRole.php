<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserRole extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'auth.tbl_user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'description'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'role_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user that owns this role assignment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role for this assignment.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Scope for searching user roles.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                })->orWhereHas('role', function ($query) use ($search) {
                    $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                })->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        });
    }

    /**
     * Scope for sorting user roles.
     */
    public function scopeSort(Builder $query, string $column = 'created_at', string $direction = 'desc'): Builder
    {
        $validColumns = ['created_at', 'updated_at'];
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';

        // Handle relationship sorting
        if ($column === 'user_name') {
            return $query->orderBy(User::select('name')
                ->whereColumn('users.id', 'auth.tbl_user_roles.user_id')
                ->limit(1), $direction);
        }

        if ($column === 'role_name') {
            return $query->orderBy(Role::select('name')
                ->whereColumn('auth.tbl_roles.id', 'auth.tbl_user_roles.role_id')
                ->limit(1), $direction);
        }

        $column = in_array($column, $validColumns) ? $column : 'created_at';
        return $query->orderBy($column, $direction);
    }
}
