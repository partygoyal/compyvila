"use client";

import { API_URL } from "@/src/lib/api";
import Link from "next/link";
import { useState } from "react";

export default function LoginPage() {
    const [loading, setLoading] =
        useState(false);

    const [showPassword, setShowPassword] =
        useState(false);

    const [rememberMe, setRememberMe] =
        useState(true);

    const [form, setForm] = useState({
        email: "",
        password: "",
    });

    const submit = async (
        e: React.FormEvent
    ) => {
        e.preventDefault();

        setLoading(true);

        const response = await fetch(
            `${API_URL}/login`,
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify(form),
            }
        );

        const data = await response.json();

        setLoading(false);

        if (data.token) {

            if (rememberMe) {

                localStorage.setItem(
                    "token",
                    data.token
                );

            } else {

                sessionStorage.setItem(
                    "token",
                    data.token
                );

            }

            window.location.href =
                "/dashboard";

            return;
        }

        alert(
            data.message ||
            "Invalid email or password"
        );
    };

    const loginWithGoogle = () => {
        window.location.href =
            `${API_URL}/auth/google/redirect`;
    };

    return (
        <div className="
            min-h-screen
            bg-slate-50
            flex
            items-center
            justify-center
            px-6
        ">

            <div className="
                w-full
                max-w-md
                bg-white
                rounded-3xl
                border
                border-slate-100
                p-10
                shadow-sm
            ">

                <div className="text-center mb-8">

                    <h1 className="
                        text-4xl
                        font-bold
                        bg-gradient-to-r
                        from-indigo-500
                        to-violet-500
                        bg-clip-text
                        text-transparent
                    ">
                        Welcome Back
                    </h1>

                    <p className="text-slate-500 mt-3">
                        Login to your CompyVila account.
                    </p>

                </div>

                <form
                    onSubmit={submit}
                    className="space-y-5"
                >

                    <div>

                        <label className="text-sm text-slate-600">
                            Email Address
                        </label>

                        <input
                            type="email"
                            required
                            value={form.email}
                            onChange={(e) =>
                                setForm({
                                    ...form,
                                    email:
                                        e.target.value,
                                })
                            }
                            className="
                                w-full
                                mt-2
                                border
                                border-slate-200
                                rounded-xl
                                p-3
                            "
                        />

                    </div>

                    <div>

                        <label className="text-sm text-slate-600">
                            Password
                        </label>

                        <div className="relative mt-2">

                            <input
                                type={
                                    showPassword
                                        ? "text"
                                        : "password"
                                }
                                required
                                value={form.password}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        password:
                                            e.target.value,
                                    })
                                }
                                className="
                                    w-full
                                    border
                                    border-slate-200
                                    rounded-xl
                                    p-3
                                "
                            />

                            <button
                                type="button"
                                onClick={() =>
                                    setShowPassword(
                                        !showPassword
                                    )
                                }
                                className="
                                    absolute
                                    right-4
                                    top-3
                                    text-sm
                                    text-slate-500
                                "
                            >
                                {showPassword
                                    ? "Hide"
                                    : "Show"}
                            </button>

                        </div>

                    </div>

                    <div className="
                        flex
                        items-center
                        justify-between
                    ">

                        <label className="
                            flex
                            items-center
                            gap-2
                            text-sm
                            text-slate-600
                        ">

                            <input
                                type="checkbox"
                                checked={rememberMe}
                                onChange={(e) =>
                                    setRememberMe(
                                        e.target.checked
                                    )
                                }
                            />

                            Remember Me

                        </label>

                        <button
                            type="button"
                            className="
                                text-sm
                                text-indigo-600
                            "
                        >
                            Forgot Password?
                        </button>

                    </div>

                    <button
                        disabled={loading}
                        className="
                            w-full
                            py-4
                            rounded-2xl
                            text-white
                            font-medium
                            bg-gradient-to-r
                            from-indigo-400
                            to-violet-400
                        "
                    >
                        {loading
                            ? "Signing In..."
                            : "Login"}
                    </button>

                </form>

                <div className="my-6 flex items-center gap-3">
                    <div className="h-px flex-1 bg-slate-100" />
                    <span className="text-xs text-slate-400">
                        OR
                    </span>
                    <div className="h-px flex-1 bg-slate-100" />
                </div>

                <button
                    type="button"
                    onClick={loginWithGoogle}
                    className="
                        w-full
                        py-4
                        rounded-2xl
                        border
                        border-slate-200
                        text-slate-700
                        font-medium
                        hover:bg-slate-50
                    "
                >
                    Continue with Google
                </button>

                <p className="
                    text-center
                    text-sm
                    text-slate-500
                    mt-6
                ">
                    Don&apos;t have an account?{" "}

                    <Link
                        href="/register"
                        className="text-indigo-600"
                    >
                        Create Account
                    </Link>

                </p>

            </div>

        </div>
    );
}
