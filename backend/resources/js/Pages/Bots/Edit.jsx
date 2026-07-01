import React, { useEffect, useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import DashboardLayout from '../../Layouts/DashboardLayout';
import BotForm from '../../Components/Bots/BotForm';
import ConfirmModal from '../../Components/ConfirmModal';

export default function Edit({ bot, workspaces }) {
    const { data, setData, post, processing, errors, transform } = useForm({
        workspace_id: bot.workspace_id || '',
        name: bot.name || '',
        description: bot.description || '',
        system_prompt: bot.system_prompt || '',
        model: bot.model || 'gpt-4o-mini',
        documents: [],
        websites: [],
        texts: [],
        existing_sources: bot.sources || [],
        _method: 'put',
    });

    const [sourceToDelete, setSourceToDelete] = useState(null);
    const [deletingSource, setDeletingSource] = useState(false);

    useEffect(() => {
        setData('existing_sources', bot.sources || []);
    }, [bot.sources]);

    const requestRemoveSource = (source) => {
        setSourceToDelete(source);
    };

    const confirmRemoveSource = () => {
        if (!sourceToDelete) {
            return;
        }

        const deletedId = sourceToDelete.id;

        setDeletingSource(true);

        router.delete(
            route('bots.sources.destroy', [bot.id, deletedId]),
            {
                preserveScroll: true,
                preserveState: false,
                onSuccess: () => {
                    setSourceToDelete(null);
                },
                onFinish: () => setDeletingSource(false),
            }
        );
    };

    const buildSources = () => {
        const sources = [];

        data.documents.forEach((document) => {
            sources.push({
                type: 'document',
                file: document.file,
            });
        });

        data.websites.forEach((website) => {
            sources.push({
                type: 'website',
                title: website.title,
                url: website.url,
            });
        });

        data.texts.forEach((text) => {
            sources.push({
                type: 'text',
                title: text.title,
                content: text.content,
            });
        });

        return sources;
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        transform(() => ({
            workspace_id: data.workspace_id,
            name: data.name,
            description: data.description,
            system_prompt: data.system_prompt,
            model: data.model,
            sources: buildSources(),
            _method: 'put',
        }));

        post(route('bots.update', bot.id), {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    return (
        <DashboardLayout
            title="Edit AI Bot"
            subtitle="Update this assistant and manage its knowledge sources."
        >
            <form onSubmit={handleSubmit}>
                <BotForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    workspaces={workspaces}
                    submitLabel="Update Bot"
                    processingLabel="Updating Bot..."
                />
            </form>

            {data.existing_sources.length > 0 && (
                <section className="workspace-table-card">
                    <div className="workspace-toolbar">
                        <div>
                            <p className="panel-card__eyebrow">
                                Existing Sources
                            </p>
                            <h2>
                                Current Knowledge ({data.existing_sources.length})
                            </h2>
                        </div>
                    </div>

                    <div className="source-list">
                        {data.existing_sources.map((source) => (
                            <div
                                key={source.id}
                                className="source-item"
                            >
                                <div>
                                    <strong>
                                        {source.title}
                                    </strong>
                                    <small>
                                        {source.type}
                                    </small>
                                </div>

                                <button
                                    type="button"
                                    className="workspace-action workspace-action--delete"
                                    onClick={() => requestRemoveSource(source)}
                                >
                                    Remove
                                </button>
                            </div>
                        ))}
                    </div>
                </section>
            )}

            <ConfirmModal
                open={Boolean(sourceToDelete)}
                title="Remove Source"
                message={
                    sourceToDelete
                        ? `Remove "${sourceToDelete.title}" from this bot? This cannot be undone.`
                        : ''
                }
                confirmLabel="Remove"
                processing={deletingSource}
                onCancel={() => {
                    if (!deletingSource) {
                        setSourceToDelete(null);
                    }
                }}
                onConfirm={confirmRemoveSource}
            />
        </DashboardLayout>
    );
}
