import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import TransactionForm from '@/pages/dashboard/partials/forms/transaction-form';
import { router } from '@inertiajs/react';
import { ChevronDownIcon, ChevronUpIcon, MoreHorizontal } from 'lucide-react';
import { useEffect, useState } from 'react';
import TransactionAggregateData = App.Data.App.Dashboard.TransactionAggregateData;
import UserTransactionData = App.Data.App.Dashboard.UserTransactionData;
import TagData = App.Data.App.Dashboard.TagData;
import UserManualEntryData = App.Data.App.ManualEntry.UserManualEntryData;

export default function ({
    transactionAggregates,
    transactions,
    tags,
    currencies,
    userManualEntries,
}: {
    transactionAggregates: TransactionAggregateData[];
    transactions?: UserTransactionData[];
    tags: TagData[];
    currencies: { [key: string]: string };
    userManualEntries: UserManualEntryData[];
}) {
    const [openStates, setOpenStates] = useState<{ [key: string]: boolean }>({});
    const [currentlyOpened, setCurrentlyOpened] = useState<string | number | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [state, setState] = useState<boolean>(false);
    const [transaction, setTransaction] = useState<UserTransactionData | undefined>(undefined);
    const [open, setOpen] = useState<boolean>(false);

    useEffect(() => {
        if (!state) return;
        router.reload({
            data: { tagId: currentlyOpened },
            only: ['transactions'],
            onStart: () => setLoading(true),
            onFinish: () => setLoading(false),
        });
    }, [currentlyOpened, state]);

    return (
        <div className={`w-full`}>
            {transactionAggregates.map((transactionAggregate: TransactionAggregateData) => (
                <Collapsible
                    key={transactionAggregate.name}
                    open={openStates[transactionAggregate.name]}
                    onOpenChange={(isOpen) => {
                        setOpenStates((prevStates) => {
                            Object.keys(prevStates).map((key) => {
                                prevStates[key] = false;
                            });

                            if (transactionAggregate.tagId === 'total') {
                                return { ...prevStates, [transactionAggregate.name]: false };
                            }

                            setCurrentlyOpened(transactionAggregate.tagId);
                            setState(isOpen);
                            return { ...prevStates, [transactionAggregate.name]: isOpen };
                        });
                    }}
                    className={`w-full lg:px-8`}
                >
                    <div className={`w-full`}>
                        <CollapsibleTrigger asChild>
                            <button className={`border-secondary w-full border-b py-5`}>
                                <span className={`text-foreground grid grid-cols-3`}>
                                    <span className={`col-span-2 text-left`}>{transactionAggregate.name}</span>
                                    <span className={`flex flex-row justify-between`}>
                                        <span>{transactionAggregate.value}</span>
                                        {transactionAggregate.tagId !== 'total' ? (
                                            openStates[transactionAggregate.name] ? (
                                                <ChevronUpIcon className={`transition-all transition-discrete`} />
                                            ) : (
                                                <ChevronDownIcon className={`transition-all transition-discrete`} />
                                            )
                                        ) : (
                                            <></>
                                        )}
                                    </span>
                                </span>
                            </button>
                        </CollapsibleTrigger>
                    </div>

                    <CollapsibleContent
                        className={`data-[state="open"]:animate-slide-down data-[state="closed"]:animate-slide-up w-full`}
                    >
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[100px]">Account</TableHead>
                                    <TableHead>Description</TableHead>
                                    <TableHead>Amount</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {loading && (
                                    <TableRow>
                                        <TableCell>
                                            <Skeleton className={`h-8 w-[100px]`} />
                                        </TableCell>
                                        <TableCell>
                                            <Skeleton className={`h-8 w-[100px]`} />
                                        </TableCell>
                                        <TableCell>
                                            <Skeleton className={`h-8 w-[100px]`} />
                                        </TableCell>
                                        <TableCell></TableCell>
                                    </TableRow>
                                )}
                                {transactions &&
                                    !loading &&
                                    transactions.map((transaction: UserTransactionData) => {
                                        return (
                                            <TableRow key={transaction.id}>
                                                <TableCell className="font-medium">
                                                    {transaction.bankAccountName || transaction.cashWalletName}
                                                </TableCell>
                                                <TableCell>{transaction.description}</TableCell>
                                                <TableCell>{transaction.balance}</TableCell>
                                                <TableCell className={`flex justify-end`}>
                                                    <DropdownMenu>
                                                        <DropdownMenuTrigger asChild>
                                                            <Button variant="ghost" className="h-8 w-8 p-0">
                                                                <span className="sr-only">Open menu</span>
                                                                <MoreHorizontal />
                                                            </Button>
                                                        </DropdownMenuTrigger>
                                                        <DropdownMenuContent>
                                                            <DropdownMenuItem
                                                                onClick={() => {
                                                                    setTransaction(transaction);
                                                                    setOpen(true);
                                                                }}
                                                            >
                                                                Edit
                                                            </DropdownMenuItem>
                                                            <DropdownMenuItem>Billing</DropdownMenuItem>
                                                        </DropdownMenuContent>
                                                    </DropdownMenu>
                                                </TableCell>
                                            </TableRow>
                                        );
                                    })}
                            </TableBody>
                        </Table>
                    </CollapsibleContent>
                </Collapsible>
            ))}

            <Dialog open={open} onOpenChange={() => setOpen(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update transaction</DialogTitle>
                    </DialogHeader>
                    <TransactionForm
                        transaction={transaction}
                        closeModal={() => setOpen(false)}
                        tags={tags}
                        currencies={currencies}
                        userManualWallets={userManualEntries}
                    />
                </DialogContent>
            </Dialog>
        </div>
    );
}
