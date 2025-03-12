<?php

declare(strict_types=1);

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PrivacyController;
use App\Http\Controllers\App\TosController;
use App\Http\Controllers\App\UserBankAccountController;
use App\Http\Controllers\App\UserBudgetController;
use App\Http\Controllers\App\UserCryptoWalletController;
use App\Http\Controllers\App\UserKrakenAccountController;
use App\Http\Controllers\App\UserManualEntryController;
use App\Http\Controllers\App\UserStockMarketController;
use App\Http\Controllers\App\UserTransactionController;
use App\Http\Middleware\Subscribed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('welcome'))->name('home');

Route::get('/terms', TosController::class)->name('terms.show');
Route::get('/privacy', PrivacyController::class)->name('policy.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('manual-entry', UserManualEntryController::class)->except('show', 'edit');
    Route::resource('budget', UserBudgetController::class)->except('show', 'index', 'edit');
    Route::post('spending/transactions', [UserTransactionController::class, 'store'])->name('spending.transaction.store');
    Route::put('spending/transactions/{user_transaction}', [UserTransactionController::class, 'update'])->name('spending.transaction.update');

    Route::get(
        'subscribe',
        static fn () => Inertia::render('Auth/Subscribe'),
    )->name('subscribe');

    Route::middleware([Subscribed::class])->group(function () {
        Route::get('renew-bank-connection/{userBankAccount}', [UserBankAccountController::class, 'renewRedirect'])->name('bank-account.renew-redirect');
        Route::get('user-bank-redirect/{bankInstitution}', [UserBankAccountController::class, 'connectRedirect'])->name('bank-account.redirect');
        Route::get('connect-bank/{userBankSession}', [UserBankAccountController::class, 'renew'])->name('bank-account.renew-callback');
        Route::get('connect-bank', [UserBankAccountController::class, 'connect'])->name('bank-account.callback');
        Route::post('bank-account', [UserBankAccountController::class, 'search'])->name('bank-account.search');

        Route::resource('bank-account', UserBankAccountController::class)->except('show', 'create', 'edit');
        Route::resource('digital-wallet', UserCryptoWalletController::class)->except('show', 'edit');
        Route::resource('stock-market', UserStockMarketController::class)->except('show', 'edit');
        Route::resource('kraken-account', UserKrakenAccountController::class)->except('show', 'edit');
    });

    Route::get('subscription-checkout', static function (Request $request): RedirectResponse {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $priceId = Config::string('services.stripe.monthly_price_id');

        return $user->newSubscription('default', $priceId)
            ->trialDays(14)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('stripe.subscription-success'),
                'cancel_url' => route('stripe.subscription-canceled'),
            ])->redirect();
    })->name('stripe.subscription-checkout');

    Route::get('stripe/success', static function () {
        $user = auth()->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $user->trial_ends_at = now()->addDays(14); // @phpstan-ignore-line
        $user->save();

        return redirect(route('profile.edit'));
    })->name('stripe.subscription-success');

    Route::get('stripe/cancel', static fn () => redirect(route('profile.edit')))->name('stripe.subscription-canceled');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
