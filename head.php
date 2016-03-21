<?php
/*
Start session and load loginapp 
*/
session_start();
include __DIR__ . '/loginapp.php';

$title = '';
$dashboardactive = '';
$dashboardactivembl = '';
$skillsactive = '';
$skillsactivembl = '';
$mailactive = '';
$mailactivembl = '';
$marketactive = '';
$marketactivembl = '';
$walletactive = '';
$walletactivembl = '';
$assetsactive = '';
$assetsactivembl = '';
$contactsactive = '';
$contactsactivembl = '';
$planetsactive = '';
$planetsactivembl = '';
$industryactive = '';
$industryactivembl = '';
$calendaractive = '';
$calendaractivembl = '';
$apikeysactive = '';
$apikeysactivembl = '';
$disabled = '';

$url = $_SERVER['REQUEST_URI'];
switch ($url) {
    case (strpos($url, 'index.php') !== false):
        $title = 'Dashboard';
        $dashboardactive = ' nav_active';
        break;
    case (strpos($url, 'character.php') !== false):
        $title = 'Character Sheet';
        break;
    case (strpos($url, 'mail.php') !== false):
        $title = 'Eve Mail';
        $mailactive = ' nav_active';
        break;
    case (strpos($url, 'skills.php') !== false):
        $title = 'Skills';
        $skillsactive = ' nav_active';
        break;
    case (strpos($url, 'market.php') !== false):
        $title = 'Market';
        $marketactive = ' nav_active';
        break;
    case (strpos($url, 'wallet.php') !== false):
        $title = 'Wallet';
        $walletactive = ' nav_active';
        break;
    case (strpos($url, 'assets.php') !== false):
        $title = 'Assets';
        $assetsactive = ' nav_active';
        break;
    case (strpos($url, 'contacts.php') !== false):
        $title = 'Contacts';
        $contactsactive = ' nav_active';
        break;
    case (strpos($url, 'industry.php') !== false):
        $title = 'Industry';
        $industryactive = ' nav_active';
        break;
    case (strpos($url, 'calendar.php') !== false):
        $title = 'Calendar';
        $calendaractive = ' nav_active';
        break;
    case (strpos($url, 'planets.php') !== false):
        $title = 'Planets';
        $planetsactive = ' nav_active';
        break;
    case (strpos($url, 'account.php') !== false):
        $title = 'Account';
        $disabled = 'disabled ';
        break;
    case (strpos($url, 'apikeys.php') !== false):
        $title = 'Key Management';
        $apikeysactive = ' nav_active';
        break;
    default:
        $title = 'Unknown';
        break;
}

if (count($_SESSION) === 0 && (strpos($url, 'account.php') === false)) {
    header('Location: account.php');
    die();
}

if (empty($_SESSION['keyID']) && (strpos($url, 'apikeys.php') === false) && (strpos($url, 'account.php') === false)) {
    header('Location: apikeys.php');
    die();
}

if (!empty($_SESSION['user_name']) && (strpos($url, 'account.php') === 1)){
    header('Location: index.php');
    die();
}

if (isset($_GET['c']) && ((int)$_GET['c'] === 0 || (int)$_GET['c'] === 1 || (int)$_GET['c'] === 2)) {
    $_SESSION['selectedCharacter'] = $_GET['c'];
    $selectedChar = $_GET['c'];
}

function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini === 0) {
        return '';
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="Project EVIE">
    <meta name="description"
          content="Project EVIE is a web-app that displays data from the EVE Online API system, the purpose of it is to provide a fast and secure way of checking information from an account.">
    <meta name="author" content="Name: Jeroen Akkerman, Email: jeroen.akkerman@outlook.com, In-game: Ionaru Otsada">
    <meta name="theme-color" content="#202020">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="mobile_manifest" href="config/mobile_manifest.json">
    <link rel="icon"
          href="http://www.iconwanted.com/downloads/exhumed/mega-games-pack-17-icons-by-exhumed/png/128x128/eve-online-1.png">
    <title>Project EVIE - <?php echo $title; ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/Nav.css">
    <link rel="stylesheet" href="css/EVIE.css">
</head>
<body>