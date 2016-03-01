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

        $(document).ready(function () {
            getRefTypes();
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
            getBalance(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
            getWalletJournal(keyID, vCode, charIDs, refTypes, <?php echo $selectedChar ?>);
            getWalletTransactions(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
        });

        function getRefTypes() {
            $.ajax({
                url: "https://api.eveonline.com/eve/RefTypes.xml.aspx",
                error: function (xhr, status, error) {
                    showError("RefType Names");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        refTypes[rows[i].getAttribute("refTypeID")] = rows[i].getAttribute("refTypeName");
                    }
                }
            });
        }

        function getBalance(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i],
                error: function (xhr, status, error) {
                    showError("Balance");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var balance;
                    var rows = xml.getElementsByTagName("row");
                    for (var i2 = 0; i2 < rows.length; i2++) {
                        var row = rows[i2];
                        balance = row.getAttribute("balance");
                        var options = {
                            useEasing: false,
                            useGrouping: true,
                            separator: '.',
                            decimal: ',',
                            prefix: '',
                            suffix: ' ISK'
                        };
                        var count = new CountUp("balanceSpan", 0, balance, 2, 1, options);
                        count.start();
                    }
                }
            });
        }

        function getWalletJournal(keyID, vCode, charIDs, refTypes, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/WalletJournal.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i] + "&rowCount=50",
                error: function (xhr, status, error) {
                    showError("Wallet Journal");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var rows = xml.getElementsByTagName("row");
                    var ownerName1;
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
                            }
                            var output = '';
                            output += '<tr>';
                            output += '<td data-label="Date">' + date + '</td>';
                            output += '<td data-label="refType">' + refTypeID + '</td>';
                            if (ownerName1 != "X") {
                                output += '<td data-label="From"><a onclick="getCharData(' + "'" + ownerName1 + "'" + ')">' + ownerName1 + '</a></td>';
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
                        $('#moreJournal50').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + refID + '", "50")');
                        $('#moreJournal100').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + refID + '", "100")');
                        $('#moreJournal250').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + refID + '", "250")');
                        $('#moreJournal1000').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + refID + '", "1000")');
                        $('#moreJournalAll').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + refID + '", "2560")');
                    }
                    else {
                        $('#moreJournal').html('There is no journal info available.');
                    }
                }
            });
        }

        function getMoreWalletJournal(keyID, vCode, charID, fromID, amountToLoad) {
            $('#loadingiconW').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $.ajax({
                url: "https://api.eveonline.com/char/WalletJournal.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charID + "&rowCount=" + amountToLoad + "&fromID=" + fromID,
                error: function (xhr, status, error) {
                    showError("Extended Wallet Journal");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var ownerName1;
                    var color;
                    var date;
                    var amount;
                    var refTypeID;
                    var balance;
                    var refID;
                    var rows = xml.getElementsByTagName("row");
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
                            }
                            var output = '';
                            output += '<tr>';
                            output += '<td data-label="Date">' + date + '</td>';
                            output += '<td data-label="refType">' + refTypeID + '</td>';
                            if (ownerName1 != "X") {
                                output += '<td data-label="From"><a onclick="getCharData(' + "'" + ownerName1 + "'" + ')">' + ownerName1 + '</a></td>';
                            }
                            else {
                                if ($(window).width() > 768) {
                                    output += '<td></td>';
                                }
                            }
                            output += '<td style="color:' + color + '" data-label="Amount">' + (parseFloat(amount)).formatMoney(2, ',', '.') + ' ISK</td>';
                            output += '<td data-label="Balance">' + (parseFloat(balance)).formatMoney(2, ',', '.') + ' ISK</td></tr>';
                            $('#WalletJournalBody').append(output);
                        }
                        $('#moreJournal50').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "' + refID + '", "50")');
                        $('#moreJournal100').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "' + refID + '", "100")');
                        $('#moreJournal250').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "' + refID + '", "250")');
                        $('#moreJournal1000').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "' + refID + '", "1000")');
                        $('#moreJournalAll').attr('onclick', 'getMoreWalletJournal("' + keyID + '", "' + vCode + '", "' + charID + '", "' + refID + '", "2560")');
                    }
                    else {
                        $('#moreJournal').html('There is no more journal info available.');
                    }
                    $('#loadingiconW').html('');
                }
            });
        }

        function getWalletTransactions(keyID, vCode, charIDs, i) {
            $.ajax({
                url: "https://api.eveonline.com/char/WalletTransactions.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i] + "&rowCount=50",
                error: function (xhr, status, error) {
                    showError("Wallet Transactions");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
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
                        $('#moreTransactions50').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + transactionID + '", "50")');
                        $('#moreTransactions100').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + transactionID + '", "100")');
                        $('#moreTransactions250').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + transactionID + '", "250")');
                        $('#moreTransactions1000').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + transactionID + '", "1000")');
                        $('#moreTransactionsAll').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charIDs[i] + '", "' + transactionID + '", "2560")');
                    }
                    else {
                        $('#moreTransactions').html('There is no transaction info available.');
                    }
                }
            });
        }

        function getMoreWalletTransactions(keyID, vCode, charID, fromID, amountToLoad) {
            $('#loadingiconT').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            $.ajax({
                url: "https://api.eveonline.com/char/WalletTransactions.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charID + "&rowCount=" + amountToLoad + "&fromID=" + fromID,
                error: function (xhr, status, error) {
                    showError("Extended Wallet Transactions");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
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
                        $('#moreTransactions50').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "' + transactionID + '", "50")');
                        $('#moreTransactions100').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "' + transactionID + '", "100")');
                        $('#moreTransactions250').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "' + transactionID + '", "250")');
                        $('#moreTransactions1000').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "' + transactionID + '", "1000")');
                        $('#moreTransactionsAll').attr('onclick', 'getMoreWalletTransactions("' + keyID + '", "' + vCode + '", "' + charID + '", "' + transactionID + '", "2560")');
                    }
                    else {
                        $('#moreTransactions').html('There is no more transaction info available.');

                    }
                    $('#loadingiconT').html('');
                }
            });
        }
    </script>
    </body>
    </html>
<?php ob_flush(); ?>