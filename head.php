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
$settingsactive = '';
$settingsactivembl = '';

$url = $_SERVER['REQUEST_URI'];
switch ($url) {
    case (strpos($url, 'index.php') !== false):
        $title = 'Dashboard';
        $dashboardactive = 'class="active"';
        $dashboardactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'character.php') !== false):
        $title = 'Character Sheet';
        break;
    case (strpos($url, 'mail.php') !== false):
        $title = 'Eve Mail';
        $mailactive = 'class="active"';
        $mailactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'skills.php') !== false):
        $title = 'Skills';
        $skillsactive = 'class="active"';
        $skillsactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'market.php') !== false):
        $title = 'Market';
        $marketactive = 'class="active"';
        $marketactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'wallet.php') !== false):
        $title = 'Wallet';
        $walletactive = 'class="active"';
        $walletactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'assets.php') !== false):
        $title = 'Assets';
        $assetsactive = 'class="active"';
        $assetsactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'contacts.php') !== false):
        $title = 'Contacts';
        $contactsactive = 'class="active"';
        $contactsactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'industry.php') !== false):
        $title = 'Industry';
        $industryactive = 'class="active"';
        $industryactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'calendar.php') !== false):
        $title = 'Calendar';
        $calendaractive = 'class="active"';
        $calendaractivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'settings.php') !== false):
        $title = 'Settings';
        $settingsactive = 'class="active"';
        $settingsactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'planets.php') !== false):
        $title = 'Planets';
        $planetsactive = 'class="active"';
        $planetsactivembl = 'style="background-color: #404040;"';
        break;
    case (strpos($url, 'account.php') !== false):
        $title = 'Account';
        break;
    case (strpos($url, 'apikeys.php') !== false):
        $title = 'API Key Management';
        break;
    default:
        $title = 'Unknown';
        break;
}

if (count($_SESSION) === 0 && (strpos($url, 'account.php') === false)) {
    header('Location: account.php?char=0');
    die();
}

if (empty($_SESSION['keyID']) && (strpos($url, 'apikeys.php') === false) && (strpos($url, 'account.php') === false)) {
    header('Location: apikeys.php?char=0');
    die();
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
    <link rel="icon"
          href="http://www.iconwanted.com/downloads/exhumed/mega-games-pack-17-icons-by-exhumed/png/128x128/eve-online-1.png">
    <title>Project EVIE - <?php echo $title; ?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/EVIE.css">
</head>
<body>