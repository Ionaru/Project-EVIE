if (document.getElementById("passAlong_keyID") !== null) {
    var keyID = document.getElementById("passAlong_keyID").value;
    var vCode = document.getElementById("passAlong_vCode").value;
    var selectedCharacter = document.getElementById("passAlong_selectedCharacter").value;
    var selectedCharacterID;
}

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

//Fix the icons in case people are viewing this in Internet Explorer
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
});

//Get Character ID from a name
function getCharData(charName) {
    $.ajax({
        url: "https://api.eveonline.com/eve/CharacterID.xml.aspx?names=" + charName,
        error: function (xhr, status, error) {
            showError("Character ID");
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
        $.ajax({
            url: "https://api.eveonline.com/eve/CharacterInfo.xml.aspx?characterID=" + charID,
            error: function (xhr, status, error) {
                showError("Character ID");
                // TODO: implement fancy error logging
            },
            success: function (xml) {
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
        });
    }
    else {
        //noinspection JSUnresolvedVariable,JSUnresolvedFunction
        CCPEVE.showInfo(1377, charID);
    }
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