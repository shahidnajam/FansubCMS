<?php
/*
 *  This file is part of FansubCMS.
 *
 *  FansubCMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FansubCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FansubCMS.  If not, see <http://www.gnu.org/licenses/>
 */

/**
 * This class is for simple use of Akismet to check for spam
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @package FansubCMS
 * @subpackage Validator
 * @version SVN: $Id
 */
class FansubCMS_Validator_Akismet {
	/**
	 * @var string $_apiKey The WordPress API Key
	 */
	private $_apiKey;
	/**
	 * @var Akismet
	 */
	private $_akismet;
	
	/**
	 * creates an instance an sets the API key to the one specified in the environment config
	 */
	public function __construct() {
		$conf = Zend_Registry::get('environmentSettings');
		$this->setApiKey($conf->wordpress->api); // also generates the akismet instance
	}
	
	/**
	 * gets an instance of akismet to private property
	 * @param string $api
	 * @return Akismet
	 */
	private function setAkismet($api) {
		$this->_akismet = new Akismet(getenv('SERVER_NAME').getenv('REQUEST_URI'),$api);
		return $this->_akismet;
	}
	
	/**
	 * sets the api key
	 * @param string $api
	 * @return Akismet
	 */
	public function setApiKey($api) {
		$this->_apiKey = $api;
		return $this->setAkismet($api);
	}
	
	/**
	 * get the configured instance of Akismet
	 * @return Akismet
	 */
	public function getAkismet() {
		return $this->_akismet;
	}
}