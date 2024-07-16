<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $table = 'subscriptions';
    protected $fillable = [
        'email', 'token', 'confirmed', 'unsub_token'
    ];

    protected $casts = [
        'confirmed' => 'boolean',
    ];
}
