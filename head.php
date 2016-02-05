<?php
/*
Start session and load loginapp 
*/
session_start();
include "loginapp.php";

$url = get_string_between($_SERVER['REQUEST_URI'], '/eve/', '?');
   switch  ($url) {
    case "index.php":
        $title = 'Dashboard';
        break;
    case "character.php":
        $title = 'Character Sheet';
        break;
    case "mail.php":
        $title = 'Eve Mail';
        break;
    case "skills.php":
        $title = 'Skills';
        break;
    case "market.php":
        $title = 'Market';
        break;
    case "wallet.php":
        $title = 'Wallet';
        break;
    case "assets.php":
        $title = 'Assets';
        break;
    case "contacts.php":
        $title = 'Contacts';
        break;
    case "industry.php":
        $title = 'Industry';
        break;
    case "calendar.php":
        $title = 'Calendar';
        break;
    case "settings.php":
        $title = 'Settings';
        break; 
    case "planets.php":
        $title = 'Planets';
        break;
    case "account.php":
        $title = 'Account';
        break;
    case "apikeys.php":
        $title = 'API Key Management';
        break;
    default:
        $title = 'Unknown';
        break;
}

if ((empty($_SESSION)) && ($url != "account.php")){
    header("Location: /eve/account.php?char=0");
    die();
}

if ((empty($_SESSION['keyID'])) && ($url != "apikeys.php") && ($url != "account.php")){
    header("Location: /eve/apikeys.php?char=0");
    die();
}  

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
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
<meta name="description" content="Project EVIE is a web-app that displays data from the EVE Online API system, the purpose of it is to provide a fast and secure way of checking information from an account.">
<meta name="author" content="Name: Jeroen Akkerman, Email: jeroen.akkerman@outlook.com, In-game: Ionaru Otsada">
<link rel="icon" href="http://www.iconwanted.com/downloads/exhumed/mega-games-pack-17-icons-by-exhumed/png/128x128/eve-online-1.png">
<title>Project EVIE - <?php echo $title; ?></title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href="css/dashboard.css" rel="stylesheet">
<link rel="stylesheet" href="css/EVIE.css">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>