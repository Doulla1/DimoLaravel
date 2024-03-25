<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>DimoVR</title>
    <link rel="icon" href="{{ asset('storage/images/DimoVR_logo.png') }}" type="image/png">
</head>
<body>
<div style="height:97vh;width:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;">
    <h1 style="text-align:center;">Salut {{ $user->lastname  }}  !</h1>
    <h2 style="text-align:center;">Ton inscription à la formation : {{ $program->name }} a bien été prise en compte.</h2>
    <h3 style="text-align:center;">Tu peux dès à présent commencer tes cours !</h3>
</div>
</body>
</html>
