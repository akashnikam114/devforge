<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RazorpayWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'payload_data',
    ];

    protected $casts = [
        'payload_data' => 'array',
    ];
}
