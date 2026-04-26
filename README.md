# PHP Backend for MFPList project

## Security setup

Database credentials are now read from environment variables at runtime.

Set these variables before starting the app:

- `MFPLIST_DB_HOST`
- `MFPLIST_DB_NAME`
- `MFPLIST_DB_USER`
- `MFPLIST_DB_PASSWORD`

Example (PowerShell):

```powershell
$env:MFPLIST_DB_HOST = "localhost"
$env:MFPLIST_DB_NAME = "mfplist"
$env:MFPLIST_DB_USER = "your_db_user"
$env:MFPLIST_DB_PASSWORD = "your_db_password"
```

## Security hardening included

- DAO write/read operations now use prepared statements for safer SQL execution.
- Login password verification supports secure `password_hash` values and auto-upgrades legacy SHA-512 hashes after successful login.
- API tokens are stored as SHA-256 digests in the database instead of plaintext.
- Login endpoint now has basic rate limiting for repeated failed attempts.
- Internal exception details are logged server-side and no longer returned to API clients.