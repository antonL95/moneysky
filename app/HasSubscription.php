<?php

declare(strict_types=1);

namespace App;

use App\Models\UserBankSession;
use App\Models\UserCryptoWallets;
use App\Models\UserKrakenAccount;
use App\Models\UserManualEntry;
use App\Models\UserStockMarket;

trait HasSubscription
{
    public function canAddAdditionalResource(
        string $resource,
    ): bool {
        if ($this->demo) {
            return false;
        }

        $unlimitedPriceId = config('services.stripe.unlimited_plan_id');

        if (!\is_string($unlimitedPriceId)) {
            return false;
        }

        if ($this->subscribed(price: $unlimitedPriceId)) {
            return true;
        }

        if ($resource === UserManualEntry::class) {
            return true;
        }

        if ($resource === UserKrakenAccount::class) {
            return true;
        }

        $plusPriceId = config('services.stripe.plus_plan_id');

        if (!\is_string($plusPriceId)) {
            return false;
        }

        $numberOfTickers = UserStockMarket::count();

        $numberOfBankAccounts = UserBankSession::count();

        $numberOfCryptoWallets = UserCryptoWallets::count();

        if ($this->subscribed(price: $plusPriceId)) {
            if ($resource === UserStockMarket::class) {
                $numberOfTickers = UserStockMarket::count();

                return $numberOfTickers < 15;
            }

            if ($resource === UserBankSession::class) {
                return $numberOfBankAccounts <= 0;
            }

            if ($resource === UserCryptoWallets::class) {
                return $numberOfCryptoWallets <= 0;
            }
        }

        if ($numberOfTickers < 3 && $numberOfCryptoWallets <= 0 && $numberOfBankAccounts <= 0 && $resource === UserStockMarket::class) {
            return true;
        }

        if ($numberOfTickers <= 0 && $numberOfCryptoWallets <= 0 && $numberOfBankAccounts <= 0 && $resource === UserCryptoWallets::class) {
            return true;
        }

        if ($numberOfTickers <= 0 && $numberOfCryptoWallets <= 0 && $numberOfBankAccounts <= 0 && $resource === UserBankSession::class) {
            return true;
        }

        return false;
    }

    public function checkSubscriptionType(
        string $type,
    ): bool {
        $plusPriceId = config('services.stripe.plus_plan_id');
        $unlimitedPriceId = config('services.stripe.unlimited_plan_id');

        if (!\is_string($plusPriceId) || !\is_string($unlimitedPriceId)) {
            return false;
        }

        if ($type === 'unlimited' && $this->subscribed(price: $unlimitedPriceId)) {
            return true;
        }

        if ($type === 'plus' && $this->subscribed(price: $plusPriceId)) {
            return true;
        }

        return false;
    }
}
