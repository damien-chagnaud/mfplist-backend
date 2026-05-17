# PHP Backend for MFPList project

## Configuration files

Configuration is managed via PHP files in the `conf/` folder. Each file reads environment variables with local fallback defaults for development.

### `conf/app_conf.php`

Application settings with environment variable support:

| Setting | Env Variable | Default | Purpose |
|---------|--------------|---------|---------|
| `app_name` | `COPILOC_APP_NAME` | `Copiloc Data API` | Application name |
| `version` | `COPILOC_APP_VERSION` | `1.0.0` | Version string |
| `site_url` | `COPILOC_SITE_URL` | (empty) | Base URL prefix for routes |
| `database_system` | `COPILOC_DATABASE_SYSTEM` | `mariadb` | `mariadb` or `sqlite` |
| `debug` | `COPILOC_DEBUG` | `false` | Debug mode toggle |

### `conf/db_conf.php`

Database credentials with environment variable support:

| Setting | Env Variable | Default |
|---------|--------------|---------|
| `host` | `COPILOC_DB_HOST` | (empty) |
| `db_name` | `COPILOC_DB_NAME` | (empty) |
| `username` | `COPILOC_DB_USER` | (empty) |
| `password` | `COPILOC_DB_PASSWORD` | (empty) |

**Security note**: For production, always set these via OS environment variables rather than editing the PHP fallbacks.

### `conf/clients.json`

Client application registry for CORS enforcement. Each entry defines:

```json
{
    "uuid": "client-app-uuid",
    "name": "Client App Name",
    "allowed_origin": "https://client.example.com",
    "active": true,
    "status": "active",
    "access_dao": ["machines", "modules"]
}
```

- `uuid`: Unique identifier sent in `X-APP-ID` header by clients
- `allowed_origin`: CORS origin allowed for this client
- `active`: Enable/disable the client app
- `status`: Client status descriptor
- `access_dao`: DAOs (data access objects) this client can access

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
