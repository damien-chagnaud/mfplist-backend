<?php

// Local DB configuration fallback file.
// Prefer environment variables for production deployments.
return array(
	'host' => getenv('MFPLIST_DB_HOST') ?: 'localhost',
	'db_name' => getenv('MFPLIST_DB_NAME') ?: '',
	'username' => getenv('MFPLIST_DB_USER') ?: '',
	'password' => getenv('MFPLIST_DB_PASSWORD') ?: '',
);

