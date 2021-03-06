<?php
if (isset ($_SESSION['selectedCharacter'])) {
    if (count($_SESSION) !== 0) {
        $selectedChar = $_SESSION['selectedCharacter'];
        if ($selectedChar > 2) {
            $selectedChar = 0;
        } else if ($selectedChar === '') {
            $selectedChar = 0;
        }
    } else {
        $selectedChar = 0;
    }
} else {
    $selectedChar = 0;
}
$url = $_SERVER['REQUEST_URI'];
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="<?php echo $disabled_m; ?>navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand">Project EVIE - <?php echo $title; ?></span>
        </div>
        <div id="navbar" class="navbar-collapse collapse container-fluid">
            <?php
            if (count($_SESSION) !== 0) {
                echo '<ul class="nav navbar-nav hidden-xs">';
                if (!(($url === 'apikeys.php') || empty($_SESSION['user_name']))) {
                    echo '<li><a class="top_bar_button" href="apikeys.php">API Keys</a></li>';
                }
                echo '<li><a class="top_bar_button" href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a></li></ul>';
            }
            if ((strpos($url, 'index.php') === false) && (strpos($url, 'account.php') === false) && (strpos($url, 'apikeys.php') === false)) {
                echo '<ul id="charLinks" class="nav navbar-nav navbar-right hidden-xs"></ul>';
            }
            ?>

            <div class="row visible-xs mobile_nav_box">
                <div class="col-xs-3 visible-xs"><a href="index.php">
                        <img alt="Dashboard" class="img mobile_nav_image" src="icons/charactersheet.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="skills.php">
                        <img alt="Skills" class="img mobile_nav_image" src="icons/skills.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="contacts.php">
                        <img alt="Contacts" class="img mobile_nav_image" src="icons/contacts.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="mail.php">
                        <img alt="Mail" class="img mobile_nav_image" src="icons/evemail.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="market.php">
                        <img alt="Market" class="img mobile_nav_image" src="icons/market.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="wallet.php">
                        <img alt="Wallet" class="img mobile_nav_image" src="icons/wallet.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="assets.php">
                        <img alt="Assets" class="img mobile_nav_image" src="icons/assets.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="planets.php">
                        <img alt="Planets" class="img mobile_nav_image" src="icons/planets.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="industry.php">
                        <img alt="Industry" class="img mobile_nav_image" src="icons/industry.svg"></a>
                </div>
                <div class="col-xs-3 visible-xs"><a href="calendar.php">
                        <img alt="Calendar" class="img mobile_nav_image" src="icons/calendar.svg"></a>
                </div>
                <?php if (!empty($_SESSION['user_name'])) {
                    echo '<div class="col-xs-3 visible-xs">';
                    echo '<a href="apikeys.php">';
                    echo '<img alt="Apikeys" class="img mobile_nav_image" src="icons/other.svg"></a></div>';
                    echo '<div class="col-xs-3 visible-xs"><a href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">';
                    echo '<img alt="Logout" class="img mobile_nav_image" src="icons/lockedcontainer.svg"></a></div>';
                } ?>
            </div>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 col-md-1">
            <div class="sidebar">
                <ul class="nav nav-sidebar">
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Dashboard</strong><br>The main Hub for all your Characters.</div>'
                           href="index.php">
                            <img id="imageDashboard" alt="Dashboard" class="img sidebarimg"
                                 src="icons/charactersheet.svg"></a></li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Skills</strong><br>Admit it, you have 5 million SP in mining.</div>'
                           href="skills.php">
                            <img id="imageSkills" alt="Skills" class="img sidebarimg" src="icons/skills.svg"></a></li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Contacts</strong><br>CCP knows you stalk Spaceship Barbie.</div>'
                           href="contacts.php">
                            <img id="imageContacts" alt="Contacts" class="img sidebarimg" src="icons/contacts.svg"></a>
                    </li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Mail</strong><br>Mostly spam.</div>'
                           href="mail.php">
                            <img id="imageMail" alt="Mail" class="img sidebarimg" src="icons/evemail.svg"></a>
                    </li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Market</strong><br>Info from eve-central.com</div>'
                           href="market.php">
                            <img id="imageMarket" alt="Market" class="img sidebarimg" src="icons/market.svg"></a></li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Wallet</strong><br>Where your sweet sweet ISKies are kept.</div>'
                           href="wallet.php">
                            <img id="imageWallet" alt="Wallet" class="img sidebarimg" src="icons/wallet.svg"></a></li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Assets</strong><br>All your exotic dancers, male.</div>'
                           href="assets.php">
                            <img id="imageAssets" alt="Assets" class="img sidebarimg" src="icons/assets.svg"></a></li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Planets</strong><br>Because strip mining is amazing.</div>'
                           href="planets.php">
                            <img id="imagePlanets" alt="Planets" class="img sidebarimg" src="icons/planets.svg"></a>
                    </li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Industry</strong><br>Building things for profit!</div>'
                           href="industry.php">
                            <img id="imageIndustry" alt="Industry" class="img sidebarimg" src="icons/industry.svg"></a>
                    </li>
                    <li><a class="<?php echo $disabled; ?>sidebar_button EVETime"
                           data-toggle="tooltip" data-html="true"
                           data-placement="right"
                           data-container="body"
                           title='<div class="text-left"><strong>Calendar</strong><br>Yep, you are late!</div>'
                           href="calendar.php">
                            <span id="EVETime_Hours">00</span>:<span id="EVETime_Minutes">00</span></a></li>
                </ul>
            </div>
        </div>

        <div id="mainbody" class="col-sm-10 col-md-11 col-xs-12 main">