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

require_once DOKU_INC . 'lib/plugins/syntax.php';

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


        if ($mode == 'xhtml') $renderer->doc .= '</div>';
        if ($mode == 'odt') $renderer->p_open();

        return true;
    }
}
