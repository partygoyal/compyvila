"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { Competition, Registration } from "@/src/types/api";
import Link from "next/link";
import { useEffect, useState } from "react";

export default function CompetitionsPage() {
    const [competitions, setCompetitions] = useState<Competition[]>([]);
    const [registrations, setRegistrations] = useState<Registration[]>([]);

    useEffect(() => {

        fetch(`${API_URL}/competitions`)
            .then((res) => res.json())
            .then(setCompetitions);

        fetch(`${API_URL}/my-registrations`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then(setRegistrations);

    }, []);

    const isRegistered = (competitionId: number) => {
        return registrations.some(
            (item) => item.competition_id === competitionId
        );
    };

    return (
        <div className="space-y-6">

            {/* Header */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <p className="text-sm text-slate-500">
                    Competition Marketplace
                </p>

                <h1 className="text-4xl font-bold text-slate-800 mt-2">
                    Available Competitions
                </h1>

                <p className="text-sm text-slate-500 mt-3">
                    Browse competitions and register for new opportunities.
                </p>

            </div>

            {/* Stats */}

            <div className="grid md:grid-cols-3 gap-6">

                <div className="
                    rounded-3xl
                    p-6
                    text-white
                    bg-gradient-to-r
                    from-indigo-400
                    to-indigo-500
                ">

                    <p className="text-sm opacity-90">
                        Total Competitions
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {competitions.length}
                    </h2>

                </div>

                <div className="
                    rounded-3xl
                    p-6
                    text-white
                    bg-gradient-to-r
                    from-sky-400
                    to-blue-500
                ">

                    <p className="text-sm opacity-90">
                        Registered
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {registrations.length}
                    </h2>

                </div>

                <div className="
                    rounded-3xl
                    p-6
                    text-white
                    bg-gradient-to-r
                    from-violet-400
                    to-purple-500
                ">

                    <p className="text-sm opacity-90">
                        Opportunities
                    </p>

                    <h2 className="text-2xl font-bold mt-4">
                        Open
                    </h2>

                </div>

            </div>

            {/* Competition Cards */}

            <div className="grid lg:grid-cols-2 gap-6">

                {competitions.map((competition) => (

                    <div
                        key={competition.id}
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

                        {/* Banner */}

                        {competition.banner ? (

                            <img
                                src={`http://127.0.0.1:8000/storage/${competition.banner}`}
                                alt={competition.title}
                                className="w-full h-56 object-cover"
                            />

                        ) : (

                            <div className="
                                h-56
                                bg-gradient-to-r
                                from-indigo-100
                                via-sky-100
                                to-violet-100
                                flex
                                items-center
                                justify-center
                                text-7xl
                            ">
                                🏆
                            </div>

                        )}

                        <div className="p-6">

                            {/* Badges */}

                            <div className="flex flex-wrap gap-2 mb-4">

                                <span className="
                                    px-3 py-1 rounded-full
                                    bg-indigo-100
                                    text-indigo-700
                                    text-xs
                                ">
                                    {competition.category?.name}
                                </span>

                                <span className="
                                    px-3 py-1 rounded-full
                                    bg-sky-100
                                    text-sky-700
                                    text-xs
                                ">
                                    ₹{competition.entry_fee}
                                </span>

                            </div>

                            <h2 className="text-xl font-semibold text-slate-900">
                                {competition.title}
                            </h2>

                            <p className="
                                mt-3
                                text-sm
                                text-slate-500
                                line-clamp-3
                            ">
                                {competition.description}
                            </p>

                            <div className="
                                mt-5
                                pt-5
                                border-t
                                border-slate-100
                                grid
                                grid-cols-2
                                gap-4
                            ">

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Submission
                                    </p>

                                    <p className="font-medium mt-1">
                                        {competition.submission_type}
                                    </p>

                                </div>

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Rounds
                                    </p>

                                    <p className="font-medium mt-1">
                                        {competition.total_rounds}
                                    </p>

                                </div>

                            </div>

                            <div className="mt-6 flex gap-3">

                                <Link
                                    href={`/competitions/${competition.slug}`}
                                    className="
                                        flex-1
                                        text-center
                                        py-3
                                        rounded-2xl
                                        text-white
                                        text-sm
                                        font-medium
                                        bg-gradient-to-r
                                        from-indigo-400
                                        to-violet-400
                                    "
                                >
                                    View Details
                                </Link>

                                {isRegistered(
                                    competition.id
                                ) ? (

                                    <span
                                        className="
                                            px-4
                                            py-3
                                            rounded-2xl
                                            text-sm
                                            font-medium
                                            bg-emerald-100
                                            text-emerald-700
                                        "
                                    >
                                        Registered
                                    </span>

                                ) : (

                                    <span
                                        className="
                                            px-4
                                            py-3
                                            rounded-2xl
                                            text-sm
                                            font-medium
                                            bg-amber-100
                                            text-amber-700
                                        "
                                    >
                                        Available
                                    </span>

                                )}

                            </div>

                        </div>

                    </div>

                ))}

            </div>

        </div>
    );
}
