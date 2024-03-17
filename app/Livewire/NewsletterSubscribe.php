<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class NewsletterSubscribe extends Component
{
    use Interactions;

    #[Rule(['required', 'email'])]
    public string $email;

    public function subscribe(): void
    {
        $this->validate();

        $subscriber = NewsletterSubscriber::createOrFirst([
            'email' => $this->email,
        ]);

        if ($subscriber->wasRecentlyCreated) {
            $this->toast()->success('You have been subscribed to our newsletter.')->send();
            $this->email = '';

            return;
        }

        $this->toast()->info('You are already subscribed to our newsletter.')->send();
        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.newsletter-subscribe');
    }
}
