"use client";

import { API_URL, STORAGE_URL, authHeaders } from "@/src/lib/api";
import ShareButton from "@/src/components/ShareButton";
import type { Submission } from "@/src/types/api";
import { useEffect, useState } from "react";

export default function SubmissionDetailPage({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const [submission, setSubmission] =
        useState<Submission | null>(null);

    useEffect(() => {
        (async () => {
            const { id } = await params;

            fetch(`${API_URL}/submission/${id}`, {
                headers: authHeaders(),
            })
                .then((res) => res.json())
                .then(setSubmission);
        })();
    }, [params]);

    if (!submission) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                <p className="text-slate-500">
                    Loading submission...
                </p>
            </div>
        );
    }

    const reviews = submission.reviews || [];

    return (
        <div className="space-y-6">

            {/* Header */}

            <div className="bg-white rounded-3xl border border-slate-100 overflow-hidden">

                {submission.file_type === "image" &&
                    submission.file_path && (

                    <img
                        src={`${STORAGE_URL}/${submission.file_path}`}
                        alt={submission.title || "Submission"}
                        className="w-full h-[450px] object-cover"
                    />

                )}

                <div className="p-8">

                    <div className="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">

                        <div>

                            <p className="text-sm text-slate-500">
                                Submission Entry
                            </p>

                            <h1 className="text-4xl font-bold text-slate-800 mt-2">
                                {submission.title}
                            </h1>

                            <p className="text-slate-500 mt-4">
                                {submission.description}
                            </p>

                        </div>

                        <div className="flex flex-wrap gap-3">
                            <span className="
                                px-4
                                py-2
                                rounded-full
                                text-sm
                                font-medium
                                bg-indigo-100
                                text-indigo-700
                            ">
                                {submission.status}
                            </span>

                            <ShareButton
                                path={`/dashboard/submissions/${submission.id}`}
                                title={submission.title || "My submission"}
                                text={`View my submission for ${submission.competition?.title || "CompyVila"}`}
                            />
                        </div>

                    </div>

                </div>

            </div>

            {/* Stats */}

            <div className="grid md:grid-cols-4 gap-6">

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-indigo-400 to-indigo-500">

                    <p className="text-sm opacity-90">
                        Submission ID
                    </p>

                    <h2 className="text-3xl font-bold mt-3">
                        #{submission.id}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-sky-400 to-blue-500">

                    <p className="text-sm opacity-90">
                        File Type
                    </p>

                    <h2 className="text-2xl font-bold mt-4 capitalize">
                        {submission.file_type}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-violet-400 to-purple-500">

                    <p className="text-sm opacity-90">
                        Competition
                    </p>

                    <h2 className="text-lg font-bold mt-4">
                        {submission.competition?.title}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-cyan-400 to-sky-500">

                    <p className="text-sm opacity-90">
                        Round
                    </p>

                    <h2 className="text-lg font-bold mt-4">
                        {submission.round?.round_number}
                    </h2>

                </div>

            </div>

            {/* Details */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <h2 className="text-2xl font-semibold mb-6">
                    Submission Details
                </h2>

                <div className="grid md:grid-cols-2 gap-6">

                    <div>

                        <p className="text-xs text-slate-500">
                            Competition
                        </p>

                        <p className="font-medium mt-1">
                            {submission.competition?.title}
                        </p>

                    </div>

                    <div>

                        <p className="text-xs text-slate-500">
                            Round
                        </p>

                        <p className="font-medium mt-1">
                            {submission.round?.title}
                        </p>

                    </div>

                    <div>

                        <p className="text-xs text-slate-500">
                            Submitted By
                        </p>

                        <p className="font-medium mt-1">
                            {submission.user?.name}
                        </p>

                    </div>

                    <div>

                        <p className="text-xs text-slate-500">
                            Status
                        </p>

                        <p className="font-medium mt-1 capitalize">
                            {submission.status}
                        </p>

                    </div>

                </div>

            </div>

            {/* Judge Reviews */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <div className="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">

                    <div>

                        <p className="text-sm text-slate-500">
                            Evaluation
                        </p>

                        <h2 className="text-2xl font-semibold">
                            Judge Scores
                        </h2>

                    </div>

                    <span className="px-4 py-2 rounded-full bg-slate-100 text-slate-600 text-sm">
                        {reviews.length} {reviews.length === 1 ? "review" : "reviews"}
                    </span>

                </div>

                {reviews.length === 0 ? (

                    <p className="text-slate-500">
                        Judges have not scored this submission yet.
                    </p>

                ) : (

                    <div className="space-y-6">

                        {reviews.map((review) => (

                            <div
                                key={review.id}
                                className="rounded-3xl border border-slate-100 p-6"
                            >

                                <div className="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">

                                    <div>

                                        <p className="text-sm text-slate-500">
                                            Judge
                                        </p>

                                        <h3 className="text-xl font-semibold text-slate-900 mt-1">
                                            {review.judge?.name || "Judge"}
                                        </h3>

                                    </div>

                                    <div className="
                                        px-6
                                        py-3
                                        rounded-2xl
                                        text-white
                                        bg-gradient-to-r
                                        from-indigo-400
                                        to-violet-400
                                    ">

                                        <div className="text-xs opacity-90">
                                            Total Score
                                        </div>

                                        <div className="text-3xl font-bold">
                                            {review.total_score}/40
                                        </div>

                                    </div>

                                </div>

                                <div className="grid md:grid-cols-4 gap-4">

                                    <ScoreBox label="Creativity" value={review.creativity} />
                                    <ScoreBox label="Presentation" value={review.presentation} />
                                    <ScoreBox label="Quality" value={review.quality} />
                                    <ScoreBox label="Performance" value={review.performance} />

                                </div>

                                {review.comments && (

                                    <div className="mt-6">

                                        <p className="text-xs text-slate-500 mb-2">
                                            Judge Comments
                                        </p>

                                        <div className="
                                            bg-slate-50
                                            rounded-2xl
                                            p-5
                                            text-slate-700
                                        ">
                                            {review.comments}
                                        </div>

                                    </div>

                                )}

                            </div>

                        ))}

                    </div>

                )}

            </div>

        </div>
    );
}

function ScoreBox({
    label,
    value,
}: {
    label: string;
    value?: number;
}) {
    return (
        <div className="bg-slate-50 rounded-2xl p-4">
            <p className="text-xs text-slate-500">
                {label}
            </p>

            <h3 className="text-2xl font-bold mt-2">
                {value ?? 0}
            </h3>
        </div>
    );
}
