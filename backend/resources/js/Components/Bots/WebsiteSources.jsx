import React, { useState } from 'react';

export default function WebsiteSources({ data, setData }) {
    const [website, setWebsite] = useState({
        title: '',
        url: '',
    });

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

    const removeWebsite = (id) => {
        setData(
            'websites',
            data.websites.filter((item) => item.id !== id)
        );
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
                                onClick={() => removeWebsite(site.id)}
                            >
                                Remove
                            </button>

                        </div>
                    ))}

                </div>
            )}

        </div>
    );
}