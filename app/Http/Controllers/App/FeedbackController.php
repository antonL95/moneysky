<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Data\Request\FeedbackRequestData;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

final class FeedbackController
{
    public function store(FeedbackRequestData $data): void
    {
        Feedback::create([
            'user_id' => Auth::user()?->id,
            'notified' => false,
            'type' => $data->type,
            'description' => $data->description,
        ]);
    }
}
