import TableAction from '@/components/table-action';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import BankAccountForm from '@/pages/bank-account/partials/bank-account-form';
import SearchBanks from '@/pages/bank-account/partials/search-banks';
import { Head, Link, router } from '@inertiajs/react';
import { MoreVertical, Trash } from 'lucide-react';
import { useState } from 'react';
import UserBankAccountData = App.Data.App.BankAccount.UserBankAccountData;
import BankAccountStatus = App.Enums.BankAccountStatus;
import BankInstitutionData = App.Data.App.BankAccount.BankInstitutionData;

export default function Index({
    columns,
    rows,
    banks,
}: {
    columns: string[];
    rows?: UserBankAccountData[];
    banks?: BankInstitutionData[];
}) {
    const [open, setOpen] = useState(false);
    const [openEdit, setOpenEdit] = useState(false);
    const [selectedRow, setSelectedRow] = useState<UserBankAccountData | null>(null);

    const createStatusTag = (status: BankAccountStatus) => {
        switch (status) {
            case 'READY':
                return <Badge color={'green'}>Ready</Badge>;
            case 'DISCOVERED':
                return <Badge color={'blue'}>Discovered</Badge>;
            case 'ERROR':
                return <Badge color={'red'}>Error</Badge>;
            case 'EXPIRED':
                return <Badge color={'red'}>Expired</Badge>;
            case 'PROCESSING':
                return <Badge color={'blue'}>Processing</Badge>;
            case 'SUSPENDED':
                return <Badge color={'red'}>Suspended</Badge>;
        }
    };

    const destroy = (row: UserBankAccountData): void => {
        router.delete(
            route('bank-account.destroy', {
                bank_account: row.id,
            }),
        );
    };
    const handleEditClick = (row: UserBankAccountData) => {
        setSelectedRow(row);
        setOpenEdit(true);
    };

    return (
        <AppLayout>
            <Head title="Bank account" />
            <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                <div className={`flex self-end`}>
                    <Button onClick={() => setOpen(true)}>Connect bank</Button>

                    <SearchBanks open={open} setOpen={setOpen} banks={banks} />
                </div>
                <Table>
                    <TableHeader>
                        <TableRow>
                            {columns.map((column: string) => (
                                <TableHead key={column}>{column}</TableHead>
                            ))}
                            <TableHead className="relative w-0">
                                <span className="sr-only">Actions</span>
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {rows?.map((row: UserBankAccountData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.id}</TableCell>
                                <TableCell>{row.name}</TableCell>
                                <TableCell>{row.balance}</TableCell>
                                <TableCell>{createStatusTag(row.status)}</TableCell>
                                {row.accessExpired ||
                                row.status === 'EXPIRED' ||
                                row.status === 'SUSPENDED' ||
                                row.status === 'ERROR' ? (
                                    <div className="flex flex-row items-center justify-center py-4">
                                        <Button asChild>
                                            <Link href={route('bank-account.renew-redirect', { id: row.id })}>
                                                Renew access
                                            </Link>
                                        </Button>
                                        <DropdownMenu>
                                            <DropdownMenuTrigger aria-label="More options" asChild>
                                                <Button variant={'ghost'}>
                                                    <MoreVertical className={'h-5 w-5'} />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenu>
                                                <DropdownMenuItem
                                                    className={`hover:bg-red-500`}
                                                    onClick={() => destroy(row)}
                                                >
                                                    <Trash className={'mr-2 h-5 w-5'} type={'light'} />
                                                    Delete
                                                </DropdownMenuItem>
                                            </DropdownMenu>
                                        </DropdownMenu>
                                    </div>
                                ) : (
                                    <TableCell>
                                        <TableAction
                                            row={row}
                                            destroy={() => destroy(row)}
                                            handleEditClick={() => handleEditClick(row)}
                                        />
                                    </TableCell>
                                )}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
            <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update bank account</DialogTitle>
                    </DialogHeader>
                    {selectedRow && <BankAccountForm bankAccount={selectedRow} closeModal={() => setOpenEdit(false)} />}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
