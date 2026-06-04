import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";

import LayoutWrapper from "@/src/components/layout/LayoutWrapper";

const geistSans = Geist({
    variable: "--font-geist-sans",
    subsets: ["latin"],
});

const geistMono = Geist_Mono({
    variable: "--font-geist-mono",
    subsets: ["latin"],
});

export const metadata: Metadata = {
    title: {
        default: "CompyVila",
        template: "%s | CompyVila",
    },
    description:
        "Join competitions, showcase talent, earn certificates and win recognition.",
};

export default function RootLayout({
    children,
}: Readonly<{
    children: React.ReactNode;
}>) {
    return (
        <html lang="en">

            <body
                className={`
                    ${geistSans.variable}
                    ${geistMono.variable}
                    antialiased
                    bg-slate-50
                    text-slate-900
                `}
            >

                <LayoutWrapper>
                    {children}
                </LayoutWrapper>

            </body>

        </html>
    );
}