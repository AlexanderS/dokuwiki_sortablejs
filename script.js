/* DOKUWIKI:include tablesorter/js/jquery.tablesorter.js */

jQuery(document).ready(function() {

    jQuery("div.sortable table").each(function() {
        $table = jQuery(this);

        // Tablesorter requires a THEAD element, so we find the first TH and move
        // its parent row to the new THEAD.
        $thead = jQuery("<thead></thead>");
        $table.find("tr th").first().parent("tr").prependTo($thead);
        $thead.prependTo($table);

        // Get and parse options
        var input = $table.parents("div.sortable").attr('class').split(' ');
        var sortListOptions = [];
        for (var i=0; i < input.length; i++) {
            var option = input[i];
            if (option==="sortable") continue;
            // Collapse all numbers together to get the column number
            var col_num = parseInt(option.replace(/[^\d]/g, ''));
            // Any 'R' anywhere is 'reverse'
            var sort_dir = (option.toLowerCase().indexOf("r") >= 0) ? 1 : 0;
            // Put the options together, for column numbering from 0 not 1
            sortListOptions.push([col_num - 1, sort_dir]);
        }

        // Enable Tablesorter
        $table.addClass("tablesorter").tablesorter({
            sortList: sortListOptions
        });

    });

});
