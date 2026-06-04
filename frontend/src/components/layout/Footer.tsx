import Link from "next/link";

export default function Footer() {
    return (
        <footer className="bg-white border-t border-slate-100 mt-24">

            <div className="max-w-7xl mx-auto px-6 py-16">

                <div className="grid md:grid-cols-4 gap-10">

                    {/* Brand */}

                    <div>

                        <h3 className="
                            text-3xl
                            font-bold
                            bg-gradient-to-r
                            from-indigo-500
                            to-sky-500
                            bg-clip-text
                            text-transparent
                        ">
                            CompyVila
                        </h3>

                        <p className="mt-4 text-sm text-slate-500 leading-relaxed">
                            India&apos;s modern competition platform for
                            creators, performers, artists, coders
                            and talented individuals.
                        </p>

                    </div>

                    {/* Platform */}

                    <div>

                        <h4 className="font-semibold text-slate-900 mb-4">
                            Platform
                        </h4>

                        <div className="space-y-3">

                            <Link
                                href="/competitions"
                                className="block text-sm text-slate-500 hover:text-slate-900"
                            >
                                Competitions
                            </Link>

                            <Link
                                href="/dashboard"
                                className="block text-sm text-slate-500 hover:text-slate-900"
                            >
                                Dashboard
                            </Link>

                            <Link
                                href="/register"
                                className="block text-sm text-slate-500 hover:text-slate-900"
                            >
                                Register
                            </Link>

                        </div>

                    </div>

                    {/* Categories */}

                    <div>

                        <h4 className="font-semibold text-slate-900 mb-4">
                            Categories
                        </h4>

                        <div className="space-y-3 text-sm text-slate-500">

                            <p>Photography</p>
                            <p>Dance</p>
                            <p>Singing</p>
                            <p>Coding</p>
                            <p>Art</p>

                        </div>

                    </div>

                    {/* Contact */}

                    <div>

                        <h4 className="font-semibold text-slate-900 mb-4">
                            Contact
                        </h4>

                        <div className="space-y-3">

                            <p className="text-sm text-slate-500">
                                support@compyvila.com
                            </p>

                            <p className="text-sm text-slate-500">
                                India
                            </p>

                        </div>

                    </div>

                </div>

                {/* Bottom */}

                <div className="mt-12 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">

                    <p className="text-sm text-slate-500">
                        © {new Date().getFullYear()} CompyVila.
                        All rights reserved.
                    </p>

                    <div className="flex gap-6">

                        <Link
                            href="/privacy-policy"
                            className="text-sm text-slate-500 hover:text-slate-900"
                        >
                            Privacy
                        </Link>

                        <Link
                            href="/terms"
                            className="text-sm text-slate-500 hover:text-slate-900"
                        >
                            Terms
                        </Link>

                    </div>

                </div>

            </div>

        </footer>
    );
}
