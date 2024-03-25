<?php

declare(strict_types=1);

use App\Bank\Http\Controllers\UserBankAccountRedirect;
use App\Bank\Models\BankInstitution;
use App\Http\Middleware\Subscribed;
use App\Livewire\AddUserCryptoWallet;
use App\Livewire\AddUserKrakenAccount;
use App\Livewire\AddUserManualEntries;
use App\Livewire\AddUserStockMarket;
use App\Livewire\Dashboard;
use App\Livewire\ShowBankAccounts;
use App\Livewire\ShowCryptoWallets;
use App\Livewire\ShowKrakenAccounts;
use App\Livewire\ShowManualEntries;
use App\Livewire\ShowStockMarket;
use App\Livewire\UpdateUserCryptoWallet;
use App\Livewire\UpdateUserKrakenAccount;
use App\Livewire\UpdateUserManualEntries;
use App\Livewire\UpdateUserStockMarket;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Route;

Route::get('/', static fn () => view('welcome'))
    ->name('home');

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    AuthenticateSession::class,
    'verified',
])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('app.home');

    // kraken accounts
    Route::get('/kraken-account', ShowKrakenAccounts::class)->name('app.kraken-accounts');
    Route::get('/add-kraken-account', AddUserKrakenAccount::class)->name('app.add-kraken-accounts');
    Route::get('/update-kraken-account/{account}', UpdateUserKrakenAccount::class)->name('app.update-kraken-accounts');

    // manual entry
    Route::get('/cash-wallets', ShowManualEntries::class)->name('app.manual-entries');
    Route::get('/add-cash-wallet', AddUserManualEntries::class)->name('app.add-manual-entry');
    Route::get('/update-cash-wallet/{wallet}', UpdateUserManualEntries::class)->name('app.update-manual-entry');

    Route::middleware(Subscribed::class)->group(function () {
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
        Route::get('/add-crypto-wallets', AddUserCryptoWallet::class)->name('app.add-crypto-wallets');
        Route::get('/edit-crypto-wallets/{wallet}', UpdateUserCryptoWallet::class)->name('app.edit-crypto-wallets');

        // stock market
        Route::get('/stock-market', ShowStockMarket::class)->name('app.stock-market');
        Route::get('/add-stock-market', AddUserStockMarket::class)->name('app.add-stock-market');
        Route::get('/update-stock-market/{ticker}', UpdateUserStockMarket::class)->name('app.update-stock-market');
    });

    Route::get('/subscription-checkout/{plan}', static function (Request $request) {
        if ($request->get('plan') === 'yearly') {
            $priceId = config('services.stripe.yearly_plan');
        } else {
            $priceId = config('services.stripe.monthly_plan');
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
        static fn (Request $request) => !$request->user()->subscribed()
            ? redirect(route('subscription-checkout', 'monthly'))
            : $request->user()->redirectToBillingPortal(),
    )
        ->name('billing');
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
