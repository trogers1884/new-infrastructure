<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ResourceAssociation extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'auth.tbl_resource_associations';

    protected $fillable = [
        'user_id',
        'resource_type_id',
        'role_id',
        'description',
        'resource_id'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'resource_type_id' => 'integer',
        'role_id' => 'integer',
        'resource_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user that owns the resource association.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the resource type that owns the resource association.
     */
    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class);
    }

    /**
     * Get the role that owns the resource association.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Scope a query to search resource associations.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                })
                    ->orWhereHas('resourceType', function ($query) use ($search) {
                        $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                    })
                    ->orWhereHas('role', function ($query) use ($search) {
                        $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                    })
                    ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        });
    }

    /**
     * Scope a query to sort resource associations.
     */
    public function scopeSort(Builder $query, ?string $column = 'created_at', ?string $direction = 'desc'): Builder
    {
        $validColumns = ['created_at', 'updated_at'];
        $column = in_array($column, $validColumns) ? $column : 'created_at';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';

        return $query->orderBy($column, $direction);
    }
}
