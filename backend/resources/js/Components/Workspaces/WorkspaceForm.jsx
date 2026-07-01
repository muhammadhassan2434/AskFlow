import React from 'react';

export default function WorkspaceForm({ data, setData, errors, processing, submitLabel }) {
    return (
        <div className="workspace-form-card">
            <div className="workspace-form-grid">
                <div className="workspace-field workspace-field--wide">
                    <label htmlFor="name">Workspace name</label>
                    <input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(event) => setData('name', event.target.value)}
                        placeholder="Enter Workspace name"
                        className="workspace-input"
                    />
                    {errors.name && <p className="workspace-error">{errors.name}</p>}
                </div>

                <div className="workspace-field workspace-field--wide">
                    <label htmlFor="description">Description</label>
                    <textarea
                        id="description"
                        value={data.description}
                        onChange={(event) => setData('description', event.target.value)}
                        placeholder="Describe what this workspace is used for."
                        className="workspace-input workspace-textarea"
                        rows="5"
                    />
                    {errors.description && <p className="workspace-error">{errors.description}</p>}
                </div>

                <label className="workspace-toggle">
                    <input
                        type="checkbox"
                        checked={data.is_active}
                        onChange={(event) => setData('is_active', event.target.checked)}
                    />
                    <span>
                        <strong>Active workspace</strong>
                        <small>Keep this workspace visible and usable.</small>
                    </span>
                </label>
                {errors.is_active && <p className="workspace-error">{errors.is_active}</p>}
            </div>

            <div className="workspace-form-actions">
                <a href="/workspaces" className="panel-button panel-button--ghost workspace-button-link">
                    Cancel
                </a>
                <button type="submit" className="panel-button panel-button--primary" disabled={processing}>
                    {processing ? 'Saving...' : submitLabel}
                </button>
            </div>
        </div>
    );
}