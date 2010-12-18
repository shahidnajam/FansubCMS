<?php
/**
 * Dieser View_Helper bietet die Möglichkeit, einen Markdown formatierten Text
 * in HTML zu transformieren.
 * 
 * @see MarkdownExtra_Parser
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @package FansubCMS
 * @subpackage View_Helper
 * @version 1.0
 * 
 */

class FansubCMS_View_Helper_Textile {
	/**
	 * Changes textile to HTML
	 * @param string $text
	 * @return string
	 */
	public function textile($text) {
		$parser = Zend_Markup::factory('Textile');
		return $parser->render($text);
	}
	
}