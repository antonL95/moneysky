import TableAction from '@/components/table-action';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import ManualEntryForm from '@/pages/manual-entry/partials/manual-entry-form';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import UserManualEntryData = App.Data.App.ManualEntry.UserManualEntryData;

export default function Index({
    columns,
    rows,
    currencies,
}: {
    columns: string[];
    rows?: UserManualEntryData[];
    currencies: { [key: string]: string };
}) {
    const [open, setOpen] = useState(false);
    const [openEdit, setOpenEdit] = useState(false);
    const [selectedRow, setSelectedRow] = useState<UserManualEntryData | null>(null);

    const destroy = (row: UserManualEntryData) => {
        router.delete(
            route('manual-entry.destroy', {
                manual_entry: row.id,
            }),
        );
    };
    const handleEditClick = (row: UserManualEntryData) => {
        setSelectedRow(row);
        setOpenEdit(true);
    };

    return (
        <AppLayout>
            <Head title="Cash wallets" />

            <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                <div className={`flex self-end`}>
                    <Button color={'sky'} onClick={() => setOpen(true)}>
                        Add manual entry
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
                        {rows?.map((row: UserManualEntryData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.id}</TableCell>
                                <TableCell>{row.name}</TableCell>
                                <TableCell>{row.description}</TableCell>
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
                        <DialogTitle>Add manual entry</DialogTitle>
                    </DialogHeader>
                    <ManualEntryForm currencies={currencies} closeModal={() => setOpen(false)} />
                </DialogContent>
            </Dialog>
            <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update manual entry</DialogTitle>
                    </DialogHeader>
                    {selectedRow && (
                        <ManualEntryForm
                            currencies={currencies}
                            manualEntry={selectedRow}
                            closeModal={() => setOpenEdit(false)}
                        />
                    )}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
