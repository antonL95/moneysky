import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

export const useToast = () => {
    const page = usePage<SharedData>();
    return page.props.flash as unknown as App.Data.App.FlashData;
};
