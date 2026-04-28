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

- `MFPLIST_DB_HOST`
- `MFPLIST_DB_NAME`
- `MFPLIST_DB_USER`
- `MFPLIST_DB_PASSWORD`

Example (PowerShell, for overriding at the OS level):

```powershell
$env:MFPLIST_DB_HOST = "localhost"
$env:MFPLIST_DB_NAME = "mfplist"
$env:MFPLIST_DB_USER = "your_db_user"
$env:MFPLIST_DB_PASSWORD = "your_db_password"
```

## CORS configuration

Cross-Origin Resource Sharing is configured via the `allowed_origins` array in `conf/app_conf.json`:

```json
"allowed_origins": [
    "http://localhost",
    "http://localhost:9059"
]
```

- Only listed origins receive an `Access-Control-Allow-Origin` header.
- Preflight `OPTIONS` requests are handled automatically (responds `204` and exits).
- A `Vary: Origin` header is sent so proxies/CDNs cache responses correctly.
- Add `"*"` to the list to allow all origins (not recommended for production).
- Implemented in `lib/headers.php` (`Headers::setCorsHeaders()`) and applied in `public/bootstrap.php` before any other output.

## Security hardening included

- DAO write/read operations use prepared statements to prevent SQL injection.
- Login password verification supports secure `password_hash` values and auto-upgrades legacy SHA-512 hashes after successful login.
- API tokens are stored as SHA-256 digests in the database instead of plaintext.
- Login endpoint has rate limiting: max **8 attempts per 15 minutes** per email+IP combination (returns HTTP `429`).
- Internal exception details are logged server-side via `lib/logger.php` and never returned to API clients.
- Database connection errors are caught and abstracted before being surfaced to callers.