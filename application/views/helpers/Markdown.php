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
require_once('markdown.php');

class FansubCMS_View_Helper_Markdown {
	/**
	 * Wandelt Markdown in HTML um
	 * @param string $text
	 * @return string
	 */
	public function markdown($text) {
		$parser = new MarkdownExtra_Parser();
		$text = $parser->transform($text);
		return $parser->doHardBreaks($text);
	}
	
}