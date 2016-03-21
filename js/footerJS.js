$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

$(function(){
    $('a').each(function() {
        if ($(this).prop('href') == window.location.href) {
            $(this).addClass('nav_active');
            $(this).parent().addClass('nav_active');
        }
    });
});

var currentTime;
var serverOpen = "False";
var onlinePlayers = 0;
var keyID;
var vCode;
var selectedCharacter;
var selectedCharacterID;
var charIDs = [];
var charNames = [];
var hours;
var minutes;
var seconds;

jQuery(document).ready(function () {
    $('#browser').append(window.navigator.userAgent);
    if ((window.navigator.userAgent.indexOf("MSIE") > -1) || (window.navigator.userAgent.indexOf("Trident") > -1)) {
        $("#imageDashboard").attr('src', 'icons/charactersheet.png');
        $("#imageSkills").attr('src', 'icons/skills.png');
        $("#imageMail").attr('src', 'icons/evemail.png');
        $("#imageMarket").attr('src', 'icons/market.png');
        $("#imageWallet").attr('src', 'icons/wallet.png');
        $("#imageAssets").attr('src', 'icons/assets.png');
        $("#imageContacts").attr('src', 'icons/contacts.png');
        $("#imagePlanets").attr('src', 'icons/planets.png');
        $("#imageIndustry").attr('src', 'icons/industry.png');
        $("#imageCalendar").attr('src', 'icons/calendar.png');
        $("#imageSettings").attr('src', 'icons/settings.png');
    }
    var characterIDs = $.Deferred();
    var serverStatus = $.Deferred();

    doCharInit();
    getServerStatus();

    $.when( characterIDs, serverStatus ).done(function () {
        executePage();
    });

    function doCharInit(){
        if (document.getElementById("passAlong_keyID") !== null) {
            keyID = document.getElementById("passAlong_keyID").value;
            vCode = document.getElementById("passAlong_vCode").value;
            selectedCharacter = document.getElementById("passAlong_selectedCharacter").value;
            if($.totalStorage('charIDs_' + keyID) == null) {
                $.ajax({
                    url: "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode,
                    error: function (xhr, status, error) {
                        showError("Character Init", xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        var rows = xml.getElementsByTagName("row");
                        for (var i = 0; i < rows.length; i++) {
                            var row = rows[i];
                            charIDs[i] = row.getAttribute("characterID");
                            charNames[i] = row.getAttribute("name");
                        }
                        $.totalStorage('charIDs_' + keyID, charIDs);
                        $.totalStorage('charNames_' + keyID, charNames);
                        processChar(charIDs);
                        characterIDs.resolve();
                    }
                });
            }
            else {
                charIDs = $.totalStorage('charIDs_' + keyID);
                charNames = $.totalStorage('charNames_' + keyID);
                processChar(charIDs);
                characterIDs.resolve();
            }
        }
    }

    function processChar(charIDs){
        for (var i = 0; i < charIDs.length; i++) {
            var css = "characterInactive";
            if (i == selectedCharacter) {
                css = "characterActive";
                selectedCharacterID = charIDs[i];
            }
            $('#charLinks').css('visibility', 'visible').append('<li><a id="charLink' + i + '" class="' + css + '" href="?c=' + i + '""><img alt="char' + i + '" id="char' + i + '" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>');
            $('#char' + i).css('visibility', 'visible').attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg');
            $('#charmbl' + i).css('visibility', 'visible').attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg');
        }
    }

    function getServerStatus(){
        $.ajax({
            url: "https://api.eveonline.com/server/ServerStatus.xml.aspx",
            error: function (xhr, status, error) {
                showError("Server Status", xhr, status, error);
                // TODO: implement fancy error logging
            },
            success: function (xml) {
                currentTime = xml.getElementsByTagName('currentTime')[0].childNodes[0].nodeValue;
                serverOpen = xml.getElementsByTagName('serverOpen')[0].childNodes[0].nodeValue;
                onlinePlayers = xml.getElementsByTagName('onlinePlayers')[0].childNodes[0].nodeValue;
                hours = parseInt(currentTime.slice(-8, -6));
                minutes = parseInt(currentTime.slice(-5, -3));
                seconds = parseInt(currentTime.slice(-2));
                displayTime(hours, minutes);
                setTimeout(function(){
                    timeKeeper(hours, minutes, seconds);
                }, 60000 - (seconds * 1000));
                serverStatus.resolve();
            }
        });
    }
});

function timeKeeper(){
    doTimeTick();
    function doTimeTick(){
        minutes = parseInt(minutes);
        minutes += 1;
        displayTime(hours, minutes);
    }
    var timer = setInterval(doTimeTick, 60000);
}

function displayTime(h, m){
    hours = parseInt(h);
    minutes = parseInt(m);
    if(minutes == 60){
        hours += 1;
        minutes = 0;
    }
    if(minutes < 10){
        minutes = "0" + minutes;
    }
    if(hours == 24){
        hours = 0;
    }
    if(hours < 10){
        hours = "0" + hours;
    }
    $('#EVETime_Hours').html(hours);
    $('#EVETime_Minutes').html(minutes);
}

//Get Character ID from a name
function getCharData(charName) {
    $.ajax({
        url: "https://api.eveonline.com/eve/CharacterID.xml.aspx?names=" + charName,
        error: function (xhr, status, error) {
            showError("Character Data", xhr, status, error);
            // TODO: implement fancy error logging
        },
        success: function (xml) {
            var rows = xml.getElementsByTagName("row");
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var charID = row.getAttribute("characterID");
            }
            if (window.navigator.userAgent.indexOf("EVE-IGB") == -1) {
                getCharDataFromID(charID);
            }
            else {
                //noinspection JSUnresolvedVariable,JSUnresolvedFunction
                CCPEVE.showInfo(1377, charID);
            }
        }
    });
}

