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
        $this->Lexer->addEntryPattern(
            '<sortable\\b[^>]*>(?=.*?</sortable>)',
            $mode,
            'plugin_sortablejs');
    }

    function postConnect() {
        $this->Lexer->addExitPattern(
            '</sortable>',
            'plugin_sortablejs');
    }

    function handle($match, $state, $pos, &$handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                return array($state, $match);
                break;
            case DOKU_LEXER_MATCHED:
                return array($state, $match);
                break;
            case DOKU_LEXER_UNMATCHED:
                return array($state, $match);
                break;
            case DOKU_LEXER_EXIT:
                return array($state, '');
                break;
        }
        return array();
    }

    function render($mode, &$renderer, $data) {
        list($state, $match) = $data;

        switch ($state) {
            case DOKU_LEXER_ENTER:
                if ($mode == 'xhtml')
                    if (preg_match('/<sortable\\b([^>]+)>/', $match, $matches)) {
                        $renderer->doc .= '<div class="sortable" data-sort="' . str_replace("'", '&quot;', trim($matches[1])) . '">';
                    }
                    else {
                        $renderer->doc .= '<div class="sortable">';
                    }
                break;

            case DOKU_LEXER_UNMATCHED:
                if ($mode == 'xhtml')
                    $renderer->doc .= $renderer->_xmlEntities($match);
                else
                    $renderer->doc .= $match;
                break;

            case DOKU_LEXER_EXIT:
                if ($mode == 'xhtml')
                    $renderer->doc .= '</div>';
                break;
        }

        return true;
    }
}
