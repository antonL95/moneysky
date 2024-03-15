<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class NewsletterSubscribe extends Component
{
    #[Rule(['required', 'email'])]
    public string $email;

    public function subscribe(): void
    {
        $this->validate();

        NewsletterSubscriber::createOrRestore([
            'email' => $this->email,
        ]);

        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.newsletter-subscribe');
    }
}
