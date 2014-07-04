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

class SNEAdmin extends SpecialPage {
	private $returntitle;
	
	function __construct() {
		//undelete => sysop group (c.f. http://www.mediawiki.org/wiki/Special:ListGroupRights)
		parent::__construct(SNECoreUtil::getSpecialPageLocal('Admin'), 'undelete', true);
		SpecialPage::setGroup($this, 'sneCore');
	}

	// Here the inline output of the Special page will be created
	function execute($par) {
		global $wgOut, $wgUser; 
		$allQueryArray = array();

		// show title
		$this->setHeaders();
		$this->returntitle = Title::makeTitle(NS_SPECIAL, SNECoreUtil::getSpecialPageLocal('Admin'));
		
		// check user permission
		if (!$this->userCanExecute($wgUser)) {
			$this->displayRestrictionError();
			return;
		}
	
		$wgOut->setPagetitle(SNECoreUtil::getMsg('AdminTitle'));	

		if ($par == 'initdb'){
			// init db was chosen
			$this->executeInitDb();
			
		} else if ($par == 'dropdb'){
			// dropping the db was chosen
			$this->executeDropDb();
	
		} else if ($par == 'reindexall'){
			// reindex pages
			$this->executeReindexAll();
		}
		else {
			// standard html output of config page
			$this->executeDefault();
		}
	}
	
	/**
	 *	standard html output of config page 
	 */
	
	private function executeDefault(){
		global $wgOut;
		
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminWelcome'));
		
		$imgOk = HTML::rawElement('img', array('width' => '20', 'height' => '20', 'src' => SNECoreUtil::getImagePath().'ok.png'));
		$imgProblem = HTML::rawElement('img', array('width' => '20', 'height' => '20', 'src' => SNECoreUtil::getImagePath().'problem.png'));
		
		$rows = array();
		//if any of the tables aren't initialized show initdb only
		if (!SNECoreConfig::tableExists('smwq_query')||!SNECoreConfig::tableExists('smwq_select')||!SNECoreConfig::tableExists('smwq_constraint')){
			$col1 = HTML::rawElement('td', array(), SNECoreUtil::getMsg('AdminDBNotRunning'));
			$col2 = HTML::rawElement('td', array(), $imgProblem);
			$initDb = HTML::rawElement('input', array('type' => 'submit', 'value' => SNECoreUtil::getMsg('AdminDBInit'), 'method' => 'POST'));
			$initDbForm = HTML::rawElement('form', array('name' => 'initdb', 'action' => htmlspecialchars($this->returntitle->getFullURL()). '/initdb', 'method' => 'get'), $initDb);
			$col3 = HTML::rawElement('td', array(), $initDbForm);
			$rows[] = HTML::rawElement('tr', array(), $col1.$col2.$col3);
		}
		//if tables are initialized show dropdb and reindex all
		else{
			$col1 = HTML::rawElement('td', array(), SNECoreUtil::getMsg('AdminDBRunning'));
			$col2 = HTML::rawElement('td', array(), $imgOk);
			$dropDb = HTML::rawElement('input', array('type' => 'submit', 'value' => SNECoreUtil::getMsg('AdminDBDrop'), 'method' => 'POST'));
			$dropDbForm = HTML::rawElement('form', array('name' => 'dropdb', 'action' => htmlspecialchars($this->returntitle->getFullURL()). '/dropdb', 'method' => 'get'), $dropDb);
			$col3 = HTML::rawElement('td', array(), $dropDbForm);
			$rows[] = HTML::rawElement('tr', array(), $col1.$col2.$col3);
			$emptyCol = HTML::rawElement('td', array());
			$reindexDb = HTML::rawElement('input', array('type' => 'submit', 'value' => SNECoreUtil::getMsg('AdminReindex'), 'method' => 'POST'));
			$reindexDbForm = HTML::rawElement('form', array('name' => 'reindexall', 'action' => htmlspecialchars($this->returntitle->getFullURL()). '/reindexall', 'method' => 'get'), $reindexDb);
			$col4 = HTML::rawElement('td', array(), $reindexDbForm);
			$rows[] = HTML::rawElement('tr', array(), $emptyCol.$emptyCol.$col4);
		}
		
		$table = HTML::rawElement('table', array('width' => '100%'), implode('',$rows));
		$wgOut->addHTML($table);
	}
	
	/**
	 *	output when dropping databases
	 */

	private function executeDropDb(){
		global $wgOut;
		try {
			$count = QueryStorage::dropDbTables(QueryStorage::getDb());
			$wgOut->addHTML(SNECoreUtil::getMsg('AdminDBDropMsg', $count));
		
		} catch (Exception $e) {
			$wgOut->addHTML(SNECoreUtil::getMsg('ErrorAdminDBRights', $e));
		}
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($this->returntitle->getFullURL())), SNECoreUtil::getMsg('AdminTitle'));
		$paragraph = HTML::rawElement('p', array(), SNECoreUtil::getMsg('GeneralReturnTo').' '.$link);
		$wgOut->addHTML($paragraph);
	}
	
	/**
	 *	output when initializing databases
	 */
	
	private function executeInitDb(){
		global $wgOut;
		try {
			$count = QueryStorage::createDbTables(QueryStorage::getDb());
			$wgOut->addHTML(SNECoreUtil::getMsg('AdminDBInitMsg', $count));
		} catch (Exception $e) {
			$wgOut->addHTML(SNECoreUtil::getMsg('ErrorAdminDBRights', $e));
		}
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($this->returntitle->getFullURL())), SNECoreUtil::getMsg('AdminTitle'));
		$paragraph = HTML::rawElement('p', array(), SNECoreUtil::getMsg('GeneralReturnTo').' '.$link);
		$wgOut->addHTML($paragraph);
	}
	
	/**
	 *	function that executes the findAllWikiPages method and circles it with the appropriate HTML code
	 */
	
	private function executeReindexAll(){
		global $wgOut;
		set_time_limit(0);
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminReindexMsg'));
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminFindWikiPagesTime', date(DATE_RFC822)));
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminFindWikiPagesExtractPages'));
		$numPages = QueryMonitor::crawlForPages();
		$numConcepts = QueryMonitor::crawlForConcepts();
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminFindWikiPagesParsingPages', $numPages+$numConcepts));
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminPurgeWikiPagesComplete', $numPages));
		$wgOut->addHTML(SNECoreUtil::getMsg('AdminPurgeWikiConceptsComplete', $numConcepts));
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($this->returntitle->getFullURL())), SNECoreUtil::getMsg('AdminTitle'));
		$paragraph = HTML::rawElement('p', array(), SNECoreUtil::getMsg('GeneralReturnTo').' '.$link);
		$wgOut->addHTML($paragraph);
	}
}
?>