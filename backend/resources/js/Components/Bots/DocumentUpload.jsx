import React, { useRef } from 'react';

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

    const addFiles = (files) => {
        const selectedFiles = Array.from(files);

        const newFiles = [];

        selectedFiles.forEach((file) => {

            const extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (!allowedExtensions.includes(extension)) {
                return;
            }

            const exists = data.documents.some(
                (item) =>
                    item.file.name === file.name &&
                    item.file.size === file.size
            );

            if (exists) {
                return;
            }

            newFiles.push({
                id: crypto.randomUUID(),
                type: 'document',
                file,
            });

        });

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

    const removeFile = (id) => {

        setData(
            'documents',
            data.documents.filter(
                (item) => item.id !== id
            )
        );

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
                                onClick={() =>
                                    removeFile(document.id)
                                }
                            >
                                Remove
                            </button>

                        </div>

                    ))}

                </div>

            )}

        </div>
    );
}