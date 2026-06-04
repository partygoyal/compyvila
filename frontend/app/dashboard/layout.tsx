"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { User } from "@/src/types/api";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import AuthGuard from "@/src/components/AuthGuard";
import NotificationBell from "@/src/components/dashboard/NotificationBell";

export default function DashboardLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const pathname = usePathname();
    const router = useRouter();
    const [user, setUser] = useState<User | null>(null);
    const [userLoaded, setUserLoaded] = useState(false);

    useEffect(() => {
        fetch(`${API_URL}/me`, {
            headers: authHeaders(),
        })
            .then((response) => response.json())
            .then(setUser)
            .finally(() => {
                setUserLoaded(true);
            });
    }, []);

    const isJudge = user?.roles?.includes("judge");

    useEffect(() => {
        if (!userLoaded || !isJudge) {
            return;
        }

        const judgeAllowedPath =
            pathname.startsWith("/dashboard/judge");

        if (!judgeAllowedPath) {
            router.replace("/dashboard/judge");
        }
    }, [isJudge, pathname, router, userLoaded]);

    const menus = [
        {
            name: "Dashboard",
            href: "/dashboard",
            icon: "📊",
        },
        {
            name: "Competitions",
            href: "/dashboard/competitions",
            icon: "🏆",
            participantOnly: true,
        },
        {
            name: "Registrations",
            href: "/dashboard/registrations",
            icon: "📝",
            participantOnly: true,
        },
        {
            name: "Submissions",
            href: "/dashboard/submissions",
            icon: "📤",
            participantOnly: true,
        },
        {
            name: "Certificates",
            href: "/dashboard/certificates",
            icon: "🎖️",
            participantOnly: true,
        },
        {
            name: "Judge Dashboard",
            href: "/dashboard/judge",
            icon: "⚖️",
            judgeOnly: true,
        },
        {
            name: "Judge Reviews",
            href: "/dashboard/judge/submissions",
            icon: "✅",
            judgeOnly: true,
        },
        {
            name: "Profile",
            href: "/dashboard/profile",
            icon: "👤",
            shared: true,
        },
    ];

    const visibleMenus = menus.filter((menu) => {
        if (!userLoaded) {
            return menu.shared;
        }

        if (isJudge) {
            return menu.judgeOnly;
        }

        return !menu.judgeOnly;
    });

    return (
        <AuthGuard>

            <div className="min-h-screen bg-slate-50 flex">

                {/* Sidebar */}

                <aside className="w-72 bg-white border-r border-slate-100 flex flex-col">

                    <div className="p-6">

                        <Link href="/">

                            <h2 className="text-3xl font-bold bg-gradient-to-r from-indigo-500 to-sky-500 bg-clip-text text-transparent">
                                CompyVila
                            </h2>

                        </Link>

                        <p className="text-xs text-slate-500 mt-1">
                            Competition Platform
                        </p>

                    </div>

                    <nav className="flex-1 px-4">

                        {visibleMenus.map((menu) => (

                            <Link
                                key={menu.href}
                                href={menu.href}
                                className={`
                                    flex items-center
                                    gap-3
                                    px-4
                                    py-3
                                    rounded-2xl
                                    mb-2
                                    text-sm
                                    transition
                                    ${
                                        pathname === menu.href
                                            ? "bg-gradient-to-r from-indigo-400 to-violet-400 text-white shadow-lg shadow-indigo-100"
                                            : "text-slate-600 hover:bg-slate-100"
                                    }
                                `}
                            >

                                <span>
                                    {menu.icon}
                                </span>

                                {menu.name}

                            </Link>

                        ))}

                    </nav>

                    <div className="p-4">

                        <button
                            onClick={() => {
                                localStorage.removeItem("token");
                                sessionStorage.removeItem("token");

                                window.location.href = "/login";
                            }}
                            className="
                                w-full
                                py-3
                                rounded-2xl
                                bg-gradient-to-r
                                from-slate-600
                                to-slate-700
                                text-white
                                text-sm
                                font-medium
                            "
                        >
                            Logout
                        </button>

                    </div>

                </aside>

                {/* Content */}

                <div className="flex-1 flex flex-col">

                    <header className="bg-white border-b border-slate-100 px-8 py-5">

                        <div className="flex justify-between items-center">

                            <div>

                                <h1 className="text-xl font-semibold text-slate-800">
                                    Dashboard
                                </h1>

                                <p className="text-xs text-slate-500 mt-1">
                                    Welcome to CompyVila
                                </p>

                            </div>

                            <div className="flex items-center gap-3">
                                <NotificationBell />

                                <Link
                                    href="/"
                                    className="
                                        px-5
                                        py-2
                                        rounded-xl
                                        bg-gradient-to-r
                                        from-indigo-400
                                        to-sky-400
                                        text-white
                                        text-sm
                                    "
                                >
                                    View Website
                                </Link>
                            </div>

                        </div>

                    </header>

                    <main className="p-8">
                        {children}
                    </main>

                </div>

            </div>

        </AuthGuard>
    );
}
