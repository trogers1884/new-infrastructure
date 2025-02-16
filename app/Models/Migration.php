<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Migration extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'public.migrations';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'migration',
        'batch'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'batch' => 'integer',
    ];

    /**
     * Scope a query to search migrations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(public.migrations.migration) LIKE ?', [$search]);
            });
        });
    }

    /**
     * Format the migration name for display.
     *
     * @return string
     */
    public function getFormattedNameAttribute(): string
    {
        // Convert migration filename to a more readable format
        // Example: "2024_01_23_create_users_table" becomes "Create Users Table"
        $name = str_replace('.php', '', $this->migration);
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_/', '', $name);
        $name = str_replace('_', ' ', $name);
        return ucwords($name);
    }
}
