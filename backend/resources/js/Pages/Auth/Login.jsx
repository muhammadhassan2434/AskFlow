import React from 'react';
import { useForm } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
    });

    const handleSubmit = (event) => {
        event.preventDefault();

        post('/login', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <main className="auth-page">
            <section className="auth-shell" aria-label="AskFlow login">
                <aside className="auth-brand-panel">
                    <div>
                        <div className="auth-logo">AF</div>
                        <h1>Welcome back to AskFlow.</h1>
                        <p>
                            Continue into the same focused workspace pattern for managing questions and access.
                        </p>
                    </div>

                    <div className="auth-stat-grid" aria-hidden="true">
                        <div className="auth-stat">
                            <strong>Fast</strong>
                            <span>Simple access</span>
                        </div>
                        <div className="auth-stat">
                            <strong>Clear</strong>
                            <span>Consistent UI</span>
                        </div>
                    </div>
                </aside>

                <div className="auth-form-panel">
                    <div className="auth-form-wrap">
                        <p className="auth-kicker">AskFlow</p>
                        <h2 className="auth-title">Login to your account</h2>
                        <p className="auth-subtitle">
                            Enter your email and password to continue.
                        </p>

                        <form className="auth-form" onSubmit={handleSubmit}>
                            <div className="auth-field">
                                <label htmlFor="email">Email address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autoComplete="email"
                                    placeholder="you@example.com"
                                    className="auth-input"
                                    value={data.email}
                                    onChange={(event) => setData('email', event.target.value)}
                                />
                                {errors.email && <p className="auth-error">{errors.email}</p>}
                            </div>

                            <div className="auth-field">
                                <label htmlFor="password">Password</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autoComplete="current-password"
                                    placeholder="Enter your password"
                                    className="auth-input"
                                    value={data.password}
                                    onChange={(event) => setData('password', event.target.value)}
                                />
                                {errors.password && <p className="auth-error">{errors.password}</p>}
                            </div>

                            <button type="submit" className="auth-submit" disabled={processing}>
                                {processing ? 'Logging in...' : 'Login'}
                            </button>
                        </form>

                        <p className="auth-footer">
                            New to AskFlow? <a href="/signup" className="auth-link">Create an account</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>
    );
}