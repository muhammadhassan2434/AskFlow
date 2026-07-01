import React from 'react';
import { router } from '@inertiajs/react';
import Sidebar from '../Components/Sidebar';
import FlashToast from './toast';

export default function DashboardLayout({ children, title, subtitle, actions = null }) {
    const handleLogout = () => {
        router.post('/logout');
    };

    return (
        <main className="panel-shell">
            <FlashToast />

            <Sidebar />

            <section className="panel-main">
                <header className="panel-header">
                    <div>
                        <p className="panel-kicker">AskFlow Panel</p>
                        <h1>{title}</h1>
                        {subtitle && <p>{subtitle}</p>}
                    </div>

                    <div className="panel-header__actions">
                        {actions}
                        <button type="button" className="panel-button panel-button--danger" onClick={handleLogout}>
                            Logout
                        </button>
                    </div>
                </header>

                {children}
            </section>
        </main>
    );
}
