"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { Submission } from "@/src/types/api";
import Link from "next/link";
import { useEffect, useState } from "react";

export default function JudgeSubmissionsPage() {
    const [submissions, setSubmissions] = useState<Submission[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch(`${API_URL}/judge/submissions`, {
            headers: authHeaders(),
        })
            .then((response) => response.json())
            .then(setSubmissions)
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                Loading submissions...
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                <p className="text-sm text-slate-500">
                    Judge Reviews
                </p>

                <h1 className="text-4xl font-bold text-slate-800 mt-2">
                    Assigned Submissions
                </h1>
            </div>

            <div className="grid lg:grid-cols-2 gap-6">
                {submissions.length === 0 ? (
                    <div className="bg-white rounded-3xl border border-slate-100 p-8 text-slate-500">
                        No submissions are assigned to you yet.
                    </div>
                ) : (
                    submissions.map((submission) => {
                        const reviewed =
                            (submission.reviews?.length || 0) > 0;

                        return (
                            <div
                                key={submission.id}
                                className="bg-white rounded-3xl border border-slate-100 p-6"
                            >
                                <div className="flex justify-between gap-4">
                                    <div>
                                        <p className="text-xs text-slate-500">
                                            {submission.competition?.title}
                                        </p>

                                        <h2 className="text-xl font-semibold text-slate-900 mt-2">
                                            {submission.title || "Untitled submission"}
                                        </h2>

                                        <p className="text-sm text-slate-500 mt-2">
                                            By {submission.user?.name || "Participant"}
                                        </p>

                                        {submission.status === "shortlisted" && (
                                            <p className="inline-flex mt-3 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                                                Shortlisted
                                            </p>
                                        )}
                                    </div>

                                    <span className={`
                                        h-fit
                                        px-3
                                        py-1
                                        rounded-full
                                        text-xs
                                        ${
                                            reviewed
                                                ? "bg-emerald-100 text-emerald-700"
                                                : "bg-amber-100 text-amber-700"
                                        }
                                    `}>
                                        {reviewed ? "Reviewed" : "Pending"}
                                    </span>
                                </div>

                                <div className="mt-6 flex justify-between items-center">
                                    <p className="text-sm text-slate-500">
                                        {submission.round?.title}
                                    </p>

                                    <Link
                                        href={`/dashboard/judge/submissions/${submission.id}`}
                                        className="
                                            px-5
                                            py-3
                                            rounded-2xl
                                            text-white
                                            text-sm
                                            bg-gradient-to-r
                                            from-indigo-400
                                            to-violet-400
                                        "
                                    >
                                        {reviewed ? "Edit Review" : "Review"}
                                    </Link>
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </div>
    );
}
