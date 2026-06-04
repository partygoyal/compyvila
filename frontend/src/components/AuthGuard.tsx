"use client";

import { useEffect, useState } from "react";

export default function AuthGuard({
    children,
}: {
    children: React.ReactNode;
}) {
    const [ready, setReady] = useState(false);

    useEffect(() => {

        const token =
            localStorage.getItem("token") ||
            sessionStorage.getItem("token");

        if (!token) {
            window.location.href = "/login";
            return;
        }

        const timeout = window.setTimeout(() => {
            setReady(true);
        }, 0);

        return () => window.clearTimeout(timeout);
    }, []);

    if (!ready) {
        return (
            <div className="flex items-center justify-center min-h-screen">
                <p className="text-slate-500">
                    Loading...
                </p>
            </div>
        );
    }

    return <>{children}</>;
}
