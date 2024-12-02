<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>

<body>
    <p>@lang('labels.email_title_one_new_company')</p>
    <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
    <p>@lang('labels.email_title_two_new_company') <strong>{{ $tempPassword }}</strong></p>
    </p>
    <p>@lang('labels.email_title_three_new_company')</p>
</body>

</html>
