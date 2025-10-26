<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification de création de compte bancaire</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { background-color: #333; color: white; padding: 10px; text-align: center; font-size: 12px; }
        .highlight { background-color: #ffff99; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Création de votre compte bancaire</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $utilisateur->nom }} {{ $utilisateur->prenom }},</p>
            <p>Votre compte bancaire a été créé avec succès. Voici les informations nécessaires pour vous connecter :</p>

            <div class="highlight">
                <strong>Numéro de compte :</strong> {{ $compteBancaire->numero_compte }}<br>
                <strong>Mot de passe temporaire :</strong> {{ $motDePasseTemporaire }}
            </div>

            <p><strong>Instructions de connexion :</strong></p>
            <ol>
                <li>Accédez à notre plateforme de banque en ligne.</li>
                <li>Utilisez votre numéro de compte comme identifiant.</li>
                <li>Entrez le mot de passe temporaire fourni ci-dessus.</li>
                <li>Vous serez invité à changer votre mot de passe lors de votre première connexion.</li>
            </ol>

            <p>Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.</p>

            <p>Si vous avez des questions, n'hésitez pas à contacter notre service client.</p>

            <p>Cordialement,<br>
            L'équipe de SenBanques</p>
        </div>
        <div class="footer">
            <p>Cette adresse email est générée automatiquement. Veuillez ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>