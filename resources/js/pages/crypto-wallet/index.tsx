import TableAction from '@/components/table-action';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import CryptoWalletForm from '@/pages/crypto-wallet/partials/crypto-wallet-form';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import UserCryptoWalletData = App.Data.App.CryptoWallet.UserCryptoWalletData;

export default function Index({ columns, rows }: { columns: string[]; rows?: UserCryptoWalletData[] }) {
    const [open, setOpen] = useState(false);
    const [openEdit, setOpenEdit] = useState(false);
    const [selectedRow, setSelectedRow] = useState<UserCryptoWalletData | null>(null);

    const destroy = (row: UserCryptoWalletData) => {
        router.delete(
            route('digital-wallet.destroy', {
                digital_wallet: row.id,
            }),
        );
    };
    const handleEditClick = (row: UserCryptoWalletData) => {
        setSelectedRow(row);
        setOpenEdit(true);
    };

    return (
        <AppLayout>
            <Head title="Digital wallets" />
            <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                <div className={`flex self-end`}>
                    <Button color={'sky'} onClick={() => setOpen(true)}>
                        Add digital wallet
                    </Button>
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
                        {rows?.map((row: UserCryptoWalletData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.id}</TableCell>
                                <TableCell>{row.walletAddress}</TableCell>
                                <TableCell>{row.chainType}</TableCell>
                                <TableCell>{row.balance}</TableCell>
                                <TableCell>
                                    <TableAction
                                        row={row}
                                        destroy={() => destroy(row)}
                                        handleEditClick={() => handleEditClick(row)}
                                    />
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            <Dialog open={open} onOpenChange={() => setOpen(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add digital wallet</DialogTitle>
                    </DialogHeader>
                    <CryptoWalletForm closeModal={() => setOpen(false)} />
                </DialogContent>
            </Dialog>
            <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update digital wallet</DialogTitle>
                    </DialogHeader>
                    {selectedRow && <CryptoWalletForm wallet={selectedRow} closeModal={() => setOpenEdit(false)} />}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
