<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\KrakenTradingPairsFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class KrakenTradingPairs extends Model
{
    /** @use HasFactory<KrakenTradingPairsFactory> */
    use HasFactory;

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
