<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class ResourceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'auth.tbl_resource_types';
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

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $search = '%' . strtolower($search) . '%';
            return $query->where(function($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$search]);
            });
        });
    }
}
