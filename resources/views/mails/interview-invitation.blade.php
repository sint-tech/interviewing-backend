<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Interview Invitation</title>
</head>
<body>
Hi , <b>{{$invitation->name}}</b>,
We are pleased to notify you that a scheduled interview for the position of {{$invitation->vacancy->title}} at {{$invitation->vacancy->organization->name}} will take place at your own convenience.
Please click <a href="{{$invitation->url}}" >This link</a> to begin the interview, keeping in mind that the link will expire on {{$invitation->expired_at}}.
Wish you best of luck,
{{$invitation->vacancy->organization->name}}
<h3>Sint Team</h3>
</body>
</html>
