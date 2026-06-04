"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { API_URL, authHeaders } from "@/src/lib/api";
import type { Registration, Submission, User } from "@/src/types/api";

export default function DashboardPage() {

    const router = useRouter();
    const [user, setUser] = useState<User | null>(null);
    const [registrations, setRegistrations] = useState<Registration[]>([]);
    const [submissions, setSubmissions] = useState<Submission[]>([]);

    useEffect(() => {

        fetch(`${API_URL}/me`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then((data: User) => {
                setUser(data);

                if (data.roles?.includes("judge")) {
                    router.replace("/dashboard/judge");
                }
            });

        fetch(`${API_URL}/my-registrations`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then(setRegistrations);

        fetch(`${API_URL}/my-submissions`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then(setSubmissions);

    }, [router]);

    return (

        <div className="space-y-8">

            {/* Hero */}

            <div className="bg-white rounded-3xl p-8 border border-slate-100">

                <div className="flex justify-between items-center">

                    <div>

                        <p className="text-sm text-slate-500">
                            Welcome Back
                        </p>

                        <h2 className="text-4xl font-bold text-slate-800 mt-2">
                            {user?.name || "User"} 👋
                        </h2>

                        <p className="text-sm text-slate-500 mt-3">
                            Manage your competitions and submissions.
                        </p>

                    </div>

                    <div className="hidden md:flex">

                        <div className="w-20 h-20 rounded-full bg-gradient-to-r from-indigo-400 to-sky-400 flex items-center justify-center text-white text-3xl font-bold">

                            {user?.name?.charAt(0)?.toUpperCase() || "U"}

                        </div>

                    </div>

                </div>

            </div>

            {/* Stats */}

            <div className="grid md:grid-cols-3 gap-6">

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-indigo-400 to-indigo-500">

                    <p className="text-sm">
                        Registrations
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {registrations.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-sky-400 to-blue-500">

                    <p className="text-sm">
                        Submissions
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {submissions.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-violet-400 to-purple-500">

                    <p className="text-sm">
                        Certificates
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        0
                    </h2>

                </div>

            </div>

            {/* Quick Actions */}

            <div className="bg-white rounded-3xl border border-slate-100 p-6">

                <h3 className="font-semibold text-lg mb-5">
                    Quick Actions
                </h3>

                <div className="flex flex-wrap gap-3">

                    <Link
                        href="/dashboard/competitions"
                        className="
                            px-5 py-3
                            rounded-2xl
                            text-white
                            text-sm
                            bg-gradient-to-r
                            from-indigo-400
                            to-violet-400
                        "
                    >
                        Browse Competitions
                    </Link>

                    <Link
                        href="/dashboard/submissions"
                        className="
                            px-5 py-3
                            rounded-2xl
                            text-white
                            text-sm
                            bg-gradient-to-r
                            from-sky-400
                            to-blue-500
                        "
                    >
                        My Submissions
                    </Link>

                    <Link
                        href="/dashboard/profile"
                        className="
                            px-5 py-3
                            rounded-2xl
                            text-white
                            text-sm
                            bg-gradient-to-r
                            from-violet-400
                            to-pink-400
                        "
                    >
                        My Profile
                    </Link>

                </div>

            </div>

            {/* Activity */}

            <div className="grid lg:grid-cols-2 gap-6">

                <div className="bg-white rounded-3xl border border-slate-100 p-6">

                    <h3 className="font-semibold mb-4">
                        Recent Registrations
                    </h3>

                    {registrations.length === 0 ? (

                        <p className="text-sm text-slate-500">
                            No registrations found.
                        </p>

                    ) : (

                        registrations.slice(0, 5).map((item) => (

                            <div
                                key={item.id}
                                className="py-3 border-b border-slate-100 last:border-0"
                            >

                                <p className="text-sm font-medium">
                                    {item.competition?.title}
                                </p>

                                <p className="text-xs text-slate-500 mt-1">
                                    {item.status}
                                </p>

                            </div>

                        ))

                    )}

                </div>

                <div className="bg-white rounded-3xl border border-slate-100 p-6">

                    <h3 className="font-semibold mb-4">
                        Recent Submissions
                    </h3>

                    {submissions.length === 0 ? (

                        <p className="text-sm text-slate-500">
                            No submissions found.
                        </p>

                    ) : (

                        submissions.slice(0, 5).map((item) => (

                            <div
                                key={item.id}
                                className="py-3 border-b border-slate-100 last:border-0"
                            >

                                <p className="text-sm font-medium">
                                    {item.title}
                                </p>

                                <p className="text-xs text-slate-500 mt-1">
                                    {item.status}
                                </p>

                            </div>

                        ))

                    )}

                </div>

            </div>

        </div>

    );
}
