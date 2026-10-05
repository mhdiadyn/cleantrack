<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'status',
        'notes',
        'photo_before',
        'photo_after',
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
        if ($this->photo_before && Storage::disk('public')->exists($this->photo_before)) {
            return Storage::url($this->photo_before);
        }

        return null;
    }

    /**
     * Get URL for photo_after.
     */
    public function getPhotoAfterUrlAttribute(): ?string
    {
        if ($this->photo_after && Storage::disk('public')->exists($this->photo_after)) {
            return Storage::url($this->photo_after);
        }

        return null;
    }
}
