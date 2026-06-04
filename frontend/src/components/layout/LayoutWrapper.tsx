"use client";

import { usePathname } from "next/navigation";
import Navbar from "./Navbar";
import Footer from "./Footer";
import { Toaster } from "react-hot-toast";

export default function LayoutWrapper({
    children,
}: {
    children: React.ReactNode;
}) {
    const pathname = usePathname();

    const isDashboard =
        pathname.startsWith("/dashboard");

    if (isDashboard) {
        return (
            <>
                <Toaster position="top-right" />
                {children}
            </>
        );
    }

    return (
        <>
            <Toaster position="top-right" />

            <Navbar />

            {children}

            <Footer />
        </>
    );
}
