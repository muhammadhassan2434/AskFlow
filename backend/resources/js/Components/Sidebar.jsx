import React from 'react';
import { sidebarMenu } from '../Config/sidebarMenu';

const menuMarks = {
    dashboard: 'D',
    workspaces: 'W',
    bots: 'B',
    
};

export default function Sidebar() {
    const currentPath = window.location.pathname;

    return (
        <aside className="sidebar" aria-label="Main sidebar">
            <div className="sidebar__brand">
                <div className="sidebar__logo">AF</div>
                <div>
                    <p className="sidebar__brand-name">AskFlow</p>
                    <span className="sidebar__brand-meta">Control Panel</span>
                </div>
            </div>

            <nav className="sidebar__nav" aria-label="Primary navigation">
                {sidebarMenu.map((item) => {
                    const isActive = item.href === '/'
                        ? currentPath === item.href
                        : currentPath.startsWith(item.href);

                    return (
                        <a
                            key={item.key}
                            href={item.href}
                            className={isActive ? 'sidebar__link sidebar__link--active' : 'sidebar__link'}
                            aria-current={isActive ? 'page' : undefined}
                        >
                            <span className="sidebar__mark" aria-hidden="true">
                                {menuMarks[item.key]}
                            </span>
                            <span className="sidebar__label">{item.label}</span>
                            {item.badge && <span className="sidebar__badge">{item.badge}</span>}
                        </a>
                    );
                })}
            </nav>

            <div className="sidebar__foot">
                <p>Workspace</p>
                <strong>AskFlow Starter</strong>
            </div>
        </aside>
    );
}