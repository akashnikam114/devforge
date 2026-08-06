<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    use HasFactory;

    protected $fillable = [
        'restriction_name',
        'is_restriction_enabled',
        'image',
        'title',
        'sub_title',
        'url_label',
        'redirection_url',
        'is_button_enabled'
    ];
}
