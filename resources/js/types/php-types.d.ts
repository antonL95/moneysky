declare namespace App.Data.App {
    export type FlashData = {
        type: App.Enums.FlashMessageType;
        title: string;
        description: string | null;
    };
    export type UserData = {
        id: number;
        name: string;
        email: string;
        currency: string;
        isSubscribed: boolean;
        emailVerified: boolean;
    };
}
declare namespace App.Data.App.BankAccount {
    export type BankAccountData = {
        name: string;
    };
    export type BankInstitutionData = {
        id: number;
        name: string;
        logo: string;
        countries: string | null;
    };
    export type UserBankAccountData = {
        id: number;
        name: string | null;
        balance: string | null;
        accessExpired: boolean;
        status: App.Enums.BankAccountStatus;
    };
}
declare namespace App.Data.App.CryptoWallet {
    export type CryptoWalletData = {
        address: string;
        chainType: App.Enums.ChainType;
    };
    export type UserCryptoWalletData = {
        id: number;
        walletAddress: string;
        chainType: App.Enums.ChainType;
        chainName: string;
        balance: string | null;
    };
}
declare namespace App.Data.App.Dashboard {
    export type AssetData = {
        assetName: string;
        balance: string;
        balanceNumeric: number;
        color: string;
    };
    export type BudgetData = {
        name: string;
        balance: number;
        currency: string;
        tags: Array | null;
    };
    export type HistoricalAssetData = {
        date: string;
        balance: string;
        balanceNumeric: number;
    };
    export type HistoricalAssetsData = {
        assetName: string;
        color: string;
        assetsData: { [key: number]: App.Data.App.Dashboard.HistoricalAssetData } | Array;
    };
    export type TagData = {
        id: number;
        name: string;
    };
    export type TransactionAggregateData = {
        name: string;
        value: string;
        amount: number;
        tagId: string | number | null;
    };
    export type TransactionData = {
        balance: number;
        currency: string;
        description: string | null;
        transaction_tag_id: number | null;
        user_manual_entry_id: number | null;
    };
    export type UserBudgetData = {
        id: number;
        name: string;
        spent: number;
        budget: number;
        currency: string;
        tags: Array;
        budgetId: number;
    };
    export type UserTransactionData = {
        id: number;
        balance: string;
        amount: number;
        description: string | null;
        currency: string;
        bookedAt: string;
        userManualEntryId: number | null;
        transactionTagId: number | null;
        transactionType: App.Enums.TransactionType;
        bankAccountName: string | null;
        cashWalletName: string | null;
    };
}
declare namespace App.Data.App.KrakenAccount {
    export type KrakenAccountData = {
        apiKey: string;
        privateKey: string;
    };
    export type UserKrakenAccountData = {
        id: number;
        apiKey: string;
        privateKey: string;
        balance: string | null;
    };
}
declare namespace App.Data.App.ManualEntry {
    export type ManualEntryData = {
        name: string;
        description: string | null;
        balance: number;
        currency: string;
    };
    export type UserManualEntryData = {
        id: number;
        name: string;
        description: string | null;
        balance: string | null;
        amount: number;
        currency: string;
    };
}
declare namespace App.Data.App.Setting {
    export type CurrencyData = {
        currency: string;
    };
}
declare namespace App.Data.App.StockMarket {
    export type StockMarketData = {
        ticker: string;
        amount: number;
    };
    export type UserStockMarketData = {
        id: number;
        ticker: string;
        amount: number | null;
        balance: string | null;
    };
}
declare namespace App.Enums {
    export type AssetType = 'bank-accounts' | 'stock-market' | 'crypto' | 'exchange' | 'manual-entries';
    export type BalanceType =
        | 'closingAvailable'
        | 'closingBooked'
        | 'expected'
        | 'forwardAvailable'
        | 'interimAvailable'
        | 'information'
        | 'interimBooked'
        | 'nonInvoiced'
        | 'openingBooked'
        | 'openingAvailable'
        | 'previouslyClosedBooked';
    export type BankAccountStatus = 'READY' | 'DISCOVERED' | 'ERROR' | 'EXPIRED' | 'PROCESSING' | 'SUSPENDED';
    export type CacheKeys = 'transaction_aggregate:%s:%s' | 'user_transactions:%s:%s:%s' | 'exchange-rates';
    export type ChainType = 'eth' | 'matic' | 'btc';
    export type ChangeType = 'positive' | 'negative';
    export type ErrorCodes = 1000;
    export type FlashMessageAction = 'delete' | 'update' | 'create' | 'renew';
    export type FlashMessageType = 'success' | 'danger';
    export type Subscriptions = 'monthly' | 'yearly';
    export type TransactionType = 'manual' | 'automatic';
    export type UserSettingKeys = 'currency';
}
