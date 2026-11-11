# IskolarNotes

A shared library of lecture notes, reviewers, past exams and official syllabi, organized by
department, course and instructor. Students browse and download material with credits, authors
upload their notes, moderators and faculty verify uploads and resolve copyright reports, and an
administrator manages account roles.

- **Framework:** Laravel 12 (PHP 8.3), Blade views, MySQL
- **Web app:** session-based authentication (`routes/web.php`)
- **Repository scope:** web application plus an optional API layer (`routes/api.php`)

## Running the project locally

```bash
composer install
npm install && npm run build      # or: npm run dev
php artisan migrate
php artisan db:seed
php artisan serve
```

Seeded accounts (password `password123`):

| Email | Role |
|---|---|
| admin@iskolar.test | admin |
| faculty@iskolar.test | faculty |
| moderator@iskolar.test | moderator |
| author@iskolar.test | author |
| student@iskolar.test | student |

---

# Laboratory Activity 2 — RBAC

**Course:** Integrative Programming 2 — Module 5, Lesson 2 (Authorization and Role-Based Access Control)
**Selected approach:** **Dynamic Role Middleware** (Option A)

## Description

IskolarNotes is a peer-shared academic library. Any registered user can browse verified material and
download it; authors can upload their own notes for review; moderators and faculty verify uploads and
resolve copyright reports; the administrator manages accounts and their roles.

## Roles

`admin`, `faculty`, `moderator`, `author`, `student`. New public registrations may only choose
`student` or `author`; `moderator`, `faculty` and `admin` are assigned by an administrator from the
`/admin` page. No public form, query string or API field can set those roles.

## Role-and-permission matrix

| Existing page or action | Route | Admin | Faculty | Moderator | Author | Student |
|---|---|---|:--:|:--:|:--:|:--:|
| Browse verified notes (`GET /browse`) | `browse` | Allowed | Allowed | Allowed | Allowed | Allowed |
| View a note and its details (`GET /resources/{id}`) | `resources.show` | Allowed | Allowed | Allowed | Allowed | Allowed |
| Personal shelf and badges (`GET /shelf`) | `shelf` | Allowed | Allowed | Allowed | Allowed | Allowed |
| Download / vote / report (`POST /resources/...`) | `resources.*` | Allowed | Allowed | Allowed | Allowed | Allowed |
| Upload a note (`GET /upload`, `POST /upload`) | `upload` | Allowed | Allowed | Allowed | Allowed | Denied |
| Publish an official syllabus | `upload.store` | Allowed | Allowed | Denied | Denied | Denied |
| Review queue (`GET /review`) | `review` | Allowed | Allowed | Allowed | Denied | Denied |
| Approve / reject / dismiss (`POST /review/...`) | `review.*` | Allowed | Allowed | Allowed | Denied | Denied |
| Administration dashboard (`GET /admin`) | `admin` | Allowed | Denied | Denied | Denied | Denied |
| Change another user's role (`POST /admin/users/{user}/role`) | `admin.users.role` | Allowed | Denied | Denied | Denied | Denied |

At least one action is open to every authenticated role (browse and download), and several actions are
restricted to particular roles (upload, review, administration).

## Middleware

| Item | Value |
|---|---|
| Class | `App\Http\Middleware\EnsureRole` |
| Alias | `role` (registered in `bootstrap/app.php`) |
| Possible usage | `->middleware('role:admin')`, `->middleware('role:moderator,faculty,admin')` |
| Protected GET pages (web) | `/review`, `/admin` |
| Protected POST actions (web) | `/review/{resource}/approve`, `/review/{resource}/reject`, `/review/{resource}/dismiss`, `/admin/users/{user}/role` |
| Behaviour on denial | HTTP `403` with `resources/views/errors/403.blade.php`; JSON `403` for `Accept: application/json`; guests are redirected to login |

The alias is also used by the API review routes, so the same class guards both surfaces.

## How the middleware authorizes a request

`EnsureRole` reads the roles declared on the route (`role:admin`, `role:moderator,faculty,admin`) into
an array of allowed roles. It then takes the authenticated user from the session (`$request->user()`)
and compares that user's **`role` column** against the allowed list with a strict `in_array` check. The
role is never read from the request body, query string, header or cookie, so changing a form field or
URL cannot upgrade an account. Guests are redirected to the login page before any role comparison; an
authenticated user whose role is not listed receives HTTP 403 and the request never reaches the
controller. Because the check runs on the route and not in a Blade template, hiding the admin link is
only cosmetic — typing `/admin` directly, or replaying the role-change POST by hand, is refused by the
server.

## Evidence

All five screenshots are in [`evidence/`](evidence/).

| # | File | What it shows |
|---|---|---|
| 1 | [`01-admin-allowed.png`](evidence/01-admin-allowed.png) | Admin (`admin@iskolar.test`) opens `/admin` — dashboard, totals and user role table. |
| 2 | [`02-other-role-allowed.png`](evidence/02-other-role-allowed.png) | Student (`student@iskolar.test`) opens the shared page `/browse` — permitted, with no Administration or Review queue links. |
| 3 | [`03-role-denied.png`](evidence/03-role-denied.png) | Student types `/admin` directly — HTTP 403, "Requested: /admin". |
| 4 | [`04-guest-blocked.png`](evidence/04-guest-blocked.png) | Logged out, opening `/admin` redirects to the login page. |
| 5 | [`05-restricted-action-denied.png`](evidence/05-restricted-action-denied.png) | Student replays `POST /admin/users/1/role` with a valid CSRF token — 403, and the target account's role stayed `student`. |

Automated checks cover the same cases (see `tests/Feature/RbacTest.php`):

```bash
php artisan test --filter=RbacTest
```

## Reflection

The challenging part was separating "who can see a page" from "who can perform the action behind it":
the review queue is one page but three different POSTs, and the administration page posts a role
change that must fail even when the page itself is unreachable. Writing the role list as route
parameters kept one middleware class reusable and made the permission matrix readable straight from
`routes/web.php`, while registering the alias in `bootstrap/app.php` was the step most easily forgotten
until `php artisan route:list -v` proved the middleware was attached. Server-side authorization is
necessary because the browser is fully controlled by the visitor: menu items, disabled buttons and
hidden forms can all be recreated by hand, so any check that exists only in Blade is decoration. The
server must decide on every request, using data it controls, which is why this middleware reads the
`users.role` column and never a value supplied by the client.
