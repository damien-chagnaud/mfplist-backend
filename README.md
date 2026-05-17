# PHP Backend for MFPList project

## Configuration (`conf/app_conf.json`)

Application settings are centralized in `conf/app_conf.json`:

| Key | Description |
|---|---|
| `app_name` | Application name exposed as `APP_NAME` env var |
| `version` | Application version |
| `site_url` | Base URL prefix for routes (`SITE_URL`) |
| `database_system` | `mariadb` or `sqlite` |
| `debug` | Enables debug mode (`DEBUG_MODE`) |
| `allowed_origins` | List of allowed CORS origins (see below) |

Database credentials are **not** stored in `app_conf.json`. They are read from `conf/db_conf.php` at runtime and injected as environment variables by `public/bootstrap.php`.

## Security setup

Database credentials are read from `conf/db_conf.php` at runtime and exposed as environment variables:

- `COPILOC_DB_HOST`
- `COPILOC_DB_NAME`
- `COPILOC_DB_USER`
- `COPILOC_DB_PASSWORD`

Example (PowerShell, for overriding at the OS level):

```powershell
$env:COPILOC_DB_HOST = "localhost"
$env:COPILOC_DB_NAME = "mfplist"
$env:COPILOC_DB_USER = "your_db_user"
$env:COPILOC_DB_PASSWORD = "your_db_password"
```

## CORS configuration

CORS is enforced via client application validation. Each request must include an `X-APP-ID` header containing a client app UUID. The system:

1. **Validates client app UUID**: Looks up the UUID in the database via `ClientAppsManager`.
2. **Checks allowed origin**: Returns the `allowed_origin` configured for that client app (or `null` for forbidden requests → `403` response).
3. **Sets CORS headers**:
   - `Access-Control-Allow-Origin`: Matches the client app's configured origin
   - `Access-Control-Allow-Methods`: GET, POST, PUT, DELETE, OPTIONS
   - `Access-Control-Allow-Headers`: Authorization, Content-Type, Accept
   - `Access-Control-Max-Age`: 86400 seconds
   - `Vary: Origin`: For correct CDN caching
4. **Handles preflight**: `OPTIONS` requests return `204 No Content` immediately.

**Required request header:**
```
X-APP-ID: <client-app-uuid>
```

- Implemented in `public/head.php` (`setCorsHeaders()`) and called before token validation.
- No client app found → `403 Forbidden` with error response.

## Security hardening included

- DAO write/read operations use prepared statements to prevent SQL injection.
- Login password verification supports secure `password_hash` values and auto-upgrades legacy SHA-512 hashes after successful login.
- API tokens are stored as SHA-256 digests in the database instead of plaintext.
- Login endpoint has rate limiting: max **8 attempts per 15 minutes** per email+IP combination (returns HTTP `429`).
- Internal exception details are logged server-side via `lib/logger.php` and never returned to API clients.
- Database connection errors are caught and abstracted before being surfaced to callers.

## Users API

All users endpoints require a valid authenticated token and admin access level (`USER_LEVEL > 1`).

### List users

- Method: `GET`
- Route: `/users`
- Success: `200`
- Notes: returns all users and excludes sensitive fields (`password`, `token`).

### Get user by UUID

- Method: `GET`
- Route: `/users/{uuid}`
- Success: `200`
- Not found: `404`
- Notes: returns one user and excludes sensitive fields (`password`, `token`).

### Create user

- Method: `POST`
- Route: `/users`
- Success: `201`
- Required JSON fields: `password` and any other required DB fields (`uuid`, `uid`, `username`, `email`, `token_created_at`)
- Notes: password is hashed server-side using `password_hash` before insert.

Example payload:

```json
{
    "uuid": "d2fd9e55-5c28-4d13-9d4d-2c9ad2326d5c",
    "uid": "U1001",
    "username": "admin",
    "email": "admin@example.com",
    "password": "StrongPassword123!",
    "token_created_at": "2026-05-17 10:30:00",
    "level": 2
}
```

### Update user

- Method: `PUT`
- Route: `/users`
- Success: `200`
- Required JSON field: `uuid` (target user)
- Notes: if `password` is present, it is hashed server-side before update.

Example payload:

```json
{
    "uuid": "d2fd9e55-5c28-4d13-9d4d-2c9ad2326d5c",
    "username": "admin-renamed",
    "email": "admin.renamed@example.com",
    "level": 2
}
```

### Delete user by UUID

- Method: `DELETE`
- Route: `/users/{uuid}`
- Success: `200`
- Not found: `404`