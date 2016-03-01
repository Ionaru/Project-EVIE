(function () {
    var getPlanetInstallations, getPlanets;

    getPlanets = function (keyID, vCode, selectedCharacterID) {
        $.ajax({
            url: 'https://api.eveonline.com/char/PlanetaryColonies.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + selectedCharacterID,
            error: function (xhr, status, error) {
                showError("Planet Data");
                // TODO: implement fancy error logging
            },
            success: function (xml) {
                var i, len, numberOfPins, planetID, planetName, planetTypeID, planetTypeName, row, rows, solarSystemName, src, upgradeLevel;
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
        });
    };

    getPlanetInstallations = function (keyID, vCode, selectedCharacterID, planetID) {
        $.ajax({
            url: 'https://api.eveonline.com/char/PlanetaryPins.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + selectedCharacterID + '&planetID=' + planetID,
            error: function (xhr, status, error) {
                showError("Planetary Installations");
                // TODO: implement fancy error logging
            },
            success: function (xml) {
                var activeHarvesters, contentQuantity, currentTime, expiryTime, i, pinID, quantityPerCycle, row, rows, schematicID, totalHarvesters, typeID, typeName;
                rows = xml.getElementsByTagName('row');
                currentTime = xml.getElementsByTagName('currentTime')[0].childNodes[0].nodeValue;
                totalHarvesters = 0;
                for (i = 0; i < rows.length; i++) {
                    activeHarvesters = 0;
                    row = rows[i];
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
                            parseTimeRemaining(currentTime, expiryTime, "#Timeleft" + pinID, true, "Harvesting cycle finished");
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
                }
                $('#installations' + planetID).append(', ' + totalHarvesters + ' ' + Pluralize('Harvester', 'Harvesters', totalHarvesters));
            }
        });
    };

    $(document).ready(function () {
        getPlanets(keyID, vCode, selectedCharacterID);
    });

}).call(this);