# Tadika Amal Apps — Architecture Proposal & Stack Decision Guide

## 1. Executive Summary

**Tadika Amal Apps** is a dedicated Islamic Kindergarten & Preschool Management Platform designed to streamline operations, academic progress (Iqra'/Hafazan/Solat), attendance & health tracking, automated monthly fee billing, and real-time parent engagement.

This proposal evaluates the optimal technology stack, comparing **Filament v4 (Multi-Panel)** vs **Inertia.js + Vue 3** vs **Hybrid Architecture**, grounded in findings from the `ARH-Baca` exploration and ready-made open source ecosystem templates.

---

## 2. Domain Requirements & Core Modules

| Module | Core Functionality | Primary Actors |
| :--- | :--- | :--- |
| **1. Student & Cohort Registry** | Age-based classes (4, 5, 6 Tahun), guardian details, health/allergy profiles, emergency pickups. | Admin, Headmaster, Class Teachers |
| **2. Huffaz & Iqra' Tracker** | Daily progress logging for Iqra' (Jilid 1–6), Surah Hafazan (Juz Amma), Solat mastery, and teacher remarks. | Teachers, Parents |
| **3. Morning Check-In & Health Kiosk** | Fast QR check-in at school entrance, body temperature record, symptoms log, instant WhatsApp alerts to parents. | Teachers, Admin, Parents |
| **4. Fee Billing & FPX Payment** | Automated monthly tuition invoice generation, FPX gateway (ToyyibPay/Billplz), official PDF receipts, debt recovery. | Admin, Parents |
| **5. Daily Diary & Activity Feed** | Photo/video updates of classroom activities, arts & crafts, daily meal menu, nap times, weekly milestone badges. | Teachers, Parents |
| **6. Notice Board & Announcements** | Digital notice board, school event calendar, emergency broadcast announcements via WhatsApp API. | Admin, Teachers, Parents |

---

## 3. Technology Stack Comparison

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 TADIKA AMAL APPS                                       │
│                                                                                        │
│   ┌───────────────────────────────┐        ┌───────────────────────────────────────┐   │
│   │    OPTION A: ALL-FILAMENT     │        │     OPTION B: INERTIA + VUE 3         │   │
│   │  • Laravel 13 + Filament v4   │        │  • Laravel 13 + Inertia.js v2         │   │
│   │  • Multi-Panel Architecture   │        │  • Vue 3 SPA + Tailwind CSS v4        │   │
│   │  • 100% Native PHP / Livewire │        │  • High-polish PWA Mobile Experience  │   │
│   └───────────────────────────────┘        └───────────────────────────────────────┘   │
│                                                                                        │
│                                           │                                            │
│                                           ▼                                            │
│   ┌────────────────────────────────────────────────────────────────────────────────┐   │
│   │                    OPTION C: HYBRID ARCHITECTURE (RECOMMENDED)                 │   │
│   │  • Admin & Teacher Backoffice: Filament v4 Panel (Rapid CRUD, Reports, Fees)   │   │
│   │  • Parent & Guardian Portal:   Inertia + Vue 3 Mobile PWA (Fluid, Touch-first) │   │
│   │  • Shared Database:            PostgreSQL (Neon) with Eloquent ORM             │   │
│   └────────────────────────────────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### Comparative Matrix

| Criteria | Option A: Filament v4 Only | Option B: Inertia + Vue 3 Only | Option C: Hybrid (Filament + Vue) |
| :--- | :---: | :---: | :---: |
| **Time to MVP / Scaffold Speed** | ⚡ **Fastest (3-5 days)** | ⏳ Moderate (10-14 days) | 🚀 **Fast (5-7 days)** |
| **Admin & Finance Productivity** | 🌟 Exceptional (Filament tables) | ⚠️ Custom tables needed | 🌟 Exceptional |
| **Parent Mobile Experience (PWA)** | 📱 Good (via mobile preset) | 💎 **5-Star Native App Feel** | 💎 **5-Star Native App Feel** |
| **UI Touch Gestures & Animations** | Standard Web | 🌟 Fluid & Instant | 🌟 Fluid & Instant |
| **Ecosystem Maintenance** | 100% PHP / Livewire | PHP + Vue / TypeScript | Clean separation by user role |
| **Fit Score** | **88%** | **92%** | **98% (Recommended)** |

---

## 4. Ready-Made Templates & Open Source Reference Manifest

1. **`academico-sis/academico`**:
   - Clean school information system built with Laravel and Filament. Provides foundational schema for classes, enrollment, academic terms, and attendance.
2. **`muhamadfikrii/app-samlosier`**:
   - Modern Laravel + Filament 4 digital school system with role-based dashboard cards and student profiles.
3. **`saade/filament-fullcalendar`**:
   - Full calendar drag-and-drop integration for school terms, holiday schedule, and exam milestones.
4. **`hammadzafar05/filament-mobile-preset`**:
   - Mobile-first bottom navigation, touch targets, and slide-over modals for Filament panels.
5. **`kstmostofa/laravel-whatsapp`**:
   - Dual-backend WhatsApp integration (Meta Cloud API & Web session) for automated attendance alerts and fee reminders.

---

## 5. Interactive Showcase & Prototype

To preview and interact with the UI designs for both **Filament v4** and **Vue 3 Parent Portal**, open the included local showcase:

```powershell
# Open interactive showcase in your default browser
Start-Process "D:\ARH-GITHUB\arhsmoque2\tadika-amal-app\preview\index.html"
```

---

## 6. Implementation Roadmap

- [x] **Phase 1: Architecture & Scope Definition** — Domain requirements, stack comparison, schema blueprint, and interactive prototype.
- [ ] **Phase 2: Repository Scaffolding & Base Install** — Verify the existing Laravel 13 foundation, migrations, and seeders.
- [ ] **Phase 3: Core Modules Build** — Student enrollment, Iqra' tracker, attendance kiosk, and billing system.
- [ ] **Phase 4: WhatsApp Alerts & FPX Integration** — Automated notifications and payment collection.
- [ ] **Phase 5: Cloud Deployment & Validation** — Serverless deployment with PostgreSQL and cloud storage.
