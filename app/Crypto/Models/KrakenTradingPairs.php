<?php

declare(strict_types=1);

namespace App\Crypto\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class KrakenTradingPairs extends Model
{
    use HasTimestamps;

    protected $fillable = [
        'key_pair',
        'crypto',
        'fiat',
        'trade_value_cents',
    ];

    protected $casts = [
        'trade_value_cents' => 'integer',
    ];
}
