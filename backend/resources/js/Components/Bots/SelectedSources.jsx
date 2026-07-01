import React from 'react';

export default function SelectedSources({ data }) {

    const total =
        data.documents.length +
        data.websites.length +
        data.texts.length;

    if (total === 0) {
        return null;
    }

    return (

        <section className="workspace-table-card">

            <div className="workspace-toolbar">

                <div>

                    <p className="panel-card__eyebrow">
                        Training Data
                    </p>

                    <h2>
                        Selected Sources ({total})
                    </h2>

                </div>

            </div>

            <div className="source-list">

                {data.documents.map((file) => (

                    <div
                        key={file.id}
                        className="source-item"
                    >

                        <div>

                            <strong>

                                📄 {file.file.name}

                            </strong>

                            <small>

                                {(file.file.size / 1024).toFixed(1)} KB

                            </small>

                        </div>

                    </div>

                ))}

                {data.websites.map((site) => (

                    <div
                        key={site.id}
                        className="source-item"
                    >

                        <div>

                            <strong>

                                🌐 {site.title || 'Website'}

                            </strong>

                            <small>

                                {site.url}

                            </small>

                        </div>

                    </div>

                ))}

                {data.texts.map((text) => (

                    <div
                        key={text.id}
                        className="source-item"
                    >

                        <div>

                            <strong>

                                📝 {text.title || 'Text Source'}

                            </strong>

                            <small>

                                {text.content.length} Characters

                            </small>

                        </div>

                    </div>

                ))}

            </div>

        </section>

    );
}