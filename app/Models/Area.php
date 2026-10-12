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
        'floor',
        'pos_x',
        'pos_y',
        'photo_360',
        'photo_type',
        'description',
        'is_active',
    ];

    /**
     * The accessors to append to the model's array and JSON form.
     *
     * @var list<string>
     */
    protected $appends = [
        'code',
        'current_status',
        'photo_360_url',
        'is_360',
        'is_standard',
        'photo_type_label',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'floor' => 'integer',
        'pos_x' => 'float',
        'pos_y' => 'float',
    ];

    /**
     * Accessor for area code (e.g. AR-001).
     */
    public function getCodeAttribute(): string
    {
        return 'AR-'.str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the current real-time cleanliness status for this area.
     */
    public function getCurrentStatusAttribute(): string
    {
        $today = now()->toDateString();
        $latest = $this->cleaningMonitorings()->whereDate('check_date', $today)->first();

        if (! $latest) {
            return 'belum_dimonitor';
        }

        if ($latest->status === 'Sudah Dibersihkan' || $latest->cleanliness_condition === 'Bersih') {
            return 'bersih';
        }

        if ($latest->status === 'Sedang Dibersihkan' || $latest->cleanliness_condition === 'Cukup Bersih') {
            return 'perlu_tindakan';
        }

        return 'kotor';
    }

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

    /**
     * Get URL for photo_360.
     */
    public function getPhoto360UrlAttribute(): ?string
    {
        if (! $this->photo_360) {
            return null;
        }

        if (str_starts_with($this->photo_360, 'http://') || str_starts_with($this->photo_360, 'https://')) {
            return $this->photo_360;
        }

        return asset('storage/'.ltrim($this->photo_360, '/'));
    }

    /**
     * Check if area photo is a 360 degree panoramic view.
     */
    public function getIs360Attribute(): bool
    {
        return empty($this->photo_type) || $this->photo_type === 'panorama_360';
    }

    /**
     * Check if area photo is a standard (non-360) room photo.
     */
    public function getIsStandardAttribute(): bool
    {
        return $this->photo_type === 'standard';
    }

    /**
     * Get human-readable photo type label.
     */
    public function getPhotoTypeLabelAttribute(): string
    {
        return $this->is_standard ? 'Foto Ruangan Standar' : 'Panorama 360° Virtual';
    }
}
