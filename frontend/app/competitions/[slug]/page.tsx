import RegisterCompetitionButton from "@/src/components/RegisterCompetitionButton";
import ShareButton from "@/src/components/ShareButton";
import { API_URL, STORAGE_URL } from "@/src/lib/api";
import Link from "next/link";

interface PageProps {
  params: Promise<{
    slug: string;
  }>;
}

async function getCompetition(slug: string) {
  const res = await fetch(
    `${API_URL}/competitions/${slug}`,
    {
      cache: "no-store",
    }
  );

  return res.json();
}

export default async function CompetitionDetailPage({
  params,
}: PageProps) {
  const { slug } = await params;

  const competition = await getCompetition(slug);

  return (
      <div className="bg-slate-50">

          {/* Banner */}

          {competition.banner && (

              <div className="max-w-7xl mx-auto px-6 pt-8">

                  <img
                      src={`${STORAGE_URL}/${competition.banner}`}
                      alt={competition.title}
                      className="w-full h-[500px] object-cover rounded-3xl"
                  />

              </div>

          )}

          <div className="max-w-7xl mx-auto px-6 py-10">

              <div className="grid lg:grid-cols-3 gap-8">

                  {/* LEFT CONTENT */}

                  <div className="lg:col-span-2">

                      {/* Badges */}

                      <div className="flex flex-wrap gap-3 mb-6">

                          <span className="
                              px-4 py-2 rounded-full
                              bg-indigo-100 text-indigo-700
                              text-sm font-medium
                          ">
                              {competition.category?.name}
                          </span>

                          <span className="
                              px-4 py-2 rounded-full
                              bg-sky-100 text-sky-700
                              text-sm font-medium
                          ">
                              ₹{competition.entry_fee}
                          </span>

                          <span className="
                              px-4 py-2 rounded-full
                              bg-violet-100 text-violet-700
                              text-sm font-medium
                          ">
                              {competition.submission_type}
                          </span>

                      </div>

                      <h1 className="text-5xl font-bold text-slate-900">
                          {competition.title}
                      </h1>

                      <p className="
                          mt-6
                          text-slate-600
                          leading-relaxed
                          text-lg
                      ">
                          {competition.description}
                      </p>

                      {/* Actions */}

                      <div className="flex flex-wrap gap-4 mt-8">

                          <Link
                              href={`/competitions/${competition.slug}/leaderboard`}
                              className="
                                  px-6 py-3
                                  rounded-2xl
                                  text-white
                                  bg-gradient-to-r
                                  from-indigo-400
                                  to-violet-400
                              "
                          >
                              🏆 Leaderboard
                          </Link>

                          <Link
                              href={`/competitions/${competition.slug}/winners`}
                              className="
                                  px-6 py-3
                                  rounded-2xl
                                  text-white
                                  bg-gradient-to-r
                                  from-sky-400
                                  to-blue-500
                              "
                          >
                              🥇 Winners
                          </Link>

                          {competition.allow_public_voting && (
                              <Link
                                  href={`/competitions/${competition.slug}/voting`}
                                  className="
                                      px-6 py-3
                                      rounded-2xl
                                      text-white
                                      bg-gradient-to-r
                                      from-emerald-400
                                      to-teal-500
                                  "
                              >
                                  View Submissions & Vote
                              </Link>
                          )}

                          <ShareButton
                              path={`/competitions/${competition.slug}`}
                              title={competition.title}
                              text={competition.description}
                              variant="solid"
                          />

                      </div>

                      {/* Details */}

                      <div className="
                          mt-10
                          bg-white
                          rounded-3xl
                          border border-slate-100
                          p-8
                      ">

                          <h2 className="text-2xl font-semibold mb-6">
                              Competition Details
                          </h2>

                          <div className="grid md:grid-cols-2 gap-6">

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Category
                                  </p>

                                  <p className="font-medium mt-1">
                                      {competition.category?.name}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Entry Fee
                                  </p>

                                  <p className="font-medium mt-1">
                                      ₹{competition.entry_fee}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Submission Type
                                  </p>

                                  <p className="font-medium mt-1">
                                      {competition.submission_type}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Total Rounds
                                  </p>

                                  <p className="font-medium mt-1">
                                      {competition.total_rounds}
                                  </p>
                              </div>

                          </div>

                      </div>

                      {/* Rules */}

                      <div className="
                          mt-8
                          bg-white
                          rounded-3xl
                          border border-slate-100
                          p-8
                      ">

                          <h2 className="text-2xl font-semibold mb-6">
                              Rules & Guidelines
                          </h2>

                          <p className="text-slate-600 leading-relaxed">
                              {competition.rules}
                          </p>

                      </div>

                      {/* Dates */}

                      <div className="
                          mt-8
                          bg-white
                          rounded-3xl
                          border border-slate-100
                          p-8
                      ">

                          <h2 className="text-2xl font-semibold mb-6">
                              Important Dates
                          </h2>

                          <div className="grid md:grid-cols-2 gap-6">

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Registration Start
                                  </p>

                                  <p className="font-medium">
                                      {competition.registration_start}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Registration End
                                  </p>

                                  <p className="font-medium">
                                      {competition.registration_end}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Competition Start
                                  </p>

                                  <p className="font-medium">
                                      {competition.competition_start}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Competition End
                                  </p>

                                  <p className="font-medium">
                                      {competition.competition_end}
                                  </p>
                              </div>

                          </div>

                      </div>

                  </div>

                  {/* RIGHT SIDEBAR */}

                  <div>

                      <div className="
                          sticky top-24
                          bg-white
                          rounded-3xl
                          border border-slate-100
                          p-8
                      ">

                          <h3 className="text-xl font-semibold mb-6">
                              Join Competition
                          </h3>

                          <div className="space-y-5 mb-8">

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Entry Fee
                                  </p>

                                  <p className="text-2xl font-bold">
                                      ₹{competition.entry_fee}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Submission Type
                                  </p>

                                  <p className="font-medium">
                                      {competition.submission_type}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Total Rounds
                                  </p>

                                  <p className="font-medium">
                                      {competition.total_rounds}
                                  </p>
                              </div>

                              <div>
                                  <p className="text-xs text-slate-500">
                                      Public Voting
                                  </p>

                                  <p className="font-medium">
                                      {competition.allow_public_voting
                                          ? "Enabled"
                                          : "Disabled"}
                                  </p>
                              </div>

                          </div>

                          {competition.allow_public_voting && (
                              <Link
                                  href={`/competitions/${competition.slug}/voting`}
                                  className="
                                      block
                                      w-full
                                      text-center
                                      px-6
                                      py-3
                                      rounded-2xl
                                      bg-emerald-100
                                      text-emerald-700
                                      font-medium
                                  "
                              >
                                  Open Submissions
                              </Link>
                          )}

                          <div className="mt-4">
                              <ShareButton
                                  path={`/competitions/${competition.slug}`}
                                  title={competition.title}
                                  text={competition.description}
                              />
                          </div>

                          <RegisterCompetitionButton
                              competitionId={competition.id}
                          />

                      </div>

                  </div>

              </div>

          </div>

      </div>
  );
}
