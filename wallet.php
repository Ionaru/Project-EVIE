<?php ob_start();
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php'; ?>
    <div id="WalletContent" class="container-fluid">
        <a data-toggle="tooltip" data-placement="left" title="Back to top" href="#" class="back-to-top">Back to Top</a>
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
            getBalance(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
        }

        function getRefTypes() {
            $.ajax({
                url: "https://api.eveonline.com/eve/RefTypes.xml.aspx",
                error: function (xhr, status, error) {
                    showError("RefType Names", xhr, status, error);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        refTypes[rows[i].getAttribute("refTypeID")] = rows[i].getAttribute("refTypeName");
                    }
                    getWalletJournal(keyID, vCode, charIDs[<?php echo $selectedChar ?>]);
                    getWalletTransactions(keyID, vCode, charIDs[<?php echo $selectedChar ?>]);
                }
            });
        }

        function getBalance(keyID, vCode, charIDs, i) {
            if (!$.totalStorage('characterBalance_' + keyID + charIDs[i]) || isCacheExpired($.totalStorage('characterBalance_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text'])) {
                $.ajax({
                    url: "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                    error: function (xhr, status, error) {
                        showError("Account balance for character " + charIDs[i], xhr, status, error);
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

        function parseBalance(data) {
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

        function getWalletJournal(keyID, vCode, charID, rowCount, fromID) {
            $('#loadingiconW').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var url = "https://api.eveonline.com/char/WalletJournal.xml.aspx?";
            url += "keyID=" + keyID;
            url += "&vCode=" + vCode;
            url += "&characterID=" + charID;
            if (rowCount) {
                url += "&rowCount=" + rowCount;
            }
            else {
                url += "&rowCount=50";
            }
            if (fromID) {
                url += "&fromID=" + fromID;
            }
            $.ajax({
                url: url,
                error: function (xhr, status, error) {
                    showError("Wallet Journal", xhr, status, error);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    parseWalletJournal(xml, charID);
                }
            });
        }

        function parseWalletJournal(xml, charID) {
            var rows = xml.getElementsByTagName("row");
            var ownerName1;
            var ownerID1;
            var date;
            var amount;
            var refTypeID;
            var balance;
            var refID;
            var color;
            if (rows.length != 0) {
                for (var i2 = 0; i2 < rows.length; i2++) {
                    var row = rows[i2];
                    ownerName1 = "X";
                    date = row.getAttribute("date");
                    amount = row.getAttribute("amount");
                    refTypeID = refTypes[row.getAttribute("refTypeID")];
                    balance = row.getAttribute("balance");
                    refID = row.getAttribute("refID");
                    if (amount < 0) {
                        color = "red";
                    }
                    else {
                        color = "green";
                        ownerName1 = row.getAttribute("ownerName1");
                        ownerID1 = row.getAttribute("ownerID1");
                    }
                    var output = '';
                    output += '<tr>';
                    output += '<td data-label="Date">' + date + '</td>';
                    output += '<td data-label="refType">' + refTypeID + '</td>';
                    if (ownerName1 != "X") {
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
                $('#moreJournal50').attr('onclick', 'getWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "50", "' + refID + '")');
                $('#moreJournal100').attr('onclick', 'getWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "100", "' + refID + '")');
                $('#moreJournal250').attr('onclick', 'getWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "250", "' + refID + '")');
                $('#moreJournal1000').attr('onclick', 'getWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "1000", "' + refID + '")');
                $('#moreJournalAll').attr('onclick', 'getWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "2560", "' + refID + '")');
            }
            else {
                $('#moreJournal').html('There is no (more) journal info available.');
            }
            $('#loadingiconW').html('');
        }

        function getWalletTransactions(keyID, vCode, charID, rowCount, fromID) {
            $('#loadingiconT').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var url = "https://api.eveonline.com/char/WalletTransactions.xml.aspx?";
            url += "keyID=" + keyID;
            url += "&vCode=" + vCode;
            url += "&characterID=" + charID;
            if (rowCount) {
                url += "&rowCount=" + rowCount;
            }
            else {
                url += "&rowCount=50";
            }
            if (fromID) {
                url += "&fromID=" + fromID;
            }
            $.ajax({
                url: url,
                error: function (xhr, status, error) {
                    showError("Wallet Transactions", xhr, status, error);
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    parseWalletTransactions(xml, charID);
                }
            });
        }

        function parseWalletTransactions(xml, charID) {
            var date;
            var quantity;
            var typeName;
            var typeID;
            var price;
            var clientName;
            var transactionType;
            var transactionID;
            var color;
            var info;
            var rows = xml.getElementsByTagName("row");
            if (rows.length != 0) {
                for (var i2 = 0; i2 < rows.length; i2++) {
                    var row = rows[i2];
                    date = row.getAttribute("transactionDateTime");
                    quantity = row.getAttribute("quantity");
                    typeName = row.getAttribute("typeName");
                    typeID = row.getAttribute("typeID");
                    price = row.getAttribute("price");
                    clientName = row.getAttribute("clientName");
                    transactionType = row.getAttribute("transactionType");
                    transactionID = row.getAttribute("transactionID");
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
                $('#moreTransactions50').attr('onclick', 'getWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "50", "' + transactionID + '")');
                $('#moreTransactions100').attr('onclick', 'getWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "100", "' + transactionID + '")');
                $('#moreTransactions250').attr('onclick', 'getWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "250", "' + transactionID + '")');
                $('#moreTransactions1000').attr('onclick', 'getWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "1000", "' + transactionID + '")');
                $('#moreTransactionsAll').attr('onclick', 'getWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "2560", "' + transactionID + '")');
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