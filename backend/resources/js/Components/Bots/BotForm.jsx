import React from 'react';

import BasicInformation from './BasicInformation';
import SourceTabs from './SourceTabs';

export default function BotForm({
    data,
    setData,
    errors,
    processing,
    workspaces,
    submitLabel,
    processingLabel = 'Saving Bot...',
}) {
    return (
        <>

            <BasicInformation
                data={data}
                setData={setData}
                errors={errors}
                workspaces={workspaces}
            />

            <SourceTabs
                data={data}
                setData={setData}
            />

            <div className="workspace-form-card">

                <div className="workspace-form-actions">

                    <a
                        href="/bots"
                        className="panel-button panel-button--ghost workspace-button-link"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        className="panel-button panel-button--primary"
                        disabled={processing}
                    >
                        {processing
                            ? processingLabel
                            : submitLabel}
                    </button>

                </div>

            </div>

        </>
    );
}
