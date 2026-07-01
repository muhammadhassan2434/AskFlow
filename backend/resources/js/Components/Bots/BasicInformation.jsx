import React from 'react';

export default function BasicInformation({
    data,
    setData,
    errors,
    workspaces,
}) {
    return (
        <section className="workspace-form-card">

            <div className="workspace-toolbar">

                <div>

                    <p className="panel-card__eyebrow">
                        AI Bot
                    </p>

                    <h2>
                        Basic Information
                    </h2>

                    <p>
                        Give your AI assistant a name and tell it how it should behave.
                    </p>

                </div>

            </div>

            <div className="workspace-form-grid">

                {/* Workspace */}

                <div className="workspace-field workspace-field--wide">

                    <label htmlFor="workspace">
                        Workspace <span className="text-danger">*</span>
                    </label>

                    <select
                        id="workspace"
                        className="workspace-input"
                        value={data.workspace_id}
                        onChange={(e) =>
                            setData('workspace_id', e.target.value)
                        }
                    >
                        <option value="">
                            Select Workspace
                        </option>

                        {workspaces.map((workspace) => (

                            <option
                                key={workspace.id}
                                value={workspace.id}
                            >
                                {workspace.name}
                            </option>

                        ))}

                    </select>

                    {errors.workspace_id && (
                        <p className="workspace-error">
                            {errors.workspace_id}
                        </p>
                    )}

                </div>

                {/* Bot Name */}

                <div className="workspace-field workspace-field--wide">

                    <label htmlFor="name">
                        Bot Name <span className="text-danger">*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        className="workspace-input"
                        placeholder="Customer Support Assistant"
                        value={data.name}
                        onChange={(e) =>
                            setData('name', e.target.value)
                        }
                    />

                    {errors.name && (
                        <p className="workspace-error">
                            {errors.name}
                        </p>
                    )}

                </div>

                {/* Description */}

                <div className="workspace-field workspace-field--wide">

                    <label htmlFor="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        rows="4"
                        className="workspace-input workspace-textarea"
                        placeholder="Briefly describe what this bot is used for."
                        value={data.description}
                        onChange={(e) =>
                            setData('description', e.target.value)
                        }
                    />

                    <small className="workspace-help">
                        Optional. Helps you identify this bot later.
                    </small>

                    {errors.description && (
                        <p className="workspace-error">
                            {errors.description}
                        </p>
                    )}

                </div>

                {/* AI Instructions */}

                <div className="workspace-field workspace-field--wide">

                    <label htmlFor="system_prompt">
                        AI Instructions
                    </label>

                    <textarea
                        id="system_prompt"
                        rows="8"
                        className="workspace-input workspace-textarea"
                        placeholder={`Example:

You are a helpful customer support assistant.

Only answer using the uploaded documents.

If you don't know the answer, politely say you don't know.

Keep responses short and professional.`}
                        value={data.system_prompt}
                        onChange={(e) =>
                            setData(
                                'system_prompt',
                                e.target.value
                            )
                        }
                    />

                    <small className="workspace-help">
                        These instructions tell the AI how it should respond.
                    </small>

                    {errors.system_prompt && (
                        <p className="workspace-error">
                            {errors.system_prompt}
                        </p>
                    )}

                </div>

            </div>

        </section>
    );
}