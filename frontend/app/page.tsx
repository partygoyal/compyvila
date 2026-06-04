import Hero from "@/src/components/home/Hero";
import HowItWorks from "@/src/components/home/HowItWorks";
import Categories from "@/src/components/home/Categories";
import FeaturedCompetitions from "@/src/components/home/FeaturedCompetitions";
import Stats from "@/src/components/home/Stats";

export default function HomePage() {
    return (
        <>
            <Hero />
            <FeaturedCompetitions />
            <HowItWorks />
            <Categories />
            <Stats />
        </>
    );
}