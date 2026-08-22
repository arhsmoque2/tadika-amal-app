# Scenarios

> These scenarios describe what actors do inside the platform.
> They do not describe curriculum, subjects, or school-specific workflow.
> The platform does not know those things — the school does.
> Iqra', Hafazan, and Solat are examples of what a school might configure. They are not built-in features.

---

## [SCN-PROFILE-SETUP] Define the Student Profile Structure

> An admin defines what information the school wants to collect for each student — choosing from standard fields and adding any custom fields or card sections the school needs.

**Actor:** Admin

**Trigger:** First-time platform setup, or when the school decides to capture additional information.

**Preconditions:** Admin is authenticated with configuration permission.

**Event flow:**

1. Admin opens the student profile builder.
2. The platform shows a default profile template with globally recognised starting fields: Full Name, MyKid Number, Date of Birth, Gender, Home Address, Passport-sized Photo, Parent/Guardian Name, Relationship, Phone Number.
3. Admin reviews the default fields. Keeps what is needed. Removes what is not.
4. Admin adds custom fields the school requires — e.g., "Nama Panggilan", "Kumpulan Darah", "Sekolah Asal", or any other field the school tracks on paper today.
5. Admin groups fields into named card sections — e.g., "Maklumat Murid", "Maklumat Penjaga", "Kesihatan", "Kecemasan".
6. Admin saves the profile structure. It becomes the template for all student registrations in this school.

**Useful exit:** A student profile template exists that reflects the school's actual data needs — not a platform assumption.

**Failure/recovery:** Structure changes after students are registered update the template for future registrations. Existing student records are not silently mutated; added fields appear blank on existing profiles. Removed fields are archived, not deleted.

**Importance:** This is the foundation of the student record. Every other module references it.

---

## [SCN-STUDENT-REGISTER] Register a Student

> A teacher or admin creates a new student record by filling in the school's configured profile template.

**Actor:** Admin / Teacher

**Trigger:** A new student joins the school.

**Preconditions:** A student profile structure has been configured.

**Event flow:**

1. Admin opens the student registry and starts a new student record.
2. The platform presents the school's configured profile template.
3. Admin fills in the fields — required fields must be completed; optional fields can be left blank.
4. Admin uploads the student's passport-sized photo.
5. Admin assigns the student to a class cohort.
6. Admin saves. The student is now part of the class roster and accessible from attendance, assessment, and reporting.

**Useful exit:** Student appears in the class roster, ready for daily use.

**Failure/recovery:** Required fields are enforced before save. Duplicate MyKid numbers are flagged. Partial records can be saved as draft and completed later.

---

## [SCN-TIMETABLE-SETUP] Build the Class Timetable

> A teacher builds their own class timetable — defining time slots, naming subjects or sessions, and organising the school day as they actually run it.

**Actor:** Teacher / Admin

**Trigger:** Start of term, or when the school day structure changes.

**Preconditions:** Teacher is authenticated and assigned to a class.

**Event flow:**

1. Teacher opens the timetable builder for their class.
2. The platform provides a blank weekly grid — days across, time slots down — auto-synced to the current academic calendar.
3. The platform offers standard slot type labels as a starting point: Subject, Recess, Assembly, Free Play. Teacher uses these or ignores them.
4. Teacher fills in each slot: slot name (whatever they call it), start time, end time.
5. Teacher adds, removes, or renames slots freely. The platform does not validate slot names — the school decides what to call things.
6. Teacher saves. The timetable is stored and displayed day-by-day, auto-showing today's schedule when the teacher opens the app each morning.

**Useful exit:** Teacher has a timetable on the platform that reflects their actual school day. When they open the app, they see today's schedule without manual date entry.

**Failure/recovery:** Timetable can be edited at any time. Changes take effect from the next school day unless explicitly backdated by admin.

---

## [SCN-ATTENDANCE] Take Daily Attendance

