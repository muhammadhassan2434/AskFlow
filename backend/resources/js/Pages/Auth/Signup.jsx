import React from 'react';

export default function Signup() {
    return (
        <main className="auth-page">
            <section className="auth-shell" aria-label="AskFlow signup">
                <aside className="auth-brand-panel">
                    <div>
                        <div className="auth-logo">AF</div>
                        <h1>Build a calmer question flow.</h1>
                        <p>
                            AskFlow keeps signups, teams, and workspace access in one consistent product pattern.
                        </p>
                    </div>

                    <div className="auth-stat-grid" aria-hidden="true">
                        <div className="auth-stat">
                            <strong>01</strong>
                            <span>Create account</span>
                        </div>
                        <div className="auth-stat">
                            <strong>02</strong>
                            <span>Start workspace</span>
                        </div>
                    </div>
                </aside>

                <div className="auth-form-panel">
                    <div className="auth-form-wrap">
                        <p className="auth-kicker">AskFlow</p>
                        <h2 className="auth-title">Create your account</h2>
                        <p className="auth-subtitle">
                            Use your name, email, and password to set up access.
                        </p>

                        <form className="auth-form">
                            <div className="auth-field">
                                <label htmlFor="name">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    autoComplete="name"
                                    placeholder="Your full name"
                                    className="auth-input"
                                />
                            </div>

                            <div className="auth-field">
                                <label htmlFor="email">Email address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autoComplete="email"
                                    placeholder="you@example.com"
                                    className="auth-input"
                                />
                            </div>

                            <div className="auth-field">
                                <label htmlFor="password">Password</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autoComplete="new-password"
                                    placeholder="Create a password"
                                    className="auth-input"
                                />
                            </div>

                            <div className="auth-field">
                                <label htmlFor="password_confirmation">Confirm password</label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autoComplete="new-password"
                                    placeholder="Confirm your password"
                                    className="auth-input"
                                />
                            </div>

                            <button type="button" className="auth-submit">
                                Sign up
                            </button>
                        </form>

                        <p className="auth-footer">
                            Already have an account? <a href="/login" className="auth-link">Login</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    );
}