/* DOKUWIKI:include scripts/jquery.tablesorter.js */

jQuery.tablesorter.addParser({
  id: 'ips',
  is: function(s, table, cell) {
    return false;
  },
  format: function(s, table, cell, cellIndex) {
    if (s.indexOf(":") != -1) {
      // IPv6 - needs some magic parsing
      str = s.toLowerCase();
      ip = str.split('%')[0];
      segments = ip.split(":");

      var seglen = segments.length;
      if ((segments[0] == '') && (segments[1] == '') && (segments[2] == "")) {
        segments.shift();
        segments.shift();
      }
      else if ((segments[0] == '') && (segments[1] == '')) {
        segments.shift();
      }
      else if ((segments[seglen-1] == '') && (segments[seglen-2] == '')) {
        segments.pop();
      }

      var numsegments = 8;
      if (segments[segments.length-1].indexOf(".") != -1) {
        numsegments = 7;
      }

      var pos;
      for (pos=0; pos<segments.length; pos++) {
        if (segments[pos] == '') {
          segments.splice(pos, 1, "0000");
          pos++;
          while (segments.length < numsegments) {
            segments.splice(pos, 0, "0000");
            pos++;
          }
        }

        segments[pos] = ("000" + segments[pos]).slice(-4);
      }
      return segments.join(":");
    }
    else if (s.indexOf(".") != -1) {
      // IPv4
      var i, a = s.split("."),
        r = "",
        l = a.length;
      for (i = 0; i < l; i++) {
        r += ("00" + a[i]).slice(-3);
      }
      return r;
    }

    return s;
  },
  type: 'text'
});

jQuery(document).ready(function() {
    jQuery("div.sortable").each(function() {
        $div = jQuery(this);
        $table = $div.find('table').first();

        // tablesorter requires a <thead> element, so we find the first
        // <th> and move its parent row to the new <thead>.
        $thead = jQuery("<thead></thead>");
        $table.find("tr th").first().parent("tr").prependTo($thead);
        $thead.prependTo($table);

        // enable tablesorter
        opts = $div.attr('data-sort');
        if (typeof opts != "undefined") {
            try {
                $table.tablesorter(jQuery.parseJSON(opts));
                return;
            }
            catch (e) {
                console.log('%s: %s', e, e.message)
            }
        }

        $table.tablesorter();
    });
});
