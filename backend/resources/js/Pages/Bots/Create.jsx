import React from "react";

import { useForm } from "@inertiajs/react";
import { route } from "ziggy-js";
import DashboardLayout from "../../Layouts/DashboardLayout";

import BotForm from "../../Components/Bots/BotForm";

export default function Create({ workspaces }) {
    const { data, setData, post, processing, errors, transform } = useForm({
        workspace_id: "",

        name: "",

        description: "",

        system_prompt: "",

        model: "gpt-4o-mini",

        documents: [],

        websites: [],

        texts: [],
    });

    const handleSubmit = (event) => {
    event.preventDefault();

    const payload = {
        workspace_id: data.workspace_id,
        name: data.name,
        description: data.description,
        system_prompt: data.system_prompt,
        model: data.model,
        sources: [],
    };

    data.documents.forEach((document) => {
        payload.sources.push({
            type: "document",
            file: document.file,
        });
    });

    data.websites.forEach((website) => {
        payload.sources.push({
            type: "website",
            title: website.title,
            url: website.url,
        });
    });

    data.texts.forEach((text) => {
        payload.sources.push({
            type: "text",
            title: text.title,
            content: text.content,
        });
    });

    transform(() => payload);

    post(route("bots.store"), {
        forceFormData: true,
        preserveScroll: true,
    });
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
                    processingLabel="Creating Bot..."
                />
            </form>
        </DashboardLayout>
    );
}
