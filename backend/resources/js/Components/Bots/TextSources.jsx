import React, { useState } from 'react';

export default function TextSources({ data, setData }) {

    const [text, setText] = useState({
        title: '',
        content: '',
    });

    const addText = () => {

        if (!text.content.trim()) return;

        setData('texts', [
            ...data.texts,
            {
                id: crypto.randomUUID(),
                title: text.title.trim(),
                content: text.content.trim(),
                type: 'text',
            },
        ]);

        setText({
            title: '',
            content: '',
        });
    };

    const removeText = (id) => {

        setData(
            'texts',
            data.texts.filter((item) => item.id !== id)
        );
    };

    return (

        <div className="bot-source-card">

            <div className="workspace-field">

                <label>Title</label>

                <input
                    type="text"
                    className="workspace-input"
                    placeholder="FAQs"
                    value={text.title}
                    onChange={(e) =>
                        setText({
                            ...text,
                            title: e.target.value,
                        })
                    }
                />

            </div>

            <div className="workspace-field">

                <label>Content</label>

                <textarea
                    rows="8"
                    className="workspace-input workspace-textarea"
                    placeholder="Paste your documentation..."
                    value={text.content}
                    onChange={(e) =>
                        setText({
                            ...text,
                            content: e.target.value,
                        })
                    }
                />

            </div>

            <button
                type="button"
                className="panel-button panel-button--primary"
                onClick={addText}
            >
                Add Text
            </button>

            {data.texts.length > 0 && (

                <div className="source-list">

                    {data.texts.map((item) => (

                        <div
                            key={item.id}
                            className="source-item"
                        >

                            <div>

                                <strong>
                                    {item.title || 'Text Source'}
                                </strong>

                                <small>

                                    {item.content.length} Characters

                                </small>

                            </div>

                            <button
                                type="button"
                                className="workspace-action workspace-action--delete"
                                onClick={() => removeText(item.id)}
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