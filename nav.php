<?php
if (isset ($_GET['char'])) {
    if (count($_GET) !== 0) {
        $selectedChar = $_GET['char'];
        if ($selectedChar > 2) {
            $selectedChar = 0;
            header('Location: index.php?char=0');
            die();
        } else if ($selectedChar === '') {
            $selectedChar = 0;
            header('Location: index.php?char=0');
            die();
        }
    } else {
        $selectedChar = 0;
        header('Location: index.php?char=0');
        die();
    }
} else {
    $selectedChar = 0;
    header('Location: index.php?char=0');
    die();
}
$settingscss = '"max-height: 50px; background-color: #404040; border-top: 3px solid #337ab7;"';
$url = $_SERVER['REQUEST_URI'];
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar"><span class="sr-only">Toggle navigation</span> <span
                    class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button>
            <span class="navbar-brand">Project EVIE - <?php echo $title; ?></span></div>
        <div id="navbar" class="navbar-collapse collapse container-fluid">
            <?php if (count($_SESSION) !== 0) {
                echo '<ul class="nav navbar-nav hidden-xs" style="max-height: 50px;">';
                if (($url !== 'apikeys.php') && (!empty($_SESSION['user_name']))) {
                    echo '<li><a class="top_bar_button'. $apikeysactive .'" href="apikeys.php?char=' . $selectedChar . '">API Keys</a></li>';
                }
                echo '<li><a class="top_bar_button" href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a></li>';
                echo '</ul>';
            }
            if ((strpos($url, 'index.php') === false) && (strpos($url, 'account.php') === false) && (strpos($url, 'apikeys.php') === false)) {
                echo '
                <ul id="charLinks" class="nav navbar-nav navbar-right hidden-xs"></ul>';
                }
            ?>

            <div class="row visible-xs" style="border-bottom: 3px solid #337ab7;">
                <div <?php echo $dashboardactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"index.php?char=' . $selectedChar . '"'; ?>><img alt="Dashboard"
                                                                                          style="width: 100%;"
                                                                                          class="img"
                                                                                          src="icons/charactersheet.svg"></a>
                </div>
                <div <?php echo $skillsactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"skills.php?char=' . $selectedChar . '"'; ?>><img alt="Skills"
                                                                                           style="width: 100%;"
                                                                                           class="img"
                                                                                           src="icons/skills.svg"></a>
                </div>
                <div <?php echo $mailactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"mail.php?char=' . $selectedChar . '"'; ?>><img alt="Mail" style="width: 100%;"
                                                                                         class="img"
                                                                                         src="icons/evemail.svg"></a>
                </div>
                <div <?php echo $marketactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"market.php?char=' . $selectedChar . '"'; ?>><img alt="Market"
                                                                                           style="width: 100%;"
                                                                                           class="img"
                                                                                           src="icons/market.svg"></a>
                </div>
                <div <?php echo $walletactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"wallet.php?char=' . $selectedChar . '"'; ?>><img alt="Wallet"
                                                                                           style="width: 100%;"
                                                                                           class="img"
                                                                                           src="icons/wallet.svg"></a>
                </div>
                <div <?php echo $assetsactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"assets.php?char=' . $selectedChar . '"'; ?>><img alt="Assets"
                                                                                           style="width: 100%;"
                                                                                           class="img"
                                                                                           src="icons/assets.svg"></a>
                </div>
                <div <?php echo $contactsactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"contacts.php?char=' . $selectedChar . '"'; ?>><img alt="Contacts"
                                                                                             style="width: 100%;"
                                                                                             class="img"
                                                                                             src="icons/contacts.svg"></a>
                </div>
                <div <?php echo $planetsactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"planets.php?char=' . $selectedChar . '"'; ?>><img alt="Planets"
                                                                                            style="width: 100%;"
                                                                                            class="img"
                                                                                            src="icons/planets.svg"></a>
                </div>
                <div <?php echo $industryactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"industry.php?char=' . $selectedChar . '"'; ?>><img alt="Industry"
                                                                                             style="width: 100%;"
                                                                                             class="img"
                                                                                             src="icons/industry.svg"></a>
                </div>
                <div <?php echo $calendaractivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"calendar.php?char=' . $selectedChar . '"'; ?>><img alt="Calendar"
                                                                                             style="width: 100%;"
                                                                                             class="img"
                                                                                             src="icons/calendar.svg"></a>
                </div>
                <div <?php echo $settingsactivembl ?> class="col-xs-3 visible-xs"><a
                        href=<?php echo '"apikeys.php?char=' . $selectedChar . '"'; ?>><img alt="Settings"
                                                                                            style="width: 100%;"
                                                                                            class="img"
                                                                                            src="icons/settings.svg"></a>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2 col-md-1">
            <div class="sidebar">
                <ul class="nav nav-sidebar">
                    <li <?php echo $dashboardactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                          data-container="body"
                                                          title='<div class="text-left"><strong>Dashboard</strong><br>The main Hub for all your Characters.</div>'
                                                          href=<?php echo '"index.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageDashboard" alt="Dashboard" class="img sidebarimg"
                                src="icons/charactersheet.svg"></a></li>
                    <li <?php echo $skillsactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                       data-container="body"
                                                       title='<div class="text-left"><strong>Skills</strong><br>Admit it, you have 5 million SP in mining.</div>'
                                                       href=<?php echo '"skills.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageSkills" alt="Skills" class="img sidebarimg"
                                src="icons/skills.svg"></a></li>
                    <li <?php echo $mailactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                     data-container="body"
                                                     title='<div class="text-left"><strong>Mail</strong><br>Mostly spam.</div>'
                                                     href=<?php echo '"mail.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageMail" alt="Mail" class="img sidebarimg" src="icons/evemail.svg"></a>
                    </li>
                    <li <?php echo $marketactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                       data-container="body"
                                                       title='<div class="text-left"><strong>Market</strong><br>Info from eve-central.com</div>'
                                                       href=<?php echo '"market.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageMarket" alt="Market" class="img sidebarimg"
                                src="icons/market.svg"></a></li>
                    <li <?php echo $walletactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                       data-container="body"
                                                       title='<div class="text-left"><strong>Wallet</strong><br>Where your sweet sweet ISKies are kept.</div>'
                                                       href=<?php echo '"wallet.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageWallet" alt="Wallet" class="img sidebarimg"
                                src="icons/wallet.svg"></a></li>
                    <li <?php echo $assetsactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                       data-container="body"
                                                       title='<div class="text-left"><strong>Assets</strong><br>All your exotic dancers, male.</div>'
                                                       href=<?php echo '"assets.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageAssets" alt="Assets" class="img sidebarimg"
                                src="icons/assets.svg"></a></li>
                    <li <?php echo $contactsactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                         data-container="body"
                                                         title='<div class="text-left"><strong>Contacts</strong><br>CCP knows you stalk Spaceship Barbie.</div>'
                                                         href=<?php echo '"contacts.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageContacts" alt="Contacts" class="img sidebarimg"
                                src="icons/contacts.svg"></a></li>
                    <li <?php echo $planetsactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                        data-container="body"
                                                        title='<div class="text-left"><strong>Planets</strong><br>Because strip mining is amazing.</div>'
                                                        href=<?php echo '"planets.php?char=' . $selectedChar . '"'; ?>><img
                                id="imagePlanets" alt="Planets" class="img sidebarimg"
                                src="icons/planets.svg"></a></li>
                    <li <?php echo $industryactive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                         data-container="body"
                                                         title='<div class="text-left"><strong>Industry</strong><br>Building things for profit!</div>'
                                                         href=<?php echo '"industry.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageIndustry" alt="Industry" class="img sidebarimg"
                                src="icons/industry.svg"></a></li>
                    <li <?php echo $calendaractive ?>><a class="sidebar_button" data-toggle="tooltip" data-html="true" data-placement="right"
                                                         data-container="body"
                                                         title='<div class="text-left"><strong>Calendar</strong><br>You forgot that birthday, again!</div>'
                                                         href=<?php echo '"calendar.php?char=' . $selectedChar . '"'; ?>><img
                                id="imageCalendar" alt="Calendar" class="img sidebarimg"
                                src="icons/calendar.svg"></a></li>
                </ul>
            </div>
        </div>

        <div id="mainbody" class="col-sm-10 col-md-11 col-xs-12 main">