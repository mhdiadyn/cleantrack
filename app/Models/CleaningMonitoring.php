<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleaningMonitoring extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cleaning_monitorings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'area_id',
        'cleanliness_condition',
        'start_time',
        'end_time',
        'status',
        'notes',
        'photo_before',
        'photo_after',
        'photo_360',
        'photo_360_type',
        'check_date',
        'check_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_date' => 'date:Y-m-d',
    ];

    /**
     * Get formatted start time (Jam Mulai / Sebelum).
     */
    public function getFormattedStartTimeAttribute(): ?string
    {
        if (! $this->start_time) {
            return null;
        }

        return substr($this->start_time, 0, 5);
    }

    /**
     * Get formatted end time (Jam Selesai / Sesudah).
     */
    public function getFormattedEndTimeAttribute(): ?string
    {
        if (! $this->end_time) {
            return null;
        }

        return substr($this->end_time, 0, 5);
    }

    /**
     * Get cleaning duration text (e.g. 30 Menit).
     */
    public function getDurationTextAttribute(): ?string
    {
        if (! $this->start_time || ! $this->end_time) {
            return null;
        }

        try {
            $start = Carbon::createFromTimeString($this->start_time);
            $end = Carbon::createFromTimeString($this->end_time);

            if ($end->lessThan($start)) {
                $end->addDay();
            }

            $diffMinutes = $start->diffInMinutes($end);
            if ($diffMinutes >= 60) {
                $hours = floor($diffMinutes / 60);
                $mins = $diffMinutes % 60;

                return $mins > 0 ? "{$hours} Jam {$mins} Menit" : "{$hours} Jam";
            }

            return "{$diffMinutes} Menit";
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the petugas/user that recorded this monitoring.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the area associated with this monitoring.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Get URL for photo_before.
     */
    public function getPhotoBeforeUrlAttribute(): ?string
    {
        if (! $this->photo_before) {
            return null;
        }

        if (str_starts_with($this->photo_before, 'http://') || str_starts_with($this->photo_before, 'https://')) {
            return $this->photo_before;
        }

        return asset('storage/'.ltrim($this->photo_before, '/'));
    }

    /**
     * Get URL for photo_after.
     */
    public function getPhotoAfterUrlAttribute(): ?string
    {
        if (! $this->photo_after) {
            return null;
        }

        if (str_starts_with($this->photo_after, 'http://') || str_starts_with($this->photo_after, 'https://')) {
            return $this->photo_after;
        }

        return asset('storage/'.ltrim($this->photo_after, '/'));
    }

    /**
     * Get URL for photo_360.
     */
    public function getPhoto360UrlAttribute(): ?string
    {
        if ($this->photo_360) {
            if (str_starts_with($this->photo_360, 'http://') || str_starts_with($this->photo_360, 'https://')) {
                return $this->photo_360;
            }

            return asset('storage/'.ltrim($this->photo_360, '/'));
        }

        // Fallback to area's 360 photo if available
        if ($this->area && $this->area->photo_360_url) {
            return $this->area->photo_360_url;
        }

        return null;
    }

    /**
     * Get resolved photo type for 360/room photo.
     */
    public function getResolvedPhoto360TypeAttribute(): string
    {
        if ($this->photo_360) {
            return $this->photo_360_type ?: 'panorama_360';
        }

        if ($this->area && $this->area->photo_type) {
            return $this->area->photo_type;
        }

        return 'panorama_360';
    }

    /**
     * Check if monitoring photo 360 is a panoramic equirectangular photo.
     */
    public function getIs360Attribute(): bool
    {
        return $this->resolved_photo_360_type === 'panorama_360';
    }

    /**
     * Check if monitoring photo is a standard room photo.
     */
    public function getIsStandardAttribute(): bool
    {
        return $this->resolved_photo_360_type === 'standard';
    }
}
