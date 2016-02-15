<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>

    <div>
        <div id="sellOrders"></div>
        <div id="buyOrders"></div>
        <hr>
        <div id="market"></div>
    </div>

<?php include __DIR__ . '/foot.php'; ?>
    <script>

        $('#myModal').on('shown.bs.modal', function () {
            console.log("Modal Opened");
            $('#myInput').focus()

        });

        $('#myTabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show')
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

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
                    $('#WalletContent').append('<h2>Journal</h2><table class="table"><thead><tr><th style="width: 25%">Date (EVE Time)</th><th style="width: 25%">Type</th><th style="width: 25%">Amount</th><th style="width: 25%">Balance</th></tr></thead><tbody id="WalletJournalBody' + <?php echo($selectedChar + 1) ?> +'"></tbody></table></div>');
                    $('#WalletContent').append('<h2>Transactions</h2><table class="table"><thead><tr><th style="width: 20%">Date (EVE Time)</th><th style="width: 40%">Information</th><th style="width: 40%">Price</th></tr></thead><tbody id="WalletTransactionsBody' + <?php echo($selectedChar + 1) ?> +'"></tbody></table></div>');
                    for (var i = 0; i < charIDs.length; i++) {
                        $("#char" + i).attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg');
                        $("#charmbl" + i).attr('src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg');
                        $("#char" + i).css("visibility", "visible");
                        $("#charmbl" + i).css("visibility", "visible");
                        $("#charLink" + i).css("visibility", "visible");
                        //$("#charlink" + i).attr('title','Hello');
                    }
                    //getWalletJournal(keyID, vCode, charIDs, refTypes, <?php echo $selectedChar ?>);
                    //getWalletTransactions(keyID, vCode, charIDs, refTypes, <?php echo $selectedChar ?>);
                    //getSkillInTraining(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    //getCharacterSheet(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                }
            };
            charRequest.open("GET", "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode, true);
            charRequest.send();
        });

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
                                $("#CurrentlyTraining").html('<a style="color: black;" href="skills.php?char=' + i + '">' + skillName + " " + skillLvl + "</a>");
                            }
                        };
                        request2.open("GET", "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + skillID, true);
                        request2.send();
                    }
                    else {
                        $("#CurrentlyTraining").html('<a style="color: black;" href="skills.php?char=' + i + '">No skill in training</a>');
                    }

                }
            };
            request.open("GET", "https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getCharacterSheet(keyID, vCode, charIDs, i) {
            var infoRequest = new XMLHttpRequest();
            infoRequest.onreadystatechange = function () {
                if (infoRequest.readyState == 4 && infoRequest.status == 200) {
                    var xml = infoRequest.responseXML;
                    charName = xml.getElementsByTagName("name")[0].childNodes[0].nodeValue;
                    charRace = xml.getElementsByTagName("race")[0].childNodes[0].nodeValue;
                    bloodline = xml.getElementsByTagName("bloodLine")[0].childNodes[0].nodeValue;
                    ancestry = xml.getElementsByTagName("ancestry")[0].childNodes[0].nodeValue;
                    corpName = xml.getElementsByTagName("corporationName")[0].childNodes[0].nodeValue;
                    //corpDate = xml.getElementsByTagName("corporationDate")[0].childNodes[0].nodeValue;
                    allianceName = "";
                    if (xml.getElementsByTagName("allianceName")[0] != null) {
                        allianceName = xml.getElementsByTagName("allianceName")[0].childNodes[0].nodeValue;
                        //allianceDate = xml.getElementsByTagName("allianceDate")[0].childNodes[0].nodeValue;
                    }
                    //securityStatus = xml.getElementsByTagName("securityStatus")[0].childNodes[0].nodeValue;
                    //console.log(charName);
                    $("#CharacterSheet").html('<h2>' + charName + '</h2>');
                    $("#CharacterSheet").append('<h3>' + corpName + '</h3>');
                    if (allianceName != null) {
                        $("#CharacterSheet").append('<h3>' + allianceName + '</h3>');
                    }
                }
            };
            infoRequest.open("GET", "https://api.eveonline.com/char/CharacterSheet.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            infoRequest.send();
        }

        function getItemName(typeID, level) {
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
                    $('#skill' + typeID).html(skillName + ' ' + level);
                }
            };
            request2.open("GET", "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + typeID, true);
            request2.send();
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

        function getWalletJournal(keyID, vCode, charIDs, refTypes, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
                    //console.log(xml);
                    var rows = xml.getElementsByTagName("row");
                    if (rows.length != 0) {
                        for (var i2 = 0; i2 < rows.length; i2++) {
                            var row = rows[i2];
                            date = row.getAttribute("date");
                            amount = row.getAttribute("amount");
                            refTypeID = refTypes[row.getAttribute("refTypeID")];
                            balance = row.getAttribute("balance");
                            refID = row.getAttribute("refID");
                            ownerName1 = row.getAttribute("ownerName1");
                            ownerName2 = row.getAttribute("ownerName2");
                            if (amount < 0) {
                                var color = "red";
                            }
                            else {
                                var color = "green";
                            }
                            $('#WalletJournalBody' + (i + 1)).append('<tr><td data-label="Date">' + date + '</td><td data-label="refType">' + refTypeID + '</td><td style="color:' + color + '" data-label="Amount">' + (parseFloat(amount)).formatMoney(2, ',', '.') + ' ISK</td><td data-label="Balance">' + (parseFloat(balance)).formatMoney(2, ',', '.') + ' ISK</td></tr>');
                        }
                    }
                    else {
                        $('#WalletJournalBody' + (i + 1)).append('<tr><td data-label="Date">There is no journal info available.</td><td></td><td></td><td></td>"></tr>');

                    }
                }
            };
            request.open("GET", "https://api.eveonline.com/char/WalletJournal.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getWalletTransactions(keyID, vCode, charIDs, refTypes, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    if (rows.length != 0) {
                        for (var i2 = 0; i2 < rows.length; i2++) {
                            var row = rows[i2];
                            date = row.getAttribute("transactionDateTime");
                            quantity = row.getAttribute("quantity");
                            typeName = row.getAttribute("typeName");
                            price = row.getAttribute("price");
                            clientName = row.getAttribute("clientName");
                            transactionType = row.getAttribute("transactionType");
                            if (transactionType == "buy") {
                                var color = "red";
                                var info = " bought from ";
                            }
                            else {
                                var color = "green";
                                var info = " sold to ";
                            }
                            $('#WalletTransactionsBody' + (i + 1)).append('<tr><td data-label="Date">' + date + '</td><td data-label="Information">' + quantity + ' x <a>' + typeName + '</a>' + info + '<a>' + clientName + '</a></td><td data-label="Price" style="color: ' + color + '">' + (parseFloat(price * quantity)).formatMoney(2, ',', '.') + ' ISK (' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK per item)</td></tr>');
                        }
                    }
                    else {
                        $('#WalletTransactionsBody' + (i + 1)).append('<tr><td data-label="Date">There is no transaction info available.</td><td></td><td></td>"></tr>');

                    }
                }
            };
            request.open("GET", "https://api.eveonline.com/char/WalletTransactions.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i] + "&rowCount=50", true);
            request.send();
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