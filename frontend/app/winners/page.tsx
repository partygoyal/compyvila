import { API_URL } from "@/src/lib/api";

type GlobalWinnerItem = {
    name: string;
    title: string | null;
    score: number | string;
};

async function getWinners(): Promise<GlobalWinnerItem[]> {
    const response = await fetch(`${API_URL}/winners`, {
        cache: "no-store",
    });

    if (!response.ok) {
        return [];
    }

    return response.json();
}

export default async function GlobalWinnersPage() {
    const winners = await getWinners();
    const champion = winners[0];

    return (
        <div className="max-w-7xl mx-auto px-6 py-10">
            <div className="
                rounded-3xl
                bg-gradient-to-r
                from-yellow-300
                via-amber-400
                to-orange-400
                p-10
                text-white
                mb-10
            ">
                <h1 className="text-5xl font-bold">
                    Global Winners
                </h1>

                <p className="mt-4 text-white/90">
                    Celebrating top performers across CompyVila.
                </p>
            </div>

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
                    <p className="mt-4 uppercase tracking-widest text-sm">
                        Current Top Performer
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {champion.name}
                    </h2>

                    <p className="mt-3 text-lg">
                        {champion.title || "Untitled submission"}
                    </p>
                </div>
            )}

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
                    {winners.length === 0 ? (
                        <div className="p-8 text-slate-500">
                            No winners are available yet.
                        </div>
                    ) : (
                        winners.map((winner, index) => (
                            <div
                                key={`${winner.name}-${winner.title}-${index}`}
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
                                        bg-amber-100
                                        text-amber-700
                                        flex
                                        items-center
                                        justify-center
                                        font-bold
                                    ">
                                        #{index + 1}
                                    </div>

                                    <div>
                                        <h3 className="font-semibold text-slate-900">
                                            {winner.name}
                                        </h3>

                                        <p className="text-sm text-slate-500">
                                            {winner.title || "Untitled submission"}
                                        </p>
                                    </div>
                                </div>

                                <span className="
                                    px-4
                                    py-2
                                    rounded-full
                                    bg-amber-100
                                    text-amber-700
                                    text-sm
                                    font-medium
                                ">
                                    {winner.score} points
                                </span>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
