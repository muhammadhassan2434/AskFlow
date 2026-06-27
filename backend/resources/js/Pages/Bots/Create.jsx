    import React from 'react';

    import { useForm } from '@inertiajs/react';
import {  router } from '@inertiajs/react';
import { route } from 'ziggy-js';
    import DashboardLayout from '../../Layouts/DashboardLayout';

    import BotForm from '../../Components/Bots/BotForm';

    export default function Create({
        workspaces,
    }) {

        const {
            data,
            setData,
            post,
            processing,
            errors,
        } = useForm({

            workspace_id: '',

            name: '',

            description: '',

            system_prompt: '',

            documents: [],

            websites: [],

            texts: [],

        });
        console.log('Documents', data.documents);
console.log('Websites', data.websites);
console.log('Texts', data.texts);

       const handleSubmit = (event) => {
    event.preventDefault();

    const formData = new FormData();

    formData.append('workspace_id', data.workspace_id);
    formData.append('name', data.name);
    formData.append('description', data.description);
    formData.append('system_prompt', data.system_prompt);

    let index = 0;

    // Documents
    data.documents.forEach((document) => {

        formData.append(`sources[${index}][type]`, 'document');
        formData.append(`sources[${index}][file]`, document.file);

        index++;

    });

    // Websites
    data.websites.forEach((website) => {

        formData.append(`sources[${index}][type]`, 'website');
        formData.append(`sources[${index}][title]`, website.title);
        formData.append(`sources[${index}][url]`, website.url);

        index++;

    });

    // Text Sources
    data.texts.forEach((text) => {

        formData.append(`sources[${index}][type]`, 'text');
        formData.append(`sources[${index}][title]`, text.title);
        formData.append(`sources[${index}][content]`, text.content);

        index++;

    });

    router.post(route('bots.store'), formData);
};

        return (

            <DashboardLayout
                title="Create AI Bot"
                subtitle="Create your AI assistant and upload knowledge sources."
            >

                <form onSubmit={handleSubmit}>

                    <BotForm
                        data={data}
                        setData={setData}
                        errors={errors}
                        processing={processing}
                        workspaces={workspaces}
                        submitLabel="Create Bot"
                    />

                </form>

            </DashboardLayout>

        );

    }