<?php
/*******************************************************************************
*	This file is part of Woogle4MediaWiki
*   (http://www.mediawiki.org/wiki/Extension:Woogle4MediaWiki)
*
*	Copyright (c) 2007 - 2009 Hans-Jörg Happel and
*	FZI Forschungszentrum Informatik and der Universität Karlsruhe (TH)
*
*   Woogle4MediaWiki is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Woogle4MediaWiki is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Woogle4MediaWiki.  If not, see <http://www.gnu.org/licenses/>.
*******************************************************************************/ 

class SNEAskLog extends SpecialPage {
	
	function __construct() {
		parent::__construct(SNEUtil::getSpecialPageLocal('AskLog'), '', true);
		SpecialPage::setGroup($this, 'sne');
	}

	// Here the inline output of the Special page will be created
	
	function execute($par) {
		global $wgOut, $wgUser;
		
		// show title
		$this->setHeaders();
		$this->returntitle = Title::makeTitle(NS_SPECIAL, SNEUtil::getSpecialPageLocal('AskLog'));
		
		$wgOut->setPagetitle(SNEUtil::getMsg('AskLogTitle'));
		
		if (!$this->userCanExecute($wgUser)) {
			$this->displayRestrictionError();
			return true;
		}
			
		if (!SNECoreConfig::tableExists('smwq_query')||!SNECoreConfig::tableExists('smwq_select')||!SNECoreConfig::tableExists('smwq_constraint')){
			$wgOut->addHTML(SNEUtil::getMsg('DBFail'));
			$admin = Title::makeTitle(NS_SPECIAL, SNECoreUtil::getSpecialPageLocal('Admin'));
			$link = HTML::rawElement('a', array('href' => htmlspecialchars($admin->getFullURL())), SNECoreUtil::getMsg('AdminTitle'));
			$wgOut->addHTML(SNEUtil::getMsg('RedirectToAdmin', $link));
			return true;
		}
		
		if(!empty($par)){
			$par = explode('&',$par);
			foreach($par as $key => $value){
				$temp = explode('=', $value);
				$params[$temp[0]] = $temp[1]; 
			}
		}
		
		if(!isset($params['limit'])){
			$params['limit'] = 5;
		}
		if(!isset($params['offset'])){
			$params['offset'] = 0;
		}
		
		$wgOut->addHTML(self::createNavigationBar($params, SNESMWQQueryFinder::findAllCount()));
		
		$queries = SNESMWQQueryFinder::findAll($params['offset'], $params['limit']);
		
		if(empty($queries)){
			$wgOut->addHTML(SNEUtil::getMsg('AskLogQueriesWithResults'));
		}
		$firstrun = true;
		$rows = '';
		foreach($queries as $key => $object){
			if($firstrun){
				$headings = $object->iterateForHeadings();
				$rows .= HTML::rawElement('tr', array(), $headings);
				$firstrun = false;
			}
			$rows .= HTML::rawElement('tr', array(), $object->iterateForRows());
		}
		$table = HTML::rawElement('table', array('border' => '1'), $rows);
		$wgOut->addHTML($table);
	}
	

	/**
	 * method that creates the navigationbar according to information from $_POST parameters, URL transmitted Parameters ($params)
	 * and also the database queried ASK-queries
	 * @param		$queriesArray	Array holding the ASK-queries 
	 * @param		$params			associative array that holds all the URL-transmitted parameters (e.g. ..AskLog/limit=2&offset=10&...)
	 * @param		$_POST			Associative Array that holds the $_POST params of the Searchbox if any
	 * @return		$htmlstring		HTML navigation bar code
	 */
	
	
	private function createNavigationBar($params, $count){
		//BEGIN
		$htmlstring = '( ';
		if($params['offset'] > 0){
			$holder = $params['offset'];
			$params['offset'] = 0;
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params, 'begin'), SNEUtil::getMsg('AskLogBrowsingBegin'));
			$params['offset'] = $holder;
		}
		else{
			$htmlstring .= SNEUtil::getMsg('AskLogBrowsingBegin');
		}
		$htmlstring .= ' | ';
		
		//END
		if(($count - $params['offset']) > $params['limit']){
			$a = $count % $params['limit'];
			$holder = $params['offset'];
			if($a == 0){
				$params['offset'] = $count - $params['limit'];
			}
			else{
				$params['offset'] = $count - $a;
			}
			
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params, 'end'), SNEUtil::getMsg('AskLogBrowsingEnd'));
			$params['offset'] = $holder;
		}
		else{
			$htmlstring .= SNEUtil::getMsg('AskLogBrowsingEnd');
		}
		$htmlstring .= ' ) ';
		
		
		//SHOW
		$htmlstring .= SNEUtil::getMsg('AskLogBrowsingShow');
		

		//PREVIOUS
		$htmlstring .= ' ( ';
		if($params['offset'] - $params['limit'] >= 0){
			$holder = $params['offset'];
			$params['offset'] = $params['offset'] - $params['limit'];
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), SNEUtil::getMsg('AskLogBrowsingPrevious'));
			$params['offset'] = $holder;
		}
		else{
			$htmlstring .= SNEUtil::getMsg('AskLogBrowsingPrevious');
		}

		
		//NEXT
		$htmlstring .= ' | ';
		if((($count - $params['offset']) > $params['limit'])){
			$holder = $params['offset'];
			$params['offset'] = $params['offset'] + $params['limit'];
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), SNEUtil::getMsg('AskLogBrowsingNext'));
			$params['offset'] = $holder;
		}
		else{
			$htmlstring .= SNEUtil::getMsg('AskLogBrowsingNext');
		}
		$htmlstring .= ' )';
		
		//LIMIT 2
		$htmlstring .= ' ( ';
		if($params['limit'] != 2){
			$params['limit'] = 2;
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), "2");
			$htmlstring .= ' | ';
		}
		else{
			$htmlstring .= '2 | ';
		}
		//LIMIT 5
		if($params['limit'] != 5){
			$params['limit'] = 5;
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), "5");
			$htmlstring .= ' | ';
		}
		else{
			$htmlstring .= '5 | ';
		}
		//LIMIT 10
		if($params['limit'] != 10){
			$params['limit'] = 10;
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), "10");
			$htmlstring .= ' | ';
		}
		else{
			$htmlstring .= '10 | ';
		}
		//LIMIT 25
		if($params['limit'] != 25){
			$params['limit'] = 25;
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), "25");
			$htmlstring .= ' | ';
		}
		else{
			$htmlstring .= '25 | ';
		}
		//LIMIT 50
		if($params['limit'] != 50){
			$params['limit'] = 50;
			$htmlstring .= HTML::rawElement('a', self::generateHTMLLink($params), "50");
		}
		else{
			$htmlstring .= '50';
		}
		$htmlstring .= ' ) ';
		return $htmlstring;
	}
	
	/**
	 * generates an html link for the article according to the given navigation bar parameters
	 * @param				$params						navigation bar parameters
	 * @return 				multitype:string			html link for the article with the navbar parameters
	 */
	
	private static function generateHTMLLink($params){
		global $wgTitle;
		return array('href' => htmlspecialchars($wgTitle->getFullURL()).'/'.self::generateNavBarParameters($params), '');
	}
	
	/**
	 * transforms the navigation bar parameters into a string
	 * @param			$params					compacts the navigation bar parameters as a string
	 */
	
	private static function generateNavBarParameters($params){
		$string = '';
		if(!empty($params)){
			foreach($params as $key => $value){
				$string .= $key.'='.$value.'&';
			}
		}
		return substr($string,0,-1);
	}
}
?>