/* DOKUWIKI:include jquery.tablesorter.js */

jQuery(document).ready(function() {

    // Tablesorter requires a THEAD element, so we find the first TH and move
    // its parent row to the new THEAD.
    jQuery("div.sortable table").each(function() {
        $thead = jQuery("<thead></thead>");
        jQuery(this).find("tr th").first().parent("tr").prependTo($thead);
        $thead.prependTo(jQuery(this));
    });

    // Enable Tablesorter on all sortable tables.
    jQuery("div.sortable table").addClass("tablesorter").tablesorter();

});
