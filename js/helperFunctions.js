(function () {

    var problems = 0;

    this.getTypeNames = function (typeIDsRaw) {
        /**
         * @Function to convert typeIDs to TypeNames
         *
         * @param {Array} typeIDsRaw - An array with IDs to convert
         *
         * Pages will collect all typeIDs in a single array and send it to this function at the end of all other processes
         *
         */
        var arraySize = typeIDsRaw.length;
        if (arraySize > 0) {
            /**
             *  Check the array for duplicates and remove them
             */
            var typeIDs = uniq(typeIDsRaw);
            /**
             * Split the typeIDs array into smaller parts if needed (max 250 in an array)
             * It will recall this function for every array chunk it creates
             */
            var maxSize = 250;
            if (arraySize > maxSize) {
                for (var i = 0; i < typeIDs.length; i += maxSize) {
                    getTypeNames(typeIDs.slice(i, i + maxSize));
                }
            }
            /**
             * Convert the array into a long string seperated by commas
             * And cut the last comma
             */
            var typeIDString = "";
            for (i = 0; i < typeIDs.length; i++) {
                typeIDString += typeIDs[i] + ",";
            }
            typeIDString = typeIDString.substring(0, typeIDString.length - 1);
            /**
             * Send the request to the EVE Online API servers with the final string (typeIDString) and change html elements to the correct TypeNames
             */
            $.ajax({
                url: "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + typeIDString,
                error: function (xhr, status, error) {
                    showError("Get Item Names");
                    // TODO: implement fancy error logging
                },
                success: function (xml) {
                    var rows = xml.getElementsByTagName("row");
                    for (var i = 0; i < rows.length; i++) {
                        var row = rows[i];
                        var typeID = row.getAttribute("typeID");
                        var typeName = row.getAttribute("typeName");
                        $("[id=" + typeID + "]").html(typeName);
                    }
                }
            });
        }
    };

    this.parseTimeRemaining = function (now, end, elementID, doTimeTick, expiredMessage) {
        var _day, _hour, _minute, _second, d, days, distance, ds, h, hours, hs, m, minutes, ms, output, s, seconds, ss, timer;
        $(elementID).html("Calculating Time...");
        try {
            now = Date.parse(now.replace(/\-/ig, '/').split('.')[0]);
            end = Date.parse(end.replace(/\-/ig, '/').split('.')[0]);
        }
        catch (TypeError) {

        }

        timer = void 0;
        distance = end - now;

        if (doTimeTick == null) {
            doTimeTick = false;
        }
        if (expiredMessage == null) {
            expiredMessage = '0';
        }

        if (distance < 1) {
            clearInterval(timer);
            $(elementID).html(expiredMessage + '<br>');
            return;
        }
        d = " day";
        ds = " days";
        h = " hour";
        hs = " hours";
        m = " minute";
        ms = " minutes";
        s = " second";
        ss = " seconds";
        function calculateTime() {
            output = '';
            _second = 1000;
            _minute = _second * 60;
            _hour = _minute * 60;
            _day = _hour * 24;
            days = Math.floor(distance / _day);
            hours = Math.floor((distance % _day) / _hour);
            minutes = Math.floor((distance % _hour) / _minute);
            seconds = Math.floor((distance % _minute) / _second);
            if (days > 0) {
                output += days + " " + (Pluralize(d, ds, days));
            }
            if (hours > 0) {
                if (minutes === 0 && seconds === 0 && days !== 0) {
                    output += " and " + hours + " " + (Pluralize(h, hs, hours));
                } else if (days !== 0 && (minutes !== 0 || seconds !== 0)) {
                    output += ", " + hours + " " + (Pluralize(h, hs, hours));
                } else {
                    output += hours + " " + (Pluralize(h, hs, hours));
                }
            }
            if (minutes > 0) {
                if (seconds === 0 && (days !== 0 || hours !== 0)) {
                    output += " and " + minutes + " " + (Pluralize(m, ms, minutes));
                } else if ((hours !== 0 || days !== 0) && (seconds !== 0 || hours !== 0)) {
                    output += ", " + minutes + " " + (Pluralize(m, ms, minutes));
                } else {
                    output += minutes + " " + (Pluralize(m, ms, minutes));
                }
            }
            if (seconds > 0) {
                output += " and " + seconds + " " + (Pluralize(s, ss, seconds));
            }
            distance -= 1000;
            $(elementID).html(output);
        }

        if (doTimeTick) {
            timer = setInterval(calculateTime, 1000);
        }
        else {
            calculateTime();
        }
    };

    this.Pluralize = function (Single, Plural, number) {
        if (number === 1) {
            return Single;
        } else {
            return Plural;
        }
    };

    this.Number.prototype.formatMoney = function (c, d, t) {
        var i, j, n, s;
        if (c == null) {
            c = 2;
        }
        if (d == null) {
            d = ',';
        }
        if (t == null) {
            t = '.';
        }
        n = this;
        s = n < 0 ? '-' : '';
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '';
        j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
    };

    this.uniq = function (a) {
        var seen = {};
        return a.filter(function (item) {
            return seen.hasOwnProperty(item) ? false : (seen[item] = true);
        });
    };

    this.showError = function (module) {
        if ($("#alertBox").length == 0) {
            $("#alertSpan").html('<div id="alertBox" class="alert alert-danger" role="alert"><p><strong>One or more problems were detected while loading this page. :(</strong></p></div>');
        }
        problems++;
        $("#alertBox").append('<p>- Problem #' + problems + ' - There was an error in the \'' + module + '\' module.</p>');
    };

}).call(this);
