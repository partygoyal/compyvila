import Link from "next/link";

export default function Stats() {
    const stats = [
        {
            number: "500+",
            label: "Competitions",
        },
        {
            number: "10K+",
            label: "Participants",
        },
        {
            number: "250+",
            label: "Judges",
        },
        {
            number: "5K+",
            label: "Certificates",
        },
    ];

    return (
        <section className="py-24 bg-slate-50">

            <div className="max-w-7xl mx-auto px-6">

                {/* Stats */}

                <div className="bg-white rounded-[32px] border border-slate-100 p-10 shadow-sm">

                    <div className="text-center mb-12">

                        <span
                            className="
                                inline-flex
                                px-4
                                py-2
                                rounded-full
                                bg-indigo-50
                                text-indigo-600
                                text-sm
                                font-medium
                            "
                        >
                            Trusted Platform
                        </span>

                        <h2 className="text-5xl font-bold mt-6 text-slate-900">
                            Growing Every Day
                        </h2>

                        <p className="text-slate-500 mt-4 max-w-2xl mx-auto">
                            Thousands of participants, judges and organizers
                            trust CompyVila to host and manage competitions.
                        </p>

                    </div>

                    <div className="grid md:grid-cols-4 gap-8">

                        {stats.map((item) => (

                            <div
                                key={item.label}
                                className="text-center"
                            >

                                <div className="
                                    text-5xl
                                    font-bold
                                    bg-gradient-to-r
                                    from-indigo-500
                                    to-sky-500
                                    bg-clip-text
                                    text-transparent
                                ">
                                    {item.number}
                                </div>

                                <p className="mt-3 text-slate-500 text-sm">
                                    {item.label}
                                </p>

                            </div>

                        ))}

                    </div>

                </div>

                {/* CTA */}

                <div className="
                    mt-12
                    rounded-[32px]
                    overflow-hidden
                    bg-gradient-to-r
                    from-indigo-400
                    via-violet-400
                    to-sky-400
                    p-12
                    text-center
                    text-white
                ">

                    <h2 className="text-4xl font-bold">
                        Ready to Showcase Your Talent?
                    </h2>

                    <p className="mt-4 text-white/90 max-w-2xl mx-auto">
                        Join competitions, submit your best work,
                        earn certificates and get recognized.
                    </p>

                    <div className="mt-8 flex flex-wrap justify-center gap-4">

                        <Link
                            href="/competitions"
                            className="
                                px-8
                                py-4
                                rounded-2xl
                                bg-white
                                text-slate-900
                                font-medium
                            "
                        >
                            Browse Competitions
                        </Link>

                        <Link
                            href="/register"
                            className="
                                px-8
                                py-4
                                rounded-2xl
                                border
                                border-white/30
                                bg-white/10
                                backdrop-blur
                                font-medium
                            "
                        >
                            Create Account
                        </Link>

                    </div>

                </div>

            </div>

        </section>
    );
}