import {
    Camera,
    Music,
    Code,
    Palette,
    PenTool,
    Video,
    BookOpen,
    Trophy,
} from "lucide-react";

export default function Categories() {
    const categories = [
        {
            name: "Photography",
            icon: Camera,
            gradient: "from-indigo-400 to-violet-400",
            count: "25+ Competitions",
        },
        {
            name: "Dance",
            icon: Trophy,
            gradient: "from-pink-400 to-rose-400",
            count: "18+ Competitions",
        },
        {
            name: "Singing",
            icon: Music,
            gradient: "from-sky-400 to-blue-500",
            count: "15+ Competitions",
        },
        {
            name: "Coding",
            icon: Code,
            gradient: "from-cyan-400 to-sky-500",
            count: "12+ Competitions",
        },
        {
            name: "Art",
            icon: Palette,
            gradient: "from-violet-400 to-purple-500",
            count: "10+ Competitions",
        },
        {
            name: "Writing",
            icon: PenTool,
            gradient: "from-indigo-400 to-blue-500",
            count: "8+ Competitions",
        },
        {
            name: "Quiz",
            icon: BookOpen,
            gradient: "from-sky-400 to-cyan-400",
            count: "20+ Competitions",
        },
        {
            name: "Video Creation",
            icon: Video,
            gradient: "from-purple-400 to-pink-400",
            count: "14+ Competitions",
        },
    ];

    return (
        <section className="py-24 bg-white">

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
                        Explore Categories
                    </span>

                    <h2 className="text-5xl font-bold mt-6 text-slate-900">
                        Competition Categories
                    </h2>

                    <p className="text-slate-500 max-w-2xl mx-auto mt-4">
                        Discover competitions tailored to your passion,
                        creativity, skills and talent.
                    </p>

                </div>

                <div className="grid lg:grid-cols-4 md:grid-cols-2 gap-6">

                    {categories.map((category) => {

                        const Icon = category.icon;

                        return (
                            <div
                                key={category.name}
                                className="
                                    bg-white
                                    rounded-3xl
                                    p-6
                                    border
                                    border-slate-100
                                    hover:shadow-xl
                                    transition
                                    group
                                "
                            >

                                <div
                                    className={`
                                        w-16
                                        h-16
                                        rounded-2xl
                                        bg-gradient-to-r
                                        ${category.gradient}
                                        flex
                                        items-center
                                        justify-center
                                        text-white
                                        mb-5
                                    `}
                                >
                                    <Icon size={28} />
                                </div>

                                <h3 className="text-xl font-semibold text-slate-900">
                                    {category.name}
                                </h3>

                                <p className="text-sm text-slate-500 mt-2">
                                    {category.count}
                                </p>

                                <div className="
                                    mt-5
                                    text-sm
                                    font-medium
                                    text-indigo-600
                                    opacity-0
                                    group-hover:opacity-100
                                    transition
                                ">
                                    Explore →
                                </div>

                            </div>
                        );
                    })}

                </div>

            </div>

        </section>
    );
}