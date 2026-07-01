import React from 'react';
import { useForm } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import WorkspaceForm from '../../Components/Workspaces/WorkspaceForm';

export default function Edit({ workspace }) {
    const { data, setData, put, processing, errors } = useForm({
        name: workspace.name || '',
        description: workspace.description || '',
        is_active: Boolean(workspace.is_active),
    });

    const handleSubmit = (event) => {
        event.preventDefault();
        put(`/workspaces/${workspace.id}`);
    };

    return (
        <DashboardLayout title="Edit Workspace" subtitle="Update workspace details and availability.">
            <form onSubmit={handleSubmit}>
                <WorkspaceForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    submitLabel="Update Workspace"
                    botsCount={workspace.bots_count ?? 0}
                />
            </form>
        </DashboardLayout>
    );
}