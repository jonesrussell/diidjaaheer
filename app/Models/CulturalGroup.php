<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulturalGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'depth_type',
        'description',
        'media_id',
        'metadata',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CulturalGroup::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CulturalGroup::class, 'parent_id')->orderBy('sort_order');
    }

    public function teachings(): HasMany
    {
        return $this->hasMany(Teaching::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Walk up the tree from parent to root, returning a Collection ordered root-first.
     */
    public function ancestors(): Collection
    {
        $ancestors = new Collection;
        $current = $this->parent;
        $maxDepth = 20;

        while ($current !== null && $maxDepth-- > 0) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return $ancestors;
    }
}
