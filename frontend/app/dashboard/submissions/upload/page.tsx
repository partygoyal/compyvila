"use client";

import { useEffect, useState } from "react";
import { API_URL, authHeaders } from "@/src/lib/api";
import type { Registration } from "@/src/types/api";

export default function UploadSubmissionPage() {
    const [registrations, setRegistrations] = useState<Registration[]>([]);
    const [selectedRegistration, setSelectedRegistration] =
        useState<Registration | null>(null);

    const [competitionId, setCompetitionId] = useState("");
    const [roundId, setRoundId] = useState("");

    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");

    const [file, setFile] = useState<File | null>(null);

    useEffect(() => {
        fetch(`${API_URL}/my-registrations`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then((data: Registration[]) =>
                setRegistrations(
                    data.filter((registration) => registration.can_submit)
                )
            );
    }, []);

    const selectRegistration = (competitionId: string) => {
        setCompetitionId(competitionId);

        const registration = registrations.find(
            (item) => item.competition_id.toString() === competitionId
        );

        setSelectedRegistration(registration || null);
        setRoundId(registration?.current_round_id?.toString() || "");
    };

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();

        const token =
            localStorage.getItem("token") ||
            sessionStorage.getItem("token");

        const formData = new FormData();

        formData.append("competition_id", competitionId);
        formData.append("competition_round_id", roundId);
        formData.append("title", title);
        formData.append("description", description);

        if (file) {
            formData.append("file", file);
        }

        const response = await fetch(
            `${API_URL}/submission`,
            {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
                body: formData,
            }
        );

        const data = await response.json();

        if (data.success) {
            alert("Submission uploaded successfully");
            window.location.href =
                "/dashboard/submissions";
        } else {
            alert(data.message || "Upload failed");
        }
    };

    return (
        <div className="max-w-3xl mx-auto">

            <h1 className="text-3xl font-bold mb-6">
                Upload Submission
            </h1>

            <form
                onSubmit={submit}
                className="space-y-4"
            >

                <select
                    className="w-full border p-3 rounded"
                    value={competitionId}
                    onChange={(e) =>
                        selectRegistration(e.target.value)
                    }
                >
                    <option value="">
                        Select Competition
                    </option>

                    {registrations.map((registration) => (
                        <option
                            key={registration.id}
                            value={registration.competition_id}
                        >
                            {registration.competition?.title}
                        </option>
                    ))}
                </select>

                {registrations.length === 0 && (
                    <div className="rounded-2xl bg-amber-50 p-4 text-sm text-amber-700">
                        You do not have any round open for submission right now.
                    </div>
                )}

                <select
                    className="w-full border p-3 rounded"
                    value={roundId}
                    disabled
                >
                    <option value="">
                        Current Round
                    </option>

                    {selectedRegistration?.current_round && (
                        <option
                            value={selectedRegistration.current_round.id}
                        >
                            Round {selectedRegistration.current_round.round_number}: {selectedRegistration.current_round.title}
                        </option>
                    )}
                </select>

                {selectedRegistration?.progress_message && (
                    <p className="text-sm text-slate-500">
                        {selectedRegistration.progress_message}
                    </p>
                )}

                <input
                    type="text"
                    placeholder="Submission Title"
                    className="w-full border p-3 rounded"
                    value={title}
                    onChange={(e) =>
                        setTitle(e.target.value)
                    }
                />

                <textarea
                    placeholder="Description"
                    className="w-full border p-3 rounded"
                    value={description}
                    onChange={(e) =>
                        setDescription(e.target.value)
                    }
                />

                <input
                    type="file"
                    onChange={(e) =>
                        setFile(
                            e.target.files?.[0] || null
                        )
                    }
                />

                <button
                    disabled={!competitionId || !roundId || !file}
                    className="
                        bg-black
                        text-white
                        px-6
                        py-3
                        rounded
                        disabled:opacity-50
                        disabled:cursor-not-allowed
                    "
                >
                    Upload Submission
                </button>

            </form>

        </div>
    );
}
