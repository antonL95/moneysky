<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

class NewsletterSubscribe extends Component
{
    use Toast;

    #[Rule(['required', 'email'])]
    public string $email;

    public function subscribe(): void
    {
        $this->validate();

        $subscriber = NewsletterSubscriber::createOrFirst([
            'email' => $this->email,
        ]);

        if ($subscriber->wasRecentlyCreated) {
            $this->success('You have been subscribed to our newsletter.');
            $this->email = '';

            return;
        }
        $this->success('You are already subscribed to our newsletter.');

        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.newsletter-subscribe');
    }
}
