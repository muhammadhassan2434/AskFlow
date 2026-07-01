import React from 'react';

export default function ConfirmModal({
    open,
    title,
    message,
    confirmLabel = 'Confirm',
    cancelLabel = 'Cancel',
    processing = false,
    onCancel,
    onConfirm,
}) {
    if (!open) {
        return null;
    }

    return (
        <div
            className="confirm-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirm-modal-title"
        >
            <div
                className="confirm-modal__backdrop"
                onClick={processing ? undefined : onCancel}
            />

            <div className="confirm-modal__panel">
                <h2 id="confirm-modal-title">
                    {title}
                </h2>

                <p>
                    {message}
                </p>

                <div className="confirm-modal__actions">
                    <button
                        type="button"
                        className="panel-button panel-button--ghost"
                        disabled={processing}
                        onClick={onCancel}
                    >
                        {cancelLabel}
                    </button>

                    <button
                        type="button"
                        className="panel-button panel-button--danger"
                        disabled={processing}
                        onClick={onConfirm}
                    >
                        {processing ? 'Deleting...' : confirmLabel}
                    </button>
                </div>
            </div>
        </div>
    );
}
