<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFPList Backend API</title>
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
        }

        * {
            box-sizing: border-box;
        }

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
            width: min(980px, 94vw);
            margin: 42px auto;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: rise 0.7s ease-out;
        }

        .hero {
            padding: 44px 36px 28px;
            border-bottom: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.94));
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
            margin-bottom: 14px;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 5vw, 2.85rem);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .subtitle {
            margin: 14px 0 0;
            max-width: 760px;
            color: var(--muted);
            font-size: 1.04rem;
            line-height: 1.65;
        }

        .content {
            padding: 28px 36px 40px;
            display: grid;
            gap: 26px;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
            background: #fff;
        }

        .panel h2 {
            margin: 0 0 8px;
            font-size: 1.1rem;
        }

        .panel p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .resources {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .resource {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px 14px;
            background: #fffdf9;
        }

        .resource strong {
            display: block;
            margin-bottom: 4px;
            font-size: 0.96rem;
        }

        .resource span {
            font-family: "IBM Plex Mono", monospace;
            font-size: 0.82rem;
            color: #385057;
            word-break: break-word;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 10px;
            border: 1px solid transparent;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 0.92rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            box-shadow: 0 8px 16px rgba(15, 118, 110, 0.26);
        }

        .btn-secondary {
            color: var(--accent-2);
            background: #f4f8fa;
            border-color: #c7d9e1;
        }

        .btn-inline {
            color: var(--accent-2);
            padding: 5px 7px;
            background: #f4f8fa;
            border-color: #c7d9e1;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .footnote {
            margin-top: 6px;
            color: #6b757a;
            font-size: 0.84rem;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 760px) {
            .hero,
            .content {
                padding: 24px 18px;
            }

            .resources {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <span class="kicker">MFPList Service</span>
            <h1>Welcome to the MFPList Backend API</h1>
            <p class="subtitle">
                This service powers authentication, synchronization, and data management for clients,
                contacts, devices, machines, parts, and device history records. It is designed for lightweight,
                token-based integrations from web and mobile apps.
            </p>
        </section>

        <section class="content">
            <article class="panel">
                <h2>Core Endpoints</h2>
                <p>
                    Use the API resources below to retrieve and update records. Each collection supports
                    GET operations and write operations for data sync workflows.
                </p>
                <div class="resources">
                    <div class="resource">
                        <strong>Machines </strong>
                            <span>/machines (GET, POST, PUT)</span>
                            <div class="actions"><a class="btn btn-inline" href="machines/infos">/infos</a></div>
                    </div>
                    <div class="resource">
                        <strong>Authentication</strong><span>/login (GET, POST)</span>
                    </div>
                </div>
            </article>

            <article class="panel">
                <h2>Quick Start</h2>
                <p>
                    Authenticate using <strong>POST /login</strong> to receive a bearer token, then include it
                    in the Authorization header for secured operations.
                </p>
                <div class="actions">
                    <a class="btn btn-primary" href="login">Open Login Endpoint</a>
                    <a class="btn btn-secondary" href="infos">Service Info</a>
                </div>
                <p class="footnote">Base URL: configured by SITE_URL in your environment.</p>
            </article>
        </section>
    </main>
</body>
</html>