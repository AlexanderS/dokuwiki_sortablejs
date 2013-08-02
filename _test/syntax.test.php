<?php

/**
 * Test the basic syntax of the SortableJS plugin.
 * 
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Sam Wilson <sam@samwilson.id.au>
 * @group   plugin_sortablejs
 */
class syntax_plugin_sortablejs_test extends DokuWikiTest {

    function setup() {
        $this->pluginsEnabled[] = 'sortablejs';
        parent::setup();
    }

    function test_serversort() {
        $in = "\n^Col1^Col3^Col3^\n"
            ."|2|two|10/12/2008|\n"
            ."|3|three|24/6/2013|\n"
            ."|1|one|25/12/2017|\n";
        $out = "\n^Col1^Col3^Col3^\n"
            ."|3|three|24/6/2013|\n"
            ."|2|two|10/12/2008|\n"
            ."|1|one|25/12/2017|\n";
        $plugin = new syntax_plugin_sortablejs();
        $in = $plugin->get_reordered_table($in, 'r1');
        $out = p_get_instructions($out);
        $this->assertEquals($out, $in);
    }

    function test_options() {
        // No options
        $in = '<sortable></sortable>';
        $out = '<div class="sortable"></div>';
        $rendered_in = p_render('xhtml', p_get_instructions($in), $info);
        $this->assertEquals($rendered_in, $out);

        // Normal options
        $in = '<sortable r1 2 4 r6></sortable>';
        $out = '<div class="sortable r1 2 4 r6"></div>';
        $rendered_in = p_render('xhtml', p_get_instructions($in), $info);
        $this->assertEquals($rendered_in, $out);

        // Quote characters in the options
        $in = '<sortable r1 \'2\' 4 "r6"></sortable>';
        $out = '<div class="sortable r1 2 4 r6"></div>';
        $rendered_in = p_render('xhtml', p_get_instructions($in), $info);
        $this->assertEquals($rendered_in, $out);
    }

}
