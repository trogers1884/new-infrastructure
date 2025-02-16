<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'config.tbl_navigation_items';

    protected $fillable = [
        'menu_type_id',
        'name',
        'route',
        'icon',
        'order_index',
        'parent_id',
        'is_active'
    ];

    protected $casts = [
        'id' => 'integer',
        'menu_type_id' => 'integer',
        'parent_id' => 'integer',
        'order_index' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the menu type that owns the navigation item.
     */
    public function menuType(): BelongsTo
    {
        return $this->belongsTo(MenuType::class, 'menu_type_id');
    }

    /**
     * Get the parent navigation item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    /**
     * Get the child navigation items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id');
    }

    /**
     * Scope a query to search navigation items.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(route) LIKE ?', [$search]);
            });
        });
    }

    /**
     * Scope a query to filter by menu type.
     */
    public function scopeByMenuType(Builder $query, ?int $menuTypeId): Builder
    {
        return $query->when($menuTypeId, function ($query) use ($menuTypeId) {
            return $query->where('menu_type_id', $menuTypeId);
        });
    }

    /**
     * Scope a query to filter by active status.
     */
    public function scopeActive(Builder $query, ?bool $active = true): Builder
    {
        return $query->where('is_active', $active);
    }

    /**
     * Scope a query to order by menu type and order index.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('menu_type_id')
            ->orderBy('order_index');
    }
}
