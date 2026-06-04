import Link from "next/link";
import { API_URL, STORAGE_URL } from "@/src/lib/api";
import type { Competition } from "@/src/types/api";

async function getCompetitions() {
    const response = await fetch(
        `${API_URL}/competitions`,
        {
            cache: "no-store",
        }
    );

    return response.json();
}

export default async function FeaturedCompetitions() {
    const competitions = await getCompetitions();

    return (
        <section className="py-24 bg-white">

            <div className="max-w-7xl mx-auto px-6">

                <div className="text-center mb-14">

                    <span className="
                        inline-flex
                        px-4
                        py-2
                        rounded-full
                        bg-indigo-50
                        text-indigo-600
                        text-sm
                        font-medium
                    ">
                        Featured Opportunities
                    </span>

                    <h2 className="text-5xl font-bold mt-6 text-slate-900">
                        Featured Competitions
                    </h2>

                    <p className="text-slate-500 max-w-2xl mx-auto mt-4">
                        Discover exciting competitions across multiple categories
                        and showcase your talent to a wider audience.
                    </p>

                </div>

                <div className="grid lg:grid-cols-3 gap-8">

                    {competitions
                        .slice(0, 6)
                        .map((competition: Competition) => (

                            <div
                                key={competition.id}
                                className="
                                    bg-white
                                    rounded-3xl
                                    overflow-hidden
                                    border
                                    border-slate-100
                                    hover:shadow-xl
                                    transition
                                "
                            >

                                {/* Banner */}

                                {competition.banner ? (

                                    <img
                                        src={`${STORAGE_URL}/${competition.banner}`}
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

                                {/* Content */}

                                <div className="p-6">

                                    <div className="flex gap-2 flex-wrap mb-4">

                                        <span className="
                                            px-3
                                            py-1
                                            rounded-full
                                            bg-indigo-100
                                            text-indigo-700
                                            text-xs
                                        ">
                                            {competition.category?.name || "Competition"}
                                        </span>

                                        <span className="
                                            px-3
                                            py-1
                                            rounded-full
                                            bg-sky-100
                                            text-sky-700
                                            text-xs
                                        ">
                                            ₹{competition.entry_fee}
                                        </span>

                                    </div>

                                    <h3 className="
                                        text-xl
                                        font-semibold
                                        text-slate-900
                                        line-clamp-2
                                    ">
                                        {competition.title}
                                    </h3>

                                    <p className="
                                        mt-3
                                        text-sm
                                        text-slate-500
                                        line-clamp-3
                                    ">
                                        {competition.description}
                                    </p>

                                    <div className="
                                        mt-6
                                        flex
                                        justify-between
                                        items-center
                                    ">

                                        <div>

                                            <p className="text-xs text-slate-400">
                                                Submission Type
                                            </p>

                                            <p className="text-sm font-medium">
                                                {competition.submission_type}
                                            </p>

                                        </div>

                                        <Link
                                            href={`/competitions/${competition.slug}`}
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
                                            View
                                        </Link>

                                    </div>

                                </div>

                            </div>

                        ))}

                </div>

                <div className="text-center mt-12">

                    <Link
                        href="/competitions"
                        className="
                            inline-flex
                            items-center
                            px-8
                            py-4
                            rounded-2xl
                            text-white
                            font-medium
                            bg-gradient-to-r
                            from-indigo-400
                            to-violet-400
                            shadow-lg
                            shadow-indigo-100
                        "
                    >
                        View All Competitions
                    </Link>

                </div>

            </div>

        </section>
    );
}
