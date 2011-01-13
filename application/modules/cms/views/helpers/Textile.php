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
require_once('classTextile.php');
class FansubCMS_View_Helper_Textile {
	/**
	 * Changes textile to HTML
	 * @param string $text
	 * @return string
	 */
	public function textile($text) {
		$parser = new Textile();
		return $parser->TextileThis($text);
	}
	
}