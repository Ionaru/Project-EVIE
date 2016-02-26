<?php ob_start();
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php'; ?>

    <h2>Account Information</h2>
    <div id="AccountInfo"></div>
    <hr>
    <h2>Characters</h2>
    <div id="CharacterDivs" class="row placeholders">
    </div>

<?php include __DIR__ . '/foot.php'; ?>
    <script>
        $(document).ready(function () {
            getAccountInfo(keyID, vCode);
            var charIDs = [];
            var charNames = [];
            var charRequest = new XMLHttpRequest();
            charRequest.onreadystatechange = function () {
                var charID, charName;
                if (charRequest.readyState == 4 && charRequest.status == 200) {
                    var xml = charRequest.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    for (i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        charID = row.getAttribute("characterID");
                        charName = row.getAttribute("name");
                        charNames[i] = charName;
                        charIDs[i] = charID;
                    }
                    for (i = 0; i < charIDs.length; i++) {
                        $("#mobilecharacter").append('<div class="col-xs-4 col-centered text-center"><a href="?char=' + i + '"><img alt="character" src="https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg"></a></div>');
                        $("#char" + i).attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg').css("visibility", "visible");
                        $("#charmbl" + i).attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg').css("visibility", "visible");
                        $("#charLink" + i).css("visibility", "visible");
                        $('#CharacterDivs').append('' +
                            '<div class="col-xs-6 col-sm-3 col-md-2 placeholder">' +
                            '<a id="CharacterDivLink' + (i + 1) + '" style="cursor: pointer;" onclick="getCharDataFromID(' + "'" + charIDs[i] + "'" + ')">' +
                            '<img id="ImageAccount1Character' + (i + 1) + '" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">' +
                            '<h4 id="NameAccount1Character' + (i + 1) + '"></h4>' +
                            '</a>' +
                            '<span id="BalanceAccount1Character' + (i + 1) + '" class="text-muted"></span>' +
                            '<p id="SkillAccount1Character' + (i + 1) + '"></p>' +
                            '<div id="countdown"></div>' +
                            '</div>');
                        $("#NameAccount1Character" + (i + 1)).html("<strong>" + charNames[i] + "</strong>");

                        getBalance(keyID, vCode, charIDs, i);
                        getSkillInTraining(keyID, vCode, charIDs, i);
                    }
                    for (var i = 0; i < charIDs.length; i++) {
                        document.getElementById("ImageAccount1Character" + (i + 1)).src = "https://image.eveonline.com/Character/" + charIDs[i] + "_256.jpg";
                        <?php if (strpos($_SERVER['REQUEST_URI'], 'index.php') === false) {
                        echo '
                              document.getElementById("char" + i).src="https://image.eveonline.com/Character/" + charIDs[i] + "_256.jpg";
                              document.getElementById("char" + i).style="max-height: 50px;";
                              ';
                    }?>
                    }
                }
            };
            charRequest.open("GET", "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode, true);
            charRequest.send();


        });

        function getAccountInfo(keyID, vCode) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var currentTime, paidUntil, createDate, logonCount, logonMinutes;
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
                    currentTime = xml.getElementsByTagName("currentTime")[0].childNodes[0].nodeValue;
                    paidUntil = xml.getElementsByTagName("paidUntil")[0].childNodes[0].nodeValue;
                    createDate = xml.getElementsByTagName("createDate")[0].childNodes[0].nodeValue;
                    logonCount = xml.getElementsByTagName("logonCount")[0].childNodes[0].nodeValue;
                    logonMinutes = xml.getElementsByTagName("logonMinutes")[0].childNodes[0].nodeValue;
                    $("#AccountInfo").append('' +
                        '<p>Account created on ' + createDate + '</p>' +
                        '<p>Account expires in <span id="accountTime"></span></p>' +
                        '<p>You have logged in ' + logonCount + ' times to the EVE servers</p>' +
                        '<p>Your total play time is <span id="playTime"></span></p>' +
                        '<p>Average session length: ' + Math.round(logonMinutes / logonCount) + ' minutes</p>');
                    getAccountTimeRemaining(currentTime, paidUntil);
                    getTimePlayed(logonMinutes);
                }
            };
            request.open("GET", "https://api.eveonline.com/account/AccountStatus.xml.aspx?keyID=" + keyID + "&vCode=" + vCode, true);
            request.send();
        }

        function getTimePlayed(playTime) {
            var distance = (playTime * 60000);
            var _second = 1000;
            var _minute = _second * 60;
            var _hour = _minute * 60;
            var _day = _hour * 24;
            $('#playTime').html("Calculating Time...");
            if (distance < 1) {

                $('#playTime').html('No time at all');

                return;
            }
            var days = Math.floor(distance / _day);
            var hours = Math.floor((distance % _day) / _hour);
            var minutes = Math.floor((distance % _hour) / _minute);
            var seconds = Math.floor((distance % _minute) / _second);
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
            $('#playTime').html(output);
        }

        function getAccountTimeRemaining(nowDate, endDate) {
            var now = Date.parse(nowDate.replace(/\-/ig, '/').split('.')[0]);
            var end = Date.parse(endDate.replace(/\-/ig, '/').split('.')[0]);

            var _second = 1000;
            var _minute = _second * 60;
            var _hour = _minute * 60;
            var _day = _hour * 24;
            var timer;
            $('#accountTime').html("Calculating Time...");

            function showRemaining() {
                now = now + 1000;
                var distance = end - now;
                if (distance < 1) {

                    clearInterval(timer);
                    $('#accountTime').html('Account expired!');

                    return;
                }
                var days = Math.floor(distance / _day);
                var hours = Math.floor((distance % _day) / _hour);
                var minutes = Math.floor((distance % _hour) / _minute);
                var seconds = Math.floor((distance % _minute) / _second);
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
                $('#accountTime').html(output);
            }

            timer = setInterval(showRemaining, 1000);
        }


        function getSkillTimeRemaining(nowDate, endDate, i) {
            var now = Date.parse(nowDate.replace(/\-/ig, '/').split('.')[0]);
            var end = Date.parse(endDate.replace(/\-/ig, '/').split('.')[0]);
            //var end = now + 3655000;

            var _second = 1000;
            var _minute = _second * 60;
            var _hour = _minute * 60;
            var _day = _hour * 24;
            var timer;
            $('#skillCharacter' + (i)).append('<br><span id="countdown' + (i) + '">Calculating Time...</span>');

            function showRemaining() {
                //var now = new Date();
                //console.log(now);
                now = now + 1000;
                var distance = end - now;
                if (distance < 1) {

                    clearInterval(timer);
                    $('#countdown' + (i)).html("Skill training completed!");

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


                $('#countdown' + (i)).html(output);
            }

            timer = setInterval(showRemaining, 1000);
        }

        function getBalance(keyID, vCode, charIDs, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var balance;
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    for (var i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        balance = row.getAttribute("balance");
                        document.getElementById("BalanceAccount1Character" + (i + 1)).innerHTML = '<a style="color: #404040;" href="wallet.php?char=' + i + '">' + (parseFloat(balance)).formatMoney(2, ',', '.') + " ISK</a>";
                    }
                }
            };
            request.open("GET", "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getSkillInTraining(keyID, vCode, charIDs, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {

                    var xml = request.responseXML;
                    if (xml.getElementsByTagName("trainingTypeID")[0] != null) {
                        var skillIDxml = xml.getElementsByTagName("trainingTypeID")[0];
                        var skillLvlxml = xml.getElementsByTagName("trainingToLevel")[0];
                        var skillIDnode = skillIDxml.childNodes[0];
                        var skillLvlnode = skillLvlxml.childNodes[0];
                        var skillID = skillIDnode.nodeValue;
                        var skillLvl = skillLvlnode.nodeValue;
                        var trainingEndTime = xml.getElementsByTagName("trainingEndTime")[0].childNodes[0].nodeValue;
                        var currentTQTime = xml.getElementsByTagName("currentTQTime")[0].childNodes[0].nodeValue;
                        getTypeNames(skillID);
                        document.getElementById("SkillAccount1Character" + (i + 1)).innerHTML = '<a id="skillCharacter' + i + '" style="color: black;" href="skills.php?char=' + i + '"><span id="' + skillID + '">Placeholder</span>' + skillLvl + '</a>';
                        getSkillTimeRemaining(currentTQTime, trainingEndTime, i);
                    }
                    else {
                        document.getElementById("SkillAccount1Character" + (i + 1)).innerHTML = '<a style="color: black;" href="skills.php?char=' + i + '">No skill in training</a>';
                    }

                }
            };
            request.open("GET", "https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }
    </script>
    </body>
    </html>
<?php
ob_flush();
?>