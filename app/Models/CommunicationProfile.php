<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunicationProfile extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_active',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'email_from_address',
        'email_from_name',
        'sms_provider',
        'sms_api_key',
        'sms_api_secret',
        'sms_from_number',
        'messaging_sender_id',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'smtp_port' => 'integer',
    ];
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeEmail($query)
    {
        return $query->where('type', 'email');
    }
    
    public function scopeSms($query)
    {
        return $query->where('type', 'sms');
    }
}
