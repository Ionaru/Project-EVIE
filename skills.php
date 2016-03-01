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
        var typeIDs = [];
        $(document).ready(function () {
            $.ajax({
                url: "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode,
                error: function (xhr, status, error) {
                    showError("Character Data");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var charIDs = [];
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        charIDs[i] = row.getAttribute("characterID");
                    }
                    for (i = 0; i < charIDs.length; i++) {
                        var css = "characterInactive";
                        if (i == selectedCharacter) {
                            css = "characterActive";
                            selectedCharacterID = charIDs[i];
                        }
                        $('#charLinks').css('visibility', 'visible').append('<li><a id="charLink' + i + '" class="' + css + '" href="?char=' + i + '"><img alt="char' + i + '" id="char' + i + '" style="max-height: 50px" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>');
                        $('#char' + i).css('visibility', 'visible').attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg');
                        $('#charmbl' + i).css('visibility', 'visible').attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg');
                    }
                    getSkillInTraining(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    //getSkillQueue(keyID, vCode, charIDs, <?php //echo $selectedChar ?>);
                    getAllSkills(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                }
            });
        });

        function getSkillInTraining(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                error: function (xhr, status, error) {
                    showError("Currently Training Skill");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    if (xml.getElementsByTagName("trainingTypeID")[0] != null) {
                        var skillID = xml.getElementsByTagName("trainingTypeID")[0].childNodes[0].nodeValue;
                        var skillLvl = xml.getElementsByTagName("trainingToLevel")[0].childNodes[0].nodeValue;
                        var trainingStartTime = xml.getElementsByTagName("trainingStartTime")[0].childNodes[0].nodeValue;
                        var trainingEndTime = xml.getElementsByTagName("trainingEndTime")[0].childNodes[0].nodeValue;
                        var currentTQTime = xml.getElementsByTagName("currentTQTime")[0].childNodes[0].nodeValue;
                        typeIDs.push(skillID);
                        $("#CurrentlyTraining").html('<p><span id="' + skillID + '">Placeholder Skill</span> ' + skillLvl + '</p><p id="skillCountdown"></p>');
                        parseTimeRemaining(currentTQTime, trainingEndTime, "#skillCountdown", true, "Skill training completed!")
                    }
                    else {
                        $("#CurrentlyTraining").html('<p>No skill in training</p>');
                    }
                }
            });
        }

        function getAllSkills(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/CharacterSheet.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                error: function (xhr, status, error) {
                    showError("Skill List");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var rowsets = xml.getElementsByTagName("rowset");
                    var skills = [];
                    for (var i2 = 0; i2 < rowsets.length; i2++) {
                        var rowset = rowsets[i2];
                        if (rowset.getAttribute("name") == "skills") {
                            var rows = rowset.getElementsByTagName("row");
                        }
                    }
                    for (i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        var typeID = row.getAttribute("typeID");
                        var skillpoints = row.getAttribute("skillpoints");
                        var level = row.getAttribute("level");
                        typeIDs.push(typeID);
                        $("#skilllist").append('<p id="skill"><span id="' + typeID + '">Placeholder Skill</span> ' + level + '</p>');
                    }
                    getTypeNames(typeIDs);
                }
            });
        }

    </script>
    </body>
    </html>
<?php ob_flush(); ?>