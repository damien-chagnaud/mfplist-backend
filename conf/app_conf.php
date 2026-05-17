<?php

// Local app configuration fallback file.
// Prefer environment variables for production deployments.

return array(
    'app_name' => getenv('COPILOC_APP_NAME') ?: 'Copiloc Data API',
    'version' => getenv('COPILOC_APP_VERSION') ?: '1.0.0',
    'site_url' => getenv('COPILOC_SITE_URL') ?: '',
    'database_system' => getenv('COPILOC_DATABASE_SYSTEM') ?: 'mariadb',
    'debug' => getenv('COPILOC_DEBUG') ? filter_var(getenv('COPILOC_DEBUG'), FILTER_VALIDATE_BOOLEAN) : false,
);
