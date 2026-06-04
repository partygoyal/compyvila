"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import type { DashboardNotification } from "@/src/types/api";
import { Bell, CheckCheck } from "lucide-react";
import Link from "next/link";
import { useCallback, useEffect, useRef, useState } from "react";

type NotificationResponse = {
    unread_count: number;
    notifications: DashboardNotification[];
};

export default function NotificationBell() {
    const [open, setOpen] = useState(false);
    const [unreadCount, setUnreadCount] = useState(0);
    const [notifications, setNotifications] = useState<DashboardNotification[]>([]);
    const wrapperRef = useRef<HTMLDivElement | null>(null);

    const loadNotifications = useCallback(async () => {
        const response = await fetch(`${API_URL}/notifications`, {
            headers: authHeaders(),
        });

        if (!response.ok) {
            return;
        }

        const data: NotificationResponse = await response.json();
        setUnreadCount(data.unread_count);
        setNotifications(data.notifications);
    }, []);

    useEffect(() => {
        const timeout = window.setTimeout(loadNotifications, 0);

        const interval = window.setInterval(loadNotifications, 8000);

        return () => {
            window.clearTimeout(timeout);
            window.clearInterval(interval);
        };
    }, [loadNotifications]);

    useEffect(() => {
        function handleClick(event: MouseEvent) {
            if (
                wrapperRef.current &&
                !wrapperRef.current.contains(event.target as Node)
            ) {
                setOpen(false);
            }
        }

        document.addEventListener("mousedown", handleClick);

        return () => document.removeEventListener("mousedown", handleClick);
    }, []);

    const markAsRead = async (notification: DashboardNotification) => {
        if (!notification.read_at) {
            await fetch(`${API_URL}/notifications/${notification.id}/read`, {
                method: "POST",
                headers: authHeaders(),
            });

            await loadNotifications();
        }

        setOpen(false);
    };

    const markAllAsRead = async () => {
        await fetch(`${API_URL}/notifications/read-all`, {
            method: "POST",
            headers: authHeaders(),
        });

        await loadNotifications();
    };

    return (
        <div
            ref={wrapperRef}
            className="relative"
        >
            <button
                type="button"
                onClick={() => setOpen((current) => !current)}
                className="
                    relative
                    h-11
                    w-11
                    rounded-xl
                    border
                    border-slate-200
                    bg-white
                    text-slate-600
                    flex
                    items-center
                    justify-center
                    hover:bg-slate-50
                    transition
                "
                aria-label="Notifications"
            >
                <Bell size={20} />

                {unreadCount > 0 && (
                    <span className="
                        absolute
                        -top-1
                        -right-1
                        min-w-5
                        h-5
                        px-1
                        rounded-full
                        bg-rose-500
                        text-white
                        text-[11px]
                        font-semibold
                        flex
                        items-center
                        justify-center
                    ">
                        {unreadCount > 9 ? "9+" : unreadCount}
                    </span>
                )}
            </button>

            {open && (
                <div className="
                    absolute
                    right-0
                    mt-3
                    w-96
                    max-w-[calc(100vw-2rem)]
                    bg-white
                    border
                    border-slate-100
                    rounded-2xl
                    shadow-xl
                    shadow-slate-200/70
                    z-50
                    overflow-hidden
                ">
                    <div className="p-4 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div>
                            <h3 className="font-semibold text-slate-900">
                                Notifications
                            </h3>

                            <p className="text-xs text-slate-500 mt-1">
                                Updates refresh automatically.
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={markAllAsRead}
                            disabled={unreadCount === 0}
                            className="
                                h-9
                                w-9
                                rounded-xl
                                bg-slate-100
                                text-slate-600
                                flex
                                items-center
                                justify-center
                                disabled:opacity-40
                            "
                            aria-label="Mark all as read"
                        >
                            <CheckCheck size={17} />
                        </button>
                    </div>

                    <div className="max-h-96 overflow-y-auto">
                        {notifications.length === 0 ? (
                            <p className="p-5 text-sm text-slate-500">
                                No notifications yet.
                            </p>
                        ) : (
                            notifications.map((notification) => (
                                <NotificationItem
                                    key={notification.id}
                                    notification={notification}
                                    onOpen={() => markAsRead(notification)}
                                />
                            ))
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

function NotificationItem({
    notification,
    onOpen,
}: {
    notification: DashboardNotification;
    onOpen: () => void;
}) {
    const content = (
        <div className={`
            p-4
            border-b
            border-slate-100
            last:border-b-0
            hover:bg-slate-50
            transition
            ${notification.read_at ? "bg-white" : "bg-indigo-50/70"}
        `}>
            <div className="flex gap-3">
                <span className={`
                    mt-1
                    h-2
                    w-2
                    rounded-full
                    shrink-0
                    ${notification.read_at ? "bg-slate-300" : "bg-indigo-500"}
                `} />

                <div>
                    <p className="text-sm font-semibold text-slate-900">
                        {notification.title}
                    </p>

                    <p className="text-sm text-slate-600 mt-1 leading-5">
                        {notification.message}
                    </p>

                    {notification.created_at && (
                        <p className="text-xs text-slate-400 mt-2">
                            {new Date(notification.created_at).toLocaleString()}
                        </p>
                    )}
                </div>
            </div>
        </div>
    );

    if (notification.url) {
        return (
            <Link
                href={notification.url}
                onClick={onOpen}
            >
                {content}
            </Link>
        );
    }

    return (
        <button
            type="button"
            onClick={onOpen}
            className="w-full text-left"
        >
            {content}
        </button>
    );
}
