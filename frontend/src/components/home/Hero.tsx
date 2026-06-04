import Link from "next/link";

export default function Hero() {
    return (
        <section className="relative overflow-hidden bg-gradient-to-br from-slate-50 via-indigo-50 to-sky-50">

            {/* Background blobs */}

            <div className="absolute top-0 left-0 w-96 h-96 bg-indigo-200 rounded-full blur-3xl opacity-30" />

            <div className="absolute bottom-0 right-0 w-96 h-96 bg-sky-200 rounded-full blur-3xl opacity-30" />

            <div className="max-w-7xl mx-auto px-6 py-24 lg:py-32">

                <div className="grid lg:grid-cols-2 gap-16 items-center">

                    {/* Left Content */}

                    <div>

                        <span className="
                            inline-flex
                            px-4
                            py-2
                            rounded-full
                            bg-white
                            border
                            border-slate-200
                            text-xs
                            font-medium
                            text-slate-600
                        ">
                            🏆 India&apos;s Talent Competition Platform
                        </span>

                        <h1 className="
                            mt-6
                            text-5xl
                            md:text-6xl
                            lg:text-7xl
                            font-bold
                            leading-tight
                            text-slate-900
                        ">
                            Compete.
                            <br />

                            <span className="bg-gradient-to-r from-indigo-500 to-sky-500 bg-clip-text text-transparent">
                                Showcase Talent.
                            </span>

                            <br />

                            Win Recognition.
                        </h1>

                        <p className="
                            mt-6
                            text-lg
                            text-slate-600
                            max-w-2xl
                            leading-relaxed
                        ">
                            Join photography, music, dance, coding,
                            writing and creative competitions.
                            Submit entries, get judged, earn certificates
                            and win awards.
                        </p>

                        <div className="mt-10 flex flex-wrap gap-4">

                            <Link
                                href="/competitions"
                                className="
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
                                Explore Competitions
                            </Link>

                            <Link
                                href="/register"
                                className="
                                    px-8
                                    py-4
                                    rounded-2xl
                                    bg-white
                                    border
                                    border-slate-200
                                    text-slate-700
                                    font-medium
                                "
                            >
                                Create Account
                            </Link>

                        </div>

                        {/* Stats */}

                        <div className="flex flex-wrap gap-8 mt-12">

                            <div>
                                <h3 className="text-3xl font-bold text-slate-900">
                                    500+
                                </h3>

                                <p className="text-sm text-slate-500">
                                    Participants
                                </p>
                            </div>

                            <div>
                                <h3 className="text-3xl font-bold text-slate-900">
                                    50+
                                </h3>

                                <p className="text-sm text-slate-500">
                                    Competitions
                                </p>
                            </div>

                            <div>
                                <h3 className="text-3xl font-bold text-slate-900">
                                    100+
                                </h3>

                                <p className="text-sm text-slate-500">
                                    Winners
                                </p>
                            </div>

                        </div>

                    </div>

                    {/* Right Side */}

                    <div className="relative">

                        <div className="
                            bg-white
                            rounded-3xl
                            p-8
                            shadow-xl
                            border
                            border-slate-100
                        ">

                            <div className="
                                h-64
                                rounded-2xl
                                bg-gradient-to-r
                                from-indigo-100
                                via-sky-100
                                to-violet-100
                                flex
                                items-center
                                justify-center
                                text-8xl
                            ">
                                🏆
                            </div>

                            <div className="mt-6">

                                <h3 className="font-semibold text-xl">
                                    Featured Competition
                                </h3>

                                <p className="text-slate-500 mt-2 text-sm">
                                    Best Wildlife Photography Competition 2026
                                </p>

                                <div className="flex gap-3 mt-5">

                                    <span className="
                                        px-3
                                        py-1
                                        rounded-full
                                        bg-indigo-100
                                        text-indigo-700
                                        text-xs
                                    ">
                                        Photography
                                    </span>

                                    <span className="
                                        px-3
                                        py-1
                                        rounded-full
                                        bg-sky-100
                                        text-sky-700
                                        text-xs
                                    ">
                                        ₹500 Entry
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>
    );
}
