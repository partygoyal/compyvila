"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { User } from "@/src/types/api";
import Link from "next/link";
import { useEffect, useState } from "react";

export default function JudgeLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch(`${API_URL}/me`, {
            headers: authHeaders(),
        })
            .then((response) => response.json())
            .then(setUser)
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                Checking judge access...
            </div>
        );
    }

    if (!user?.roles?.includes("judge")) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                <p className="text-sm text-slate-500">
                    Access denied
                </p>

                <h1 className="text-3xl font-bold text-slate-900 mt-2">
                    Judge access required
                </h1>

                <p className="text-slate-500 mt-3">
                    Your account is not assigned the judge role.
                </p>

                <Link
                    href="/dashboard"
                    className="inline-flex mt-6 text-indigo-600"
                >
                    Back to dashboard
                </Link>
            </div>
        );
    }

    return <>{children}</>;
}
