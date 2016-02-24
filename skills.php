<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>

    <div id="CurrentlyTraining" class="container-fluid">

    </div>
    <div id="1234" class="container-fluid">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Skills</a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse in">
                    <div id="5555" class="panel-body">
                        <div id="skilllist"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/foot.php'; ?>
    <script>
        $(document).ready(function () {
            var charIDs = [];
            var charRequest = new XMLHttpRequest();
            charRequest.onreadystatechange = function () {
                if (charRequest.readyState == 4 && charRequest.status == 200) {
                    var xml = charRequest.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        charID = row.getAttribute("characterID");
                        charIDs[i] = charID;
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
                    getSkillInTraining(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    //getSkillQueue(keyID, vCode, charIDs, <?php //echo $selectedChar ?>);
                    getAllSkills(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    //getTypeNames("Skill", typeIDs);
                }
            };
            charRequest.open("GET", "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode, true);
            charRequest.send();
        });

        function getSkillTimeRemaining(startDate, nowDate, endDate, i) {
            var start = Date.parse(startDate.replace(/\-/ig, '/').split('.')[0]);
            var now = Date.parse(nowDate.replace(/\-/ig, '/').split('.')[0]);
            var end = Date.parse(endDate.replace(/\-/ig, '/').split('.')[0]);
            //var end = now + 3655000;

            var _second = 1000;
            var _minute = _second * 60;
            var _hour = _minute * 60;
            var _day = _hour * 24;
            var timer;
            $('#CurrentlyTraining').append('<p id="countdown">Calculating Time...</p>');

            function showRemaining() {
                //var now = new Date();
                //console.log(now);
                now = now + 1000;
                var distance = end - now;
                if (distance < 1) {

                    clearInterval(timer);
                    $('#countdown').html("Skill training completed!");

                    return;
                }
                var days = Math.floor(distance / _day);
                var hours = Math.floor((distance % _day) / _hour);
                var minutes = Math.floor((distance % _hour) / _minute);
                var seconds = Math.floor((distance % _minute) / _second);
                //console.log(minutes);
                //console.log(seconds);
                var output = "";
                if (days > 0) {
                    if (days == 1) {
                        output += (days + " day");
                    }
                    else {
                        output += (days + " days");
                    }
                }

                if (hours > 0) {
                    if (hours == 1) {
                        if (minutes == 0 && seconds == 0 && days != 0) {
                            //When below values are 0, add "and".
                            output += (" and " + hours + " hour");
                        }
                        else if ((days != 0) && ((minutes != 0) || (seconds != 0))) {
                            //When surrounding values are not 0, add ",".
                            output += (", " + hours + " hour");
                        }
                        else {
                            //If no other values, add nothing.
                            output += (hours + " hour");
                        }
                    }
                    else {
                        if (minutes == 0 && seconds == 0 && days != 0) {
                            //When below values are 0, add "and".
                            output += (" and " + hours + " hours");
                        }
                        else if ((days != 0) && ((minutes != 0) || (seconds != 0))) {
                            //When surrounding values are not 0, add ",".
                            output += (", " + hours + " hours");
                        }
                        else {
                            //If no other values, add nothing.
                            output += (hours + " hours");
                        }
                    }
                }

                if (minutes > 0) {
                    if (minutes == 1) {
                        if (seconds == 0 && ((days != 0) || (hours != 0))) {
                            //When below values are 0, add "and".
                            output += (" and " + minutes + " minute");
                        }
                        else if (((hours != 0) || (days != 0)) && ((seconds != 0) || (hours != 0))) {
                            //When surrounding values are not 0, add ",".
                            output += (", " + minutes + " minute");
                        }
                        else {
                            output += (minutes + " minute ");
                        }
                    }
                    else {
                        if (seconds == 0 && ((days != 0) || (hours != 0))) {
                            output += (" and " + minutes + " minutes");
                        }
                        else if (((hours != 0) || (days != 0)) && ((seconds != 0) || (hours != 0))) {
                            output += (", " + minutes + " minutes");
                        }
                        else {
                            //If no other values, add nothing.
                            output += (minutes + " minutes");
                        }
                    }
                }

                if (seconds > 0) {
                    if (seconds == 1) {
                        output += (" and " + seconds + " second");
                    }
                    else {
                        output += (" and " + seconds + " seconds");
                    }
                }


                //$('#countdown' + (i)).html(output);
                $("#countdown").html(output);
            }

            timer = setInterval(showRemaining, 1000);
        }

        function getRefTypes() {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var refTypes = new Object();
                    var xml = request.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        refTypes[row.getAttribute("refTypeID")] = row.getAttribute("refTypeName");
                    }
                    console.log(refTypes);
                    return refTypes;
                }
            };
            request.open("GET", "https://api.eveonline.com/eve/RefTypes.xml.aspx", true);
            request.send();
        }

        function getSkillInTraining(keyID, vCode, charIDs, i) {
            //console.log(charIDs.length);
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {

                    var xml = request.responseXML;
                    //console.log(xml);
                    if (xml.getElementsByTagName("trainingTypeID")[0] != null) {
                        var skillIDxml = xml.getElementsByTagName("trainingTypeID")[0];
                        var skillLvlxml = xml.getElementsByTagName("trainingToLevel")[0];
                        //console.log(skillLvlxml);
                        var skillIDnode = skillIDxml.childNodes[0];
                        var skillLvlnode = skillLvlxml.childNodes[0];
                        //console.log(skillLvlnode);
                        var skillID = skillIDnode.nodeValue;
                        var skillLvl = skillLvlnode.nodeValue;
                        var trainingStartTime = xml.getElementsByTagName("trainingStartTime")[0].childNodes[0].nodeValue;
                        var trainingEndTime = xml.getElementsByTagName("trainingEndTime")[0].childNodes[0].nodeValue;
                        var currentTQTime = xml.getElementsByTagName("currentTQTime")[0].childNodes[0].nodeValue;
                        //console.log(skillLvl);
                        var request2 = new XMLHttpRequest();
                        request2.onreadystatechange = function () {
                            if (request2.readyState == 4 && request2.status == 200) {

                                var xml2 = request2.responseXML;
                                //console.log(xml2)
                                var rows = xml2.getElementsByTagName("row");
                                for (var i2 = 0; i2 < rows.length; i2++) {
                                    var row = rows[i2];
                                    //console.log(row);
                                    skillName = row.getAttribute("typeName");
                                }
                                $("#CurrentlyTraining").html('<p>' + skillName + " " + skillLvl + "</p>");
                                getSkillTimeRemaining(trainingStartTime, currentTQTime, trainingEndTime, i);
                            }
                        };
                        request2.open("GET", "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + skillID, true);
                        request2.send();
                    }
                    else {
                        $("#CurrentlyTraining").html('<p>No skill in training</p>');
                    }

                }
            };
            request.open("GET", "https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getAllSkills(keyID, vCode, charIDs, i) {
            //console.log(charIDs.length);
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {

                    var xml = request.responseXML;
                    //console.log(xml);
                    var rowsets = xml.getElementsByTagName("rowset");
                    var skills = new Array();
                    //console.log(rowsets);
                    for (var i2 = 0; i2 < rowsets.length; i2++) {
                        var rowset = rowsets[i2];
                        if (rowset.getAttribute("name") == "skills") {
                            //console.log(rowset.getAttribute("name"));
                            var rows = rowset.getElementsByTagName("row");
                        }
                        //skillName = row.getAttribute("typeName");
                    }
                    //console.log(rows);
                    for (i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        //console.log(row);

                        typeID = row.getAttribute("typeID");
                        skillpoints = row.getAttribute("skillpoints");
                        level = row.getAttribute("level");
                        skills[i2] = typeID;
                        // = getItemName(typeID, level);
                        $("#skilllist").append('<p id="skill"><span id="skill' + typeID + '">Placeholder Skill</span> ' + level + '</p>');
                    }
                    //console.log(skills);
                    getSkillNames(skills);
                }
            };
            request.open("GET", "https://api.eveonline.com/char/CharacterSheet.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getSkillNames(skills) {
            var maxSize = 250;
            if (skills.length > maxSize) {
                for (var i2 = 0; i2 < skills.length; i2 += maxSize) {
                    skillsPart = skills.slice(i2, i2 + maxSize);
                    getSkillNames(skillsPart);
                }
            }
            var skillIDs = "";
            for (i2 = 0; i2 < skills.length; i2++) {
                skillIDs += skills[i2] + ",";
            }
            skillIDs = skillIDs.substring(0, skillIDs.length - 1);
            //console.log(skillIDs);
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var xml2 = request.responseXML;
                    //console.log(xml2)
                    var rows = xml2.getElementsByTagName("row");
                    for (var i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        //console.log(row);
                        typeID = row.getAttribute("typeID");
                        skillName = row.getAttribute("typeName");
                        //console.log(typeID + " = " + skillName);
                        $('#skill' + typeID).html(skillName);
                    }

                }
            };
            request.open("GET", "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + skillIDs, true);
            request.send();
        }

        function getName(keyID, vCode, charIDs) {
            var bool = true;
            for (var i = 0; i < charIDs.length; i++) {
                var li = '<li role="presentation">';
                var request = new XMLHttpRequest();
                request.open("GET", "https://api.eveonline.com/char/CharacterSheet.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], false);
                request.send();
                var xml = request.responseXML;
                var name = xml.getElementsByTagName("name")[0];
                var y = name.childNodes[0];
                z = y.nodeValue;
                if (bool) {
                    li = '<li role="presentation" class="active">';
                    bool = false;
                }
            }
        }

        Number.prototype.formatMoney = function (c, d, t) {
            var n = this,
                c = isNaN(c = Math.abs(c)) ? 2 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };
    </script>
    </body>
    </html>
<?php ob_flush(); ?>