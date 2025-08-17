# Badminton Connect

Aplikasi komunitas bulutangkis untuk menemukan partner bermain, memesan lapangan, dan mengelola turnamen/pertandingan. Dibangun dengan Laravel 12, Livewire 3, dan Sanctum.

## Fitur

- Courts: kelola dan lihat daftar lapangan (nama, lokasi, tarif per jam).
- Map & radius: peta Google di halaman Courts dengan marker dan filter radius.
- Bookings: buat pemesanan lapangan dengan waktu mulai/akhir dan harga opsional.
- Tournaments: kelola turnamen dengan tanggal mulai/akhir, status, dan peserta.
- Matches: jadwalkan pertandingan terkait penyelenggara, turnamen, dan lapangan.
- Partner Finder: posting permintaan mencari partner dan lihat daftar permintaan terbuka.
- Cari Partner di peta: tampilkan request terbuka di peta + filter radius.
- Auth: login/register dan token API via Laravel Sanctum.

## Stack

- Backend: PHP 8.2, Laravel 12, Sanctum, Breeze, Livewire 3 + Volt.
- Frontend: Blade, Tailwind CSS, Vite.
- Database: SQLite (default `.env`), migrasi terkelola.

## Menjalankan Secara Lokal

1. Instal dependensi PHP dan JS:
   - `composer install`
   - `npm install`
2. Salin file env dan generate key (jika belum):
   - `cp .env.example .env`
   - `php artisan key:generate`
3. Gunakan SQLite (sudah disetel di `.env`) dan buat file DB:
   - `mkdir -p database && touch database/database.sqlite`
4. Jalankan migrasi:
   - `php artisan migrate`
5. Set kunci Google Maps (untuk fitur peta + autocomplete):
   - Tambahkan `GOOGLE_MAPS_API_KEY=...` ke `.env`
6. Jalankan aplikasi (mode dev terpadu):
   - `composer run dev`

Aplikasi web tersedia di `http://localhost:8000` (default artisan serve), Vite di `http://localhost:5173`.

## Maps & Geolocation

- API yang perlu diaktifkan di Google Cloud (APIs & Services → Library):
  - Maps JavaScript API, Places API (wajib), Geocoding API (opsional untuk reverse/forward geocoding server-side).
- Buat API key (Credentials → Create credentials → API key) dan batasi:
  - Application restrictions: Websites/HTTP referrers (`http://localhost/*`, `http://127.0.0.1/*`, `http://localhost:8000/*`, `http://localhost:5173/*`, lalu domain produksi).
  - API restrictions: hanya Maps JavaScript API dan Places API (tambahkan Geocoding jika dipakai).
- Fitur terkait:
  - `/courts`: peta + “Gunakan lokasiku”, input radius, pencarian tempat (Places Autocomplete).
  - `/courts/create`: form buat lapangan dengan Places Autocomplete yang mengisi `address`, `latitude`, `longitude`, `place_id` otomatis.
  - `/partners/find`: peta request partner + “Gunakan lokasiku”, radius, dan Autocomplete.

## API Cepat (v1)

Base path: `/api/v1`

- Register:
  - `curl -X POST http://localhost:8000/api/v1/register \
    -H 'Content-Type: application/json' \
    -d '{"name":"Alice","email":"alice@example.com","password":"secret123","password_confirmation":"secret123","device_name":"cli"}'`
- Login:
  - `curl -X POST http://localhost:8000/api/v1/login \
    -H 'Content-Type: application/json' \
    -d '{"email":"alice@example.com","password":"secret123","device_name":"cli"}'`
  - Respon berisi `token` Bearer.
- Contoh akses endpoint (Courts):
  - `curl http://localhost:8000/api/v1/courts`
  - Buat court: `curl -X POST http://localhost:8000/api/v1/courts -H 'Authorization: Bearer <TOKEN>' -H 'Content-Type: application/json' -d '{"name":"Court A","location":"Jakarta"}'`

- Filter jarak (km) untuk courts/partner-requests:
  - `GET /api/v1/courts?lat=-6.2&lng=106.82&radius=10`
  - `GET /api/v1/partner-requests?lat=-6.2&lng=106.82&radius=15`

Endpoint lain: `bookings`, `tournaments`, `matches`, `partner-requests`. Lihat definisi rute di `routes/api.php`.

## Rute Web

- `/courts` — daftar lapangan
- `/courts/create` — buat lapangan (login diperlukan) dengan Places Autocomplete
- `/bookings/create` — form pemesanan (login diperlukan)
- `/tournaments` — daftar turnamen
- `/partners/find` — cari partner bermain
- `/dashboard`, `/profile` — area pengguna (Breeze)

## Postman

- Koleksi tersedia di `docs/postman/badminton-connect.postman_collection.json` (sudah termasuk login yang otomatis menyimpan token ke variable koleksi).

## Troubleshooting

- Peta blank: pastikan `GOOGLE_MAPS_API_KEY` valid, API yang diperlukan sudah di-enable, dan browser mengizinkan geolocation.
- Autocomplete tidak muncul: pastikan script memuat `libraries=places` dan key punya akses ke Places API.
- Error rate/kuota: batasi panggilan dan aktifkan kuota/alerts di Google Cloud.

## Lisensi

Proyek ini menggunakan lisensi MIT.
