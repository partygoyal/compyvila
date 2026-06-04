"use client";

import { API_URL, authHeaders } from "@/src/lib/api";
import { countryCodes } from "@/src/lib/countryCodes";
import type { User } from "@/src/types/api";
import { useEffect, useState } from "react";
import toast from "react-hot-toast";

type ProfileForm = {
    country_code: string;
    mobile: string;
    date_of_birth: string;
    age: string;
    gender: string;
    country: string;
    state: string;
    city: string;
    address: string;
    bio: string;
    instagram: string;
    facebook: string;
    youtube: string;
    website: string;
};

function calculateAge(dateOfBirth: string) {
    if (!dateOfBirth) {
        return "";
    }

    const birthDate = new Date(dateOfBirth);

    if (Number.isNaN(birthDate.getTime())) {
        return "";
    }

    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (
        monthDiff < 0 ||
        (
            monthDiff === 0 &&
            today.getDate() < birthDate.getDate()
        )
    ) {
        age -= 1;
    }

    return age.toString();
}

export default function ProfilePage() {
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    const [form, setForm] = useState<ProfileForm>({
        country_code: "+91",
        mobile: "",
        date_of_birth: "",
        age: "",
        gender: "",

        country: "India",
        state: "",
        city: "",
        address: "",

        bio: "",

        instagram: "",
        facebook: "",
        youtube: "",
        website: "",
    });

    const [user, setUser] = useState<User | null>(null);

    useEffect(() => {
        fetch(`${API_URL}/me`, {
            headers: authHeaders(),
        })
            .then((res) => res.json())
            .then((data) => {

                setUser(data);

                if (data.profile) {
                    setForm({
                        country_code:
                            data.profile.country_code || "+91",
                        mobile: data.profile.mobile || "",
                        date_of_birth:
                            data.profile.date_of_birth || "",
                        age: data.profile.age?.toString() || "",
                        gender: data.profile.gender || "",

                        country:
                            data.profile.country || "India",

                        state:
                            data.profile.state || "",

                        city:
                            data.profile.city || "",

                        address:
                            data.profile.address || "",

                        bio:
                            data.profile.bio || "",

                        instagram:
                            data.profile.instagram || "",

                        facebook:
                            data.profile.facebook || "",

                        youtube:
                            data.profile.youtube || "",

                        website:
                            data.profile.website || "",
                    });
                }

                setLoading(false);
            });
    }, []);

    const updateField = (
        key: keyof ProfileForm,
        value: string
    ) => {
        setForm((prev) => ({
            ...prev,
            [key]: value,
            ...(key === "date_of_birth"
                ? {
                    age: calculateAge(value),
                }
                : {}),
        }));
    };

    const saveProfile = async () => {
        setSaving(true);

        const response = await fetch(
            `${API_URL}/profile/update`,
            {
                method: "POST",
                headers: {
                    ...authHeaders(),
                    "Content-Type":
                        "application/json",
                },
                body: JSON.stringify(form),
            }
        );

        const data = await response.json();

        setSaving(false);

        if (data.success) {
            setForm((prev) => ({
                ...prev,
                date_of_birth:
                    data.profile?.date_of_birth ||
                    prev.date_of_birth,
                age:
                    data.profile?.age?.toString() ||
                    calculateAge(prev.date_of_birth),
            }));

            toast.success("Profile updated successfully.");
            return;
        }

        toast.error(
            data.message ||
            "Failed to update profile."
        );
    };

    if (loading) {
        return (
            <div className="bg-white rounded-3xl border border-slate-100 p-8">
                Loading profile...
            </div>
        );
    }

    return (
        <div className="space-y-8">

            {/* Header */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <div className="flex items-center gap-6">

                    <div className="
                        w-24 h-24
                        rounded-full
                        bg-gradient-to-r
                        from-indigo-400
                        to-violet-400
                        flex
                        items-center
                        justify-center
                        text-white
                        text-4xl
                        font-bold
                    ">
                        {user?.name?.charAt(0)}
                    </div>

                    <div>

                        <p className="text-sm text-slate-500">
                            Participant Profile
                        </p>

                        <h1 className="text-4xl font-bold text-slate-800">
                            {user?.name}
                        </h1>

                        <p className="text-slate-500">
                            {user?.email}
                        </p>

                    </div>

                </div>

            </div>

            {/* Personal Information */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <h2 className="text-xl font-semibold mb-6">
                    Personal Information
                </h2>

                <div className="grid md:grid-cols-2 gap-6">

                    <div>
                        <label className="text-sm text-slate-500">
                            Mobile Number
                        </label>

                        <div className="mt-2 grid grid-cols-[130px_1fr] gap-3">
                            <select
                                value={form.country_code}
                                onChange={(e) =>
                                    updateField(
                                        "country_code",
                                        e.target.value
                                    )
                                }
                                className="border border-slate-200 rounded-xl p-3"
                            >
                                {countryCodes.map((country) => (
                                    <option
                                        key={country.code}
                                        value={country.code}
                                    >
                                        {country.code}
                                    </option>
                                ))}
                            </select>

                            <input
                                type="text"
                                value={form.mobile}
                                onChange={(e) =>
                                    updateField(
                                        "mobile",
                                        e.target.value
                                    )
                                }
                                className="w-full border border-slate-200 rounded-xl p-3"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="text-sm text-slate-500">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            value={form.date_of_birth}
                            onChange={(e) =>
                                updateField(
                                    "date_of_birth",
                                    e.target.value
                                )
                            }
                            className="w-full mt-2 border border-slate-200 rounded-xl p-3"
                        />
                    </div>

                    <div>
                        <label className="text-sm text-slate-500">
                            Age
                        </label>

                        <input
                            type="text"
                            value={form.age}
                            disabled
                            className="w-full mt-2 border border-slate-200 rounded-xl p-3 bg-slate-50"
                        />
                    </div>

                    <div>
                        <label className="text-sm text-slate-500">
                            Gender
                        </label>

                        <select
                            value={form.gender}
                            onChange={(e) =>
                                updateField(
                                    "gender",
                                    e.target.value
                                )
                            }
                            className="w-full mt-2 border border-slate-200 rounded-xl p-3"
                        >
                            <option value="">
                                Select Gender
                            </option>

                            <option value="male">
                                Male
                            </option>

                            <option value="female">
                                Female
                            </option>

                            <option value="other">
                                Other
                            </option>

                        </select>
                    </div>

                </div>

            </div>

            {/* Location */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <h2 className="text-xl font-semibold mb-6">
                    Location
                </h2>

                <div className="grid md:grid-cols-2 gap-6">

                    <input
                        type="text"
                        placeholder="Country"
                        value={form.country}
                        onChange={(e) =>
                            updateField(
                                "country",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                    <input
                        type="text"
                        placeholder="State"
                        value={form.state}
                        onChange={(e) =>
                            updateField(
                                "state",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                    <input
                        type="text"
                        placeholder="City"
                        value={form.city}
                        onChange={(e) =>
                            updateField(
                                "city",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                    <textarea
                        placeholder="Full Address"
                        value={form.address}
                        onChange={(e) =>
                            updateField(
                                "address",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3 md:col-span-2"
                        rows={3}
                    />

                </div>

            </div>

            {/* About */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <h2 className="text-xl font-semibold mb-6">
                    About Me
                </h2>

                <textarea
                    value={form.bio}
                    onChange={(e) =>
                        updateField(
                            "bio",
                            e.target.value
                        )
                    }
                    rows={5}
                    placeholder="Tell us about yourself..."
                    className="w-full border border-slate-200 rounded-xl p-3"
                />

            </div>

            {/* Social Links */}

            <div className="bg-white rounded-3xl border border-slate-100 p-8">

                <h2 className="text-xl font-semibold mb-6">
                    Social Links
                </h2>

                <div className="grid md:grid-cols-2 gap-6">

                    <input
                        type="url"
                        placeholder="Instagram URL"
                        value={form.instagram}
                        onChange={(e) =>
                            updateField(
                                "instagram",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                    <input
                        type="url"
                        placeholder="Facebook URL"
                        value={form.facebook}
                        onChange={(e) =>
                            updateField(
                                "facebook",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                    <input
                        type="url"
                        placeholder="YouTube URL"
                        value={form.youtube}
                        onChange={(e) =>
                            updateField(
                                "youtube",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                    <input
                        type="url"
                        placeholder="Website URL"
                        value={form.website}
                        onChange={(e) =>
                            updateField(
                                "website",
                                e.target.value
                            )
                        }
                        className="border border-slate-200 rounded-xl p-3"
                    />

                </div>

            </div>

            {/* Save */}

            <div className="flex justify-end">

                <button
                    onClick={saveProfile}
                    disabled={saving}
                    className="
                        px-8
                        py-4
                        rounded-2xl
                        text-white
                        font-medium
                        bg-gradient-to-r
                        from-indigo-400
                        to-violet-400
                    "
                >
                    {saving
                        ? "Saving..."
                        : "Save Profile"}
                </button>

            </div>

        </div>
    );
}
