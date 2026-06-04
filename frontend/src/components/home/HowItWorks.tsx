import {
    UserPlus,
    Upload,
    Trophy,
    Award,
} from "lucide-react";

export default function HowItWorks() {
    const steps = [
        {
            icon: UserPlus,
            title: "Create Account",
            description:
                "Register your account and explore competitions across multiple categories.",
            gradient:
                "from-indigo-400 to-violet-400",
            step: "01",
        },
        {
            icon: Upload,
            title: "Submit Entry",
            description:
                "Upload your photos, videos, artwork, documents or performances.",
            gradient:
                "from-sky-400 to-blue-500",
            step: "02",
        },
        {
            icon: Trophy,
            title: "Get Evaluated",
            description:
                "Judges review your submissions and score them based on competition criteria.",
            gradient:
                "from-violet-400 to-purple-500",
            step: "03",
        },
        {
            icon: Award,
            title: "Win & Earn",
            description:
                "Receive certificates, rankings, awards and recognition for your achievements.",
            gradient:
                "from-cyan-400 to-sky-500",
            step: "04",
        },
    ];

    return (
        <section className="py-24 bg-slate-50">

            <div className="max-w-7xl mx-auto px-6">

                <div className="text-center mb-16">

                    <span
                        className="
                            inline-flex
                            px-4
                            py-2
                            rounded-full
                            bg-indigo-50
                            text-indigo-600
                            text-sm
                            font-medium
                        "
                    >
                        Simple Process
                    </span>

                    <h2 className="text-5xl font-bold mt-6 text-slate-900">
                        How It Works
                    </h2>

                    <p className="text-slate-500 max-w-2xl mx-auto mt-4">
                        Participate in competitions in just a few simple steps.
                    </p>

                </div>

                <div className="grid lg:grid-cols-4 md:grid-cols-2 gap-8">

                    {steps.map((step) => {

                        const Icon = step.icon;

                        return (
                            <div
                                key={step.title}
                                className="
                                    bg-white
                                    rounded-3xl
                                    p-8
                                    border
                                    border-slate-100
                                    hover:shadow-xl
                                    transition
                                    relative
                                "
                            >

                                <div
                                    className="
                                        absolute
                                        top-6
                                        right-6
                                        text-4xl
                                        font-bold
                                        text-slate-100
                                    "
                                >
                                    {step.step}
                                </div>

                                <div
                                    className={`
                                        w-16
                                        h-16
                                        rounded-2xl
                                        bg-gradient-to-r
                                        ${step.gradient}
                                        flex
                                        items-center
                                        justify-center
                                        text-white
                                        mb-6
                                    `}
                                >
                                    <Icon size={30} />
                                </div>

                                <h3 className="text-xl font-semibold text-slate-900">
                                    {step.title}
                                </h3>

                                <p className="mt-4 text-sm text-slate-500 leading-relaxed">
                                    {step.description}
                                </p>

                            </div>
                        );
                    })}

                </div>

            </div>

        </section>
    );
}