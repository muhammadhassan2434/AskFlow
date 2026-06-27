import React from 'react';
import { router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

export default function Index({ bots }) {

    const deleteBot = (bot) => {
        if (!window.confirm(`Delete ${bot.name}?`)) {
            return;
        }

        router.delete(`/bots/${bot.id}`);
    };

    return (
        <DashboardLayout
            title="Bots"
            subtitle="Create and manage AI assistants for your workspaces."
        >
            <section className="workspace-toolbar">
                <div>
                    <p className="panel-card__eyebrow">AI Bots</p>
                    <h2>All Bots</h2>
                </div>

                <a
                    href="/bots/create"
                    className="panel-button panel-button--primary workspace-button-link"
                >
                    Create Bot
                </a>
            </section>

            <section className="workspace-table-card">
                {bots.length === 0 ? (
                    <div className="workspace-empty">
                        <h3>No bots created yet</h3>

                        <p>
                            Create your first AI bot to answer questions from
                            uploaded documents.
                        </p>

                        <a
                            href="/bots/create"
                            className="panel-button panel-button--primary workspace-button-link"
                        >
                            Create Bot
                        </a>
                    </div>
                ) : (
                    <div className="workspace-table">

                        <div className="workspace-table__head">
                            <span>Name</span>
                            <span>Workspace</span>
                            <span>Status</span>
                            <span>Created</span>
                            <span>Actions</span>
                        </div>

                        {bots.map((bot) => (
                            <div
                                key={bot.id}
                                className="workspace-table__row"
                            >
                                <div>
                                    <strong>{bot.name}</strong>
                                </div>

                                <div>
                                    {bot.workspace?.name ?? '-'}
                                </div>

                                <span
                                    className={
                                        bot.status === 'active'
                                            ? 'status-pill status-pill--open'
                                            : 'status-pill status-pill--pending'
                                    }
                                >
                                    {bot.status}
                                </span>

                                <time>
                                    {new Date(
                                        bot.created_at
                                    ).toLocaleDateString()}
                                </time>

                                <div className="workspace-row-actions">

                                    <a
                                        href={`/bots/${bot.id}/edit`}
                                        className="workspace-action workspace-action--edit"
                                    >
                                        Edit
                                    </a>

                                    <button
                                        type="button"
                                        className="workspace-action workspace-action--delete"
                                        onClick={() => deleteBot(bot)}
                                    >
                                        Delete
                                    </button>

                                </div>

                            </div>
                        ))}

                    </div>
                )}
            </section>
        </DashboardLayout>
    );
}