import React from 'react';
import { useForm } from '@inertiajs/react';

export default function Signup() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (event) => {
        event.preventDefault();

        post('/signup', {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

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

                        <form className="auth-form" onSubmit={handleSubmit}>
                            <div className="auth-field">
                                <label htmlFor="name">Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    autoComplete="name"
                                    placeholder="Your full name"
                                    className="auth-input"
                                    value={data.name}
                                    onChange={(event) => setData('name', event.target.value)}
                                />
                                {errors.name && <p className="auth-error">{errors.name}</p>}
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
                                    autoComplete="new-password"
                                    placeholder="Create a password"
                                    className="auth-input"
                                    value={data.password}
                                    onChange={(event) => setData('password', event.target.value)}
                                />
                                {errors.password && <p className="auth-error">{errors.password}</p>}
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
                                    value={data.password_confirmation}
                                    onChange={(event) => setData('password_confirmation', event.target.value)}
                                />
                                {errors.password_confirmation && (
                                    <p className="auth-error">{errors.password_confirmation}</p>
                                )}
                            </div>

                            <button type="submit" className="auth-submit" disabled={processing}>
                                {processing ? 'Creating account...' : 'Sign up'}
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