"use client";

import Link from "next/link";
import { useEffect } from "react";
import toast from "react-hot-toast";

export default function GoogleCallbackPage() {
    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const token = params.get("token");

        if (!token) {
            toast.error("Google login failed.");
            window.location.href = "/login?google_login=failed";
            return;
        }

        localStorage.setItem("token", token);
        toast.success("Signed in with Google.");
        window.location.href = "/dashboard";
    }, []);

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
                text-center
                shadow-sm
            ">
                <h1 className="text-3xl font-bold text-slate-900">
                    Finishing Google sign in
                </h1>

                <p className="mt-4 text-slate-500">
                    Please wait while we connect your CompyVila account.
                </p>

                <Link
                    href="/login"
                    className="inline-flex mt-6 text-indigo-600"
                >
                    Back to login
                </Link>
            </div>
        </div>
    );
}
