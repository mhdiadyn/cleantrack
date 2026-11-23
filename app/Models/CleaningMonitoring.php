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
        'custom_area_name',
        'cleanliness_condition',
        'start_time',
        'end_time',
        'status',
        'notes',
        'photo_before',
        'photo_after',
        'room_photo',
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
     * Get the area name saved for this inspection.
     */
    public function getDisplayAreaNameAttribute(): string
    {
        return $this->custom_area_name ?: ($this->area?->name ?? '-');
    }

    /**
     * Get the area code saved for this inspection.
     */
    public function getDisplayAreaCodeAttribute(): ?string
    {
        return $this->area?->code ?? ($this->custom_area_name ? 'Manual' : null);
    }

    /**
     * Get the location saved for this inspection.
     */
    public function getDisplayAreaLocationAttribute(): string
    {
        return $this->area?->location ?? ($this->custom_area_name ? 'Input manual' : '-');
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

    public function getRoomPhotoUrlAttribute(): ?string
    {
        if ($this->room_photo) {
            if (str_starts_with($this->room_photo, 'http://') || str_starts_with($this->room_photo, 'https://')) {
                return $this->room_photo;
            }

            return asset('storage/'.ltrim($this->room_photo, '/'));
        }

        return null;
    }
}
