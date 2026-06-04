"use client";

import { API_URL } from "@/src/lib/api";
import { countryCodes } from "@/src/lib/countryCodes";
import Link from "next/link";
import { useState } from "react";

export default function RegisterPage() {
    const [loading, setLoading] = useState(false);

    const [showPassword, setShowPassword] =
        useState(false);

    const [showConfirmPassword, setShowConfirmPassword] =
        useState(false);

    const [form, setForm] = useState({
        name: "",
        email: "",
        countryCode: "+91",
        mobile: "",
        password: "",
        confirmPassword: "",
    });

    const submit = async (
        e: React.FormEvent
    ) => {
        e.preventDefault();

        if (
            form.password !==
            form.confirmPassword
        ) {
            alert("Passwords do not match");
            return;
        }

        setLoading(true);

        const response = await fetch(
            `${API_URL}/register`,
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    name: form.name,
                    email: form.email,
                    country_code: form.countryCode,
                    mobile: form.mobile,
                    password: form.password,
                }),
            }
        );

        const data = await response.json();

        setLoading(false);

        if (data.success) {

            localStorage.setItem(
                "token",
                data.token
            );

            window.location.href =
                "/dashboard";

        } else {

            alert(
                data.message ||
                "Registration failed"
            );

        }
    };

    const registerWithGoogle = () => {
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
                max-w-lg
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
                        Create Account
                    </h1>

                    <p className="text-slate-500 mt-3">
                        Join competitions and showcase your talent.
                    </p>

                </div>

                <form
                    onSubmit={submit}
                    className="space-y-5"
                >

                    <div>

                        <label className="text-sm text-slate-600">
                            Full Name
                        </label>

                        <input
                            type="text"
                            required
                            value={form.name}
                            onChange={(e) =>
                                setForm({
                                    ...form,
                                    name:
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
                            Mobile Number
                        </label>

                        <div className="mt-2 grid grid-cols-[130px_1fr] gap-3">
                            <select
                                value={form.countryCode}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        countryCode:
                                            e.target.value,
                                    })
                                }
                                className="
                                    border
                                    border-slate-200
                                    rounded-xl
                                    p-3
                                "
                            >
                                {countryCodes.map((country) => (
                                    <option
                                        key={country.code}
                                        value={country.code}
                                    >
                                        {country.code}
                                    </option>
                                ))}
                            </select>

                            <input
                                type="text"
                                required
                                value={form.mobile}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        mobile:
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
                        </div>

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

                    <div>

                        <label className="text-sm text-slate-600">
                            Confirm Password
                        </label>

                        <div className="relative mt-2">

                            <input
                                type={
                                    showConfirmPassword
                                        ? "text"
                                        : "password"
                                }
                                required
                                value={
                                    form.confirmPassword
                                }
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        confirmPassword:
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
                                    setShowConfirmPassword(
                                        !showConfirmPassword
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
                                {showConfirmPassword
                                    ? "Hide"
                                    : "Show"}
                            </button>

                        </div>

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
                            ? "Creating Account..."
                            : "Create Account"}
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
                    onClick={registerWithGoogle}
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
                    Already have an account?{" "}

                    <Link
                        href="/login"
                        className="text-indigo-600"
                    >
                        Login
                    </Link>

                </p>

            </div>

        </div>
    );
}
