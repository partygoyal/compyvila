"use client";

import Link from "next/link";
import { useEffect, useState } from "react";

export default function Navbar() {
    const [loggedIn, setLoggedIn] = useState(false);

    useEffect(() => {

        const token =
            localStorage.getItem("token") ||
            sessionStorage.getItem("token");

        const timeout = window.setTimeout(() => {
            setLoggedIn(!!token);
        }, 0);

        return () => window.clearTimeout(timeout);
    }, []);

    return (
        <header className="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">

            <div className="max-w-7xl mx-auto px-6">

                <div className="h-20 flex items-center justify-between">

                    {/* Logo */}

                    <Link href="/">

                        <h1 className="
                            text-3xl
                            font-bold
                            bg-gradient-to-r
                            from-indigo-500
                            to-sky-500
                            bg-clip-text
                            text-transparent
                        ">
                            CompyVila
                        </h1>

                    </Link>

                    {/* Menu */}

                    <nav className="hidden md:flex items-center gap-8">

                        <Link
                            href="/"
                            className="text-sm font-medium text-slate-600 hover:text-slate-900 transition"
                        >
                            Home
                        </Link>

                        <Link
                            href="/competitions"
                            className="text-sm font-medium text-slate-600 hover:text-slate-900 transition"
                        >
                            Competitions
                        </Link>

                        <Link
                            href="/winners"
                            className="text-sm font-medium text-slate-600 hover:text-slate-900 transition"
                        >
                            Winners
                        </Link>

                        <Link
                            href="/leaderboard"
                            className="text-sm font-medium text-slate-600 hover:text-slate-900 transition"
                        >
                            Leaderboard
                        </Link>

                    </nav>

                    {/* Right Side */}

                    <div className="flex items-center gap-3">

                        {loggedIn ? (

                            <Link
                                href="/dashboard"
                                className="
                                    px-5
                                    py-3
                                    rounded-2xl
                                    text-white
                                    text-sm
                                    font-medium
                                    bg-gradient-to-r
                                    from-indigo-400
                                    to-violet-400
                                    shadow-lg
                                    shadow-indigo-100
                                "
                            >
                                Dashboard
                            </Link>

                        ) : (

                            <>

                                <Link
                                    href="/login"
                                    className="
                                        text-sm
                                        font-medium
                                        text-slate-600
                                        hover:text-slate-900
                                    "
                                >
                                    Login
                                </Link>

                                <Link
                                    href="/register"
                                    className="
                                        px-5
                                        py-3
                                        rounded-2xl
                                        text-white
                                        text-sm
                                        font-medium
                                        bg-gradient-to-r
                                        from-indigo-400
                                        to-violet-400
                                        shadow-lg
                                        shadow-indigo-100
                                    "
                                >
                                    Register
                                </Link>

                            </>

                        )}

                    </div>

                </div>

            </div>

        </header>
    );
}
