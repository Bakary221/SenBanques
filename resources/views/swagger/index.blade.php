<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - SenBanque</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3.25.0/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin:0;
            background: #fafafa;
        }
        .auth-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            z-index: 1000;
            font-size: 12px;
        }
        .auth-info button {
            margin-left: 5px;
            padding: 2px 8px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <div id="auth-info" class="auth-info" style="display: none;">
        <strong>Auth Status:</strong> <span id="auth-status">Not authenticated</span>
        <button onclick="clearToken()">Clear Token</button>
        <button onclick="testAuth()">Test Auth</button>
    </div>
    <script src="https://unpkg.com/swagger-ui-dist@3.25.0/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@3.25.0/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: '/api/v1/docs',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                validatorUrl: null,
                tryItOutEnabled: true,
                requestInterceptor: function(request) {
                    request.headers['Accept'] = 'application/json';
                    // Ajouter automatiquement le token Bearer si disponible
                    const token = localStorage.getItem('swagger_token');
                    if (token) {
                        request.headers['Authorization'] = 'Bearer ' + token;
                    }
                    return request;
                },
                responseInterceptor: function(response) {
                    // Sauvegarder automatiquement le token après connexion
                    if (response.url.includes('/auth/login') && response.status === 200) {
                        try {
                            const data = JSON.parse(response.data);
                            if (data.success && data.data && data.data.access_token) {
                                localStorage.setItem('swagger_token', data.data.access_token);
                                updateAuthStatus();
                                console.log('Token sauvegardé automatiquement');
                            }
                        } catch (e) {
                            console.log('Impossible de parser la réponse de login');
                        }
                    }
                    return response;
                },
                onComplete: function() {
                    updateAuthStatus();
                    document.getElementById('auth-info').style.display = 'block';
                }
            });

            // Fonctions utilitaires pour l'authentification
            window.clearToken = function() {
                localStorage.removeItem('swagger_token');
                updateAuthStatus();
                console.log('Token supprimé');
            };

            window.testAuth = function() {
                const token = localStorage.getItem('swagger_token');
                if (!token) {
                    alert('Aucun token trouvé. Veuillez vous connecter d\'abord.');
                    return;
                }

                fetch('/api/v1/auth/user', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Authentification valide!\nUtilisateur: ' + data.data.user.prenom + ' ' + data.data.user.nom + '\nRôle: ' + data.data.user.role);
                    } else {
                        alert('Erreur d\'authentification: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du test d\'authentification');
                });
            };

            window.updateAuthStatus = function() {
                const token = localStorage.getItem('swagger_token');
                const statusEl = document.getElementById('auth-status');
                if (token) {
                    statusEl.textContent = 'Authenticated';
                    statusEl.style.color = 'green';
                } else {
                    statusEl.textContent = 'Not authenticated';
                    statusEl.style.color = 'red';
                }
            };
        };
    </script>
</body>
</html>