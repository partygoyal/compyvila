"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { JudgeAssignment, Submission } from "@/src/types/api";
import Link from "next/link";
import { useEffect, useState } from "react";

export default function JudgeDashboardPage() {
    const [assignments, setAssignments] = useState<JudgeAssignment[]>([]);
    const [submissions, setSubmissions] = useState<Submission[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            fetch(`${API_URL}/judge/assignments`, {
                headers: authHeaders(),
            }).then((response) => response.json()),
            fetch(`${API_URL}/judge/submissions`, {
                headers: authHeaders(),
            }).then((response) => response.json()),
        ])
            .then(([assignmentData, submissionData]) => {
                setAssignments(assignmentData);
                setSubmissions(submissionData);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    const reviewedCount = submissions.filter(
        (submission) => (submission.reviews?.length || 0) > 0
    ).length;

    const pendingCount = submissions.length - reviewedCount;

    if (loading) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                Loading judge dashboard...
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                <p className="text-sm text-slate-500">
                    Judge Workspace
                </p>

                <h1 className="text-4xl font-bold text-slate-800 mt-2">
                    Review Dashboard
                </h1>

                <p className="text-sm text-slate-500 mt-3">
                    Review assigned submissions and submit judging scores.
                </p>
            </div>

            <div className="grid md:grid-cols-3 gap-6">
                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-indigo-400 to-indigo-500">
                    <p className="text-sm opacity-90">
                        Assignments
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {assignments.length}
                    </h2>
                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-sky-400 to-blue-500">
                    <p className="text-sm opacity-90">
                        Pending Reviews
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {pendingCount}
                    </h2>
                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-emerald-400 to-teal-500">
                    <p className="text-sm opacity-90">
                        Completed Reviews
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {reviewedCount}
                    </h2>
                </div>
            </div>

            <div className="grid lg:grid-cols-2 gap-6">
                <div className="bg-white rounded-3xl border border-slate-100 p-6">
                    <h2 className="text-xl font-semibold text-slate-800">
                        Assigned Rounds
                    </h2>

                    <div className="mt-5 divide-y divide-slate-100">
                        {assignments.length === 0 ? (
                            <p className="text-sm text-slate-500">
                                No judge assignments found.
                            </p>
                        ) : (
                            assignments.map((assignment) => (
                                <div
                                    key={assignment.id}
                                    className="py-4"
                                >
                                    <p className="font-medium text-slate-900">
                                        {assignment.competition?.title}
                                    </p>

                                    <p className="text-sm text-slate-500 mt-1">
                                        {assignment.round?.title}
                                    </p>
                                </div>
                            ))
                        )}
                    </div>
                </div>

                <div className="bg-white rounded-3xl border border-slate-100 p-6">
                    <h2 className="text-xl font-semibold text-slate-800">
                        Latest Submissions
                    </h2>

                    <div className="mt-5 divide-y divide-slate-100">
                        {submissions.slice(0, 5).map((submission) => (
                            <div
                                key={submission.id}
                                className="py-4 flex justify-between gap-4"
                            >
                                <div>
                                    <p className="font-medium text-slate-900">
                                        {submission.title || "Untitled submission"}
                                    </p>

                                    <p className="text-sm text-slate-500 mt-1">
                                        {submission.user?.name || "Participant"}
                                    </p>
                                </div>

                                <Link
                                    href={`/dashboard/judge/submissions/${submission.id}`}
                                    className="text-sm text-indigo-600"
                                >
                                    Review
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
