
<?php
  $dashboardactive = "";
  $dashboardactivembl = "";
  $skillsactive = ""; 
  $skillsactivembl = "";
  $mailactive = "";
  $mailactivembl = "";
  $marketactive = "";
  $marketactivembl = "";
  $walletactive = "";
  $walletactivembl = "";
  $assetsactive = "";
  $assetsactivembl = "";
  $contactsactive = "";
  $contactsactivembl = "";
  $planetsactive = "";
  $planetsactivembl = "";
  $industryactive = "";
  $industryactivembl = "";
  $calendaractive = "";
  $calendaractivembl = "";
  $settingsactive = "";
  $settingsactivembl = "";             

   switch  ($url) {
    case "index.php":
        $dashboardactive = 'class="active"';
        $dashboardactivembl = 'style="background-color: #404040;"';
        $title = 'Dashboard';
        break;
    case "skills.php":
        $skillsactive = 'class="active"';
        $skillsactivembl = 'style="background-color: #404040;"';
        $title = 'Skills';
        break;
    case "mail.php":
        $mailactive = 'class="active"';
        $mailactivembl = 'style="background-color: #404040;"';
        $title = 'Eve Mail';
        break;
    case "market.php":
        $marketactive = 'class="active"';
        $marketactivembl = 'style="background-color: #404040;"';
        $title = 'Market';
        break;
    case "wallet.php":
        $walletactive = 'class="active"';
        $walletactivembl = 'style="background-color: #404040;"';
        $title = 'Wallet';
        break;
    case "assets.php":
        $assetsactive = 'class="active"';
        $assetsactivembl = 'style="background-color: #404040;"';
        $title = 'Assets';
        break;
    case "contacts.php":
        $contactsactive = 'class="active"';
        $contactsactivembl = 'style="background-color: #404040;"';
        $title = 'Contacts';
        break;
    case "planets.php":
        $planetsactive = 'class="active"';
        $planetsactivembl = 'style="background-color: #404040;"';
        $title = 'Planets';
        break;
    case "industry.php":
        $industryactive = 'class="active"';
        $industryactivembl = 'style="background-color: #404040;"';
        $title = 'Industry';
        break;
    case "calendar.php":
        $calendaractive = 'class="active"';
        $calendaractivembl = 'style="background-color: #404040;"';
        $title = 'Calendar';
        break;
    case "settings.php":
        $settingsactive = 'class="active"';
        $settingsactivembl = 'style="background-color: #404040;"';
        $title = 'Settings';
        break;
    case "account.php":
        //$accountactive = 'class="active"';
        //$accountactivembl = 'style="background-color: #404040;"';
        $title = 'Account';
        break;
    case "apikeys.php":
        //$accountactive = 'class="active"';
        //$accountactivembl = 'style="background-color: #404040;"';
        //$title = 'Account';
        break;
    default:
        $title = 'Unknown';
        break;
}

  if(isset ($_GET['char'])){
    if(!empty($_GET)){ 
      $selectedChar = $_GET['char'];
      if($selectedChar > 2){
         $selectedChar = 0;
         header("Location: /eve/index.php?char=0");
         die(); 
      }
      else if ($selectedChar == "") {
         $selectedChar = 0;
         header("Location: /eve/index.php?char=0");
         die();
      }
    }
    else{
       $selectedChar = 0;
       header("Location: /eve/index.php?char=0");
       die(); 
    }
  }
  else{
       $selectedChar = 0;
       header("Location: /eve/index.php?char=0");
       die(); 
  }
$charactivecss = '"padding-top: 12px; max-height: 50px; background-color: #404040; border-top: 3px solid #337ab7;"'; 
$charinactivecss = '"max-height: 50px; visibility: hidden;"';
$settingscss = '"max-height: 50px; background-color: #404040; border-top: 3px solid #337ab7;"'; 
$char0active = $charinactivecss;
$char1active = $charinactivecss;
$char2active = $charinactivecss; 
    
switch ($selectedChar) {
    case 0:
        $char0active = $charactivecss;
        break;
    case 1:
        $char1active = $charactivecss;
        break;
    case 2:
        $char2active = $charactivecss;
        break;
}

