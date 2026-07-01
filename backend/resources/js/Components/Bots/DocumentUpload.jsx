import React, { useRef, useState } from 'react';
import ConfirmModal from '../ConfirmModal';

const allowedExtensions = [
    'pdf',
    'doc',
    'docx',
    'txt',
];

export default function DocumentUpload({
    data,
    setData,
}) {
    const inputRef = useRef(null);
    const [fileToRemove, setFileToRemove] = useState(null);
    const [fileError, setFileError] = useState('');

    const addFiles = (files) => {
        const selectedFiles = Array.from(files);

        if (selectedFiles.length === 0) {
            return;
        }

        const newFiles = [];
        const rejected = [];

        selectedFiles.forEach((file) => {
            const parts = file.name.split('.');
            const extension = parts.length > 1
                ? parts.pop().toLowerCase()
                : '';

            if (!allowedExtensions.includes(extension)) {
                rejected.push(file.name);
                return;
            }

            const exists = data.documents.some(
                (item) =>
                    item.file.name === file.name &&
                    item.file.size === file.size
            );

            if (exists) {
                rejected.push(`${file.name} (already added)`);
                return;
            }

            newFiles.push({
                id: crypto.randomUUID(),
                type: 'document',
                file,
            });
        });

        if (rejected.length > 0) {
            setFileError(
                `Could not add: ${rejected.join(', ')}. Only PDF, DOC, DOCX, and TXT files are allowed.`
            );
        } else {
            setFileError('');
        }

        if (newFiles.length === 0) {
            return;
        }

        setData(
            'documents',
            [...data.documents, ...newFiles]
        );
    };

    const handleBrowse = (event) => {
        addFiles(event.target.files);
        event.target.value = '';
    };

    const handleDrop = (event) => {
        event.preventDefault();
        addFiles(event.dataTransfer.files);
    };

    const confirmRemoveFile = () => {
        if (!fileToRemove) {
            return;
        }

        setData(
            'documents',
            data.documents.filter(
                (item) => item.id !== fileToRemove.id
            )
        );

        setFileToRemove(null);
    };

    const formatSize = (bytes) => {

        if (bytes < 1024) {
            return bytes + ' B';
        }

        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        return (bytes / 1024 / 1024).toFixed(2) + ' MB';

    };

    return (
        <div className="bot-upload">

            <input
                ref={inputRef}
                hidden
                multiple
                type="file"
                accept=".pdf,.doc,.docx,.txt"
                onChange={handleBrowse}
            />

            <div
                className="bot-upload-dropzone"
                onDragOver={(e) => e.preventDefault()}
                onDrop={handleDrop}
            >

                <div className="bot-upload-content">

                    <div className="bot-upload-icon">

                        📄

                    </div>

                    <h3>

                        Upload Documents

                    </h3>

                    <p>

                        Drag & drop files here

                    </p>

                    <span>

                        or

                    </span>

                    <button
                        type="button"
                        className="panel-button panel-button--primary"
                        onClick={() => inputRef.current.click()}
                    >
                        Browse Files
                    </button>

                    <small>

                        Supported:
                        PDF, DOC, DOCX, TXT

                    </small>

                </div>

            </div>

            {fileError && (
                <p className="workspace-error">
                    {fileError}
                </p>
            )}

            {data.documents.length > 0 && (

                <div className="uploaded-files">

                    <h3>

                        Selected Documents ({data.documents.length})

                    </h3>

                    {data.documents.map((document) => (

                        <div
                            key={document.id}
                            className="uploaded-file"
                        >

                            <div>

                                <strong>

                                    {document.file.name}

                                </strong>

                                <p>

                                    {formatSize(document.file.size)}

                                </p>

                            </div>

                            <button
                                type="button"
                                className="workspace-action workspace-action--delete"
                                onClick={() => setFileToRemove(document)}
                            >
                                Remove
                            </button>

                        </div>

                    ))}

                </div>

            )}

            <ConfirmModal
                open={Boolean(fileToRemove)}
                title="Remove Document"
                message={
                    fileToRemove
                        ? `Remove "${fileToRemove.file.name}" from the upload list?`
                        : ''
                }
                confirmLabel="Remove"
                onCancel={() => setFileToRemove(null)}
                onConfirm={confirmRemoveFile}
            />
        </div>
    );
}
