import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import ConfirmModal from '../../Components/ConfirmModal';

export default function Index({ workspaces }) {
    const [workspaceToDelete, setWorkspaceToDelete] = useState(null);
    const [deleting, setDeleting] = useState(false);

    const confirmDeleteWorkspace = () => {
        if (!workspaceToDelete) {
            return;
        }

        setDeleting(true);

        router.delete(`/workspaces/${workspaceToDelete.id}`, {
            preserveScroll: true,
            onSuccess: () => setWorkspaceToDelete(null),
            onFinish: () => setDeleting(false),
        });
    };

    return (
        <DashboardLayout
            title="Workspaces"
            subtitle="Create and manage the workspaces connected to your AskFlow account."
        >
            <section className="workspace-toolbar">
                <div>
                    <p className="panel-card__eyebrow">Workspace</p>
                    <h2>All workspaces</h2>
                </div>
                <Link
                    href="/workspaces/create"
                    className="panel-button panel-button--primary workspace-button-link"
                >
                    Create Workspace
                </Link>
            </section>

            <section className="workspace-table-card">
                {workspaces?.length === 0 ? (
                    <div className="workspace-empty">
                        <h3>No workspaces yet</h3>
                        <p>Create your first workspace to organize questions and team activity.</p>
                        <a href="/workspaces/create" className="panel-button panel-button--primary workspace-button-link">
                            Create Workspace
                        </a>
                    </div>
                ) : (
                    <div className="workspace-table">
                        <div className="workspace-table__head">
                            <span>Name</span>
                            <span>Description</span>
                            <span>Status</span>
                            <span>Created</span>
                            <span>Actions</span>
                        </div>

                        {workspaces.map((workspace) => (
                            <div className="workspace-table__row" key={workspace.id}>
                                <div>
                                    <strong>{workspace.name}</strong>
                                </div>
                                <p className="workspace-description">
                                    {workspace.description || 'No description added.'}
                                </p>
                                <span className={workspace.is_active ? 'status-pill status-pill--open' : 'status-pill status-pill--pending'}>
                                    {workspace.is_active ? 'Active' : 'Inactive'}
                                </span>
                                <time>{new Date(workspace.created_at).toLocaleDateString()}</time>
                                <div className="workspace-row-actions">
                                    <Link
                                        href={`/workspaces/${workspace.id}/edit`}
                                        className="workspace-action workspace-action--edit"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        className="workspace-action workspace-action--delete"
                                        onClick={() => setWorkspaceToDelete(workspace)}
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </section>

            <ConfirmModal
                open={Boolean(workspaceToDelete)}
                title="Delete Workspace"
                message={
                    workspaceToDelete
                        ? `Delete "${workspaceToDelete.name}"? This cannot be undone.`
                        : ''
                }
                confirmLabel="Delete"
                processing={deleting}
                onCancel={() => {
                    if (!deleting) {
                        setWorkspaceToDelete(null);
                    }
                }}
                onConfirm={confirmDeleteWorkspace}
            />
        </DashboardLayout>
    );
}
