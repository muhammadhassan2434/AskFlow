import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function useFlashToast() {
    const { flash } = usePage().props;

useEffect(() => {
    if (!flash) return;

    switch (flash.type) {
        case 'success':
            window.toast.success(flash.message);
            break;
        case 'error':
            window.toast.error(flash.message);
            break;
        case 'warning':
            window.toast.warning(flash.message);
            break;
        default:
            window.toast.info(flash.message);
    }
}, [flash]);
}