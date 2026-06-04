"use client";

import { API_URL, STORAGE_URL, authHeaders } from "@/src/lib/api";
import type { Review, Submission } from "@/src/types/api";
import Link from "next/link";
import { useEffect, useMemo, useState } from "react";
import toast from "react-hot-toast";

type ReviewForm = {
    creativity: string;
    presentation: string;
    quality: string;
    performance: string;
    comments: string;
};

const emptyForm: ReviewForm = {
    creativity: "0",
    presentation: "0",
    quality: "0",
    performance: "0",
    comments: "",
};

export default function JudgeSubmissionReviewPage({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const [submission, setSubmission] =
        useState<Submission | null>(null);
    const [form, setForm] = useState<ReviewForm>(emptyForm);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [shortlisting, setShortlisting] = useState(false);

    useEffect(() => {
        (async () => {
            const { id } = await params;

            const response = await fetch(`${API_URL}/judge/submissions`, {
                headers: authHeaders(),
            });

            const submissions: Submission[] = await response.json();
            const current = submissions.find(
                (item) => item.id === Number(id)
            );
            const review = current?.reviews?.[0];

            setSubmission(current || null);

            if (review) {
                setForm(reviewToForm(review));
            }

            setLoading(false);
        })();
    }, [params]);

    const totalScore = useMemo(() => {
        return [
            form.creativity,
            form.presentation,
            form.quality,
            form.performance,
        ].reduce((total, value) => total + Number(value || 0), 0);
    }, [form]);

    const updateField = (
        key: keyof ReviewForm,
        value: string
    ) => {
        setForm((prev) => ({
            ...prev,
            [key]: value,
        }));
    };

    const saveReview = async () => {
        if (!submission) {
            return;
        }

        setSaving(true);

        const response = await fetch(`${API_URL}/judge/review`, {
            method: "POST",
            headers: {
                ...authHeaders(),
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                submission_id: submission.id,
                creativity: Number(form.creativity),
                presentation: Number(form.presentation),
                quality: Number(form.quality),
                performance: Number(form.performance),
                comments: form.comments,
            }),
        });

        const data = await response.json();
        setSaving(false);

        if (data.success) {
            toast.success("Review saved.");
            return;
        }

        toast.error(data.message || "Review could not be saved.");
    };

    const toggleShortlist = async () => {
        if (!submission) {
            return;
        }

        setShortlisting(true);

        const nextShortlisted = submission.status !== "shortlisted";

        const response = await fetch(
            `${API_URL}/judge/submissions/${submission.id}/shortlist`,
            {
                method: "POST",
                headers: {
                    ...authHeaders(),
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    shortlisted: nextShortlisted,
                }),
            }
        );

        const data = await response.json();
        setShortlisting(false);

        if (data.success) {
            setSubmission(data.submission);
            toast.success(
                nextShortlisted
                    ? "Participant shortlisted."
                    : "Participant removed from shortlist."
            );
            return;
        }

        toast.error(data.message || "Shortlist status could not be updated.");
    };

    if (loading) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                Loading review...
            </div>
        );
    }

    if (!submission) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                <p className="text-slate-500">
                    Submission not found or not assigned to you.
                </p>

                <Link
                    href="/dashboard/judge/submissions"
                    className="inline-flex mt-4 text-indigo-600"
                >
                    Back to assigned submissions
                </Link>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="bg-white rounded-3xl border border-slate-100 overflow-hidden">
                {submission.file_type === "image" && submission.file_path && (
                    <img
                        src={`${STORAGE_URL}/${submission.file_path}`}
                        alt={submission.title || "Submission"}
                        className="w-full h-[420px] object-cover"
                    />
                )}

                <div className="p-8">
                    <p className="text-sm text-slate-500">
                        {submission.competition?.title}
                    </p>

                    <h1 className="text-4xl font-bold text-slate-800 mt-2">
                        {submission.title || "Untitled submission"}
                    </h1>

                    <p className="text-sm text-slate-500 mt-3">
                        By {submission.user?.name || "Participant"} · {submission.round?.title}
                    </p>

                    {submission.status === "shortlisted" && (
                        <p className="inline-flex mt-4 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                            Shortlisted
                        </p>
                    )}

                    {submission.description && (
                        <p className="text-slate-600 mt-5">
                            {submission.description}
                        </p>
                    )}
                </div>
            </div>

            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                <div className="flex justify-between gap-6 items-start mb-8">
                    <div>
                        <p className="text-sm text-slate-500">
                            Evaluation
                        </p>

                        <h2 className="text-2xl font-semibold text-slate-900 mt-1">
                            Score Submission
                        </h2>
                    </div>

                    <div className="px-6 py-3 rounded-2xl text-white bg-gradient-to-r from-indigo-400 to-violet-400">
                        <p className="text-xs opacity-90">
                            Total
                        </p>

                        <p className="text-3xl font-bold">
                            {totalScore}/40
                        </p>
                    </div>
                </div>

                <div className="grid md:grid-cols-4 gap-5">
                    {scoreFields.map((field) => (
                        <div key={field.key}>
                            <label className="text-sm text-slate-500">
                                {field.label}
                            </label>

                            <input
                                type="number"
                                min={0}
                                max={10}
                                value={form[field.key]}
                                onChange={(event) =>
                                    updateField(
                                        field.key,
                                        event.target.value
                                    )
                                }
                                className="w-full mt-2 border border-slate-200 rounded-xl p-3"
                            />
                        </div>
                    ))}
                </div>

                <div className="mt-6">
                    <label className="text-sm text-slate-500">
                        Comments
                    </label>

                    <textarea
                        rows={5}
                        value={form.comments}
                        onChange={(event) =>
                            updateField("comments", event.target.value)
                        }
                        className="w-full mt-2 border border-slate-200 rounded-xl p-3"
                    />
                </div>

                <div className="mt-8 flex justify-end gap-3">
                    <Link
                        href="/dashboard/judge/submissions"
                        className="px-6 py-3 rounded-2xl bg-slate-100 text-slate-700"
                    >
                        Back
                    </Link>

                    <button
                        onClick={toggleShortlist}
                        disabled={shortlisting}
                        className="
                            px-6
                            py-3
                            rounded-2xl
                            bg-emerald-100
                            text-emerald-700
                            disabled:opacity-60
                        "
                    >
                        {shortlisting
                            ? "Updating..."
                            : submission.status === "shortlisted"
                                ? "Remove Shortlist"
                                : "Shortlist"}
                    </button>

                    <button
                        onClick={saveReview}
                        disabled={saving}
                        className="
                            px-8
                            py-3
                            rounded-2xl
                            text-white
                            bg-gradient-to-r
                            from-indigo-400
                            to-violet-400
                            disabled:opacity-60
                        "
                    >
                        {saving ? "Saving..." : "Save Review"}
                    </button>
                </div>
            </div>
        </div>
    );
}

const scoreFields: {
    key: keyof Omit<ReviewForm, "comments">;
    label: string;
}[] = [
    {
        key: "creativity",
        label: "Creativity",
    },
    {
        key: "presentation",
        label: "Presentation",
    },
    {
        key: "quality",
        label: "Quality",
    },
    {
        key: "performance",
        label: "Performance",
    },
];

function reviewToForm(review: Review): ReviewForm {
    return {
        creativity: review.creativity?.toString() || "0",
        presentation: review.presentation?.toString() || "0",
        quality: review.quality?.toString() || "0",
        performance: review.performance?.toString() || "0",
        comments: review.comments || "",
    };
}
