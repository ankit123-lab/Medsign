<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang='en'>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
        <div>
            <p>Hello <?= $first_name ?>!,</p>
            <p style="padding-top:15px">
                Someone has requested a link to change your password, and you can do this through the link below.
            </p>
            <p style="padding-top: 15px">
                <a href="<?= $reset_password_url ?>">Reset your password</a>
            </p>
            <p style="padding-top: 15px">
                If you didn't request this, please ignore this email.
            </p>
            <p style="padding-top: 15px">
                Your password won't change until you access the link above and create a new one.
            </p>
            <p style="padding-top: 15px">
                Regards,<br>
                    <?= APP_NAME ?>
            </p>
        </div>
    </body>
</html>