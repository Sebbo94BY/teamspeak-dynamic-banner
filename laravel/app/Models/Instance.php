<?php

namespace App\Models;

use App\Http\Controllers\BannerVariableController;
use App\Models\Concerns\TracksUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instance extends Model
{
    use HasFactory;
    use TracksUpdatedBy;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'virtualserver_name',
        'host',
        'voice_port',
        'serverquery_port',
        'is_ssh',
        'serverquery_username',
        'serverquery_password',
        'client_nickname',
        'default_channel_id',
        'autostart_enabled',
        'health_last_error',
        'health_last_error_at',
        'health_last_runtime_error_at',
        'health_last_success_at',
        'health_last_check_healthy',
        'health_last_check_results',
        'health_channel_list',
        'health_last_checked_at',
        'health_problem_started_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'serverquery_password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_ssh' => 'boolean',
            'serverquery_password' => 'encrypted',
            'autostart_enabled' => 'boolean',
            'health_last_error_at' => 'datetime',
            'health_last_runtime_error_at' => 'datetime',
            'health_last_success_at' => 'datetime',
            'health_last_check_healthy' => 'boolean',
            'health_last_check_results' => 'array',
            'health_channel_list' => 'array',
            'health_last_checked_at' => 'datetime',
            'health_problem_started_at' => 'datetime',
        ];
    }

    /**
     * Get the process associated with the model.
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(InstanceProcess::class, 'id', 'instance_id');
    }

    /**
     * Persist runtime state without treating it as an administrator changing
     * the instance configuration.
     *
     * Health checks and automatic bot recovery are operational metadata. They
     * must not alter updated_at, which powers the dashboard's recent
     * configuration changes timeline.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function saveOperationalState(array $attributes): bool
    {
        // Commands may be exercised with an in-memory model in tests. A bot
        // loaded by the application always has a persisted instance, while
        // attempting an UPDATE for a model that does not exist would obscure
        // the original runtime error.
        if (! $this->exists) {
            $this->forceFill($attributes);

            return false;
        }

        return static::withoutTimestamps(fn (): bool => $this->forceFill($attributes)->save());
    }

    /**
     * Get the instance variables
     */
    public function variables(): array
    {
        $varController = new BannerVariableController;

        return $varController->getInstanceVariables($this);
    }
}
