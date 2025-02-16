<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Components\Admin\Traits\UserAuthorization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UserAuthorization;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'pgsql';

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'auth.tbl_user_roles',
            'user_id',
            'role_id'
        )->withTimestamps()
            ->withPivot('description');
    }

    /**
     * Check if the user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->exists();
    }

    /**
     * Check if the user has any of the specified roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()
            ->whereIn('name', $roleNames)
            ->exists();
    }

    /**
     * Scope a query to filter users by search term.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        Log::info('Search scope called with:', ['search' => $search]);

        return $query->when($search, function (Builder $query) use ($search): Builder {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function (Builder $query) use ($search): Builder {
                return $query->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
            });
        });
    }

    /**
     * Scope a query to filter users by active status.
     */
    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        Log::info('Filter status scope called with:', ['status' => $status]);

        return $query->when($status !== null, function (Builder $query) use ($status): Builder {
            return $query->where('active', $status === 'active');
        });
    }

    /**
     * Get the user's full name or email if name is not set.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }
}
