<?php

declare(strict_types=1);

use App\Bank\Http\Controllers\UserBankAccountRedirect;
use App\Livewire\AddCryptoWallet;
use App\Livewire\AddUserKrakenAccount;
use App\Livewire\CryptoWallets;
use App\Livewire\Dashboard;
use App\Livewire\EditCryptoWallet;
use App\Livewire\ShowBankAccounts;
use App\Livewire\ShowKrakenAccounts;
use App\Livewire\UpdateUserKrakenAccount;
use App\Livewire\Welcome;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;

Route::get('/', Welcome::class)
    ->middleware(RedirectIfAuthenticated::class)
    ->name('home');

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    AuthenticateSession::class,
    'verified',
])->group(function () {
    Route::prefix('app')->group(function () {
        Route::get('/', Dashboard::class)->name('app.home');
        Route::get('/profile', static fn () => view('profile.show'))->name('profile.show');
        Route::get('/bank-data-redirect', UserBankAccountRedirect::class)->name('app.bank-data-redirect');
        Route::get('/crypto-wallets', CryptoWallets::class)->name('app.crypto-wallets');
        Route::get('/add-crypto-wallets', AddCryptoWallet::class)->name('app.add-crypto-wallets');
        Route::get('/edit-crypto-wallets/{wallet}', EditCryptoWallet::class)->name('app.edit-crypto-wallets');
        Route::get('/bank-accounts', ShowBankAccounts::class)->name('app.bank-accounts');
        Route::get('/kraken-account', ShowKrakenAccounts::class)->name('app.kraken-accounts');
        Route::get('/add-kraken-account', AddUserKrakenAccount::class)->name('app.add-kraken-accounts');
        Route::get('/update-kraken-account/{account}', UpdateUserKrakenAccount::class)->name('app.update-kraken-accounts');
    });

    Route::get('/subscription-checkout', static function (Request $request) {
        return $request->user()
            ->newSubscription('default', 'price_1Os1caK5HYS5TbLBmsjD52yB')
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('your-success-route'),
                'cancel_url' => route('your-cancel-route'),
            ]);
    })->name('subscription-checkout');

    Route::get('/stripe/success', static fn () => redirect(route('app.home'))->with('message', 'Subscription successful!'))->name('your-success-route');

    Route::get('/stripe/cancel', static fn () => redirect(route('app.home'))->with('message', 'Subscription canceled!'))->name('your-cancel-route');

    Route::get('/billing', static fn (Request $request) => $request->user()->redirectToBillingPortal())->name('billing');
});

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
