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
            var charID, charName;
            var charIDs = [];
            var charNames = [];
            $.ajax({
                url: "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode,
                error: function (xhr, status, error) {
                    showError("Character Data");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
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
            });


        });

        function getAccountInfo(keyID, vCode) {
            $.ajax({
                url: "https://api.eveonline.com/account/AccountStatus.xml.aspx?keyID=" + keyID + "&vCode=" + vCode,
                error: function (xhr, status, error) {
                    showError("Account Information");
                     // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var currentTime, paidUntil, createDate, logonCount, logonMinutes;
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
                    var logonTime = logonMinutes * 60000;
                    parseTimeRemaining(currentTime, paidUntil, "#accountTime", true, "Account expired!");
                    parseTimeRemaining(0, logonTime, "#playTime", false, "No time at all");
                }
            });
        }

        function getBalance(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                error: function (xhr, status, error) {
                    showError("Account balance for character " + charIDs[i]);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var balance;
                    var rows = xml.getElementsByTagName("row");
                    for (var i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        balance = row.getAttribute("balance");
                        document.getElementById("BalanceAccount1Character" + (i + 1)).innerHTML = '<a style="color: #404040;" href="wallet.php?char=' + i + '">' + (parseFloat(balance)).formatMoney(2, ',', '.') + " ISK</a>";
                    }
                }
            });
        }

        function getSkillInTraining(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                error: function (xhr, status, error) {
                    showError("Skill training for character " + charIDs[i]);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    if (xml.getElementsByTagName("trainingTypeID")[0] != null) {
                        var skillIDs = [];
                        var skillID = xml.getElementsByTagName("trainingTypeID")[0].childNodes[0].nodeValue;
                        skillIDs.push(skillID);
                        var skillLvl = xml.getElementsByTagName("trainingToLevel")[0].childNodes[0].nodeValue;
                        var trainingEndTime = xml.getElementsByTagName("trainingEndTime")[0].childNodes[0].nodeValue;
                        var currentTQTime = xml.getElementsByTagName("currentTQTime")[0].childNodes[0].nodeValue;
                        getTypeNames(skillIDs);
                        document.getElementById("SkillAccount1Character" + (i + 1)).innerHTML = '<a id="skillCharacter' + i + '" style="color: black;" href="skills.php?char=' + i + '"><span id="' + skillID + '">Placeholder</span> ' + skillLvl + '</a><br><span id="countdown' + i + '"></span>';
                        parseTimeRemaining(currentTQTime, trainingEndTime, "#countdown" + i, true, "Skill training completed!");
                    }
                    else {
                        document.getElementById("SkillAccount1Character" + (i + 1)).innerHTML = '<a style="color: black;" href="skills.php?char=' + i + '">No skill in training</a>';
                    }
                }
            });
        }
    </script>
    </body>
    </html>
<?php
ob_flush();
?>