> A teacher marks each student in their class as present or absent for today. Records accumulate automatically. No paper register needed.

**Actor:** Teacher

**Trigger:** Start of the school day, every school day.

**Preconditions:** Student roster exists for the class.

**Event flow:**

1. Teacher opens today's attendance view. The platform auto-populates today's date and the full class roster.
2. Teacher taps each student — **Hadir** or **Tidak Hadir**.
3. For absent students, teacher optionally notes a reason in a free-text field.
4. Teacher saves. The record is timestamped and stored.
5. Attendance accumulates day by day. No manual totalling. No paper filing.

**Useful exit:** Attendance for today is saved. It joins the running record for every student.

**Failure/recovery:** Attendance can be edited by the teacher until end of school hours. After that, only an admin override with a reason note is permitted. The platform records who made the correction and when.

---

## [SCN-ASSESSMENT-SETUP] Define an Assessment Structure

> A teacher or admin defines what they want to assess and track for students — naming the assessment area, the fields within it, and the input type for each field.

**Actor:** Teacher / Admin

**Trigger:** When the school wants to start tracking a type of student progress digitally.

**Preconditions:** Student roster exists.

**Event flow:**

1. Teacher opens the assessment builder.
2. Teacher creates a new assessment area and gives it a name — whatever the school calls it (e.g., "Bacaan Iqra'", "Hafazan", "Matematik", "Kemahiran Motor" — the platform does not care).
3. For each field within that assessment area, teacher defines:
   - **Field name**: whatever the teacher calls it.
   - **Field type**: Text note / Number / Status selector / Date / Checkbox.
   - For status selectors: teacher defines the options (e.g., "Lulus / Ulang / Maju" — or anything else).
4. Teacher saves the assessment structure. It is now available as a recording template for any session with any student.

**Useful exit:** An assessment template exists that reflects what the school actually tracks — not what the platform assumes they should track.

**Failure/recovery:** Assessment structures can be edited. New fields added to an existing structure appear blank on existing records. The platform never deletes historical records when a structure is changed.

---

## [SCN-ASSESSMENT-RECORD] Record a Student Assessment

> A teacher records a student's result or progress against a configured assessment structure during or after a session.

**Actor:** Teacher

**Trigger:** Teacher completes an assessment session with a student or group.

**Preconditions:** An assessment structure is configured. The student is on the class roster.

**Event flow:**

1. Teacher selects a student (or multiple students for a group result).
2. Teacher selects the assessment area.
3. The platform presents the teacher's configured fields for that assessment.
4. Teacher fills in the fields for this session.
5. Teacher saves. The record is timestamped and appended to the student's history for that assessment area.
6. Previous records are never overwritten. Each session creates a new entry.

**Useful exit:** A timestamped assessment entry exists for the student. History accumulates across sessions.

**Failure/recovery:** Teacher can edit the current session's entry before saving. After saving, corrections create a new entry with a correction note; the original is retained.

---

## [SCN-QUERY] Query and Review Any Record

> A teacher or admin pulls up any student's attendance, assessment history, or profile — for any date range — without searching through paper files.

**Actor:** Teacher (own class) / Admin (all classes)

**Trigger:** Any time — parent inquiry, weekly review, end of term, inspection preparation.

**Preconditions:** Records exist (attendance and/or assessment entries have been made).

**Event flow:**

1. Actor selects a student or the full class roster.
2. Actor selects what to view: Attendance / specific Assessment area / full profile.
3. Actor selects a date range (today / this week / this month / custom).
4. Platform returns the records matching the selection:
   - Attendance: days present, days absent, reasons, attendance rate.
   - Assessment: all session entries for the selected area, in chronological order, with teacher notes.
5. Actor can view per student or aggregated across the class.

**Useful exit:** Complete, accurate records retrieved in seconds. No paper files, no manual counting.

**Failure/recovery:** If no records exist for the selected range, the platform shows an explicit empty state — not a zero or a blank that could be mistaken for a data loss.
