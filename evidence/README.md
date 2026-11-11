# RBAC evidence

Captured from the running application at `http://127.0.0.1:8123` with the seeded accounts
(`php artisan db:seed`, password `password123`).

| File | Account | URL opened | Result |
|---|---|---|---|
| `01-admin-allowed.png` | admin@iskolar.test (admin) | `/admin` | 200 — administration dashboard with user role table |
| `02-other-role-allowed.png` | student@iskolar.test (student) | `/browse` | 200 — shared page available to every signed-in role |
| `03-role-denied.png` | student@iskolar.test (student) | `/admin` typed directly | 403 — "Access denied", requested URL shown on the page |
| `04-guest-blocked.png` | not logged in | `/admin` typed directly | redirected to `/login` |
| `05-restricted-action-denied.png` | student@iskolar.test (student) | `POST /admin/users/1/role` with `role=admin` and a valid CSRF token | 403 — no change; `users.id = 1` stayed `student` |

The student account has no "Administration" or "Review queue" link in the navigation, but that is only
cosmetic: tests 3 and 5 show the server refusing the same pages and actions when requested by hand.
