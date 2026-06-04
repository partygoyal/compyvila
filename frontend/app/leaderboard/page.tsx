import { API_URL } from "@/src/lib/api";

type GlobalLeaderboardItem = {
    name: string;
    title: string | null;
    score: number | string;
};

async function getLeaderboard(): Promise<GlobalLeaderboardItem[]> {
    const response = await fetch(`${API_URL}/results`, {
        cache: "no-store",
    });

    if (!response.ok) {
        return [];
    }

    return response.json();
}

export default async function GlobalLeaderboardPage() {
    const results = await getLeaderboard();

    return (
        <div className="max-w-7xl mx-auto px-6 py-10">
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
                    Global Leaderboard
                </h1>

                <p className="mt-4 text-white/90">
                    Top ranked submissions across CompyVila competitions.
                </p>
            </div>

            <div className="
                bg-white
                rounded-3xl
                border
                border-slate-100
                overflow-hidden
            ">
                <div className="p-8 border-b border-slate-100">
                    <h2 className="text-2xl font-semibold">
                        Rankings
                    </h2>
                </div>

                <div className="divide-y divide-slate-100">
                    {results.length === 0 ? (
                        <div className="p-8 text-slate-500">
                            No leaderboard results are available yet.
                        </div>
                    ) : (
                        results.map((item, index) => (
                            <div
                                key={`${item.name}-${item.title}-${index}`}
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
                                        #{index + 1}
                                    </div>

                                    <div>
                                        <h3 className="font-semibold text-slate-900">
                                            {item.name}
                                        </h3>

                                        <p className="text-sm text-slate-500">
                                            {item.title || "Untitled submission"}
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
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
