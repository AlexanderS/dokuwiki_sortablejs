<?php
/**
 * Sortablejs: Javascript for Sortable HTML tables, using the Tablesorter jQuery
 * plugin.
 *
 * @link    http://dokuwiki.org/plugin:sortablejs Plugin documentation
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Otto Vainio
 * @author  Sam Wilson <sam@samwilson.id.au>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_sortablejs extends DokuWiki_Syntax_Plugin {

    /** @var array Sort options */
    private $opts = array();

    function getType() {
        return 'substition';
    }

    function getPType() {
        return 'block';
    }

    function getSort() {
        return 371;
    }

    function getAllowedTypes() {
        return array('container', 'formatting', 'substition');
    }

    function connectTo($mode) {
        $pattern = '<sortable[^>]*>.*?</sortable>';
        $this->Lexer->addSpecialPattern($pattern, $mode, 'plugin_sortablejs');
    }

    function handle($match, $state, $pos, &$handler) {
        if ($state == DOKU_LEXER_SPECIAL) {
            $pattern = '|<sortable([^>]*)>(.*)</sortable>|s';
            preg_match_all($pattern, $match, $matches);
            $options = preg_replace('|["\']|', '', $matches[1][0]);
            return array($options, $matches[2][0]);
            break;
        }
        return array();
    }

    function render($mode, &$renderer, $data) {
        list($opt_string, $table) = $data;

        if ($mode == 'xhtml') $renderer->doc .= '<div class="sortable'.$opt_string.'">';
        if ($mode == 'odt') $renderer->p_close();

        $instructions = $this->get_reordered_table($table, $opt_string);
        foreach (array_slice($instructions, 1, -1) as $instruction) {
            call_user_func_array(array(&$renderer, $instruction[0]), $instruction[1]);
        }

        if ($mode == 'xhtml') $renderer->doc .= '</div>';
        if ($mode == 'odt') $renderer->p_open();

        return true;
    }

    /**
     * Split options into an array describing initial sort orders and formats.
     * 
     * @param string $opt_string Space-delimited options
     * @return string
     */
    private function parse_opts($opt_string) {
        $out = array();
        $opts = explode(' ', $opt_string);
        foreach ($opts as $o) {
            if (empty($o)) continue;
            $fmt = '';
            $dir = 'asc';
            if (strpos($o, '=')) {
                list($num, $fmt) = explode('=', $o);
            } else {
                if (strpos($o, 'r')!==false) $dir = 'desc';
                $num = preg_replace('/[^0-9]/', '', $o);
            }
            $out[$num] = array('num'=>$num, 'dir'=>$dir, 'format'=>$fmt);
        }
        return $out;
    }

    /**
     * Get instructions for a reordered table, striping the start and end
     * document items. This has public visibility for ease of testing.
     * @todo This doesn't reset byte offsets properly.
     * 
     * @param string $wikitable Wikitext of a table.
     * @return array
     */
    public function get_reordered_table($wikitable, $options) {
        // Get instructions
        $in = p_get_instructions($wikitable);

        $rows = array();
        $row = array();
        $in_row = false;
        $pre_instructions = array();
        $post_instructions = array();

        // Divide the instructions up into pre, rows, and post.
        foreach ($in as $k => $v) {

            // Start of a row
            if ($v[0] == 'tablerow_open') $in_row = true;

            // Don't sort rows with any headers
            if ($v[0] == 'tableheader_open') $row['sortable'] = false;
            elseif ($v[0] == 'tablecell_open' && !isset($row['sortable'])) $row['sortable'] = true;

            // Collect instructions that aren't in a sortable row
            if ($in_row) $row['instructions'][] = $v;
            elseif (count($rows)>0) $post_instructions[] = $v;
            else $pre_instructions[] = $v;

            // Cell values, for sorting against
            if ($v[0] == 'cdata') $row['vals'][] = $v[1][0];

            // End of a row
            if ($v[0] == 'tablerow_close') {
                // Save sortable rows for sorting; append others to pre
                if ($row['sortable']) $rows[] = $row;
                else $pre_instructions = array_merge($pre_instructions, $row['instructions']);
                $row = array(); // Reset for next pass
                $in_row = false;
            }

        } // end of instructions' loop

        // Sort the table rows with $this->sort() and the given options.
        $this->opts = $this->parse_opts($options);
        usort($rows, array($this, 'sort'));

        // Collect all the instructions back together again
        $out = $pre_instructions;
        foreach ($rows as $r) {
            $out = array_merge($out, $r['instructions']);
        }
        return array_merge($out, $post_instructions);
    }

    /**
     * Compare two table rows.
     * 
     * This comparison function must return an integer less than, equal to, or
     * greater than zero if the first argument is considered to be respectively
     * less than, equal to, or greater than the second. 
     * @todo Support format designators
     * 
     * @param array $a
     * @param array $b
     * @return integer
     */
    private function sort($a, $b) {
        $out = 0;
        foreach ($this->opts as $o) {
            $col_num = $o['num'] - 1; // First column is 0th
            if ($o['dir']=='asc') {
                $out += strcmp($a['vals'][$col_num], $b['vals'][$col_num]);
            } else {
                $out += strcmp($b['vals'][$col_num], $a['vals'][$col_num]);
            }
        }
        return $out;
    }

}
