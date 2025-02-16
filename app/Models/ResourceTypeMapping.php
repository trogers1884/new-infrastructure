<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceTypeMapping extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'pgsql';

    /**
     * The table associated with the model.
     */
    protected $table = 'auth.tbl_resource_type_mappings';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'resource_type_id';

    /**
     * Indicates if the model's primary key is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'resource_type_id',
        'table_schema',
        'table_name',
        'resource_value_column',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the resource type that owns the mapping.
     */
    public function resourceType(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id');
    }

    /**
     * Scope a query to search mappings.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(table_schema) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(table_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(resource_value_column) LIKE ?', [$search])
                    ->orWhereHas('resourceType', function ($query) use ($search) {
                        $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                    });
            });
        });
    }

    /**
     * Get the fully qualified table name.
     */
    public function getFullTableName(): string
    {
        return "{$this->table_schema}.{$this->table_name}";
    }

    /**
     * Scope a query to filter by schema.
     */
    public function scopeBySchema(Builder $query, ?string $schema): Builder
    {
        return $query->when($schema, function ($query) use ($schema) {
            return $query->where('table_schema', $schema);
        });
    }

    /**
     * Scope a query to filter by table.
     */
    public function scopeByTable(Builder $query, ?string $table): Builder
    {
        return $query->when($table, function ($query) use ($table) {
            return $query->where('table_name', $table);
        });
    }

    /**
     * Scope a query to order by the specified column.
     */
    public function scopeOrdered(Builder $query, ?string $column = 'table_schema', ?string $direction = 'asc'): Builder
    {
        $validColumns = ['table_schema', 'table_name', 'resource_value_column', 'created_at'];

        $column = in_array($column, $validColumns) ? $column : 'table_schema';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        return $query->orderBy($column, $direction);
    }
}
