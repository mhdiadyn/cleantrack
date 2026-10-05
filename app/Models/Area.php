<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Area extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all cleaning monitorings for this area.
     */
    public function cleaningMonitorings(): HasMany
    {
        return $this->hasMany(CleaningMonitoring::class, 'area_id')->latest('check_date')->latest('check_time');
    }

    /**
     * Get the latest monitoring record for this area.
     */
    public function latestMonitoring(): HasOne
    {
        return $this->hasOne(CleaningMonitoring::class, 'area_id')->latestOfMany();
    }
}
