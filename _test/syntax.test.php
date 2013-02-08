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

    function test() {
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
