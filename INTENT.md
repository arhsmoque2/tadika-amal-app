# Intent

## [INT-PURPOSE] Product Purpose

> Give Islamic kindergartens a structured digital platform to run their school operations — without prescribing how they should run them.

Tadika Amal Apps is a school operations platform. It provides the containers, tools, and infrastructure that a school fills with its own content, workflow, and logic. The platform does not know what subjects a school teaches, how it structures its Iqra' assessment, or what fields matter for a student profile. That is the school's domain. The platform's job is to make sure whatever the school defines is stored, structured, accessible, and queryable — replacing paper, scattered Excel sheets, and WhatsApp threads — without replacing the school's judgment about its own curriculum or operations.

## [INT-PRIMARY-ACTOR] Primary Actor

> The primary actor is a class teacher who needs a digital workspace that mirrors their existing daily routine — not a new system that dictates a different one.

Teachers currently work with paper registers, physical student folders, and handwritten lesson plans. The platform replaces the medium, not the method. A teacher who ticks attendance on paper should tick attendance on a screen. A teacher who records Iqra' progress on a card should record it in a profile field they or their admin defined. The transition cost must be minimal.

## [INT-SECONDARY-ACTORS] Secondary Actors and Participants

> Secondary actors configure the platform, receive its outputs, or verify that it delivers what it claims.

- **Admin/Headmaster**: sets up the school's structure — class cohorts, student profiles, timetable templates — and reviews records across the school.
- **Parent/Guardian**: a future actor (v2 and beyond). Out of scope for v1.
- **Operator (school owner/agent)**: configures the platform instance and accepts or rejects capability claims.
- **Cloud storage service**: stores uploaded files — photos, documents — referenced by the platform. A dependency, not a feature.

Human actors and external services must remain distinguishable. An external service is a participant or dependency, not a substitute for a human decision.

## [INT-OUTCOME] Desired Outcome

> A school runs its daily operations inside the platform instead of on paper — and gains the ability to query and review any record, from any date, in seconds.

The platform's value is not the fields it ships with. It is the accumulation, structure, and queryability of whatever the school chooses to record inside it. A school that has been using the platform for one term should be able to answer any question about any student's attendance or progress without opening a single folder.

## [INT-PRINCIPLES] Decision Principles

> The platform provides structure and tools. The school provides content and workflow. These two responsibilities must never be mixed.

- **Platform, not syllabus.** The app does not know what subjects are taught, what Iqra' Jilid looks like, or what a valid lesson plan contains. The school defines all of that.
- **Blank Word document, not preset PDF.** Ship sensible default fields as a starting point, but every field, card, column, and section must be extensible or replaceable by the school.
- **Automate only what is unambiguous.** Today's date, calendar sync, attendance counters, and cumulative records can be automated. Curriculum logic, grading standards, and workflow sequence cannot.
- **Same flow, no paper.** The goal is to move the school's existing operations onto a structured digital surface — not to redesign how the school operates.
- **Do not build a surface without a tool need.** Every UI element must replace a real paper or manual step that teachers currently perform. Decorative or aspirational features are out of scope.
- **Prefer an explicit empty state over a hidden absence.** A blank field the teacher has not yet filled is more honest than a field the platform assumed.

## [INT-BOUNDARY] Product Boundary

> Tadika Amal Apps is a school operations platform, not a curriculum system, learning management system, or communication platform.

In scope for v1: student profile builder with extensible fields, timetable builder with calendar sync, daily attendance register, and configurable assessment and progress records per student — all queryable and reportable by the teacher.

Out of scope for v1: parent-facing portal, WhatsApp alerts, fee billing and payment, notice board, and any feature whose primary actor is not a teacher or admin within the school.

Out of scope permanently (separate platform concern): curriculum design, grading rubrics, national assessment standards, and any logic that tells a school how it should educate.
