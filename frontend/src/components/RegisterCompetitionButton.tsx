"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { Registration, User } from "@/src/types/api";
import { useEffect, useState } from "react";
import toast from "react-hot-toast";

export default function RegisterCompetitionButton({
    competitionId,
    initialRegistered = false,
}: {
    competitionId: number;
    initialRegistered?: boolean;
}) {
    const [loading, setLoading] = useState(false);
    const [checking, setChecking] = useState(true);
    const [errors, setErrors] = useState<string[]>([]);
    const [registered, setRegistered] = useState(initialRegistered);
    const [hiddenForRole, setHiddenForRole] = useState(false);

    useEffect(() => {
        const token =
            localStorage.getItem("token") ||
            sessionStorage.getItem("token");

        if (!token) {
            const timeout = window.setTimeout(() => {
                setChecking(false);
            }, 0);

            return () => window.clearTimeout(timeout);
        }

        Promise.all([
            fetch(`${API_URL}/me`, {
                headers: authHeaders(),
            }).then((response) => response.json()),
            fetch(`${API_URL}/my-registrations`, {
                headers: authHeaders(),
            }).then((response) => response.json()),
        ])
            .then(([user, registrations]: [User, Registration[]]) => {
                if (
                    user.roles?.includes("judge") ||
                    user.roles?.includes("admin")
                ) {
                    setHiddenForRole(true);
                    return;
                }

                setRegistered(
                    registrations.some(
                        (registration) =>
                            registration.competition_id === competitionId
                    )
                );
            })
            .finally(() => {
                setChecking(false);
            });
    }, [competitionId]);

    if (hiddenForRole) {
        return null;
    }

    const registerCompetition = async () => {
        if (loading || checking || registered) {
            return;
        }

        const token =
            localStorage.getItem("token") ||
            sessionStorage.getItem("token");

        if (!token) {
            window.location.href = "/login";
            return;
        }

        setLoading(true);
        setErrors([]);

        try {
            const response = await fetch(
                `${API_URL}/competition-registration`,
                {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        competition_id: competitionId,
                        rules_accepted: true,
                    }),
                }
            );

            const data = await response.json();

            if (data.success) {
                setRegistered(true);
                toast.success("Competition registration successful.");
                return;
            }

            const messages = data.errors?.length
                ? data.errors
                : [data.message || "Registration failed."];

            setErrors(messages);
            toast.error(messages[0]);
        } catch {
            toast.error("Registration failed. Please try again.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="mt-6">
            <button
                onClick={registerCompetition}
                disabled={loading || checking || registered}
                className="
                    w-full
                    px-6
                    py-3
                    bg-black
                    text-white
                    rounded-lg
                    disabled:opacity-60
                    disabled:cursor-not-allowed
                "
            >
                {registered
                    ? "Already Registered"
                    : checking
                    ? "Checking..."
                    : loading
                    ? "Registering..."
                    : "Register Now"}
            </button>

            {errors.length > 0 && (
                <div className="mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    {errors.map((error) => (
                        <p key={error}>
                            {error}
                        </p>
                    ))}
                </div>
            )}
        </div>
    );
}
