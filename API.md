# Award Management System – REST API Reference

Base URL: `http://localhost:8000/api`

All protected routes require: `Authorization: Bearer {token}`

---

## Authentication

### POST `/auth/login`
```json
{ "email": "admin@sk.vn", "password": "admin" }
```
Returns `{ token, user: { id, name, email, role, title, judge_id? } }`

### GET `/auth/me` 🔒
Returns current user profile.

### POST `/auth/logout` 🔒
Invalidates the current token.

**Demo credentials:**
| Role         | Email            | Password |
|--------------|------------------|----------|
| Admin        | admin@sk.vn      | admin    |
| Judge 1      | judge1@sk.vn     | judge    |
| Judge 2      | judge2@sk.vn     | judge    |
| Judge 3      | judge3@sk.vn     | judge    |
| Judge 4      | judge4@sk.vn     | judge    |
| Judge 5      | judge5@sk.vn     | judge    |
| Submitter 1  | submit@sk.vn     | submit   |
| Submitter 2  | submit2@sk.vn    | submit   |
| Submitter 3  | submit3@sk.vn    | submit   |

---

## Programs

### GET `/programs` 🔒
- **Admin**: all programs
- **Judge**: programs they are assigned to
- **Submitter**: active programs only

Response: array of program objects with `categories`, `criteria`, `judge_ids`

### POST `/programs` 🔒 (Admin only)
```json
{
  "name": "Giải thưởng Sao Khuê",
  "year": 2027,
  "abbr": "SK",
  "color": "#1e3a5f",
  "deadline": "2027-06-30",
  "description": "...",
  "categories": [{ "name": "Giải pháp phần mềm", "color": "#3b82f6" }],
  "criteria": [{ "name": "Tính sáng tạo", "description": "...", "max_score": 20 }]
}
```

### GET `/programs/{id}` 🔒
Returns a single program with full details.

### PATCH `/programs/{id}` 🔒 (Admin only)
Update `name`, `year`, `abbr`, `color`, `status`, `deadline`, `description`.

---

## Program Judges

### GET `/programs/{program}/judges` 🔒
List judges assigned to a program.

### POST `/programs/{program}/judges` 🔒 (Admin only)
```json
{ "judge_id": "J1" }
```

### DELETE `/programs/{program}/judges/{judge}` 🔒 (Admin only)
Remove a judge from the program (removes their draft scores, keeps submitted).

---

## Global Judges

### GET `/judges` 🔒 (Admin/Judge only)
List all judges in the system.

---

## Submissions

### GET `/programs/{program}/submissions` 🔒
- **Admin**: all submissions; supports `?status=pending|approved|rejected&search=...`
- **Judge**: only their assigned + approved submissions
- **Submitter**: only their own submissions

### POST `/programs/{program}/submissions` 🔒 (Submitter/Admin)
```json
{
  "name": "Product Name",
  "company": "Company Ltd",
  "category_id": "C1",
  "description": "...",
  "docs": ["Hồ sơ đăng ký", "Tài liệu kỹ thuật"]
}
```

### POST `/programs/{program}/submissions/{submission}/review` 🔒 (Admin only)
```json
{ "status": "approved" }   // "pending" | "approved" | "rejected"
```

---

## Judge Assignments

### POST `/programs/{program}/submissions/{submission}/assign/{judge}` 🔒 (Admin)
Assign a judge to an approved submission (judge must be in the program).

### DELETE `/programs/{program}/submissions/{submission}/assign/{judge}` 🔒 (Admin)
Unassign a judge (removes draft score, keeps submitted).

---

## Scores

### GET `/programs/{program}/submissions/{submission}/scores` 🔒
- **Admin**: all judges' scores
- **Judge**: only their own score

Response array:
```json
{
  "id": 1,
  "submission_id": "SK26-001",
  "judge_id": "J1",
  "judge_name": "PGS.TS. Nguyễn Văn An",
  "comment": "Rất tiềm năng.",
  "is_submitted": true,
  "scores": { "cr1": 17, "cr2": 16, "cr3": 18, "cr4": 19, "cr5": 15 },
  "total": 85,
  "max_total": 100,
  "updated_at": "2026-04-24T..."
}
```

### PUT `/programs/{program}/submissions/{submission}/scores` 🔒 (Judge only)
Save draft or submit final score. Once `is_submitted: true`, the record is locked.
```json
{
  "scores": { "cr1": 17, "cr2": 16, "cr3": 18, "cr4": 19, "cr5": 15 },
  "comment": "Nhận xét...",
  "is_submitted": false
}
```

---

## Dashboard

### GET `/programs/{program}/dashboard` 🔒
Returns role-specific stats:

**Admin**: `total_submissions`, `approved`, `pending`, `rejected`, `scored_submissions`, `total_score_entries`, `judge_count`, `max_total`, `ranked[]`, `judge_progress[]`

**Judge**: `assigned`, `submitted`, `draft`, `pending`, `pct`, `avg_score`, `max_total`

**Submitter**: `total`, `approved`, `pending`, `rejected`

---

## Database Schema

| Table                  | Key columns                                      |
|------------------------|--------------------------------------------------|
| `users`                | id, email, password, role, title                 |
| `judges`               | id(J1..J5), name, email, specialty, user_id      |
| `programs`             | id(P1..), name, year, abbr, color, status        |
| `program_categories`   | (id, program_id), name, color                    |
| `program_criteria`     | (id, program_id), name, description, max_score   |
| `program_judges`       | (program_id, judge_id)                           |
| `submissions`          | id(SK26-001), program_id, name, company, status  |
| `submission_documents` | id, submission_id, name                          |
| `judge_assignments`    | (submission_id, judge_id)                        |
| `scores`               | id, submission_id, judge_id, comment, is_submitted|
| `score_details`        | score_id, criterion_id, value                    |
| `personal_access_tokens` | (Sanctum)                                      |
