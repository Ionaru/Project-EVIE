(function () {
    var getItemName, getPlanetInstallations, getPlanets, getTimeRemaining;

    getPlanets = function (keyID, vCode, selectedCharacterID) {
        var request;
        request = new XMLHttpRequest;
        request.onreadystatechange = function () {
            var i, len, numberOfPins, planetID, planetName, planetTypeID, planetTypeName, row, rows, solarSystemName, src, upgradeLevel, xml;
            if (request.readyState === 4 && request.status === 200) {
                xml = request.responseXML;
                rows = xml.getElementsByTagName('row');
                for (i = 0, len = rows.length; i < len; i++) {
                    row = rows[i];
                    solarSystemName = row.getAttribute('solarSystemName');
                    planetName = row.getAttribute('planetName');
                    planetID = row.getAttribute('planetID');
                    planetTypeID = row.getAttribute('planetTypeID');
                    planetTypeName = row.getAttribute('planetTypeName');
                    upgradeLevel = row.getAttribute('upgradeLevel');
                    numberOfPins = row.getAttribute('numberOfPins');
                    src = 'items/' + planetTypeID + '_64.png';
                    $('#planetsNew').append('<div class="planetcontainer col-sm-12 col-md-12"><div id="planetDivNew' + planetID + '" class="planet"><span class="floating-box"><img class="img img-circle planetimg" src="' + src + '" alt="PlanetImg' + planetID + '" id="planetImgNew' + planetID + '">' + planetName + planetTypeName.replace('Planet', '') + '</span><span class="floating-box">Command Center Level ' + upgradeLevel + '</span><span class="floating-box" id="installations' + planetID + '">' + numberOfPins + ' Installations</span><span class="floating-box" id="harvesterActive' + planetID + 'New"></span><span class="floating-box text-right"><a class="btn btn-primary btn-xl">Expand</a></span></div></div>');
                    $('#planets').append('<div class="planetcontainer text-center col-sm-6 col-md-4"><div id="planetDiv' + planetID + '" class="planet"><img class="img img-circle planetimg" src="' + src + '" alt="PlanetImg' + planetID + '" id="planetImg' + planetID + '"><p>' + planetName + planetTypeName.replace('Planet', '') + '<br>Command Center Level ' + upgradeLevel + '<br>' + numberOfPins + ' Installations</p></div></div>');
                    getPlanetInstallations(keyID, vCode, selectedCharacterID, planetID);
                }
            }
        };
        request.open('GET', 'https://api.eveonline.com/char/PlanetaryColonies.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + selectedCharacterID, true);
        request.send();
    };

    getPlanetInstallations = function (keyID, vCode, selectedCharacterID, planetID) {
        var request;
        request = new XMLHttpRequest;
        request.onreadystatechange = function () {
            var activeHarvesters, contentQuantity, currentTime, expiryTime, i2, pinID, quantityPerCycle, row, rows, schematicID, totalHarvesters, typeID, typeName, xml;
            if (request.readyState === 4 && request.status === 200) {
                xml = request.responseXML;
                rows = xml.getElementsByTagName('row');
                currentTime = xml.getElementsByTagName('currentTime')[0].childNodes[0].nodeValue;
                totalHarvesters = 0;
                i2 = 0;
                while (i2 < rows.length) {
                    activeHarvesters = 0;
                    row = rows[i2];
                    pinID = row.getAttribute('pinID');
                    typeName = row.getAttribute('typeName');
                    typeID = row.getAttribute('typeID');
                    schematicID = row.getAttribute('schematicID');
                    expiryTime = row.getAttribute('expiryTime');
                    quantityPerCycle = row.getAttribute('quantityPerCycle');
                    contentQuantity = row.getAttribute('contentQuantity');
                    if (!(typeName.indexOf('Command Center') > -1)) {
                        $('#planetDiv' + planetID).append(typeName + '<br>');
                    }
                    if (expiryTime !== '0001-01-01 00:00:00') {
                        totalHarvesters++;
                        if (expiryTime > currentTime) {
                            $('#planetDiv' + planetID).append('Harvesting cycle finishes in:<br><span id="Timeleft' + pinID + '"></span><br>');
                            getTimeRemaining(currentTime, expiryTime, pinID);
                            activeHarvesters++;
                            $('#harvesterActive' + planetID + 'New').html('Harvesters active!');
                        } else {
                            $('#planetDiv' + planetID).append('Harvester inactive.<br>');
                            $('#harvesterActive' + planetID + 'New').html('No active harvesters.');
                        }
                    }
                    if (activeHarvesters === 0) {

                    } else {

                    }
                    i2++;
                }
                $('#installations' + planetID).append(', ' + totalHarvesters + ' ' + Pluralize('Harvester', 'Harvesters', totalHarvesters));
            }
        };
        request.open('GET', 'https://api.eveonline.com/char/PlanetaryPins.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + selectedCharacterID + '&planetID=' + planetID, true);
        request.send();
    };

    getTimeRemaining = function (nowDate, endDate, i) {
        var end, now, showRemaining, timer;
        now = Date.parse(nowDate.replace(/\-/ig, '/').split('.')[0]);
        end = Date.parse(endDate.replace(/\-/ig, '/').split('.')[0]);
        timer = void 0;
        showRemaining = function () {
            var output;
            now = now + 1000;
            output = parseTimeRemaining(now, end);
            $('#Timeleft' + i).html(output + '<br>');
        };
        $('#Timeleft' + i).html('Calculating Time...<br>');
        timer.setInterval(showRemaining, 1000);
    };

    getItemName = function (typeID, level) {
        var request2;
        request2 = new XMLHttpRequest;
        request2.onreadystatechange = function () {
            var i2, row, rows, skillName, xml2;
            if (request2.readyState === 4 && request2.status === 200) {
                xml2 = request2.responseXML;
                rows = xml2.getElementsByTagName('row');
                i2 = 0;
                while (i2 < rows.length) {
                    row = rows[i2];
                    skillName = row.getAttribute('typeName');
                    i2++;
                }
                $('#skill' + typeID).html(skillName + ' ' + level);
            }
        };
        request2.open('GET', 'https://api.eveonline.com/eve/TypeName.xml.aspx?ids=' + typeID, true);
        request2.send();
    };

    $(document).ready(function () {
        var charIDs, charRequest;
        charIDs = [];
        charRequest = new XMLHttpRequest;
        charRequest.onreadystatechange = function () {
            var charID, i, row, rows, xml;
            if (charRequest.readyState === 4 && charRequest.status === 200) {
                xml = charRequest.responseXML;
                rows = xml.getElementsByTagName('row');
                i = 0;
                while (i < rows.length) {
                    row = rows[i];
                    charID = row.getAttribute('characterID');
                    charIDs[i] = charID;
                    i++;
                }
                i = 0;
                while (i < charIDs.length) {
                    var css = "characterInactive";
                    if (i == selectedCharacter) {
                        css = "characterActive";
                        selectedCharacterID = charIDs[i];
                    }
                    $('#charLinks').css('visibility', 'visible').append('<li><a id="charLink' + i + '" class="' + css + '" href="?char=' + i + '"><img alt="char' + i + '" id="char' + i + '" style="max-height: 50px" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>');
                    $('#char' + i).css('visibility', 'visible').attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg');
                    $('#charmbl' + i).css('visibility', 'visible').attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg');
                    i++;
                }
                getPlanets(keyID, vCode, selectedCharacterID);
            }
        };
        charRequest.open('GET', 'https://api.eveonline.com/account/Characters.xml.aspx?keyID=' + keyID + '&vCode=' + vCode, true);
        charRequest.send();
    });

}).call(this);