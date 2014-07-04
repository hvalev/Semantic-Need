<?php
/*******************************************************************************
*	This file is part of Woogle4MediaWiki
*   (http://www.mediawiki.org/wiki/Extension:Woogle4MediaWiki)
*
*	Copyright (c) 2007 - 2010 Hans-Jörg Happel and
*	FZI Forschungszentrum Informatik an der Universität Karlsruhe (TH)
*
*   Woogle4MediaWiki is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Woogle4MediaWiki is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Woogle4MediaWiki. If not, see <http://www.gnu.org/licenses/>.
*******************************************************************************/ 

// NOTE: MediaWiki core code needs to be included before this file!

// This is not a valid entry point, perform no further processing unless MEDIAWIKI is defined
if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of Woogle4MediaWiki and is not a valid entry point\n";
	die( 1 );
}

class MockUser extends User{

	private $uname;
	private $options = array();
	private $isAllowed;
	
	public function __construct($uname = 'anon') {
		$this->uname = $uname;
	}

	function getName(){
		return $this->uname;
	}	
	
	function isAnon(){
		return ($this->uname == 'anon');
	}
	
	function setOption($name, $value){
		echo '[' . date('Y-m-d H:i:s') . '] ' . 'MockUser setOption: ' . $name . ' to ' . $value . "\n"; 
		$this->options[$name] = $value;
	}
	
	function getOption($name, $default=null){
		
		if (isset($this->options[$name])) $ret = $this->options[$name];
		if (!isset($ret)){
			$ret = $default;
		} 
		//echo '[' . date('Y-m-d H:i:s') . '] ' . 'MockUser getOption: ' . $name . ' returning ' . $ret. "\n"; 
		return $ret;
	}
	/* quoted for fixing php unit test 
	function isAllowed(){
		return $this->isAllowed;
	}
	
	function setIsAllowed($bool){
		$this->isAllowed = $bool;
	}*/
	
	function saveSettings(){}

	function isLoggedIn(){
		return !($this->isAnon());
	}

}

?>
