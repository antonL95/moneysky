import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { MoreVertical, Pencil, Trash } from 'lucide-react';

type Row<T> = {
    [key in keyof T]: T[keyof T];
} & {
    id: number;
};

export default function TableAction<T extends object>({
    row,
    handleEditClick,
    destroy,
}: {
    row: Row<T>;
    handleEditClick: (row: Row<T>) => void;
    destroy: (row: Row<T>) => void;
}) {
    return (
        <div className="-mx-3 -my-1.5 sm:-mx-2.5">
            <DropdownMenu>
                <DropdownMenuTrigger aria-label="More options" asChild>
                    <Button variant={'ghost'}>
                        <MoreVertical className={'h-5 w-5'} />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem
                        onClick={() => {
                            handleEditClick(row);
                        }}
                    >
                        <Pencil className={'mr-2 h-5 w-5'} type={'light'} />
                        Edit
                    </DropdownMenuItem>
                    <DropdownMenuItem onClick={() => destroy(row)}>
                        <Trash className={'mr-2 h-5 w-5'} type={'light'} />
                        Delete
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
}
