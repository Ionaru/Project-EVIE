<?php ob_start();
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php'; ?>

    <h2>Characters</h2>
    <div id="CharacterDivs" class="row placeholders"></div>
    <hr>
    <h2>Account Information</h2>
    <div id="AccountInfo"></div>


<?php include __DIR__ . '/foot.php'; ?>

    <script>
        function executePage() {
            getAccountInfo(keyID, vCode);
            for (i = 0; i < charIDs.length; i++) {
                $('#CharacterDivs').append('' +
                    '<div class="col-xs-6 col-sm-4 placeholder">' +
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
            }
        }

        function getAccountInfo(keyID, vCode) {
            if(!$.totalStorage('accountInfo_' + keyID) || isCacheExpired($.totalStorage('accountInfo_' + keyID)['eveapi']['cachedUntil']['#text'])){
                $.ajax({
                    url: "https://api.eveonline.com/account/AccountStatus.xml.aspx?keyID=" + keyID + "&vCode=" + vCode,
                    error: function (xhr, status, error) {
                        showError("Account Information", xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage('accountInfo_' + keyID, data);
                        parseAccountInfo(data);
                    }
                });
            }
            else {
                var data = $.totalStorage('accountInfo_' + keyID);
                parseAccountInfo(data);
            }
        }

        function parseAccountInfo(data){
            var paidUntil = data['eveapi']['result']['paidUntil']['#text'];
            var createDate = data['eveapi']['result']['createDate']['#text'];
            var logonCount = data['eveapi']['result']['logonCount']['#text'];
            var logonMinutes = data['eveapi']['result']['logonMinutes']['#text'];
            $("#AccountInfo").append('' +
                '<p>Account created on ' + createDate + '</p>' +
                '<p>Account expires in <span id="accountTime"></span></p>' +
                '<p>You have logged in ' + logonCount + ' times to the EVE servers</p>' +
                '<p>Your total play time is <span id="playTime"></span></p>' +
                '<p>Average session length: ' + Math.round(logonMinutes / logonCount) + ' minutes</p>');
            var logonTime = logonMinutes * 60000;
            jQuery("time.timeago").timeago();
            parseTimeRemaining(currentTime, paidUntil, "#accountTime", true, "Account expired!");
            parseTimeRemaining(0, logonTime, "#playTime", false, "No time at all");
        }

        function getBalance(keyID, vCode, charIDs, i) {
            if(!$.totalStorage('characterBalance_' + keyID + charIDs[i]) || isCacheExpired($.totalStorage('characterBalance_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text'])){
                $.ajax({
                    url: "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                    error: function (xhr, status, error) {
                        showError("Account balance for character " + charIDs[i], xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        $.totalStorage('characterBalance_' + keyID + charIDs[i], xmlToJson(xml));
                        parseBalance(xmlToJson(xml), i);
                    }
                });
            }
            else {
                var data = $.totalStorage('characterBalance_' + keyID + charIDs[i]);
                parseBalance(data, i);
            }
        }

        function parseBalance(data, i){
            var balance;
            balance = data['eveapi']['result']['rowset']['row']['@attributes']['balance'];
            document.getElementById("BalanceAccount1Character" + (i + 1)).innerHTML = '<a href="wallet.php?c=' + i + '">' + (parseFloat(balance)).formatMoney(2, ',', '.') + " ISK</a>";
        }

        function getSkillInTraining(keyID, vCode, charIDs, i) {
            if(!$.totalStorage('skillInTraining_' + keyID + charIDs[i]) || isCacheExpired($.totalStorage('skillInTraining_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text'])){
                $.ajax({
                    url: "https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                    error: function (xhr, status, error) {
                        showError("Skill training for character " + charIDs[i], xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        $.totalStorage('skillInTraining_' + keyID + charIDs[i], xmlToJson(xml));
                        parseSkillInTraining(xmlToJson(xml), i);
                    }
                });
            }
            else{
                var data = $.totalStorage('skillInTraining_' + keyID + charIDs[i]);
                parseSkillInTraining(data, i);
            }
        }

        function parseSkillInTraining(data, i){
            if (data['eveapi']['result']['trainingTypeID']) {
                var skillIDs = [];
                var skillID = data['eveapi']['result']['trainingTypeID']['#text'];
                skillIDs.push(skillID);
                var skillLvl = data['eveapi']['result']['trainingToLevel']['#text'];
                var trainingEndTime = data['eveapi']['result']['trainingEndTime']['#text'];
                getTypeNames(skillIDs);
                document.getElementById("SkillAccount1Character" + (i + 1)).innerHTML = '<a id="skillCharacter' + i + '" href="skills.php?c=' + i + '""><span id="' + skillID + '">Placeholder</span> ' + skillLvl + '</a><br><span id="countdown' + i + '"></span>';
                parseTimeRemaining(currentTime, trainingEndTime, "#countdown" + i, true, "Skill training completed!");
            }
            else {
                document.getElementById("SkillAccount1Character" + (i + 1)).innerHTML = '<a href="skills.php?c=' + i + '">No skill in training</a>';
            }
        }
    </script>
    </body>
    </html>
<?php
ob_flush();
?>