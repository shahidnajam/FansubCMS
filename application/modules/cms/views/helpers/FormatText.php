<?php
/**
 * formats texts
 *
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @package FansubCMS
 * @subpackage View_Helper
 * @version SVN: $Id
 *
 */
class FansubCMS_View_Helper_FormatText extends Zend_View_Helper_Abstract {
	/**
	 * formats texts
	 * @param string $text
	 * @return string
	 */
    public function formatText($text) {
    	$text = str_replace("\xC2\xA0",' ',$text); // nbsp ersetzen durch normanel space
    	$text = trim($text,"\n"); // unnötige zeichen am anfang und ende entfernen
        $this->_wrapText($text);
    	$text = $this->view->escape($text);
    	$text = nl2br($text); // Zeilenumbrüche hinzufügen
    	// URLs verlinken
        $pattern = "!\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))!";
   	    $text = preg_replace($pattern, '<a href="\1" target="_blank">\1</a>', $text); // urls mit links versehen

   	    return $text;
    }

    private function _wrapText(&$text) {
        $lines = explode("\n",$text);
        $text = '';
        foreach($lines as $k => $line) {
            $words = explode(" ",$line);
            foreach($words as $j => $word) {
                $words[$j] = wordwrap($word, 35, " ", true);
            }
            $line = implode(" ",$words);
            $lines[$k] = $line;
        }
        $text = implode("\n",$lines);
    }
}