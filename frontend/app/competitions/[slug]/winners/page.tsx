"use client";

import { useEffect, useState } from "react";
import { API_URL } from "@/src/lib/api";
import type { Winner } from "@/src/types/api";

export default function WinnersPage({
    params,
}: {
    params: Promise<{ slug: string }>;
}) {
    const [winners, setWinners] =
        useState<Winner[]>([]);

    useEffect(() => {
        (async () => {

            const { slug } = await params;

            fetch(
                `${API_URL}/competition/${slug}/winners`
            )
                .then((res) => res.json())
                .then(setWinners);

        })();
    }, [params]);

    const champion =
        winners.find(
            (winner) => String(winner.position) === "1"
        );

    return (
        <div className="max-w-7xl mx-auto px-6 py-10">

            {/* Hero */}

            <div className="
                rounded-3xl
                bg-gradient-to-r
                from-indigo-400
                via-violet-400
                to-sky-400
                p-10
                text-white
                mb-10
            ">

                <h1 className="text-5xl font-bold">
                    🏆 Competition Winners
                </h1>

                <p className="mt-4 text-white/90">
                    Celebrating the best performances and outstanding achievements.
                </p>

            </div>

            {/* Champion */}

            {champion && (

                <div className="
                    bg-gradient-to-r
                    from-yellow-300
                    to-amber-400
                    rounded-3xl
                    p-10
                    text-center
                    text-white
                    mb-10
                ">

                    <div className="text-8xl">
                        👑
                    </div>

                    <p className="mt-4 uppercase tracking-widest text-sm">
                        Champion
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {champion.participant?.name}
                    </h2>

                    <p className="mt-3 text-lg">
                        1st Position Winner
                    </p>

                </div>

            )}

            {/* Podium Winners */}

            <div className="grid md:grid-cols-3 gap-6 mb-10">

                {winners
                    .filter((winner) =>
                        ["1", "2", "3"].includes(
                            String(winner.position)
                        )
                    )
                    .map((winner) => {
                        const position = String(winner.position);

                        const medal =
                            position === "1"
                                ? "🥇"
                                : position === "2"
                                ? "🥈"
                                : "🥉";

                        const gradient =
                            position === "1"
                                ? "from-yellow-300 to-amber-400"
                                : position === "2"
                                ? "from-slate-300 to-slate-400"
                                : "from-orange-300 to-orange-400";

                        return (

                            <div
                                key={winner.id}
                                className={`
                                    rounded-3xl
                                    p-8
                                    text-center
                                    text-white
                                    bg-gradient-to-r
                                    ${gradient}
                                `}
                            >

                                <div className="text-6xl">
                                    {medal}
                                </div>

                                <h2 className="text-2xl font-bold mt-4">
                                    {winner.participant?.name}
                                </h2>

                                <p className="mt-3">
                                    Position #{winner.position}
                                </p>

                                <div className="mt-6 bg-white/20 rounded-2xl py-3">
                                    Winner
                                </div>

                            </div>

                        );

                    })}

            </div>

            {/* All Winners */}

            <div className="
                bg-white
                rounded-3xl
                border
                border-slate-100
                overflow-hidden
            ">

                <div className="p-8 border-b border-slate-100">

                    <h2 className="text-2xl font-semibold">
                        Winner Rankings
                    </h2>

                </div>

                <div className="divide-y divide-slate-100">

                    {winners.map((winner) => (

                        <div
                            key={winner.id}
                            className="
                                flex
                                justify-between
                                items-center
                                p-6
                                hover:bg-slate-50
                                transition
                            "
                        >

                            <div className="flex items-center gap-4">

                                <div className="
                                    w-12
                                    h-12
                                    rounded-2xl
                                    bg-indigo-100
                                    text-indigo-700
                                    flex
                                    items-center
                                    justify-center
                                    font-bold
                                ">
                                    #{winner.position}
                                </div>

                                <div>

                                    <h3 className="font-semibold text-slate-900">
                                        {winner.participant?.name}
                                    </h3>

                                    <p className="text-sm text-slate-500">
                                        Competition Winner
                                    </p>

                                </div>

                            </div>

                            <div>

                                <span className="
                                    px-4
                                    py-2
                                    rounded-full
                                    bg-indigo-100
                                    text-indigo-700
                                    text-sm
                                    font-medium
                                ">
                                    Winner
                                </span>

                            </div>

                        </div>

                    ))}

                </div>

            </div>

        </div>
    );
}
