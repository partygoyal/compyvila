"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { Submission } from "@/src/types/api";
import { useEffect, useState } from "react";
import Link from "next/link";

export default function SubmissionsPage() {
    const [items, setItems] = useState<Submission[]>([]);

    useEffect(() => {
        fetch(`${API_URL}/my-submissions`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then(setItems);
    }, []);

    return (
        <div className="space-y-6">

            {/* Header */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <div className="flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                    <div>

                        <p className="text-sm text-slate-500">
                            Competition Entries
                        </p>

                        <h1 className="text-4xl font-bold text-slate-800 mt-2">
                            My Submissions
                        </h1>

                        <p className="text-sm text-slate-500 mt-3">
                            View and manage your submitted entries.
                        </p>

                    </div>

                    <Link
                        href="/dashboard/submissions/upload"
                        className="
                            px-6
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
                        Upload Submission
                    </Link>

                </div>

            </div>

            {/* Stats */}

            <div className="grid md:grid-cols-3 gap-6">

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-indigo-400 to-indigo-500">

                    <p className="text-sm opacity-90">
                        Total Submissions
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {items.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-sky-400 to-blue-500">

                    <p className="text-sm opacity-90">
                        Active Entries
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {items.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-violet-400 to-purple-500">

                    <p className="text-sm opacity-90">
                        Status
                    </p>

                    <h2 className="text-2xl font-bold mt-4">
                        Submitted
                    </h2>

                </div>

            </div>

            {/* Empty State */}

            {items.length === 0 && (

                <div className="bg-white rounded-3xl border border-slate-100 p-10 text-center">

                    <h3 className="text-xl font-semibold text-slate-800">
                        No submissions yet
                    </h3>

                    <p className="text-sm text-slate-500 mt-2">
                        Upload your first competition entry.
                    </p>

                </div>

            )}

            {/* Submission Cards */}

            <div className="grid lg:grid-cols-2 gap-6">

                {items.map((item) => (

                    <div
                        key={item.id}
                        className="
                            bg-white
                            rounded-3xl
                            border
                            border-slate-100
                            overflow-hidden
                            hover:shadow-lg
                            transition
                        "
                    >

                        {/* Preview */}

                        {item.thumbnail ? (

                            <img
                                src={`http://127.0.0.1:8000/storage/${item.thumbnail}`}
                                alt={item.title || "Submission"}
                                className="w-full h-56 object-cover"
                            />

                        ) : (

                            <div className="
                                h-56
                                bg-gradient-to-r
                                from-indigo-100
                                to-violet-100
                                flex
                                items-center
                                justify-center
                                text-6xl
                            ">
                                📄
                            </div>

                        )}

                        {/* Content */}

                        <div className="p-6">

                            <div className="flex justify-between items-start">

                                <div>

                                    <h2 className="text-xl font-semibold text-slate-800">
                                        {item.title}
                                    </h2>

                                    <p className="text-sm text-slate-500 mt-2">
                                        {item.competition?.title}
                                    </p>

                                </div>

                                <span className="
                                    px-3
                                    py-1
                                    rounded-full
                                    text-xs
                                    font-medium
                                    bg-indigo-100
                                    text-indigo-700
                                ">
                                    {item.status}
                                </span>

                            </div>

                            <div className="grid grid-cols-2 gap-4 mt-6">

                                <div>

                                    <p className="text-xs text-slate-500">
                                        File Type
                                    </p>

                                    <p className="font-medium mt-1">
                                        {item.file_type}
                                    </p>

                                </div>

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Submission ID
                                    </p>

                                    <p className="font-medium mt-1">
                                        #{item.id}
                                    </p>

                                </div>

                            </div>

                            <Link
                                href={`/dashboard/submissions/${item.id}`}
                                className="
                                    inline-block
                                    mt-6
                                    px-5
                                    py-3
                                    rounded-2xl
                                    text-white
                                    text-sm
                                    bg-gradient-to-r
                                    from-indigo-400
                                    to-violet-400
                                "
                            >
                                View Submission
                            </Link>

                        </div>

                    </div>

                ))}

            </div>

        </div>
    );
}
