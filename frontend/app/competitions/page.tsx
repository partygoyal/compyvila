import Link from "next/link";
import { API_URL, STORAGE_URL } from "@/src/lib/api";
import type { Competition } from "@/src/types/api";
import ShareButton from "@/src/components/ShareButton";

async function getCompetitions() {
    const res = await fetch(
        `${API_URL}/competitions`,
        {
            cache: "no-store",
        }
    );

    return res.json();
}

export default async function CompetitionsPage() {
    const competitions = await getCompetitions();

    return (
        <div className="bg-slate-50">

            {/* Hero */}

            <div className="max-w-7xl mx-auto px-6 pt-10">

                <div className="
                    rounded-3xl
                    bg-gradient-to-r
                    from-indigo-400
                    via-violet-400
                    to-sky-400
                    p-10
                    text-white
                ">

                    <h1 className="text-5xl font-bold">
                        Explore Competitions
                    </h1>

                    <p className="mt-4 text-white/90 text-lg max-w-2xl">
                        Discover competitions across photography,
                        music, dance, coding, writing, art and more.
                    </p>

                </div>

            </div>

            {/* Stats */}

            <div className="max-w-7xl mx-auto px-6 mt-8">

                <div className="grid md:grid-cols-3 gap-6">

                    <div className="
                        bg-white
                        rounded-3xl
                        border border-slate-100
                        p-6
                    ">
                        <p className="text-sm text-slate-500">
                            Total Competitions
                        </p>

                        <h2 className="text-4xl font-bold mt-2">
                            {competitions.length}
                        </h2>
                    </div>

                    <div className="
                        bg-white
                        rounded-3xl
                        border border-slate-100
                        p-6
                    ">
                        <p className="text-sm text-slate-500">
                            Categories
                        </p>

                        <h2 className="text-4xl font-bold mt-2">
                            8+
                        </h2>
                    </div>

                    <div className="
                        bg-white
                        rounded-3xl
                        border border-slate-100
                        p-6
                    ">
                        <p className="text-sm text-slate-500">
                            Opportunities
                        </p>

                        <h2 className="text-4xl font-bold mt-2">
                            Open
                        </h2>
                    </div>

                </div>

            </div>

            {/* Competition Grid */}

            <div className="max-w-7xl mx-auto px-6 py-10">

                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                    {competitions.map((competition: Competition) => (

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

                                    {competition.allow_public_voting && (
                                        <span className="
                                            px-3 py-1 rounded-full
                                            bg-emerald-100
                                            text-emerald-700
                                            text-xs
                                        ">
                                            Public voting
                                        </span>
                                    )}

                                </div>

                                {/* Title */}

                                <h2 className="
                                    text-xl
                                    font-semibold
                                    text-slate-900
                                    line-clamp-2
                                ">
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

                                {/* Details */}

                                <div className="
                                    mt-5
                                    pt-5
                                    border-t
                                    border-slate-100
                                    space-y-2
                                ">

                                    <div className="flex justify-between">

                                        <span className="text-sm text-slate-500">
                                            Submission
                                        </span>

                                        <span className="text-sm font-medium">
                                            {competition.submission_type}
                                        </span>

                                    </div>

                                    <div className="flex justify-between">

                                        <span className="text-sm text-slate-500">
                                            Status
                                        </span>

                                        <span className="
                                            px-2
                                            py-1
                                            rounded-full
                                            bg-emerald-100
                                            text-emerald-700
                                            text-xs
                                        ">
                                            {competition.status}
                                        </span>

                                    </div>

                                </div>

                                {/* CTA */}

                                <div className="mt-6 grid grid-cols-2 gap-3">
                                    <Link
                                        href={`/competitions/${competition.slug}`}
                                        className="
                                            text-center
                                            px-5
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
                                        View
                                    </Link>

                                    <ShareButton
                                        path={`/competitions/${competition.slug}`}
                                        title={competition.title}
                                        text={competition.description}
                                    />
                                </div>

                            </div>

                        </div>

                    ))}

                </div>

            </div>

        </div>
    );
}
