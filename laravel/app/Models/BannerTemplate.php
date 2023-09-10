<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BannerTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'banner_id',
        'template_id',
        'file_path_drawed_grid_text',
        'file_path_drawed_text',
        'redirect_url',
        'disable_at',
        'enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'disable_at' => 'datetime',
        'enabled' => 'boolean',
    ];

    /**
     * Get the banner associated with the model.
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Get the template associated with the model.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the template configurations for the banner.
     */
    public function configurations(): HasMany
    {
        return $this->hasMany(BannerConfiguration::class);
    }
}