//Get character info from ID
function getCharDataFromID(charID) {
    if (window.navigator.userAgent.indexOf("EVE-IGB") == -1) {
        if(parseInt(charID).between(90000000, 100000000, true)) {
            $.ajax({
                url: "https://api.eveonline.com/eve/CharacterInfo.xml.aspx?characterID=" + charID,
                error: function (xhr, status, error) {
                    showError("Character Info", xhr, status, error);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    parseCharacterData(xml, charID)
                }
            });
        }
        else {
            $.ajax({
                url: "https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=" + charID,
                error: function (xhr, status, error) {
                    showError("Character Info", xhr, status, error);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    parseMiscCharacterData(xml, charID)
                }
            });
        }
    }
    else {
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        CCPEVE.showInfo(1377, charID);
    }
}

function parseCharacterData(xml, charID){
    var charName;
    var charRace;
    var bloodline;
    var ancestry;
    var corpName;
    var corpDate;
    var allianceName;
    var allianceDate;
    var securityStatus;
    charName = xml.getElementsByTagName("characterName")[0].childNodes[0].nodeValue;
    charRace = xml.getElementsByTagName("race")[0].childNodes[0].nodeValue;
    bloodline = xml.getElementsByTagName("bloodline")[0].childNodes[0].nodeValue;
    ancestry = xml.getElementsByTagName("ancestry")[0].childNodes[0].nodeValue;
    corpName = xml.getElementsByTagName("corporation")[0].childNodes[0].nodeValue;
    corpDate = xml.getElementsByTagName("corporationDate")[0].childNodes[0].nodeValue;
    allianceName = "";
    if (xml.getElementsByTagName("alliance")[0] != null) {
        allianceName = xml.getElementsByTagName("alliance")[0].childNodes[0].nodeValue;
        allianceDate = xml.getElementsByTagName("allianceDate")[0].childNodes[0].nodeValue;
    }
    securityStatus = xml.getElementsByTagName("securityStatus")[0].childNodes[0].nodeValue;
    $("#characterModalTitle").html(charName);
    $("#characterInfoImage").attr('src', 'https://image.eveonline.com/Character/' + charID + '_256.jpg');
    var charInfo = '<p><strong>Race:</strong> ' + charRace + ' - ' + bloodline + ' - ' + ancestry + '</p><p><strong>Corporation:</strong> ' + corpName + ', joined ' + '<time class="timeago" datetime="' + corpDate + '">' + corpDate + '</time>' + '</p>';

    if (allianceName != "") {
        charInfo += '<p><strong>Alliance:</strong> ' + allianceName + '</p>';
    }
    charInfo += '<p><strong>Security Status:</strong> ' + securityStatus + '</p><p><i>More info will come here</i></p>';
    $("#characterinfo").html(charInfo);
    jQuery("time.timeago").timeago();
    $('#characterModal').modal('show');
}

function parseMiscCharacterData(xml, charID) {
    var rows = xml.getElementsByTagName("row");
    var row = rows[0];
    var charName = row.getAttribute("characterName");
    var corpName = "";
    if (row.getAttribute("corporationID") != 0) {
        corpName = row.getAttribute("corporationName");
    }
    var allianceName = "";
    if (row.getAttribute("allianceID")[0] != 0) {
        allianceName = row.getAttribute("allianceName");
    }
    $("#characterModalTitle").html(charName);
    $("#characterInfoImage").attr('src', 'https://image.eveonline.com/Character/' + charID + '_256.jpg');
    var charInfo = "";
    if (corpName != "") {
        charInfo += '<p><strong>Corporation:</strong> ' + corpName + '</p>';
    }
    if (allianceName != "") {
        charInfo += '<p><strong>Alliance:</strong> ' + allianceName + '</p>';
    }
    $("#characterinfo").html(charInfo);
    $('#characterModal').modal('show');
}


//Show item information
function getItemData(itemID) {
    if (window.navigator.userAgent.indexOf("EVE-IGB") == -1) {

    }
    else {
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        CCPEVE.showInfo(itemID);
    }
}