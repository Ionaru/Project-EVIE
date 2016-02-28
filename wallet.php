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
            var refRequest = new XMLHttpRequest();
            refRequest.onreadystatechange = function () {
                if (refRequest.readyState == 4 && refRequest.status == 200) {
                    var xml = refRequest.responseXML;
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        refTypes[row.getAttribute("refTypeID")] = row.getAttribute("refTypeName");
                    }
                    var charIDs = [];
                    var charRequest = new XMLHttpRequest();
                    charRequest.onreadystatechange = function () {
                        var charID;
                        if (charRequest.readyState == 4 && charRequest.status == 200) {
                            var xml = charRequest.responseXML;
                            var rows = xml.getElementsByTagName("row");
                            for (i = 0; i < rows.length; i++) {
                                var row = rows[i];
                                charID = row.getAttribute("characterID");
                                charIDs[i] = charID;
                            }
                            $('#WalletContent').append('' +
                                '<h2>Current balance: ' +
                                '<br class="visible-xs"/>' +
                                '<span id="balanceSpan"></span>' +
                                '</h2>' +
                                '<a class="anchor" name="Journal"></a>' +
                                '<h2 style="display: inline;">Journal</h2>' +
                                '<a href="#Transactions"> Jump to Transactions</a>' +
                                '<table class="table">' +
                                '<thead><tr>' +
                                '<th style="width: 20%">Date (EVE Time)</th>' +
                                '<th style="width: 20%">Type</th>' +
                                '<th style="width: 20%">From</th>' +
                                '<th style="width: 20%">Amount</th>' +
                                '<th style="width: 20%">Balance</th>' +
                                '</tr></thead>' +
                                '<tbody id="WalletJournalBody"></tbody>' +
                                '</table>' +
                                '<span id="moreJournal">Load more entries ' +
                                '<a style="cursor: pointer;" id="moreJournal50">50</a> ' +
                                '<a style="cursor: pointer;" id="moreJournal100">100</a> ' +
                                '<a style="cursor: pointer;" id="moreJournal250">250</a> ' +
                                '<a style="cursor: pointer;" id="moreJournal1000">1000</a> ' +
                                '<a style="cursor: pointer;" id="moreJournalAll">Max</a>' +
                                '</span> ' +
                                '<span id="loadingiconW"></span>' +
                                '<hr>' +
                                '<a class="anchor" name="Transactions"></a>' +
                                '<h2 style="display: inline;">Transactions</h2>' +
                                '<a href="#Journal"> Jump to Journal</a>' +
                                '<table class="table">' +
                                '<thead><tr>' +
                                '<th style="width: 20%">Date (EVE Time)</th>' +
                                '<th style="width: 40%">Information</th>' +
                                '<th style="width: 40%">Price</th>' +
                                '</tr></thead>' +
                                '<tbody id="WalletTransactionsBody"></tbody>' +
                                '</table>' +
                                '<span id="moreTransactions">Load more entries ' +
                                '<a style="cursor: pointer;" id="moreTransactions50">50</a> ' +
                                '<a style="cursor: pointer;" id="moreTransactions100">100</a> ' +
                                '<a style="cursor: pointer;" id="moreTransactions250">250</a> ' +
                                '<a style="cursor: pointer;" id="moreTransactions1000">1000</a> ' +
                                '<a style="cursor: pointer;" id="moreTransactionsAll">Max</a></span> ' +
                                '<span id="loadingiconT"></span>');
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
                            getWalletJournal(keyID, vCode, charIDs, refTypes, <?php echo $selectedChar ?>);
                            getWalletTransactions(keyID, vCode, charIDs, <?php echo $selectedChar ?>);
                        }
                    };
                    charRequest.open("GET", "https://api.eveonline.com/account/Characters.xml.aspx?keyID=" + keyID + "&vCode=" + vCode, true);
                    charRequest.send();
                }
            };
            refRequest.open("GET", "https://api.eveonline.com/eve/RefTypes.xml.aspx", true);
            refRequest.send();
        });

        function getRefTypes() {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var refTypes = {};
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
                        var count = new CountUp("balanceSpan", 0, balance, 2, 1, options);
                        count.start();
                    }
                }
            };
            request.open("GET", "https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i], true);
            request.send();
        }

        function getWalletJournal(keyID, vCode, charIDs, refTypes, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var ownerName1;
                var date;
                var amount;
                var refTypeID;
                var balance;
                var refID;
                var color;
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
                    //console.log(xml);
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
                                output += '<td data-label="From"><a style="cursor: pointer;" onclick="getCharData(' + "'" + ownerName1 + "'" + ')">' + ownerName1 + '</a></td>';
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
            };
            request.open("GET", "https://api.eveonline.com/char/WalletJournal.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i] + "&rowCount=50", true);
            request.send();
        }

        function getMoreWalletJournal(keyID, vCode, charID, fromID, amountToLoad) {
            //refTypes;
            $('#loadingiconW').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
                var ownerName1;
                var color;
                var date;
                var amount;
                var refTypeID;
                var balance;
                var refID;
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
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
                                ownerName1 = "";
                            }
                            else {
                                color = "green";
                                ownerName1 = row.getAttribute("ownerName1");
                            }
                            var output = '<tr><td data-label="Date">' + date + '</td>';
                            output += '<td data-label="refType">' + refTypeID + '</td>';
                            if (ownerName1 != "X") {
                                output += '<td data-label="From"><a style="cursor: pointer;" onclick="getCharData(' + "'" + ownerName1 + "'" + ')">' + ownerName1 + '</a></td>';
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
            };
            request.open("GET", "https://api.eveonline.com/char/WalletJournal.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charID + "&rowCount=" + amountToLoad + "&fromID=" + fromID, true);
            request.send();
        }

        function getWalletTransactions(keyID, vCode, charIDs, i) {
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
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
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
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
                            //console.log(clientName);
                            $('#WalletTransactionsBody').append('<tr><td data-label="Date">' + date + '</td><td data-label="Information">' + quantity + ' x <a style="cursor: pointer;" onclick="getItemData(' + "'" + typeID + "'" + ')">' + typeName + '</a>' + info + ' <a style="cursor: pointer;" onclick="getCharData(' + "'" + clientName + "'" + ')">' + clientName + '</a></td><td data-label="Price" style="color: ' + color + '">' + (parseFloat(price * quantity)).formatMoney(2, ',', '.') + ' ISK (' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK per item)</td></tr>');
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
            };
            request.open("GET", "https://api.eveonline.com/char/WalletTransactions.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charIDs[i] + "&rowCount=50", true);
            request.send();
        }

        function getMoreWalletTransactions(keyID, vCode, charID, fromID, amountToLoad) {
            $('#loadingiconT').html('<i class="fa fa-spin fa-circle-o-notch"></i>');
            var request = new XMLHttpRequest();
            request.onreadystatechange = function () {
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
                if (request.readyState == 4 && request.status == 200) {
                    var xml = request.responseXML;
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
                            //console.log(clientName);
                            $('#WalletTransactionsBody').append('<tr><td data-label="Date">' + date + '</td><td data-label="Information">' + quantity + ' x <a style="cursor: pointer;" onclick="getItemData(' + "'" + typeID + "'" + ')">' + typeName + '</a>' + info + ' <a style="cursor: pointer;" onclick="getCharData(' + "'" + clientName + "'" + ')">' + clientName + '</a></td><td data-label="Price" style="color: ' + color + '">' + (parseFloat(price * quantity)).formatMoney(2, ',', '.') + ' ISK (' + (parseFloat(price)).formatMoney(2, ',', '.') + ' ISK per item)</td></tr>');
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
            };
            request.open("GET", "https://api.eveonline.com/char/WalletTransactions.xml.aspx?keyID=" + keyID + "&vCode=" + vCode + "&characterID=" + charID + "&rowCount=" + amountToLoad + "&fromID=" + fromID, true);
            request.send();
        }
    </script>
    </body>
    </html>
<?php ob_flush(); ?>