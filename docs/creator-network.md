# Creator Network

Public page: `/creator-network`. Linked from the homepage, media kit, footer, and sitemap. Responsive CSS graphics and copy introduce paid campaigns, brand matches, and long-term associations without guaranteeing selection or earnings.

Application data: name, email, optional phone, primary platform/profile URL, follower count, location, content languages, topic and format preferences, optional indicative rate and message. Required consent records the timestamp for Compyvila contact and relevant creator-profile sharing with prospective brands. The privacy notice explains this use. Applications are private; there is no public creator directory.

Leads are stored in `creator_applications`. Admin: `/admin/creators`, also linked from the admin tabs and overview count. Search by name/email/platform, filter by review status, and paginate 20 per page. Open a lead for the complete application; update New / Reviewing / Contacted / Approved / Not a fit and private notes. Delete applications to fulfil retention/deletion requests. The deletion form asks for confirmation. No external messages or emails are sent automatically.

Public submission uses CSRF protection, five-attempts-per-minute throttling, a honeypot, field validation, and a unique email/profile fingerprint. Duplicate submissions show a useful error and retain form input. Profile URLs accept only HTTP/HTTPS; all displayed application data is escaped. Public payloads cannot set lead status or internal notes. Admin routes require the existing editorial session and return private no-store responses.

Migration: `2026_10_18_000000_create_creator_applications_table.php`. Existing publication records remain intact. Tests cover saving, consent, invalid profiles, spam, duplicate prevention, authorization, filtering, status/notes updates, escaping, and deletion. Local browser QA tests the real application-to-admin flow without creating production test leads.
