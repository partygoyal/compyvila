"use client";

import { API_URL, STORAGE_URL, authHeaders } from "@/src/lib/api";
import ShareButton from "@/src/components/ShareButton";
import { ThumbsDown, ThumbsUp } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import toast from "react-hot-toast";

type VotingSubmission = {
    id: number;
    title: string | null;
    description: string | null;
    file_path: string;
    file_type: string;
    public_up_votes_count: number;
    public_down_votes_count: number;
    public_votes?: {
        id: number;
        vote_type: "up" | "down";
    }[];
    user?: {
        name?: string;
    };
};

type Competition = {
    title: string;
    slug: string;
};

export default function PublicVotingPage({
    params,
}: {
    params: Promise<{ slug: string }>;
}) {
    const router = useRouter();
    const [slug, setSlug] = useState("");
    const [competition, setCompetition] =
        useState<Competition | null>(null);
    const [submissions, setSubmissions] =
        useState<VotingSubmission[]>([]);
    const [canVote, setCanVote] = useState(false);
    const [loading, setLoading] = useState(true);
    const [votingId, setVotingId] = useState<number | null>(null);
    const [message, setMessage] = useState("");

    const loadVoting = async (competitionSlug: string) => {
        setLoading(true);
        setMessage("");

        try {
            const token =
                localStorage.getItem("token") ||
                sessionStorage.getItem("token");

            setCanVote(Boolean(token));

            const response = await fetch(
                `${API_URL}/competition/${competitionSlug}/public-voting`,
                {
                    cache: "no-store",
                    headers: token ? authHeaders() : undefined,
                }
            );

            const data = await response.json();

            if (!data.success) {
                setMessage(data.message || "Public voting is unavailable.");
                setSubmissions([]);
                return;
            }

            setCompetition(data.competition);
            setCanVote(Boolean(token) || Boolean(data.can_vote));
            setSubmissions(data.submissions || []);
        } catch {
            setMessage("Could not load public voting right now.");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        (async () => {
            const resolvedParams = await params;

            setSlug(resolvedParams.slug);
            await loadVoting(resolvedParams.slug);
        })();
    }, [params]);

    const vote = async (
        submissionId: number,
        voteType: "up" | "down"
    ) => {
        const token =
            localStorage.getItem("token") ||
            sessionStorage.getItem("token");

        if (!token) {
            router.push("/login");
            return;
        }

        setVotingId(submissionId);

        try {
            const response = await fetch(`${API_URL}/public-votes`, {
                method: "POST",
                headers: {
                    ...authHeaders(),
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    submission_id: submissionId,
                    vote_type: voteType,
                }),
            });

            const data = await response.json();

            if (!data.success) {
                toast.error(data.message || "Vote could not be recorded.");
                return;
            }

            toast.success(
                voteType === "up"
                    ? "Thumbs up recorded."
                    : "Thumbs down recorded."
            );
            await loadVoting(slug);
        } catch {
            toast.error("Vote could not be recorded.");
        } finally {
            setVotingId(null);
        }
    };

    return (
        <div className="bg-slate-50">
            <div className="max-w-7xl mx-auto px-6 py-10">
                <div className="
                    rounded-3xl
                    bg-gradient-to-r
                    from-emerald-400
                    via-teal-400
                    to-sky-400
                    p-10
                    text-white
                    mb-10
                ">
                    <p className="uppercase tracking-widest text-sm">
                        Public voting
                    </p>

                    <h1 className="text-5xl font-bold mt-3">
                        {competition?.title || "Competition Voting"}
                    </h1>

                    <p className="mt-4 text-white/90">
                        View public submissions and give a simple thumbs up or thumbs down.
                    </p>

                    <div className="mt-6 flex flex-wrap gap-3">
                        <Link
                            href={`/competitions/${slug}`}
                            className="px-5 py-3 rounded-2xl bg-white/20 text-white"
                        >
                            Competition Details
                        </Link>

                        <Link
                            href={`/competitions/${slug}/leaderboard`}
                            className="px-5 py-3 rounded-2xl bg-white text-emerald-700"
                        >
                            Judge Leaderboard
                        </Link>

                        {!canVote && (
                            <Link
                                href="/login"
                                className="px-5 py-3 rounded-2xl bg-slate-900 text-white"
                            >
                                Login to Vote
                            </Link>
                        )}
                    </div>
                </div>

                {loading && (
                    <div className="bg-white border border-slate-100 rounded-3xl p-8">
                        Loading submissions...
                    </div>
                )}

                {!loading && message && (
                    <div className="bg-white border border-slate-100 rounded-3xl p-8 text-slate-700">
                        {message}
                    </div>
                )}

                {!loading && !message && submissions.length === 0 && (
                    <div className="bg-white border border-slate-100 rounded-3xl p-8 text-slate-700">
                        No submissions are available for public voting yet.
                    </div>
                )}

                {!loading && submissions.length > 0 && (
                    <div className="grid lg:grid-cols-3 gap-8">
                        {submissions.map((submission, index) => {
                            const selectedVote =
                                submission.public_votes?.[0]?.vote_type;

                            return (
                            <div
                                id={`submission-${submission.id}`}
                                key={submission.id}
                                className="
                                    bg-white
                                    rounded-3xl
                                    overflow-hidden
                                    border
                                    border-slate-100
                                    shadow-sm
                                "
                            >
                                {submission.file_type === "image" ? (
                                    <img
                                        src={`${STORAGE_URL}/${submission.file_path}`}
                                        alt={submission.title || "Submission"}
                                        className="w-full h-64 object-cover"
                                    />
                                ) : (
                                    <div className="
                                        h-64
                                        bg-slate-100
                                        flex
                                        items-center
                                        justify-center
                                        text-slate-500
                                        text-sm
                                        uppercase
                                        tracking-widest
                                    ">
                                        {submission.file_type}
                                    </div>
                                )}

                                <div className="p-6">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <p className="text-sm text-slate-500">
                                                Rank #{index + 1}
                                            </p>

                                            <h2 className="text-xl font-semibold text-slate-900 mt-1">
                                                {submission.title || "Untitled submission"}
                                            </h2>
                                        </div>

                                        <div className="text-right">
                                            <div className="text-3xl font-bold text-emerald-600">
                                                {submission.public_up_votes_count
                                                    - submission.public_down_votes_count}
                                            </div>

                                            <p className="text-xs text-slate-500">
                                                net votes
                                            </p>
                                        </div>
                                    </div>

                                    <p className="mt-3 text-sm text-slate-500">
                                        By {submission.user?.name || "Participant"}
                                    </p>

                                    {submission.description && (
                                        <p className="mt-4 text-sm text-slate-600 line-clamp-3">
                                            {submission.description}
                                        </p>
                                    )}

                                    <div className="mt-5">
                                        <ShareButton
                                            path={`/competitions/${slug}/voting#submission-${submission.id}`}
                                            title={submission.title || "Competition submission"}
                                            text={`View this submission in ${competition?.title || "CompyVila"}`}
                                        />
                                    </div>

                                    <div className="mt-6 grid grid-cols-2 gap-3">
                                        <button
                                            onClick={() => vote(submission.id, "up")}
                                            disabled={votingId === submission.id}
                                            className={`
                                                px-5
                                                py-3
                                                rounded-2xl
                                                font-medium
                                                flex
                                                items-center
                                                justify-center
                                                gap-2
                                                disabled:opacity-60
                                                disabled:cursor-not-allowed
                                                ${
                                                    selectedVote === "up"
                                                        ? "bg-emerald-600 text-white ring-4 ring-emerald-100"
                                                        : "bg-emerald-100 text-emerald-700"
                                                }
                                            `}
                                        >
                                            <ThumbsUp size={18} />
                                            {submission.public_up_votes_count}
                                        </button>

                                        <button
                                            onClick={() => vote(submission.id, "down")}
                                            disabled={votingId === submission.id}
                                            className={`
                                                px-5
                                                py-3
                                                rounded-2xl
                                                font-medium
                                                flex
                                                items-center
                                                justify-center
                                                gap-2
                                                disabled:opacity-60
                                                disabled:cursor-not-allowed
                                                ${
                                                    selectedVote === "down"
                                                        ? "bg-rose-600 text-white ring-4 ring-rose-100"
                                                        : "bg-rose-100 text-rose-700"
                                                }
                                            `}
                                        >
                                            <ThumbsDown size={18} />
                                            {submission.public_down_votes_count}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </div>
    );
}
