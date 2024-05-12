<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerConfiguration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_template_id',
        'x_coordinate',
        'y_coordinate',
        'text',
        'font_id',
        'font_size',
        'font_angle',
        'font_color_in_hexadecimal',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Get the banner template associated with the banner configuration.
     */
    public function bannerTemplate(): BelongsTo
    {
        return $this->belongsTo(BannerTemplate::class);
    }

    /**
     * Get the font associated with the banner configuration.
     */
    public function font(): BelongsTo
    {
        return $this->belongsTo(Font::class);
    }
}
