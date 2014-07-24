<?php
require_once "../vendor/autoload.php";

//$realm = mt_rand(1, 1000000000) . "@petwise.me";
$realm = 'vapi';
$_SESSION['realm'] = $realm;
if (!isset($_SERVER['PHP_AUTH_USER']))
{
    header("WWW-Authenticate: Basic realm=" . $_SESSION['realm']);
    header('HTTP/1.0 401 Unauthorized');
    die();
}
else
{
    $key = new \Key();
    $valid_passwords = $key->getKey();
    $valid_users = array_keys($valid_passwords);

    $user = $_SERVER['PHP_AUTH_USER'];
    $pass = $_SERVER['PHP_AUTH_PW'];

    $validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

    if (!$validated)
    {

        header("WWW-Authenticate: Basic realm=" . $_SESSION['realm']);
        header('HTTP/1.0 401 Unauthorized');
        die();
    }
    else
    {
        $quickNap = new \QuickNap();
        $quickNap->enable();
    }
}