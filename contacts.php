<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>

    <div id="contacts"></div>

<?php include __DIR__ . '/foot.php'; ?>
    <script>
        function executePage() {
            getContacts(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
        }

        function getContacts(keyID, vCode, charIDs, i) {
            var data;
            if (!$.totalStorage('contacts_' + keyID + charIDs[i]) || isCacheExpired($.totalStorage('contacts_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: "https://api.eveonline.com/char/ContactList.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                    error: function (xhr, status, error) {
                        showError("Contacts");
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage('contacts_' + keyID + charIDs[i], data);
                        parseContacts(data);
                    }
                });
            }
            else {
                data = $.totalStorage('contacts_' + keyID + charIDs[i]);
                parseContacts(data);
            }

        }

        function parseContacts(data){
            for(var i = 0; i < data["eveapi"]["result"]["rowset"]["0"]["row"].length; i++){
                $('#contacts').append('<p><img alt="contact_image" src="https://image.eveonline.com/Character/' + data["eveapi"]["result"]["rowset"]["0"]["row"][i]["@attributes"]["contactID"] + '_50.jpg"> ' + data["eveapi"]["result"]["rowset"]["0"]["row"][i]["@attributes"]["contactName"] + '<br></p>');
            }
        }
    </script>
    </body>
    </html>
<?php ob_flush(); ?>