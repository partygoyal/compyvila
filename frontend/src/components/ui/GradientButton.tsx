import type { ButtonHTMLAttributes, ReactNode } from "react";

export default function GradientButton({
    children,
    className = "",
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement> & {
    children: ReactNode;
}) {
    return (
        <button
            {...props}
            className={`
                bg-gradient-to-r
                from-violet-600
                to-pink-500
                text-white
                px-5
                py-2
                rounded-xl
                shadow-lg
                hover:scale-105
                transition
                text-sm
                font-medium
                ${className}
            `}
        >
            {children}
        </button>
    );
}
