import { Toaster } from '@/components/ui/sonner';
import { CheckCircle, OctagonAlert } from 'lucide-react';
import { useEffect } from 'react';
import { toast } from 'sonner';

export default function Toast({ flash }: { flash?: App.Data.App.FlashData }) {
    useEffect(() => {
        if (!flash) return;

        toast(flash.title, {
            description: flash.description,
            icon: flash.type === 'success' ? <CheckCircle /> : <OctagonAlert />,
            duration: 5000,
            closeButton: true,
            dismissible: true,
        });
    }, [flash]);

    return <Toaster />;
}
