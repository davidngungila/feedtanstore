<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_photo',
        'check_out_photo',
        'total_hours',
        'check_in_address',
        'check_out_address',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_hours' => 'decimal:2',
        'check_in_latitude' => 'decimal:7',
        'check_in_longitude' => 'decimal:7',
        'check_out_latitude' => 'decimal:7',
        'check_out_longitude' => 'decimal:7',
    ];

    protected $appends = ['check_in_photo_url', 'check_out_photo_url', 'working_hours_formatted'];

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        return $this->check_in_photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->check_in_photo) : null;
    }

    public function getCheckOutPhotoUrlAttribute(): ?string
    {
        return $this->check_out_photo ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->check_out_photo) : null;
    }

    public function getWorkingHoursFormattedAttribute(): ?string
    {
        if (!$this->total_hours) return null;
        $hours = floor($this->total_hours);
        $minutes = round(($this->total_hours - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
