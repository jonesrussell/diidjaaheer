<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teaching extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'media_id',
        'cultural_group_id',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function culturalGroup(): BelongsTo
    {
        return $this->belongsTo(CulturalGroup::class);
    }
}
