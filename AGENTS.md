# CLAUDE.md

Behavioral guidelines to reduce common LLM coding mistakes. Merge with project-specific instructions as needed.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

> **Important:** When running in development mode (`pnpm dev` is active), do NOT run `pnpm build` after making changes. The Vite dev server handles hot-reloading automatically. Only run `pnpm build` if explicitly asked to prepare for production.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.

---

## Project Overview: LSP UNTAN

**LSP UNTAN** (Lembaga Sertifikasi Profesi Universitas Tanjungpura) — sistem manajemen sertifikasi berbasis PWA, proyek skripsi.

### Stack
- **Backend:** Laravel 11, PHP 8.x
- **Frontend:** Vue 3 + Inertia.js v2 (SPA-like monolith, no API)
- **Styling:** Tailwind CSS v4, Tabler Icons, Lucide
- **Database:** MySQL (`skripsi_pwa`)
- **Build:** Vite 6, pnpm
- **PWA:** Service Worker, manifest, offline fallback
- **Notifikasi:** Firebase Cloud Messaging (FCM)
- **Role/Permission:** Spatie laravel-permission — 3 roles: `admin`, `asesor`, `asesi`

### Struktur Direktori
```
app/
├── Http/Controllers/{Admin,Asesi,Auth,...}
├── Models/          (11 models: User, Student, Asesi, Asesor, Skema, Sertification, Asesmen, Sertifikat, News, Asesifile, NotificationLog)
├── Enums/           (StatusSertifikasi, StatusBerkasAdministrasi, StatusFinalAsesi)
├── Policies/        (10 policies)
├── Notifications/   (12 notifikasi FCM)
├── Exports/         (Excel/PDF)
├── Services/        (FakeMessagingService)
resources/
├── js/
│   ├── Pages/{Admin,Asesi,Auth,Public}/
│   ├── Layouts/     (AdminLayout, AsesiLayout, GuestLayout)
│   └── Composables/ (useFormat, useNotification)
routes/
├── web.php          (~194 routes — admin/asesi)
├── auth.php         (Laravel Breeze auth)
```

### Modul Utama
1. **Skema Sertifikasi** — CRUD skema, upload template APL-1/APL-2/Asesmen
2. **Jadwal Sertifikasi** — kelola pendaftaran, biaya, tanggal asesmen
3. **Asesor** — CRUD, penugasan ke skema & sertifikasi
4. **Pendaftaran & Verifikasi (Asesi)** — upload berkas, verifikasi admin, status kompetensi final
5. **Asesmen** — tugas + submit file
6. **Pengumuman** — posting & lihat per sertifikasi
7. **Sertifikat** — upload admin, verifikasi publik via `/verify-certificate`
8. **Pembayaran** — Midtrans Snap + webhook
9. **Dashboard** — ApexCharts statistik
10. **Notifikasi Push** — FCM, tersimpan di `notification_logs`

### Alur Bisnis
1. Asesi daftar → isi profil → daftar sertifikasi → upload berkas
2. Admin verifikasi berkas (lengkap/revisi/pending)
3. Asesor ditugaskan → lakukan asesmen
4. Admin tentukan hasil final (kompeten/belum_kompeten/diskualifikasi)
5. Admin upload sertifikat untuk yang kompeten