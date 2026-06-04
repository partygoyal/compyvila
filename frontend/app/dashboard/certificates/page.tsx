"use client";

import { useEffect, useState } from "react";
import { API_URL, authHeaders } from "@/src/lib/api";
import type { Certificate } from "@/src/types/api";

export default function CertificatesPage() {
    const [items, setItems] = useState<Certificate[]>([]);

    useEffect(() => {
        fetch(`${API_URL}/my-certificates`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then(setItems);
    }, []);

    const downloadCertificate = async (id: number) => {
        const response = await fetch(
            `${API_URL}/certificate/${id}/download`,
            {
                headers: authHeaders(),
            }
        );

        const blob = await response.blob();

        const url = window.URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `certificate-${id}.pdf`;
        a.click();

        window.URL.revokeObjectURL(url);
    };

    return (
        <div className="space-y-6">

            {/* Header */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <p className="text-sm text-slate-500">
                    Awards & Recognition
                </p>

                <h1 className="text-4xl font-bold text-slate-800 mt-2">
                    My Certificates
                </h1>

                <p className="text-sm text-slate-500 mt-3">
                    Download and manage your earned certificates.
                </p>

            </div>

            {/* Stats */}

            <div className="grid md:grid-cols-3 gap-6">

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-indigo-400 to-indigo-500">

                    <p className="text-sm opacity-90">
                        Certificates
                    </p>

                    <h2 className="text-4xl font-bold mt-3">
                        {items.length}
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-sky-400 to-blue-500">

                    <p className="text-sm opacity-90">
                        Achievement
                    </p>

                    <h2 className="text-2xl font-bold mt-4">
                        Active
                    </h2>

                </div>

                <div className="rounded-3xl p-6 text-white bg-gradient-to-r from-violet-400 to-purple-500">

                    <p className="text-sm opacity-90">
                        Recognition
                    </p>

                    <h2 className="text-2xl font-bold mt-4">
                        Verified
                    </h2>

                </div>

            </div>

            {/* Empty State */}

            {items.length === 0 && (

                <div className="bg-white rounded-3xl border border-slate-100 p-12 text-center">

                    <div className="text-6xl mb-4">
                        🏅
                    </div>

                    <h3 className="text-xl font-semibold text-slate-800">
                        No Certificates Yet
                    </h3>

                    <p className="text-sm text-slate-500 mt-2">
                        Certificates will appear here when issued.
                    </p>

                </div>

            )}

            {/* Certificate Cards */}

            <div className="grid lg:grid-cols-2 gap-6">

                {items.map((item) => (

                    <div
                        key={item.id}
                        className="
                            bg-white
                            rounded-3xl
                            border
                            border-slate-100
                            overflow-hidden
                            hover:shadow-lg
                            transition
                        "
                    >

                        {/* Certificate Preview */}

                        <div className="
                            h-48
                            bg-gradient-to-r
                            from-indigo-100
                            via-sky-100
                            to-violet-100
                            flex
                            flex-col
                            items-center
                            justify-center
                        ">

                            <div className="text-6xl">
                                🏆
                            </div>

                            <p className="text-sm font-medium text-slate-700 mt-3">
                                Certificate of Achievement
                            </p>

                        </div>

                        {/* Content */}

                        <div className="p-6">

                            <h2 className="text-xl font-semibold text-slate-800">
                                {item.competition?.title}
                            </h2>

                            <div className="grid grid-cols-2 gap-4 mt-6">

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Certificate No
                                    </p>

                                    <p className="font-medium mt-1">
                                        {item.certificate_number}
                                    </p>

                                </div>

                                <div>

                                    <p className="text-xs text-slate-500">
                                        Type
                                    </p>

                                    <p className="font-medium mt-1 capitalize">
                                        {item.type}
                                    </p>

                                </div>

                            </div>

                            <div className="mt-4">

                                <p className="text-xs text-slate-500">
                                    Issued On
                                </p>

                                <p className="font-medium mt-1">
                                    {item.issued_at}
                                </p>

                            </div>

                            <button
                                onClick={() =>
                                    downloadCertificate(item.id)
                                }
                                className="
                                    mt-6
                                    w-full
                                    py-3
                                    rounded-2xl
                                    text-white
                                    text-sm
                                    font-medium
                                    bg-gradient-to-r
                                    from-indigo-400
                                    to-violet-400
                                    shadow-lg
                                    shadow-indigo-100
                                "
                            >
                                Download Certificate
                            </button>

                        </div>

                    </div>

                ))}

            </div>

        </div>
    );
}
