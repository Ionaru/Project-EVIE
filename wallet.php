<?php ob_start();
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php'; ?>
    <div id="WalletContent" class="container-fluid">
        <a href="#" class="back-to-top"></a>
    </div>
<?php include __DIR__ . '/foot.php'; ?>

    <script>
        var refTypes = [];
        var amountScrolled = 300;

        $(window).scroll(function () {
            if ($(window).scrollTop() > amountScrolled) {
                $('a.back-to-top').fadeIn('slow');
            } else {
                $('a.back-to-top').fadeOut('slow');
            }
        });

        function executePage() {
            $('#WalletContent').append('' +
                '<h2>Current balance: ' +
                '<br class="visible-xs"/>' +
                '<span id="balanceSpan"></span>' +
                '</h2>' +
                '<a class="anchor" name="Journal"></a>' +
                '<h2 class="walletHead">Journal</h2>' +
                '<a href="#Transactions"> Jump to Transactions</a>' +
                '<table class="journalTable table">' +
                '<thead><tr>' +
                '<th>Date (EVE Time)</th>' +
                '<th>Type</th>' +
                '<th>From</th>' +
                '<th>Amount</th>' +
                '<th>Balance</th>' +
                '</tr></thead>' +
                '<tbody id="WalletJournalBody"></tbody>' +
                '</table>' +
                '<span id="moreJournal">Load more entries ' +
                '<a id="moreJournal50">50</a> ' +
                '<a id="moreJournal100">100</a> ' +
                '<a id="moreJournal250">250</a> ' +
                '<a id="moreJournal1000">1000</a> ' +
                '<a id="moreJournalAll">Max</a>' +
                '</span> ' +
                '<span id="loadingiconW"></span>' +
                '<hr>' +
                '<a class="anchor" name="Transactions"></a>' +
                '<h2 class="walletHead">Transactions</h2>' +
                '<a href="#Journal"> Jump to Journal</a>' +
                '<table class="transactionsTable table">' +
                '<thead><tr>' +
                '<th class="Date">Date (EVE Time)</th>' +
                '<th>Information</th>' +
                '<th>Price</th>' +
                '</tr></thead>' +
                '<tbody id="WalletTransactionsBody"></tbody>' +
                '</table>' +
                '<span id="moreTransactions">Load more entries ' +
                '<a id="moreTransactions50">50</a> ' +
                '<a id="moreTransactions100">100</a> ' +
                '<a id="moreTransactions250">250</a> ' +
                '<a id="moreTransactions1000">1000</a> ' +
                '<a id="moreTransactionsAll">Max</a></span> ' +
                '<span id="loadingiconT"></span>');
            getRefTypes();
            getBalance();
        }

        function getRefTypes() {
            var data;
            if (!$.totalStorage('refIDs') || isCacheExpired($.totalStorage('refIDs')['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: "https://api.eveonline.com/eve/RefTypes.xml.aspx",
                    error: function (xhr, status, error) {
                        showError("RefType Names", xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        var rows = data["eveapi"]["result"]["rowset"]["row"];
                        for (var i = 0; i < rows.length; i++) {
                            refTypes[rows[i]['@attributes']['refTypeID']] = rows[i]['@attributes']['refTypeName'];
                        }
                        $.totalStorage('refIDs', data);
                        getWalletJournal();
                        getWalletTransactions();
                    }
                });
            }
            else {
                data = $.totalStorage('refIDs');
                var rows = data["eveapi"]["result"]["rowset"]["row"];
                for (var i = 0; i < rows.length; i++) {
                    refTypes[rows[i]['@attributes']['refTypeID']] = rows[i]['@attributes']['refTypeName'];
                }
                getWalletJournal();
                getWalletTransactions();
            }
        }

        function getBalance() {
            var data;
            if (!$.totalStorage('characterBalance_' + keyID + selectedCharacterID) || isCacheExpired($.totalStorage('characterBalance_' + keyID + selectedCharacterID)['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + selectedCharacterID,
                    error: function (xhr, status, error) {
                        showError("Account balance for character " + selectedCharacterID, xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage('characterBalance_' + keyID + selectedCharacterID, data);
                        parseBalance(data);
                    }
                });
            }
            else {
                data = $.totalStorage('characterBalance_' + keyID + selectedCharacterID);
                parseBalance(data);
            }
        }

        function parseBalance(data) {
            var balance = data['eveapi']['result']['rowset']['row']['@attributes']['balance'];
            var options = {
                useEasing: false,
                useGrouping: true,
                separator: '.',
                decimal: ',',
                prefix: '',
                suffix: ' ISK'
            };
            var animation = new CountUp("balanceSpan", 0, balance, 2, 1, options);
            animation.start();
        }

        function getWalletJournal(rowCount, fromID) {
            var data;
            $('#loadingiconW').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var url = "https://api.eveonline.com/char/WalletJournal.xml.aspx?";
            var storageName = "walletJournal_" + keyID + selectedCharacterID;
            url += "keyID=" + keyID;
            url += "&vCode=" + vCode;
            url += "&characterID=" + selectedCharacterID;
            if (rowCount) {
                storageName += "_rc=" + rowCount;
                url += "&rowCount=" + rowCount;
            }
            else {
                storageName += "_rc=50";
                url += "&rowCount=50";
            }
            if (fromID) {
                storageName += "_fid=" + fromID;
                url += "&fromID=" + fromID;
            }
            if (!$.totalStorage(storageName) || isCacheExpired($.totalStorage(storageName)['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: url,
                    error: function (xhr, status, error) {
                        showError("Wallet Journal", xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage(storageName, data);
                        parseWalletJournal(data);
                    }
                });
            }
            else {
                data = $.totalStorage(storageName);
                parseWalletJournal(data);
            }
        }

        function parseWalletJournal(data) {
            var rows = data["eveapi"]["result"]["rowset"]["row"];
            if (rows && rows.length != 0) {
                var ownerName1, ownerID1, date, amount, refTypeID, balance, refID, color;
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    ownerName1 = "";
                    color = "red";
                    date = row['@attributes']['date'];
                    amount = row['@attributes']['amount'];
                    refTypeID = refTypes[row['@attributes']['refTypeID']];
                    balance = row['@attributes']['balance'];
                    refID = row['@attributes']['refID'];
                    if (amount >= 0) {
                        color = "green";
                        ownerName1 = row['@attributes']['ownerName1'];
                        ownerID1 = row['@attributes']['ownerID1'];
                    }
                    var output = '';
                    output += '<tr>';
                    output += '<td data-label="Date">' + date + '</td>';
                    output += '<td data-label="refType">' + refTypeID + '</td>';
                    if (ownerName1 != "") {
                        output += '<td data-label="From">';
                        if (parseInt(ownerID1).between(90000000, 100000000, true)) {
                            output += '<a class="' + ownerID1 + '" onclick="getCharDataFromID(' + "'" + ownerID1 + "'" + ')">' + ownerName1 + '</a>';
                        }
                        else {
                            output += '<span class="' + ownerID1 + '">' + ownerName1 + '</span>';
                        }
                        output += '</td>';
                    }
                    else {
                        if ($(window).width() > 768) {
                            output += '<td></td>';
                        }
                    }
                    output += '<td style="color:' + color + '" data-label="Amount">' + (parseFloat(amount)).formatMoney(2, ',', '.') + ' ISK</td>';
                    output += '<td data-label="Balance">' + (parseFloat(balance)).formatMoney(2, ',', '.') + ' ISK</td></tr>';
                    output += '</tr>';
                    $('#WalletJournalBody').append(output);
                }
                $('#moreJournal50').attr('onclick', 'getWalletJournal("50", "' + refID + '")');
                $('#moreJournal100').attr('onclick', 'getWalletJournal("100", "' + refID + '")');
                $('#moreJournal250').attr('onclick', 'getWalletJournal("250", "' + refID + '")');
                $('#moreJournal1000').attr('onclick', 'getWalletJournal("1000", "' + refID + '")');
                $('#moreJournalAll').attr('onclick', 'getWalletJournal("2560", "' + refID + '")');
            }
            else {
                $('#moreJournal').html('There is no (more) journal info available.');
            }
            $('#loadingiconW').html('');
        }

        function getWalletTransactions(rowCount, fromID) {
            var data;
            $('#loadingiconT').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var storageName = "walletTransactions_" + keyID + selectedCharacterID;
            var url = "https://api.eveonline.com/char/WalletTransactions.xml.aspx?";
            url += "keyID=" + keyID;
            url += "&vCode=" + vCode;
            url += "&characterID=" + selectedCharacterID;
            if (rowCount) {
                storageName += "_rc=" + rowCount;
                url += "&rowCount=" + rowCount;
            }
            else {
                storageName += "_rc=50";
                url += "&rowCount=50";
            }
            if (fromID) {
                storageName += "_fid=" + fromID;
                url += "&fromID=" + fromID;
            }
            if (!$.totalStorage(storageName) || isCacheExpired($.totalStorage(storageName)['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: url,
                    error: function (xhr, status, error) {
                        showError("Wallet Transactions", xhr, status, error);
                        // TODO: implement fancy error logging
                    },
                    success: function (xml) {
                        data = xmlToJson(xml);
                        $.totalStorage(storageName, data);
                        parseWalletTransactions(data);
                    }
                });
            }
            else {
                data = $.totalStorage(storageName);
                parseWalletTransactions(data);
            }
        }

        function parseWalletTransactions(data) {
            var rows = data["eveapi"]["result"]["rowset"]["row"];
            if (rows && rows.length != 0) {
                var date, quantity, typeName, typeID, price, clientName, transactionType, transactionID, color, info;
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    date = row['@attributes']['transactionDateTime'];
                    quantity = row['@attributes']['quantity'];
                    typeName = row['@attributes']['typeName'];
                    typeID = row['@attributes']['typeID'];
                    price = row['@attributes']['price'];
                    clientName = row['@attributes']['clientName'];
                    transactionType = row['@attributes']['transactionType'];
                    transactionID = row['@attributes']['transactionID'];
                    if (transactionType == "buy") {
                        color = "red";
                        info = " bought from ";
                    }
                    else {
                        color = "green";
                        info = " sold to ";
                    }
                    $('#WalletTransactionsBody').append('<tr><td data-label="Date">' + date + '</td><td data-label="Information">' + quantity + ' x <a onclick="getItemData(' + "'" + typeID + "'" + ')">' + typeName + '</a>' + info + ' <a onclick="getCharData(' + "'" + clientName + "'" + ')">' + clientName + '</a></td><td data-label="Price" style="color: ' + color + '">' + (parseFloat(price * quantity)).formatMoney(2, ',', '.') + ' ISK (' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK per item)</td></tr>');
                }
                $('#moreTransactions50').attr('onclick', 'getWalletTransactions("50", "' + transactionID + '")');
                $('#moreTransactions100').attr('onclick', 'getWalletTransactions("100", "' + transactionID + '")');
                $('#moreTransactions250').attr('onclick', 'getWalletTransactions("250", "' + transactionID + '")');
                $('#moreTransactions1000').attr('onclick', 'getWalletTransactions("1000", "' + transactionID + '")');
                $('#moreTransactionsAll').attr('onclick', 'getWalletTransactions("2560", "' + transactionID + '")');
            }
            else {
                $('#moreTransactions').html('There is no (more) transaction info available.');
            }
            $('#loadingiconT').html('');
        }

    </script>
    </body>
    </html>
<?php ob_flush(); ?>