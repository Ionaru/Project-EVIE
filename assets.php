<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>

<p>Unimplemented</p>

<?php include __DIR__ . '/foot.php'; ?>
    <script>
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
                }
            });
        });
    </script>
    </body>
    </html>
<?php ob_flush(); ?>