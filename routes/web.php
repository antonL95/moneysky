<?php

declare(strict_types=1);

use App\Http\Controllers\UserBankAccountRedirect;
use App\Livewire\Dashboard;
use App\Livewire\ShowBankAccounts;
use App\Livewire\ShowCryptoWallets;
use App\Livewire\ShowKrakenAccounts;
use App\Livewire\ShowManualEntries;
use App\Livewire\ShowStockMarket;
use App\Models\BankInstitution;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;

Route::get('/', static fn (): View => view('welcome'))->name('home');

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    AuthenticateSession::class,
    'verified',
])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('app.home');

    // kraken accounts
    Route::get('/kraken-account', ShowKrakenAccounts::class)->name('app.kraken-accounts');

    // manual entry
    Route::get('/cash-wallets', ShowManualEntries::class)->name('app.manual-entries');

    // bank accounts
    Route::get('/bank-data-redirect', UserBankAccountRedirect::class)
        ->name('app.bank-data-redirect');

    Route::get('/bank-accounts', ShowBankAccounts::class)->name('app.bank-accounts');

    Route::get('/list-institutions', static function (Request $request) {
        $search = $request->get('search');
        if ($search === null) {
            $institutions = BankInstitution::limit(10)->get();
        } else {
            $institutions = BankInstitution::where(
                'name',
                'like',
                '%'.$search.'%',
            )->get();
        }

        return $institutions->map(
            function (BankInstitution $institution) {
                return [
                    'name' => $institution->name.'('.implode(',', $institution->countries).')',
                    'id' => $institution->id,
                    'image' => $institution->logo_url,
                ];
            },
        );
    })->name('app.list-institutions');

    // crypto wallets
    Route::get('/crypto-wallets', ShowCryptoWallets::class)->name('app.crypto-wallets');

    // stock market
    Route::get('/stock-market', ShowStockMarket::class)->name('app.stock-market');

    Route::get('/subscription-checkout/{plan}', static function (string $plan, Request $request) {
        if ($plan === 'plus') {
            $priceId = config('services.stripe.plus_plan_id');
        } else {
            $priceId = config('services.stripe.unlimited_plan_id');
        }

        if (!\is_string($priceId)) {
            abort(404);
        }

        return $request->user()
            ->newSubscription('default', $priceId)
            ->trialDays(7)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('stripe.subscription-success'),
                'cancel_url' => route('stripe.subscription-canceled'),
            ]);
    })->name('subscription-checkout');

    Route::get(
        '/stripe/success',
        static function () {
            $user = auth()->user();

            $user->trial_ends_at = now()->addDays(7);
            $user->save();

            return redirect(route('profile.show'));
        },
    )->name('stripe.subscription-success');

    Route::get(
        '/stripe/cancel',
        static fn () => redirect(route('profile.show')),
    )->name('stripe.subscription-canceled');

    Route::get(
        '/billing',
        static fn (Request $request) => $request->user()->redirectToBillingPortal(),
    )->name('billing');
});

// Email verification
Route::get('/email/verify', static function () {
    if (auth()->user()?->hasVerifiedEmail()) {
        return redirect(route('app.home'));
    }

    return view('auth.verify-email');
})
    ->middleware(['auth'])
    ->name('verification.notice');

Route::post('/email/verification-notification', static function () {
    auth()->user()?->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', static function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect(route('app.home', ['verified' => true]));
})->middleware(['auth', 'signed'])->name('verification.verify');
