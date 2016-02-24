(function () {

    this.getTypeNames = function (type, typeIDs){
        //Split typeIDs into smaller parts if it's too big
        var maxSize = 250;
        var typeIDsPart;
        if (typeIDs.length > maxSize) {
            for (var i = 0; i < typeIDs.length; i += maxSize) {
                typeIDsPart = typeIDs.slice(i, i + maxSize);
                getTypeNames(type ,typeIDsPart);
            }
        }

        //Create string of the IDs to send with the call
        var typeIDString = "";
        for (i = 0; i < typeIDs.length; i++) {
            typeIDString += typeIDs[i] + ",";
        }
        typeIDString = typeIDString.substring(0, typeIDString.length - 1);

        //Make the call to the API servers
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                var xml = request.responseXML;
                var rows = xml.getElementsByTagName("row");
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var typeID = row.getAttribute("typeID");
                    var typeName = row.getAttribute("typeName");
                    $("#" + type + typeID).html(typeName);
                }

            }
        };
        request.open("GET", "https://api.eveonline.com/eve/TypeName.xml.aspx?ids=" + typeIDString, true);
        request.send();
    };

    this.parseTimeRemaining = function (now, end) {
        var _day, _hour, _minute, _second, d, days, distance, ds, h, hours, hs, m, minutes, ms, output, s, seconds, ss;
        distance = end - now;
        if (distance < 1) {
            clearInterval(timer);
            $("#Timeleft " + i).html('0<br>');
            return;
        }
        _second = 1000;
        _minute = _second * 60;
        _hour = _minute * 60;
        _day = _hour * 24;
        days = Math.floor(distance / _day);
        hours = Math.floor((distance % _day) / _hour);
        minutes = Math.floor((distance % _hour) / _minute);
        seconds = Math.floor((distance % _minute) / _second);
        d = " day";
        ds = " days";
        h = " hour";
        hs = " hours";
        m = " minute";
        ms = " minutes";
        s = " second";
        ss = " seconds";
        output = '';
        if (days > 0) {
            output += days + " " + (this.Pluralize(d, ds, days));
        }
        if (hours > 0) {
            if (minutes === 0 && seconds === 0 && days !== 0) {
                output += " and " + hours + " " + (this.Pluralize(h, hs, hours));
            } else if (days !== 0 && (minutes !== 0 || seconds !== 0)) {
                output += ", " + hours + " " + (this.Pluralize(h, hs, hours));
            } else {
                output += hours + " " + (this.Pluralize(h, hs, hours));
            }
        }
        if (minutes > 0) {
            if (seconds === 0 && (days !== 0 || hours !== 0)) {
                output += " and " + minutes + " " + (this.Pluralize(m, ms, minutes));
            } else if ((hours !== 0 || days !== 0) && (seconds !== 0 || hours !== 0)) {
                output += ", " + minutes + " " + (this.Pluralize(m, ms, minutes));
            } else {
                output += minutes + " " + (this.Pluralize(m, ms, minutes));
            }
        }
        if (seconds > 0) {
            output += " and " + seconds + " " + (this.Pluralize(s, ss, seconds));
        }
        return output;
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

}).call(this);
