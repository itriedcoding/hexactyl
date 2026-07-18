<?php

namespace Hexactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServerTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'author',
        'version',
        'is_public',
        'is_default',
        'egg_id',
        'nest_id',
        'location_id',
        'docker_image',
        'startup',
        'environment',
        'limits',
        'feature_limits',
        'allocations',
        'startup_commands',
        'post_install_commands',
        'tags',
        'icon',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_default' => 'boolean',
        'startup' => 'array',
        'environment' => 'array',
        'limits' => 'array',
        'feature_limits' => 'array',
        'allocations' => 'array',
        'startup_commands' => 'array',
        'post_install_commands' => 'array',
        'tags' => 'array',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ServerTemplate $template) {
            $template->uuid = $template->uuid ?? Str::uuid()->toString();
        });
    }

    /**
     * Get the egg associated with this template.
     */
    public function egg(): BelongsTo
    {
        return $this->belongsTo(Egg::class);
    }

    /**
     * Get the nest associated with this template.
     */
    public function nest(): BelongsTo
    {
        return $this->belongsTo(Nest::class);
    }

    /**
     * Get the location associated with this template.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get servers created from this template.
     */
    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    /**
     * Mark template as used.
     */
    public function markAsUsed(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get tags as a formatted string.
     */
    public function getTagsAttribute(): ?string
    {
        return is_array($this->attributes['tags'])
            ? implode(', ', $this->attributes['tags'])
            : $this->attributes['tags'];
    }

    /**
     * Scope to only public templates.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to only default templates.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to templates for a specific author.
     */
    public function scopeForAuthor($query, string $author)
    {
        return $query->where('author', $author);
    }

    /**
     * Get the formatted usage count.
     */
    public function getFormattedUsageCountAttribute(): string
    {
        return number_format($this->usage_count);
    }
}
