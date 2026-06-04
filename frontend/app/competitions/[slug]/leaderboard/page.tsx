"use client";

import { useEffect, useState } from "react";
import { API_URL } from "@/src/lib/api";
import type { LeaderboardItem } from "@/src/types/api";

export default function LeaderboardPage({
    params,
}: {
    params: Promise<{ slug: string }>;
}) {
    const [results, setResults] = useState<LeaderboardItem[]>([]);

    useEffect(() => {
        (async () => {
            const { slug } = await params;

            fetch(
                `${API_URL}/competition/${slug}/results`
            )
                .then((res) => res.json())
                .then(setResults);
        })();
    }, [params]);

    const topThree = results.slice(0, 3);

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
                    🏆 Competition Leaderboard
                </h1>

                <p className="mt-4 text-white/90">
                    Rankings based on judges&apos; scores and competition performance.
                </p>

            </div>

            {/* Top 3 */}

            {topThree.length > 0 && (

                <div className="grid md:grid-cols-3 gap-6 mb-10">

                    {topThree.map((item, index) => {

                        const medal =
                            index === 0
                                ? "🥇"
                                : index === 1
                                ? "🥈"
                                : "🥉";

                        const gradient =
                            index === 0
                                ? "from-yellow-300 to-amber-400"
                                : index === 1
                                ? "from-slate-300 to-slate-400"
                                : "from-orange-300 to-orange-400";

                        return (

                            <div
                                key={item.rank}
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
                                    {item.participant}
                                </h2>

                                <p className="mt-2 opacity-90">
                                    {item.submission}
                                </p>

                                <div className="mt-6 text-5xl font-bold">
                                    {item.score}
                                </div>

                                <p className="mt-2">
                                    Points
                                </p>

                            </div>

                        );

                    })}

                </div>

            )}

            {/* Full Rankings */}

            <div className="
                bg-white
                rounded-3xl
                border
                border-slate-100
                overflow-hidden
            ">

                <div className="p-8 border-b border-slate-100">

                    <h2 className="text-2xl font-semibold">
                        Full Rankings
                    </h2>

                </div>

                <div className="divide-y divide-slate-100">

                    {results.map((item) => (

                        <div
                            key={item.rank}
                            className="
                                flex
                                justify-between
                                items-center
                                p-6
                                hover:bg-slate-50
                                transition
                            "
                        >

                            <div className="flex items-center gap-5">

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
                                    #{item.rank}
                                </div>

                                <div>

                                    <h3 className="font-semibold text-slate-900">
                                        {item.participant}
                                    </h3>

                                    <p className="text-sm text-slate-500">
                                        {item.submission}
                                    </p>

                                </div>

                            </div>

                            <div className="text-right">

                                <div className="text-3xl font-bold text-slate-900">
                                    {item.score}
                                </div>

                                <p className="text-xs text-slate-500">
                                    points
                                </p>

                            </div>

                        </div>

                    ))}

                </div>

            </div>

        </div>
    );
}
