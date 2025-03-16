import TableAction from '@/components/table-action';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import KrakenAccountForm from '@/pages/kraken-account/partials/kraken-account-form';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import UserKrakenAccountData = App.Data.App.KrakenAccount.UserKrakenAccountData;

export default function Index({ columns, rows }: { columns: string[]; rows?: UserKrakenAccountData[] }) {
    const [open, setOpen] = useState(false);
    const [openEdit, setOpenEdit] = useState(false);
    const [selectedRow, setSelectedRow] = useState<UserKrakenAccountData | null>(null);

    const destroy = (row: UserKrakenAccountData) => {
        router.delete(
            route('kraken-account.destroy', {
                kraken_account: row.id,
            }),
        );
    };
    const handleEditClick = (row: UserKrakenAccountData) => {
        setSelectedRow(row);
        setOpenEdit(true);
    };

    return (
        <AppLayout>
            <Head title="Kraken accounts" />

            <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                <div className={`flex self-end`}>
                    <Button onClick={() => setOpen(true)}>Add kraken account</Button>
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
                        {rows?.map((row: UserKrakenAccountData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.id}</TableCell>
                                <TableCell>{row.apiKey}</TableCell>
                                <TableCell>{row.privateKey}</TableCell>
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
                        <DialogTitle>Add kraken account</DialogTitle>
                    </DialogHeader>
                    <KrakenAccountForm closeModal={() => setOpen(false)} />
                </DialogContent>
            </Dialog>
            <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update kraken account</DialogTitle>
                    </DialogHeader>
                    {selectedRow && (
                        <KrakenAccountForm krakenAccount={selectedRow} closeModal={() => setOpenEdit(false)} />
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
