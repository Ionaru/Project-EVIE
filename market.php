<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>

    <div>
        <div id="Orders"></div>
        <div id="market"></div>
    </div>

<?php include __DIR__ . '/foot.php'; ?>
    <script>
        function executePage() {
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
            getBalance(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
            getOrders(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
        }

        function getBalance(keyID, vCode, charIDs, i) {
            if (!$.totalStorage('characterBalance_' + keyID + charIDs[i]) || isCacheExpired($.totalStorage('characterBalance_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                    error: function (xhr, status, error) {
                        showError("Account balance for character " + charIDs[i]);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage('characterBalance_' + keyID + charIDs[i], data);
                        parseBalance(data);
                    }
                });
            }
            else {
                var data = $.totalStorage('characterBalance_' + keyID + charIDs[i]);
                parseBalance(data);
            }

        }

        function parseBalance(data){
            var balance;
            balance = data['eveapi']['result']['rowset']['row']['@attributes']['balance'];
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

        function getOrders(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/MarketOrders.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                error: function (xhr, status, error) {
                    showError("Market Orders");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
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
                    var sellOrders = 0;
                    var buyOrders = 0;
                    var items = [];
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
                            issued = Date.parse(issued.replace(/\-/ig, '/').split('.')[0]);
                            expiry = issued + (duration * 86400000);
                            if (orderState == 0) {
                                items.push(typeID);
                                if (bid == 0) {
                                    sellOrders++;
                                    $('#SellOrders').append('<tr><td id="' + typeID + '">' + typeID + '</td><td>' + volRemaining + ' / ' + volEntered + '</td><td>' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK</td><td>' + stationID + ' ( range: ' + range + ' ) </td><td><span id="sellOrder' + i2 + '"></span></td></tr>');
                                    parseTimeRemaining(currentTime, expiry, "#sellOrder" + i2, true, "Expired!");
                                }
                                else if (bid == 1) {
                                    buyOrders++;
                                    $('#BuyOrders').append('<tr><td id="' + typeID + '">' + typeID + '</td><td>' + volRemaining + ' / ' + volEntered + '</td><td>' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK</td><td>' + stationID + ' ( range: ' + range + ' ) </td><td><span id="buyOrder' + i2 + '"></span></td></tr>');
                                    parseTimeRemaining(currentTime, expiry, "#buyOrder" + i2, true, "Expired!");
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
                    if (sellOrders == 0 && buyOrders == 0) {
                        $('#sellOrdersTable').html('<p>You have no open market orders.</p>');
                    }
                    else {
                        getTypeNames(items);
                    }
                }
            });
        }
    </script>
    </body>
    </html>
<?php ob_flush(); ?>