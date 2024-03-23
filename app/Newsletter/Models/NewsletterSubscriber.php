<?php

declare(strict_types=1);

namespace App\Newsletter\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'email',
    ];
}
