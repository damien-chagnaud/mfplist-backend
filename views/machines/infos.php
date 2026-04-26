<?php
$siteUrl = isset($_SERVER['SITE_URL']) ? rtrim($_SERVER['SITE_URL'], '/') : '';

function e($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$machineSample = [
	'id' => 23,
	'uuid' => '4a600223-a470-4c15-adec-9d0108551f78',
	'sn' => 'SN-2026-00918',
	'name' => 'MFP-Floor-2-North',
	'brand' => 'Ricoh',
	'type' => 'col',
	'installdate' => '2025-03-12',
	'geopos' => '50.6326,5.5797',
	'clientname' => 'Acme Industries',
	'clientsubname' => 'Headquarters'
];

$machinePayload = $machineSample;
unset($machinePayload['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Machine Service Information</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
	<style>
		:root {
			--bg-1: #f3efe6;
			--bg-2: #ebe3d4;
			--panel: #fffdf8;
			--text: #1f2a2c;
			--muted: #566268;
			--accent: #0f766e;
			--accent-2: #124e66;
			--line: #d9cfbd;
			--shadow: 0 20px 40px rgba(31, 42, 44, 0.1);
			--ok: #065f46;
			--warn: #7c2d12;
		}

		* { box-sizing: border-box; }

		html, body {
			margin: 0;
			padding: 0;
			font-family: "Archivo", sans-serif;
			color: var(--text);
			background:
				radial-gradient(circle at 12% 14%, rgba(15, 118, 110, 0.16), transparent 34%),
				radial-gradient(circle at 85% 20%, rgba(18, 78, 102, 0.18), transparent 38%),
				linear-gradient(135deg, var(--bg-1), var(--bg-2));
			min-height: 100%;
		}

		.shell {
			width: min(1020px, 94vw);
			margin: 36px auto;
			background: var(--panel);
			border: 1px solid var(--line);
			border-radius: 20px;
			box-shadow: var(--shadow);
			overflow: hidden;
			animation: rise 0.7s ease-out;
		}

		.hero {
			padding: 34px 34px 22px;
			border-bottom: 1px solid var(--line);
			background: linear-gradient(180deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.95));
		}

		.kicker {
			display: inline-block;
			font-family: "IBM Plex Mono", monospace;
			font-size: 12px;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			color: var(--accent-2);
			background: rgba(18, 78, 102, 0.1);
			border: 1px solid rgba(18, 78, 102, 0.25);
			border-radius: 999px;
			padding: 6px 12px;
			margin-bottom: 12px;
		}

		h1 {
			margin: 0;
			font-size: clamp(1.8rem, 4vw, 2.5rem);
			line-height: 1.12;
		}

		.subtitle {
			margin: 12px 0 0;
			color: var(--muted);
			line-height: 1.6;
			max-width: 840px;
		}

		.content {
			padding: 24px 34px 36px;
			display: grid;
			gap: 18px;
		}

		.panel {
			border: 1px solid var(--line);
			border-radius: 14px;
			background: #fff;
			padding: 18px;
		}

		.panel h2 {
			margin: 0 0 10px;
			font-size: 1.08rem;
		}

		.row {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 12px;
		}

		.endpoint {
			border: 1px solid var(--line);
			border-radius: 10px;
			padding: 12px;
			background: #fffdf9;
		}

		.method {
			display: inline-block;
			font-family: "IBM Plex Mono", monospace;
			font-size: 0.78rem;
			font-weight: 600;
			border-radius: 999px;
			padding: 3px 10px;
			margin-bottom: 8px;
			border: 1px solid;
		}

		.method-get { color: var(--ok); border-color: #6ee7b7; background: #ecfdf5; }
		.method-post,
		.method-put { color: var(--warn); border-color: #fdba74; background: #fff7ed; }

		.path {
			font-family: "IBM Plex Mono", monospace;
			font-size: 0.82rem;
			color: #27434d;
			word-break: break-all;
		}

		.small {
			margin-top: 6px;
			color: var(--muted);
			font-size: 0.88rem;
			line-height: 1.45;
		}

		.fields {
			width: 100%;
			border-collapse: collapse;
			font-size: 0.92rem;
		}

		.fields th,
		.fields td {
			text-align: left;
			border-bottom: 1px solid #ece3d3;
			padding: 10px 8px;
			vertical-align: top;
		}

		.fields th {
			color: #27434d;
			font-size: 0.82rem;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			font-family: "IBM Plex Mono", monospace;
		}

		pre {
			margin: 0;
			background: #f7f4ee;
			border: 1px solid #e2d8c6;
			border-radius: 10px;
			padding: 12px;
			overflow-x: auto;
			font-size: 0.82rem;
			line-height: 1.45;
			color: #273036;
		}

		.hint {
			margin: 10px 0 0;
			color: var(--muted);
			font-size: 0.86rem;
		}

		.actions {
			display: flex;
			gap: 12px;
			flex-wrap: wrap;
		}

		.btn {
			display: inline-block;
			text-decoration: none;
			border-radius: 10px;
			border: 1px solid transparent;
			padding: 10px 14px;
			font-weight: 700;
			font-size: 0.92rem;
			transition: transform 0.2s ease;
		}

		.btn-primary {
			color: #fff;
			background: linear-gradient(135deg, var(--accent), var(--accent-2));
		}

		.btn-secondary {
			color: var(--accent-2);
			background: #f4f8fa;
			border-color: #c7d9e1;
		}

		.btn:hover { transform: translateY(-1px); }

		@media (max-width: 900px) {
			.row { grid-template-columns: 1fr; }
		}

		@media (max-width: 760px) {
			.hero,
			.content {
				padding: 20px 16px;
			}
		}

		@keyframes rise {
			from { opacity: 0; transform: translateY(12px); }
			to { opacity: 1; transform: translateY(0); }
		}
	</style>
</head>
<body>
	<main class="shell">
		<section class="hero">
			<span class="kicker">Machine Service</span>
			<h1>Machine API Information</h1>
			<p class="subtitle">
				This page documents the machine service endpoints and payload format used by the
				MFPList backend. Use a bearer token for secured write operations.
			</p>
		</section>

		<section class="content">
			<article class="panel">
				<h2>Endpoints</h2>
				<div class="row">
					<div class="endpoint">
						<span class="method method-get">GET</span>
						<div class="path"><?= e($siteUrl) ?>/machines</div>
						<p class="small">Returns all machines as a JSON array.</p>
					</div>
					<div class="endpoint">
						<span class="method method-post">POST</span>
						<div class="path"><?= e($siteUrl) ?>/machines</div>
						<p class="small">Creates a machine from JSON request body.</p>
					</div>
					<div class="endpoint">
						<span class="method method-put">PUT</span>
						<div class="path"><?= e($siteUrl) ?>/machines</div>
						<p class="small">Updates a machine by its <strong>uuid</strong> value.</p>
					</div>
				</div>
				<p class="hint">
					Access control: GET requires user level &gt; 0, POST/PUT require user level &gt; 1.
				</p>
			</article>

			<article class="panel">
				<h2>Machine Fields</h2>
				<table class="fields" aria-label="Machine fields">
					<thead>
						<tr>
							<th>Field</th>
							<th>Type</th>
							<th>Description</th>
							<th>Example</th>
						</tr>
					</thead>
					<tbody>
						<tr><td>id</td><td>int</td><td>Internal numeric identifier.</td><td>23</td></tr>
						<tr><td>uuid</td><td>char(36)</td><td>Unique machine identifier.</td><td>mfp-0f2f9a7a-b99c...</td></tr>
						<tr><td>sn</td><td>char(100)</td><td>Serial number.</td><td>SN-2026-00918</td></tr>
						<tr><td>name</td><td>char(150)</td><td>Machine display name.</td><td>MFP-Floor-2-North</td></tr>
						<tr><td>brand</td><td>char(100)</td><td>Manufacturer brand.</td><td>Ricoh</td></tr>
						<tr><td>type</td><td>enum('bk', 'col')</td><td>Machine type.</td><td>col</td></tr>
						<tr><td>installdate</td><td>date</td><td>Install date string.</td><td>2025-03-12</td></tr>
						<tr><td>geopos</td><td>char(100)</td><td>GPS or textual location.</td><td>50.6326,5.5797</td></tr>
						<tr><td>clientname</td><td>char(150)</td><td>Main customer name.</td><td>Acme Industries</td></tr>
						<tr><td>clientsubname</td><td>char(150)</td><td>Customer sub-site/branch.</td><td>Headquarters</td></tr>
					</tbody>
				</table>
			</article>

			<article class="panel">
				<h2>JSON Examples</h2>
				<p class="small">POST/PUT request body:</p>
				<pre><?= e(json_encode($machinePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
				<p class="small" style="margin-top:12px;">GET response item:</p>
				<pre><?= e(json_encode($machineSample, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
			</article>

			<article class="panel">
				<h2>Navigation</h2>
				<div class="actions">
					<a class="btn btn-primary" href="<?= e($siteUrl) ?>/machines">Open Machines Endpoint</a>
					<a class="btn btn-secondary" href="/">Back to Home</a>
				</div>
			</article>
		</section>
	</main>
</body>
</html>
