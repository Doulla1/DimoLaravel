<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>DimoVR</title>
    <link rel="icon" href="{{ asset('storage/images/DimoVR_logo.png') }}" type="image/png">
</head>
<body>
<div style="height:97vh;width:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;">
    <p>Bonjour {{ $firstname }},</p>
    <p>Félicitations ! Vous venez officiellement d'intégrer l'institut DimoVR en tant quétudiant.</p>
    <p> Voici vos informations de connexion :</p>
    <ul>
        <li><strong>Identifiant:</strong> {{ $email }}</li>
        <li><strong>Mot de passe:</strong> {{ $password }}</li>
    </ul>
    <p>Vous pouvez à tout moment modifier votre mot de passe depuis votre compte</p>
    <p>Vous pouvez vous connecter à votre compte à l'adresse suivante : <a href="https://dimovr.com/sign-in">https://dimovr.com/sign-in</a></p>
    <p>Merci et bienvenue sur DimoVR!</p>
</div>
</body>
</html>
