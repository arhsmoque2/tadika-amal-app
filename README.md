<h1 align="center">🌱 Tadika Amal Apps</h1>

<p align="center">
  <strong>Comprehensive Islamic Kindergarten & Preschool Management System</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/status-proposal_%26_architecture_stage-orange.svg" alt="Architecture Stage" />
  <img src="https://img.shields.io/badge/stack-laravel_12_%2B_filament_v4_%2F_vue_3-blue.svg" alt="Laravel Stack" />
  <img src="https://img.shields.io/badge/license-proprietary-lightgrey.svg" alt="License" />
</p>

---

## 📖 About Tadika Amal Apps

**Tadika Amal Apps** is an integrated management and communication platform tailored specifically for Islamic kindergartens, preschools, and daycare centers in Malaysia (e.g. Tadika Amal, Tadika PASTI, Little Caliphs-style models).

The platform bridges **School Administration, Teachers, and Parents** across 6 key pillars:
1. **👶 Student & Cohort Enrollment**: Class management by age group (4, 5, 6 Tahun), health profiles, authorized guardian pickup lists.
2. **📖 Huffaz & Iqra' Tracker**: Daily progress tracking for Iqra' (Jilid 1–6), Quran recitation, Surah Hafazan (Juz Amma), and practical Solat.
3. **⏱️ Morning Check-In & Health Kiosk**: Rapid arrival QR scan, body temperature logs, health status, and automated parent alerts.
4. **💳 Automated Fee Billing & FPX**: Monthly tuition fee invoicing, automated WhatsApp reminders, online FPX payment, and PDF receipts.
5. **📸 Daily Activity Feed & Child Diary**: Photo/video sharing of classroom activities, meal nutrition logs, nap times, and teacher remarks.
6. **📢 Digital Notice Board & Alerts**: Calendar of events, school holidays, and official WhatsApp broadcast announcements.

---

## 🎨 Interactive Architecture Showcase & UI Preview

We have built a **live interactive visual switcher** allowing you to preview and compare:
- **Option A**: Filament v4 Multi-Panel (Admin & Teacher backoffice)
- **Option B**: Vue 3 / Inertia.js Mobile PWA (Fluid Parent Portal)
- **Option C**: Hybrid Architecture (Recommended)

To launch the preview on your computer:
```powershell
Start-Process "preview\index.html"
```

---

## 🚀 Architectural Options to Decide

See [`PROPOSAL.md`](PROPOSAL.md) for the complete comparative breakdown:

| Option | Stack | Ideal For |
| :--- | :--- | :--- |
| **Option A** | **Laravel 12 + Filament v4 (Multi-Panel)** | Rapid delivery, 100% PHP, heavy administrative data management. |
| **Option B** | **Laravel 12 + Inertia.js v2 + Vue 3 SPA** | Ultimate custom UI design, fluid animations, pure mobile app feel. |
| **Option C (Recommended)** | **Hybrid: Filament v4 (Backoffice) + Vue 3 (Parent Portal)** | High-efficiency admin tools + 5-star mobile experience for parents. |

---

## 📁 Repository Structure

```text
tadika-amal-app/
├── preview/                     # Interactive HTML/CSS/JS prototype showcase
│   ├── index.html               # Multi-stack visual previewer
│   ├── styles.css               # Tailored styling for Filament & Vue mockups
│   └── app.js                   # Interactive switcher logic
├── PROPOSAL.md                  # Comprehensive architectural proposal & comparison
├── AGENTS.md                    # Agent cold-start guide and runtime boundaries
├── HANDOFF.md                   # Handoff summary and decision receipt
└── README.md                    # Canonical project README
```

---

<p align="center">
  Built for modern, transparent, and joyful Islamic early childhood education.
</p>
