import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import toast, { Toaster } from 'react-hot-toast';

export default function FlashToast() {
    const { flash } = usePage().props;

    useEffect(() => {
        if (!flash?.message) {
            return;
        }

        if (flash.type === 'success') {
            toast.success(flash.message);
            return;
        }

        if (flash.type === 'error') {
            toast.error(flash.message);
            return;
        }

        toast(flash.message, {
            icon: flash.type === 'warning' ? '!' : 'i',
        });
    }, [flash?.type, flash?.message]);

    return (
        <Toaster
            position="top-right"
            toastOptions={{
                duration: 3500,
            }}
        />
    );
}
