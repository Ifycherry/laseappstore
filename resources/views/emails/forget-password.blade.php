<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forget Password Email</title>
    </head>
    <body>
        <h1>Hello {{ $user->firstname }}</h1>
        <p>Below is a Link to change your password</p>
        <a href="{{ $url }}">Change Password</a> <br/>
        <p>{{ $url }}</p>
        <p>Thank you</p>
    </body>
</html>