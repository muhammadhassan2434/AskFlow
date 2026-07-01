import React, { useState } from 'react';
import ConfirmModal from '../ConfirmModal';

export default function WebsiteSources({ data, setData }) {
    const [website, setWebsite] = useState({
        title: '',
        url: '',
    });
    const [websiteToRemove, setWebsiteToRemove] = useState(null);

    const addWebsite = () => {
        if (!website.url.trim()) return;

        setData('websites', [
            ...data.websites,
            {
                id: crypto.randomUUID(),
                title: website.title.trim(),
                url: website.url.trim(),
                type: 'website',
            },
        ]);

        setWebsite({
            title: '',
            url: '',
        });
    };

    const confirmRemoveWebsite = () => {
        if (!websiteToRemove) {
            return;
        }

        setData(
            'websites',
            data.websites.filter((item) => item.id !== websiteToRemove.id)
        );

        setWebsiteToRemove(null);
    };

    return (
        <div className="bot-source-card">

            <div className="workspace-field">
                <label>Website URL</label>

                <input
                    type="url"
                    className="workspace-input"
                    placeholder="https://example.com"
                    value={website.url}
                    onChange={(e) =>
                        setWebsite({
                            ...website,
                            url: e.target.value,
                        })
                    }
                />
            </div>

            <div className="workspace-field">
                <label>Title (Optional)</label>

                <input
                    type="text"
                    className="workspace-input"
                    placeholder="Company Website"
                    value={website.title}
                    onChange={(e) =>
                        setWebsite({
                            ...website,
                            title: e.target.value,
                        })
                    }
                />
            </div>

            <button
                type="button"
                className="panel-button panel-button--primary"
                onClick={addWebsite}
            >
                Add Website
            </button>

            {data.websites.length > 0 && (
                <div className="source-list">

                    {data.websites.map((site) => (
                        <div
                            key={site.id}
                            className="source-item"
                        >
                            <div>

                                <strong>
                                    {site.title || 'Untitled Website'}
                                </strong>

                                <small>{site.url}</small>

                            </div>

                            <button
                                type="button"
                                className="workspace-action workspace-action--delete"
                                onClick={() => setWebsiteToRemove(site)}
                            >
                                Remove
                            </button>

                        </div>
                    ))}

                </div>
            )}

            <ConfirmModal
                open={Boolean(websiteToRemove)}
                title="Remove Website"
                message={
                    websiteToRemove
                        ? `Remove "${websiteToRemove.title || websiteToRemove.url}" from the list?`
                        : ''
                }
                confirmLabel="Remove"
                onCancel={() => setWebsiteToRemove(null)}
                onConfirm={confirmRemoveWebsite}
            />
        </div>
    );
}
