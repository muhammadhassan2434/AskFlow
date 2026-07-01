import React, { useState } from 'react';

import DocumentUpload from './DocumentUpload';
import WebsiteSources from './WebsiteSources';
import TextSources from './TextSources';
import SelectedSources from './SelectedSources';

const tabs = [
    {
        key: 'documents',
        label: 'Documents',
        icon: '📄',
        description: 'Upload PDFs, DOCX or TXT files.',
    },
    {
        key: 'websites',
        label: 'Website',
        icon: '🌐',
        description: 'Import content from a website.',
    },
    {
        key: 'texts',
        label: 'Text',
        icon: '📝',
        description: 'Paste notes, FAQs or documentation.',
    },
];

export default function SourceTabs({
    data,
    setData,
}) {
    const [activeTab, setActiveTab] = useState('documents');

    const getCount = (key) => {
        switch (key) {
            case 'documents':
                return data.documents.length;

            case 'websites':
                return data.websites.length;

            case 'texts':
                return data.texts.length;

            default:
                return 0;
        }
    };

    return (
        <section className="workspace-form-card">

            <div className="workspace-toolbar">

                <div>

                    <p className="panel-card__eyebrow">
                        Teach Your AI
                    </p>

                    <h2>
                        Training Sources
                    </h2>

                    <p>
                        Add documents, websites or plain text to train this bot.
                    </p>

                </div>

            </div>

            {/* Tabs */}

            <div className="bot-tabs">

                {tabs?.map((tab) => (

                    <button
                        key={tab.key}
                        type="button"
                        className={`bot-tab ${
                            activeTab === tab.key ? 'bot-tab--active' : ''
                        }`}
                        onClick={() => setActiveTab(tab.key)}
                    >

                        <span className="bot-tab__icon">
                            {tab.icon}
                        </span>

                        <div className="bot-tab__content">

                            <strong>
                                {tab.label}
                            </strong>

                            <small>
                                {tab.description}
                            </small>

                        </div>

                        {getCount(tab.key) > 0 && (
                            <span className="bot-tab__badge">
                                {getCount(tab.key)}
                            </span>
                        )}

                    </button>

                ))}

            </div>

            {/* Content */}

            <div className="bot-tab-panel">

                {activeTab === 'documents' && (

                    <DocumentUpload
                        data={data}
                        setData={setData}
                    />

                )}

                {activeTab === 'websites' && (

                    <WebsiteSources
                        data={data}
                        setData={setData}
                    />

                )}

                {activeTab === 'texts' && (

                    <TextSources
                        data={data}
                        setData={setData}
                    />

                )}

            </div>

            {/* Summary */}
<div className="selected-sources-wrapper">

            <SelectedSources
                data={data}
                />
                </div>

        </section>
    );
}