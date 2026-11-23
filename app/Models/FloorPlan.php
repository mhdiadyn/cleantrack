<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FloorPlan extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'floor',
        'file_path',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'floor' => 'integer',
    ];

    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getIsPdfAttribute(): bool
    {
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
