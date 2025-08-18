# Design Notes: Courts × Find Partners

## Purpose
- Align on how Courts (venue management) and Find Partners (open sessions) integrate.
- Capture agreed decisions, open questions, and next steps. No code executed without explicit approval.

## Current Overview
- Courts: manage lapangan (name, address, lat/lng, rate), list + map with radius filter.
- Find Partners: show requests to find partners near a location; planned shift to booking-centric sessions.

## Agreed Decisions
- Map radius default: 5–10 km (start at 5 km; adjustable).
- Slot durations: standardized options 60 / 90 / 120 minutes.
- Confirmation at venue: QR code check-in for reliability.
- Desired size: default 8; host counts as 1 occupied slot automatically.
- Flow: user selects a court from the map → creates a booking (time + desired size) → session appears on Find Partners for others to join.
 - Open-to-join: only enabled for bookings created from the map flow (default false otherwise).
 - Single marker per venue: use unique Google `place_id`; a marker opens venue sheet listing details and sessions.

## Owner Verification (clarification)
“Verifikasi owner” = mekanisme agar pemilik venue resmi dapat mengklaim listing lapangan dan mengelolanya (tarif, slot, ketersediaan), sekaligus mencegah penyalahgunaan.
- Why: memastikan data akurat, mencegah pihak tak berwenang mengubah info.
- Possible methods (phased):
  - Proof upload (dokumen usaha), email domain resmi, atau micro-charge verifikasi (opsional), plus review manual.
  - Onboarding ringan dulu: “Request to claim” → admin approval.
- Outcome: `owner_user_id` pada court; fitur manajemen hanya tersedia bagi owner terverifikasi.

## Data Model Changes (pending approval)
- bookings: add `desired_size` (int, default 8) and flag `open_to_join` (bool).
- booking_participants: (`booking_id`, `user_id`, `status` requested|accepted|declined), unique pair.
- courts: `google_place_id` (string), `total_courts` (int), `owner_user_id` (nullable).
- Derived fields: `confirmed_count`, `remaining = max(desired_size - (1 + accepted_count), 0)`.

## Map & Marker Strategy
- Show Google Places markers for nearby “badminton court” plus DB courts (different icons).
- Click Google-only marker: allow booking (persist minimal court snapshot + `google_place_id`).
- Click DB court marker: show richer details (rate, slot availability, sessions).
- On Find Partners: render session markers (from bookings with `open_to_join = true`), include host and remaining slots; keep PartnerRequest for legacy/transition if needed.

## UX Flow (high level)
- Select court on map → set date, slot duration, desired size → create booking (host = 1 seat) → session listed on Find Partners → others join → host approves → QR check-in at venue.

## Open Questions
- Admin moderation for claim flow timeline and SLA?
- Map threshold for merging duplicate Google entries (same venue, multiple pins)?
- Rate limits/quotas for Google Places in prod; caching strategy.

## Next Steps (awaiting approval)
- Finalize schema changes; generate migrations.
- Update booking create UI (desired size, open-to-join) and overlays on both maps.
- Implement participants join/approve + QR check-in scaffolding.

Note: Per request, no code will be executed until you approve.
