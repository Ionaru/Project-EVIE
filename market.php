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
            if (!$.totalStorage('orders_' + keyID + charIDs[i]) || isCacheExpired($.totalStorage('orders_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: "https://api.eveonline.com/char/MarketOrders.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                    error: function (xhr, status, error) {
                        showError("Market Orders");
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage('orders_' + keyID + charIDs[i], data);
                        parseOrders(data);
                    }
                });
            }
            else {
                var data = $.totalStorage('orders_' + keyID + charIDs[i]);
                parseOrders(data);
            }
        }

        function parseOrders(data){
            var stationID, volEntered, volRemaining, orderState, typeID ,range, duration, escrow, price, bid, issued, expiry;
            var sellOrders = 0;
            var buyOrders = 0;
            var items = [];
            var rows = data['eveapi']['result']['rowset']['row'];
            if (rows.length != 0) {
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    stationID = row["@attributes"]["stationID"];
                    volEntered =row["@attributes"]["volEntered"];
                    volRemaining = row["@attributes"]["volRemaining"];
                    orderState = row["@attributes"]["orderState"];
                    typeID = row["@attributes"]["typeID"];
                    range = row["@attributes"]["range"];
                    duration = row["@attributes"]["duration"];
                    escrow = row["@attributes"]["escrow"];
                    price = row["@attributes"]["price"];
                    bid = row["@attributes"]["bid"];
                    issued = row["@attributes"]["issued"];
                    issued = Date.parse(issued.replace(/\-/ig, '/').split('.')[0]);
                    expiry = issued + (duration * 86400000);
                    if (orderState == 0) {
                        items.push(typeID);
                        if (bid == 0) {
                            sellOrders++;
                            $('#SellOrders').append('<tr><td id="' + typeID + '">' + typeID + '</td><td>' + volRemaining + ' / ' + volEntered + '</td><td>' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK</td><td>' + stationID + ' ( range: ' + range + ' ) </td><td><span id="sellOrder' + i + '"></span></td></tr>');
                            parseTimeRemaining(currentTime, expiry, "#sellOrder" + i, true, "Expired!");
                        }
                        else if (bid == 1) {
                            buyOrders++;
                            $('#BuyOrders').append('<tr><td id="' + typeID + '">' + typeID + '</td><td>' + volRemaining + ' / ' + volEntered + '</td><td>' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK</td><td>' + stationID + ' ( range: ' + range + ' ) </td><td><span id="buyOrder' + i + '"></span></td></tr>');
                            parseTimeRemaining(currentTime, expiry, "#buyOrder" + i, true, "Expired!");
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
    </script>
    </body>
    </html>
<?php ob_flush(); ?>