declare namespace App.Data {
    export type KrakenAccountData = {
        apiKey: string;
        privateKey: string;
    };
    export type StockMarketData = {
        ticker: string;
        amount: number;
    };
    export type TransactionData = {
        balance: number;
        currency: string;
        description: string | null;
        transaction_tag_id: number | null;
        user_manual_entry_id: number | null;
    };
}
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
declare namespace App.Data.App.CryptoWallet {
    export type CryptoWalletData = {
        address: string;
        chainType: App.Enums.ChainType;
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
    export type UserBudgetData = {
        id: number;
        name: string;
        spent: number;
        budget: number;
        currency: string;
        tags: Array;
        budgetId: number;
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
    export type CacheKeys = 'transaction_aggregate:%s:%s' | 'user_transactions:%s:%s:%s';
    export type ChainType = 'eth' | 'matic' | 'btc';
    export type ChangeType = 'positive' | 'negative';
    export type ErrorCodes = 1000;
    export type FeedbackType = 'bug' | 'improvement' | 'feature' | 'critical_error';
    export type FlashMessageAction = 'delete' | 'update' | 'create' | 'renew';
    export type FlashMessageType = 'success' | 'danger';
    export type Subscriptions = 'monthly' | 'yearly';
    export type TransactionType = 'manual' | 'automatic';
    export type UserSettingKeys = 'currency';
}
