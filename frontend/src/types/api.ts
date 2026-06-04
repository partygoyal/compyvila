export type CompetitionCategory = {
    name?: string;
};

export type Competition = {
    id: number;
    title: string;
    slug: string;
    description?: string;
    banner?: string | null;
    entry_fee?: string | number;
    submission_type?: string;
    status?: string;
    total_rounds?: number;
    allow_public_voting?: boolean;
    category?: CompetitionCategory | null;
    rounds?: CompetitionRound[];
};

export type CompetitionRound = {
    id: number;
    title: string;
    round_number?: number;
};

export type Registration = {
    id: number;
    competition_id: number;
    registration_number?: string;
    status?: string;
    competition?: Competition;
    current_round?: CompetitionRound | null;
    current_round_id?: number | null;
    current_round_number?: number | null;
    progress_status?: string;
    progress_message?: string;
    can_submit?: boolean;
};

export type Submission = {
    id: number;
    title?: string | null;
    description?: string | null;
    file_path?: string | null;
    file_type?: string;
    thumbnail?: string | null;
    status?: string;
    competition?: Competition;
    round?: CompetitionRound;
    user?: User;
    reviews?: Review[];
};

export type Review = {
    id: number;
    creativity?: number;
    presentation?: number;
    quality?: number;
    performance?: number;
    total_score?: number;
    comments?: string | null;
    judge?: User;
};

export type Certificate = {
    id: number;
    certificate_number?: string;
    type?: string;
    issued_at?: string;
    competition?: Competition;
};

export type UserProfile = {
    country_code?: string | null;
    mobile?: string | null;
    date_of_birth?: string | null;
    gender?: string | null;
    country?: string | null;
    state?: string | null;
    city?: string | null;
    address?: string | null;
    bio?: string | null;
    instagram?: string | null;
    facebook?: string | null;
    youtube?: string | null;
    website?: string | null;
};

export type User = {
    id: number;
    name: string;
    email?: string;
    roles?: string[];
    profile?: UserProfile | null;
};

export type DashboardNotification = {
    id: string;
    type: string;
    title: string;
    message: string;
    url?: string | null;
    data?: Record<string, unknown>;
    read_at?: string | null;
    created_at?: string;
};

export type LeaderboardItem = {
    rank: number;
    participant: string;
    submission: string;
    score: number | string;
};

export type Winner = {
    id: number;
    position: string | number;
    participant?: User;
};

export type JudgeAssignment = {
    id: number;
    competition_id: number;
    competition_round_id: number;
    competition?: Competition;
    round?: CompetitionRound;
};
