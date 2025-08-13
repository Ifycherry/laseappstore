<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Email Veriification</title>
    </head>
    <body>
        <h1>Hello {{ $user->firstname }}</h1>
        <p>This is your verification code: {{ $user->verification_code }}</p>
    </body>
</html>