?>

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <span class="navbar-brand" href="#">Project EVIE - <?php echo $title; ?></span> </div>
    <div id="navbar" class="navbar-collapse collapse container-fluid">
    <?php if (!empty($_SESSION)){
      echo '<ul class="nav navbar-nav hidden-xs" style="max-height: 50px;">';
      if(($url != "apikeys.php") && (!empty($_SESSION['user_name']))){
      echo '<li><a href="apikeys.php?char='.$selectedChar.'">API Keys</a></li>';
      }
      echo '<li><a href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a></li>';
      echo '</ul>';
    } ?>
      <ul class="nav navbar-nav navbar-right hidden-xs" style="max-height: 50px;">
      <?php 
      $url = get_string_between($_SERVER['REQUEST_URI'], '/eve/', '?');
      if(($url != "index.php") && ($url != "account.php") && ($url != "apikeys.php")){
       echo' 
        <li><a ID="charLink0" style='.$char0active.' href="?char=0"><img alt="char0" id="char0" style="max-height: 50px" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>
        <li><a ID="charLink1" style='.$char1active.' href="?char=1"><img alt="char1" id="char1" style="max-height: 50px" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>
        <li><a ID="charLink2" style='.$char2active.' href="?char=2"><img alt="char2" id="char2" style="max-height: 50px" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>
      ';}?>

      </ul> 
         <div class="row visible-xs" style="border-bottom: 3px solid #337ab7;">
            <div <?php echo $dashboardactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"index.php?char='.$selectedChar.'"'; ?>><img alt="Dashboard" style="width: 100%;" class="img" src="icons/charactersheet.svg"></a></div>
            <div <?php echo $skillsactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"skills.php?char='.$selectedChar.'"'; ?>><img alt="Skills" style="width: 100%;" class="img" src="icons/skills.svg"></a></div>
            <div <?php echo $mailactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"mail.php?char='.$selectedChar.'"'; ?>><img alt="Mail" style="width: 100%;" class="img" src="icons/evemail.svg"></a></div>
            <div <?php echo $marketactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"market.php?char='.$selectedChar.'"'; ?>><img alt="Market" style="width: 100%;" class="img" src="icons/market.svg"></a></div>
            <div <?php echo $walletactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"wallet.php?char='.$selectedChar.'"'; ?>><img alt="Wallet" style="width: 100%;" class="img" src="icons/wallet.svg"></a></div>
            <div <?php echo $assetsactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"assets.php?char='.$selectedChar.'"'; ?>><img alt="Assets" style="width: 100%;" class="img" src="icons/assets.svg"></a></div>
            <div <?php echo $contactsactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"contacts.php?char='.$selectedChar.'"'; ?>><img alt="Contacts" style="width: 100%;" class="img" src="icons/contacts.svg"></a></div>
            <div <?php echo $planetsactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"planets.php?char='.$selectedChar.'"'; ?>><img alt="Planets" style="width: 100%;" class="img" src="icons/planets.svg"></a></div>
            <div <?php echo $industryactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"industry.php?char='.$selectedChar.'"'; ?>><img alt="Industry" style="width: 100%;" class="img" src="icons/industry.svg"></a></div>
            <div <?php echo $calendaractivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"calendar.php?char='.$selectedChar.'"'; ?>><img alt="Calendar" style="width: 100%;" class="img" src="icons/calendar.svg"></a></div>
            <div <?php echo $settingsactivembl ?> class="col-xs-3 visible-xs"><a href=<?php echo '"apikeys.php?char='.$selectedChar.'"'; ?>><img alt="Settings" style="width: 100%;" class="img" src="icons/settings.svg"></a></div>
        </div>
    </div>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">  
    <div class="col-sm-2 col-md-1">
      <div class="sidebar">
        <ul class="nav nav-sidebar">
          <li <?php echo $dashboardactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Dashboard</strong><br>The main Hub for all your Characters.</div>' href=<?php echo '"index.php?char='.$selectedChar.'"'; ?>><img id="imageDashboard" alt="Dashboard" style="width: 100%;" class="img" src="icons/charactersheet.svg"></a></li>
          <li <?php echo $skillsactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Skills</strong><br>Admit it, you have 5 million SP in mining.</div>' href=<?php echo '"skills.php?char='.$selectedChar.'"'; ?>><img id="imageSkills" alt="Skills" style="width: 100%;" class="img" src="icons/skills.svg"></a></li>
          <li <?php echo $mailactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Mail</strong><br>Mostly spam.</div>' href=<?php echo '"mail.php?char='.$selectedChar.'"'; ?>><img id="imageMail" alt="Mail" style="width: 100%;" class="img" src="icons/evemail.svg"></a></li>
          <li <?php echo $marketactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Market</strong><br>Info from eve-central.com</div>' href=<?php echo '"market.php?char='.$selectedChar.'"'; ?>><img id="imageMarket" alt="Market" style="width: 100%;" class="img" src="icons/market.svg"></a></li>
          <li <?php echo $walletactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Wallet</strong><br>Where your sweet sweet ISKies are kept.</div>' href=<?php echo '"wallet.php?char='.$selectedChar.'"'; ?>><img id="imageWallet" alt="Wallet" style="width: 100%;" class="img" src="icons/wallet.svg"></a></li>
          <li <?php echo $assetsactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Assets</strong><br>All your exotic dancers, male.</div>' href=<?php echo '"assets.php?char='.$selectedChar.'"'; ?>><img id="imageAssets" alt="Assets" style="width: 100%;" class="img" src="icons/assets.svg"></a></li>
          <li <?php echo $contactsactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Contacts</strong><br>CCP knows you stalk Spaceship Barbie.</div>' href=<?php echo '"contacts.php?char='.$selectedChar.'"'; ?>><img id="imageContacts" alt="Contacts" style="width: 100%;" class="img" src="icons/contacts.svg"></a></li>
          <li <?php echo $planetsactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Planets</strong><br>Because strip mining is amazing.</div>' href=<?php echo '"planets.php?char='.$selectedChar.'"'; ?>><img id="imagePlanets" alt="Planets" style="width: 100%;" class="img" src="icons/planets.svg"></a></li>
          <li <?php echo $industryactive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Industry</strong><br>Building things for profit!</div>' href=<?php echo '"industry.php?char='.$selectedChar.'"'; ?>><img id="imageIndustry" alt="Industry" style="width: 100%;" class="img" src="icons/industry.svg"></a></li>
          <li <?php echo $calendaractive ?>><a data-toggle="tooltip" data-html="true" data-placement="right" data-container="body"  title='<div class="text-left"><strong>Calendar</strong><br>You forgot that birthday, again!</div>' href=<?php echo '"calendar.php?char='.$selectedChar.'"'; ?>><img id="imageCalendar" alt="Calendar" style="width: 100%;" class="img" src="icons/calendar.svg"></a></li>
        </ul>
      </div> 
    </div>
    
    <div id="mainbody" class="col-sm-10 col-md-11 col-xs-12 main">