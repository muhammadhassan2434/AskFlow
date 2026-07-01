import React from 'react';
import DashboardLayout from '../Layouts/DashboardLayout';

const stats = [
    {
        label: 'Total Questions',
        value: '128',
        note: '+12 this week',
    },
    {
        label: 'Active Users',
        value: '42',
        note: '8 online now',
    },
    {
        label: 'Pending Reviews',
        value: '16',
        note: 'Needs attention',
    },
    {
        label: 'Resolved Today',
        value: '24',
        note: 'Healthy flow',
    },
];

const recentQuestions = [
    {
        title: 'How do I reset my workspace access?',
        owner: 'Ayesha Khan',
        status: 'Pending',
        time: '12 min ago',
    },
    {
        title: 'Can I invite another team member?',
        owner: 'Hamza Ali',
        status: 'Open',
        time: '32 min ago',
    },
    {
        title: 'Where can I update profile details?',
        owner: 'Sara Ahmed',
        status: 'Resolved',
        time: '1 hour ago',
    },
];

const activity = [
    'New signup request received',
    'Question queue synced successfully',
    'Workspace settings reviewed',
];

export default function Dashboard() {
    return (
        <DashboardLayout
            title="Dashboard"
            subtitle="Track questions, users, and workspace activity from one clean panel."
        >
            <div className="dashboard-grid dashboard-grid--stats">
                {stats.map((stat) => (
                    <article className="metric-card" key={stat.label}>
                        <p>{stat.label}</p>
                        <strong>{stat.value}</strong>
                        <span>{stat.note}</span>
                    </article>
                ))}
            </div>

            <div className="dashboard-grid dashboard-grid--content">
                <section className="panel-card panel-card--wide">
                    <div className="panel-card__head">
                        <div>
                            <p className="panel-card__eyebrow">Question Queue</p>
                            <h2>Recent questions</h2>
                        </div>
                        <a href="/questions">View all</a>
                    </div>

                    <div className="question-list">
                        {recentQuestions.map((question) => (
                            <div className="question-row" key={question.title}>
                                <div>
                                    <h3>{question.title}</h3>
                                    <p>{question.owner}</p>
                                </div>
                                <span className={`status-pill status-pill--${question.status.toLowerCase()}`}>
                                    {question.status}
                                </span>
                                <time>{question.time}</time>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="panel-card">
                    <div className="panel-card__head">
                        <div>
                            <p className="panel-card__eyebrow">Today</p>
                            <h2>Activity</h2>
                        </div>
                    </div>

                    <div className="activity-list">
                        {activity.map((item) => (
                            <div className="activity-item" key={item}>
                                <span aria-hidden="true" />
                                <p>{item}</p>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </DashboardLayout>
    );
}