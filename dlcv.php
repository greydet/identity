<?php

require 'assets/recaptcha/autoload.php';
include 'prv/config.inc.php';

$recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret);
$rep = $recaptcha->verify($_POST['recaptchaRep'], $_SERVER['REMOTE_ADDR']);

if ($rep->isSuccess())
{
    if (!is_file($file))
    {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    $subject = "Download CV by ${_POST['email']}";
    $message = "
    <html>
    <head>
    <title>$subject</title>
    </head>
    <body>
    CV downloaded by ${_POST['email']} from IP address ${_SERVER['REMOTE_ADDR']}
    </body>
    </html>
    ";
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "To: $mail\r\n";
    $headers .= "From: $mail\r\n";
    mail($to, $subject, $message, $headers);

    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
}
else
{
    header('HTTP/1.0 403 Forbidden');
}
?>
