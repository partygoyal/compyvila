"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { Registration } from "@/src/types/api";
import { useEffect, useState } from "react";

export default function RegistrationsPage() {
    const [items, setItems] = useState<Registration[]>([]);

    useEffect(() => {
        fetch(`${API_URL}/my-registrations`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then(setItems);
    }, []);

    return (
        <div className="space-y-6">

            {/* Header */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <p className="text-sm text-slate-500">
                    Competition Entries
                </p>

                <h1 className="text-4xl font-bold text-slate-800 mt-2">
                    My Registrations
                </h1>

                <p className="text-sm text-slate-500 mt-3">
                    View all competitions you&apos;ve registered for.
                </p>

            </div>

            {/* Stats */}

            <div className="grid md:grid-cols-3 gap-6">

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-indigo-400 to-indigo-500">

                    <p className="text-sm opacity-90">
                        Total Registrations
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {items.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-sky-400 to-blue-500">

                    <p className="text-sm opacity-90">
                        Active Competitions
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {items.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-violet-400 to-purple-500">

                    <p className="text-sm opacity-90">
                        Participation
                    </p>

                    <h2 className="text-2xl font-bold mt-4">
                        Active
                    </h2>

                </div>

            </div>

            {/* Registrations */}

            {items.length === 0 ? (

                <div className="bg-white rounded-3xl border border-slate-100 p-8 text-center">

                    <p className="text-slate-500">
                        No registrations found.
                    </p>

                </div>

            ) : (

                <div className="grid gap-6">

                    {items.map((item) => (

                        <div
                            key={item.id}
                            className="bg-white rounded-3xl border border-slate-100 p-6 hover:shadow-lg transition"
                        >

                            <div className="flex flex-col md:flex-row md:justify-between md:items-center">

                                <div>

                                    <p className="text-xs text-slate-500 uppercase tracking-wide">
                                        Competition
                                    </p>

                                    <h2 className="text-xl font-semibold text-slate-800 mt-2">
                                        {item.competition?.title}
                                    </h2>

                                    <p className="text-sm text-slate-500 mt-3">
                                        Registration No:
                                        {" "}
                                        {item.registration_number}
                                    </p>

                                </div>

                                <div className="mt-4 md:mt-0">

                                    <span className="
                                        inline-flex
                                        px-4
                                        py-2
                                        rounded-full
                                        text-xs
                                        font-medium
                                        bg-indigo-100
                                        text-indigo-700
                                    ">
                                        {item.status}
                                    </span>

                                </div>

                            </div>

                            <div className="
                                mt-6
                                rounded-2xl
                                bg-slate-50
                                p-5
                                flex
                                flex-col
                                md:flex-row
                                md:items-center
                                md:justify-between
                                gap-4
                            ">

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Current Round
                                    </p>

                                    <h3 className="text-xl font-semibold text-slate-900 mt-1">
                                        {item.current_round
                                            ? `Round ${item.current_round.round_number}: ${item.current_round.title}`
                                            : "No round assigned"}
                                    </h3>

                                    <p className="text-sm text-slate-500 mt-2">
                                        {item.progress_message}
                                    </p>

                                </div>

                                <span className={`
                                    inline-flex
                                    px-4
                                    py-2
                                    rounded-full
                                    text-xs
                                    font-medium
                                    ${
                                        item.can_submit
                                            ? "bg-emerald-100 text-emerald-700"
                                            : item.progress_status === "under_review"
                                                ? "bg-amber-100 text-amber-700"
                                                : "bg-indigo-100 text-indigo-700"
                                    }
                                `}>
                                    {item.can_submit
                                        ? "Ready to submit"
                                        : item.progress_status === "under_review"
                                            ? "Under review"
                                            : item.progress_status}
                                </span>

                            </div>

                            <div className="grid md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-slate-100">

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Entry Fee
                                    </p>

                                    <p className="font-medium mt-1">
                                        ₹{item.competition?.entry_fee}
                                    </p>

                                </div>

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Submission Type
                                    </p>

                                    <p className="font-medium mt-1">
                                        {item.competition?.submission_type}
                                    </p>

                                </div>

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Total Rounds
                                    </p>

                                    <p className="font-medium mt-1">
                                        {item.competition?.total_rounds}
                                    </p>

                                </div>

                            </div>

                        </div>

                    ))}

                </div>

            )}

        </div>
    );
}
