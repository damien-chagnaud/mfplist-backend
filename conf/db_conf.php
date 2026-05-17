<?php

// Local DB configuration fallback file.
// Prefer environment variables for production deployments.
return array(
	'host' => getenv('COPILOC_DB_HOST') ?: '',
	'db_name' => getenv('COPILOC_DB_NAME') ?: '',
	'username' => getenv('COPILOC_DB_USER') ?: '',
	'password' => getenv('COPILOC_DB_PASSWORD') ?: '',
);

