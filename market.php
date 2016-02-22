<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>

    <div>
        <div id="Orders"></div>
        <div id="market"></div>
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
                        charIDs[i] = row.getAttribute("characterID");
                    }
                    $('#Orders').append('' +
                        '<h2>Current balance: ' +
                        '<br class="visible-xs"/>' +
                        '<span id="balanceSpan"></span>' +
                        '</h2>' +
                        '<span id="sellOrdersTable">' +
                        '<h2 style="display: inline;">Sell Orders</h2>' +
                        '<table class="table">' +
                        '<thead><tr>' +
                        '<th style="width: 20%">Item</th>' +
                        '<th style="width: 20%">Amount</th>' +
                        '<th style="width: 20%">Escrow</th>' +
                        '<th style="width: 20%">Location</th>' +
                        '<th style="width: 20%">Duration</th>' +
                        '</tr></thead>' +
                        '<tbody id="SellOrders"></tbody>' +
                        '</table>' +
                        '<hr>' +
                        '</span>' +
                        '<span id="buyOrdersTable">' +
                        '<h2 style="display: inline;">Buy Orders</h2>' +
                        '<table class="table">' +
                        '<thead><tr>' +
                        '<th style="width: 20%">Item</th>' +
                        '<th style="width: 20%">Amount</th>' +
                        '<th style="width: 20%">Price</th>' +
                        '<th style="width: 20%">Location</th>' +
                        '<th style="width: 20%">Duration</th>' +
                        '</tr></thead>' +
                        '<tbody id="BuyOrders"></tbody>' +
                        '</table>' +
                        '</span>');
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
                    getBalance(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    getOrders(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    //getWalletTransactions(keyID, vCode, charIDs, refTypes, <?php echo $selectedChar ?>);
                    //getSkillInTraining(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                    //getCharacterSheet(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                }
            };
            charRequest.open("GET", "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode, true);
            charRequest.send();
        });

        function getBalance(keyID, vCode, charIDs, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var balance;
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    for (var i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        //console.log(row);
                        balance = row.getAttribute("balance");
                        //console.log(balance);
                        //$("#balanceSpan").html((parseFloat(balance)).formatMoney(2, ',', '.') + " ISK");
                        var options = {
                            useEasing: false,
                            useGrouping: true,
                            separator: '.',
                            decimal: ',',
                            prefix: '',
                            suffix: ' ISK'
                        };
                        var demo = new CountUp("balanceSpan", 0, balance, 2, 1, options);
                        demo.start();
                    }
                }
            };
            request.open("GET", "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getOrders(keyID, vCode, charIDs, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var stationID;
                var volEntered;
                var volRemaining;
                var orderState;
                var typeID;
                var range;
                var duration;
                var escrow;
                var price;
                var bid;
                var issued;
                var currentTime;
                var expiry;
                if (request.readyState == 4 && request.status == 200) {
                    var sellOrders = 0;
                    var buyOrders = 0;
                    var items = [];
                    var xml = request.responseXML;
                    //console.log(xml);
                    var rows = xml.getElementsByTagName("row");
                    if (rows.length != 0) {
                        for (var i2 = 0; i2 < rows.length; i2++) {
                            var row = rows[i2];
                            stationID = row.getAttribute("stationID");
                            volEntered = row.getAttribute("volEntered");
                            volRemaining = row.getAttribute("volRemaining");
                            orderState = row.getAttribute("orderState");
                            typeID = row.getAttribute("typeID");
                            range = row.getAttribute("range");
                            duration = row.getAttribute("duration");
                            escrow = row.getAttribute("escrow");
                            price = row.getAttribute("price");
                            bid = row.getAttribute("bid");
                            issued = row.getAttribute("issued");
                            currentTime = xml.getElementsByTagName('currentTime')[0].childNodes[0].nodeValue;
                            currentTime = Date.parse(currentTime.replace(/\-/ig, '/').split('.')[0]);
                            issued = Date.parse(issued.replace(/\-/ig, '/').split('.')[0]);
                            expiry = issued + (duration * 86400000);
                            if (orderState == 0) {
                                items.push(typeID);
                                if (bid == 0) {
                                    sellOrders++;
                                    $('#SellOrders').append('<tr><td id="' + typeID + '">' + typeID + '</td><td>' + volRemaining + ' / ' + volEntered + '</td><td>' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK</td><td>' + stationID + ' ( range: ' + range + ' ) </td><td>' + parseTimeRemaining(currentTime, expiry) + '</td></tr>');
                                }
                                else if (bid == 1) {
                                    buyOrders++;
                                    $('#BuyOrders').append('<tr><td id="' + typeID + '">' + typeID + '</td><td>' + volRemaining + ' / ' + volEntered + '</td><td>' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK</td><td>' + stationID + ' ( range: ' + range + ' ) </td><td>' + parseTimeRemaining(currentTime, expiry) + '</td></tr>');
                                }
                            }
                        }
                    }
                    if (sellOrders == 0) {
                        $('#sellOrdersTable').html('');
                    }
                    if (buyOrders == 0) {
                        $('#buyOrdersTable').html('');
                    }
                    if (sellOrders == 0 && buyOrders == 0){
                        $('#sellOrdersTable').html('<p>You have no open market orders.</p>');
                    }
                    else {
                        getItemNames(items);
                    }

                }
            };
            request.open("GET", "https://api.eveonline.com/char/MarketOrders.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getItemNames(items) {
            var maxSize = 250;
            var skillsPart;
            if (items.length > maxSize) {
                for (var i2 = 0; i2 < items.length; i2 += maxSize) {
                    skillsPart = items.slice(i2, i2 + maxSize);
                    getItemNames(skillsPart);
                }
            }
            var skillIDs = "";
            for (i2 = 0; i2 < items.length; i2++) {
                skillIDs += items[i2] + ",";
            }
            skillIDs = skillIDs.substring(0, skillIDs.length - 1);
            //console.log(skillIDs);
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var typeID;
                var skillName;
                if (request.readyState == 4 && request.status == 200) {
                    var xml2 = request.responseXML;
                    //console.log(xml2)
                    var rows = xml2.getElementsByTagName("row");
                    for (var i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        //console.log(row);
                        typeID = row.getAttribute("typeID");
                        skillName = row.getAttribute("typeName");
                        $('#' + typeID).html(skillName);
                    }

                }
            };
            request.open("GET", "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + skillIDs, true);
            request.send();
        }
    </script>
    </body>
    </html>
<?php ob_flush(); ?>