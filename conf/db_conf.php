<?php

// Local DB configuration fallback file.
// Prefer environment variables for production deployments.
return array(
	'host' => getenv('MFPLIST_DB_HOST') ?: 'localhost',
	'db_name' => getenv('MFPLIST_DB_NAME') ?: 'mfplist',
	'username' => getenv('MFPLIST_DB_USER') ?: 'mfplist',
	'password' => getenv('MFPLIST_DB_PASSWORD') ?: '0]eGM_WMi9z/w6nG',
);

