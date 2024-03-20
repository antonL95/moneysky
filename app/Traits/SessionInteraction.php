<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\SessionMessage;
use Exception;
use TallStackUi\Actions\Toast;


trait SessionInteraction
{


    /**
     * @throws Exception
     */
    public function flashMessage(): Toast
    {
        if (session()->has(SessionMessage::SUCCESS->value)) {
            $message = session()->get(SessionMessage::SUCCESS->value);

            if (!\is_string($message) || $message === '') {
                $message = 'Resource was successfully created, updated or deleted.';
            }
            session()->forget(SessionMessage::SUCCESS->value);

            return $this->toast()->success($message);
        }

        if (session()->has(SessionMessage::ERROR->value)) {
            $message = session()->get(SessionMessage::ERROR->value);
            if (!\is_string($message) || $message === '') {
                $message = 'An error occurred.';
            }

            session()->forget(SessionMessage::ERROR->value);

            return $this->toast()->error($message);
        }

        if (session()->has(SessionMessage::INFO->value)) {
            $message = session()->get(SessionMessage::INFO->value);
            if (!\is_string($message) || $message === '') {
                $message = 'Information.';
            }

            session()->forget(SessionMessage::INFO->value);

            return $this->toast()->info($message);
        }

        if (session()->has(SessionMessage::WARNING->value)) {
            $message = session()->get(SessionMessage::WARNING->value);
            if (!\is_string($message) || $message === '') {
                $message = 'Warning.';
            }

            session()->forget(SessionMessage::WARNING->value);

            return $this->toast()->warning($message);
        }

        throw new Exception('No session message found.');
    }
}
