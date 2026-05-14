<?php

// Local DB configuration fallback file.
// Prefer environment variables for production deployments.
return array(
	'host' => getenv('COPILOC_DB_HOST') ?: 'localhost',
	'db_name' => getenv('COPILOC_DB_NAME') ?: 'mfplist',
	'username' => getenv('COPILOC_DB_USER') ?: 'mfplist',
	'password' => getenv('COPILOC_DB_PASSWORD') ?: '0]eGM_WMi9z/w6nG',
);

