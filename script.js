/* DOKUWIKI:include scripts/jquery.tablesorter.js */

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
