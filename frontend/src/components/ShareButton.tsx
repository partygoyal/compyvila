"use client";

import { Copy, Share2 } from "lucide-react";
import toast from "react-hot-toast";

type ShareButtonProps = {
    path: string;
    title: string;
    text?: string;
    label?: string;
    variant?: "solid" | "soft" | "light";
};

export default function ShareButton({
    path,
    title,
    text,
    label = "Share",
    variant = "soft",
}: ShareButtonProps) {
    const share = async () => {
        const url = new URL(path, window.location.origin).toString();
        const shareData = {
            title,
            text: text || title,
            url,
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
                return;
            }

            await navigator.clipboard.writeText(url);
            toast.success("Share link copied.");
        } catch (error) {
            if (error instanceof DOMException && error.name === "AbortError") {
                return;
            }

            await navigator.clipboard.writeText(url);
            toast.success("Share link copied.");
        }
    };

    return (
        <button
            type="button"
            onClick={share}
            className={`
                inline-flex
                items-center
                justify-center
                gap-2
                rounded-2xl
                px-5
                py-3
                text-sm
                font-medium
                transition
                ${variantClasses[variant]}
            `}
        >
            {navigatorCanShareIcon() ? (
                <Share2 size={17} />
            ) : (
                <Copy size={17} />
            )}
            {label}
        </button>
    );
}

const variantClasses = {
    solid: "bg-slate-900 text-white hover:bg-slate-800",
    soft: "bg-slate-100 text-slate-700 hover:bg-slate-200",
    light: "bg-white/20 text-white hover:bg-white/30",
};

function navigatorCanShareIcon() {
    if (typeof navigator === "undefined") {
        return true;
    }

    return Boolean(navigator.share);
}
