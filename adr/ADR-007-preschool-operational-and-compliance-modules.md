# ADR-007: Preschool Operational Extensions & Regulatory Compliance Suite

**Status:** Accepted

## [ADR-007-CONTEXT] Context

Following the core SIS migration (ADR-006), Malaysian Islamic preschool operations require specialized workflows that standard higher-ed or K-12 management platforms lack:
1. **WhatsApp Broadcast Communications**: Standard email is ineffective for preschool parents in Malaysia; announcements must instantly format for WhatsApp group chats with one click.
2. **Morning Health Screening (HFMD & Fever)**: Infectious disease prevention (Penyakit Tangan Kaki Mulut / HFMD) requires a 10-second gate screening workflow before children enter classrooms.
3. **RPH Digital Lesson Planning (KSPK)**: Preschool teachers require weekly themed planning (Rancangan Pengajaran Harian) aligned with National Preschool Curriculum domains.
4. **LHDN Tax Relief Invoicing & Receipts**: Parents require official digital receipts under LHDN Section 46(1)(r) (tax relief up to RM3,000 for registered kindergarten fees).
5. **JKM Incident & Injury Compliance**: Jabatan Kebajikan Masyarakat licensing mandates an immutable digital logbook for any playground scrape, fall, or minor injury.

## [ADR-007-DECISION] Decision

We build and integrate these 5 modular engines directly into the Filament `App` panel:

1. **Announcement & WhatsApp Broadcaster**:
   - `Announcement` model with automatic WhatsApp text markdown generator (`getFormattedWhatsAppTextAttribute`) and direct `https://wa.me/?text=...` deep-link action.
2. **Gate Health Screening Module**:
   - `HealthScreening` model capturing temperature, HFMD symptom checkboxes (*Bintik/Ruam, Mata Merah, Batuk*), and automatic quarantine flagging.
3. **Digital RPH Lesson Planner**:
   - `LessonPlan` model with week-by-week theme tracking, KSPK learning objectives, induction sets, craft materials, and reflection notes.
4. **Fee Management & LHDN Receipt Generator**:
   - `FeeInvoice` model paired with `FeeReceiptPdfService` to stream computerized receipts compliant with Malaysian tax regulations.
5. **JKM Incident & First Aid Logbook**:
   - `IncidentLog` model documenting injury details, initial first aid administered, teacher witnesses, and parent notification timestamps.

## [ADR-007-CONSEQUENCES] Consequences

- Complete compliance readiness for JKM and KPM licensing audits.
- Eliminates manual typing across WhatsApp groups and paper logbooks.
- All models remain multi-tenant scoped (`school_id`) and integrate with the existing database seeders and Filament panels.
