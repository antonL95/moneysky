import TableAction from '@/components/table-action';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import StockMarketForm from '@/pages/stock-market/partials/stock-market-form';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import UserStockMarketData = App.Data.App.StockMarket.UserStockMarketData;

export default function Index({ columns, rows }: { columns: string[]; rows?: UserStockMarketData[] }) {
    const [open, setOpen] = useState(false);
    const [openEdit, setOpenEdit] = useState(false);
    const [selectedRow, setSelectedRow] = useState<UserStockMarketData | null>(null);

    const destroy = (row: UserStockMarketData) => {
        router.delete(
            route('stock-market.destroy', {
                stock_market: row.id,
            }),
        );
    };
    const handleEditClick = (row: UserStockMarketData) => {
        setSelectedRow(row);
        setOpenEdit(true);
    };

    return (
        <AppLayout>
            <Head title="Stock market portfolio" />

            <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                <div className={`flex self-end`}>
                    <Button onClick={() => setOpen(true)}>Add stock ticker</Button>
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
                        {rows?.map((row: UserStockMarketData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.id}</TableCell>
                                <TableCell>{row.ticker}</TableCell>
                                <TableCell>{row.amount}</TableCell>
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
                        <DialogTitle>Add stock ticker</DialogTitle>
                    </DialogHeader>
                    <StockMarketForm closeModal={() => setOpen(false)} />
                </DialogContent>
            </Dialog>
            <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update stock ticker</DialogTitle>
                    </DialogHeader>
                    {selectedRow && <StockMarketForm stockMarket={selectedRow} closeModal={() => setOpenEdit(false)} />}
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
