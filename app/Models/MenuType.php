<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuType extends Model
{
    use SoftDeletes;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'config.tbl_menu_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope a query to search menu types by name or description.
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        });
    }

    /**
     * Scope a query to order menu types.
     *
     * @param Builder $query
     * @param string $column
     * @param string $direction
     * @return Builder
     */
    public function scopeSort(Builder $query, string $column = 'name', string $direction = 'asc'): Builder
    {
        $validColumns = ['name', 'created_at', 'updated_at'];
        $column = in_array($column, $validColumns) ? $column : 'name';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        return $query->orderBy($column, $direction);
    }

    /**
     * Get the navigation items associated with this menu type.
     *
     * @return HasMany
     */
    public function navigationItems(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'menu_type_id');
    }
}
