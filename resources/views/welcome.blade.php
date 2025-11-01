<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - API</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }
        h1 { font-size: 3rem; margin-bottom: 1rem; }
        p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
        .links { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        a {
            padding: 1rem 2rem;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .status {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        .status-item {
            display: inline-block;
            margin: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .endpoints {
            margin-top: 2rem;
            text-align: left;
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 10px;
        }
        .endpoints h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .endpoint {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.75rem;
            margin: 0.5rem 0;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        .method {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        .get { background: #61affe; color: white; }
        .post { background: #49cc90; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 API Bancaire</h1>
        <p>{{ config('app.name') }} - Version 1.0.0</p>
        
        <div class="links">
            <a href="/api/documentation">📚 Documentation API (Swagger UI)</a>
            <a href="/api/docs">📄 Swagger JSON</a>
        </div>

        <div class="status">
            <div class="status-item">✅ API Active</div>
            <div class="status-item">🔒 Laravel {{ app()->version() }}</div>
            <div class="status-item">🐘 PHP {{ PHP_VERSION }}</div>
            <div class="status-item">🗄️ PostgreSQL</div>
        </div>

        <div class="endpoints">
            <h2>🔗 Endpoints Principaux</h2>
            <div class="endpoint">
                <span class="method get">GET</span>
                /api/v1/comptes
            </div>
            <div class="endpoint">
                <span class="method get">GET</span>
                /api/v1/comptes/{numero}
            </div>
            <div class="endpoint">
                <span class="method get">GET</span>
                /api/v1/comptes/client/{telephone}
            </div>
            <div class="endpoint">
                <span class="method post">POST</span>
                /api/v1/comptes/{compteId}/bloquer
            </div>
        </div>
    </div>
</body>
</html>