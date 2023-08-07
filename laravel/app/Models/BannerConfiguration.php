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
        'fontfile_path',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Get the banner template associated with the banner configuration.
     */
    public function bannerTemplate(): BelongsTo
    {
        return $this->belongsTo(BannerTemplate::class);
    }

    /**
     * Get the template associated with the banner configuration.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
