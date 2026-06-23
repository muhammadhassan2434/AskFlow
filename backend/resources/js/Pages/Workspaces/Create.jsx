import React from 'react';
import { useForm } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import WorkspaceForm from '../../Components/Workspaces/WorkspaceForm';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        is_active: true,
    });

    const handleSubmit = (event) => {
        event.preventDefault();
        post('/workspaces');
    };

    return (
        <DashboardLayout title="Create Workspace">
            <form onSubmit={handleSubmit}>
                <WorkspaceForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    submitLabel="Create Workspace"
                />
            </form>
        </DashboardLayout>
    );
}