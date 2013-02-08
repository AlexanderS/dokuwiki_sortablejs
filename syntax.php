<?php
/**
 * Sortablejs: Javascript for Sortable HTML tables, using the Tablesorter jQuery
 * plugin.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Otto Vainio
 * @author     Sam Wilson <sam@samwilson.id.au>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_sortablejs extends DokuWiki_Syntax_Plugin {

    function getType() {
        return 'container';
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
        $this->Lexer->addEntryPattern('<sortable[^>]*>(?=.*?</sortable>)', $mode, 'plugin_sortablejs');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('</sortable>', 'plugin_sortablejs');
    }

    function handle($match, $state, $pos, &$handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER :
                // Pass all options straight through, without special characters
                $prefix = 'sortable ';
                $options = substr($match, strlen($prefix), -1);
                $clean_options = preg_replace('|["\']|', '', $options);
                return array($state, $clean_options);
                break;
            case DOKU_LEXER_UNMATCHED :
                return array($state, $match);
                break;
            case DOKU_LEXER_EXIT :
                return array($state, "");
                break;
        }
        return array();
    }

    function render($mode, &$renderer, $data) {
        list($state, $match) = $data;
        if ($mode == 'xhtml') {
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    $renderer->doc .= "<div class=\"sortable$match\">";
                    break;
                case DOKU_LEXER_UNMATCHED :
                    $instructions = p_get_instructions($match);
                    foreach ($instructions as $instruction) {
                        call_user_func_array(array(&$renderer, $instruction[0]), $instruction[1]);
                    }
                    break;
                case DOKU_LEXER_EXIT :
                    $renderer->doc .= "</div>";
                    break;
            }
            return true;
        } else if ($mode == 'odt') {
            switch ($state) {
                case DOKU_LEXER_ENTER :
                    // In ODT, tables must not be inside a paragraph. Make sure we
                    // closed any opened paragraph
                    $renderer->p_close();
                    break;
                case DOKU_LEXER_UNMATCHED :
                    $instructions = array_slice(p_get_instructions($match), 1, -1);
                    foreach ($instructions as $instruction) {
                        call_user_func_array(array(&$renderer, $instruction[0]), $instruction[1]);
                    }
                    break;
                case DOKU_LEXER_EXIT :
                    $renderer->p_open(); // re-open the paragraph
                    break;
            }
            return true;
        }
        return false;
    }

}
