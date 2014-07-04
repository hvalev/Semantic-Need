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


class SNESemanticQueryInfo  extends SpecialPage {
	
	function __construct() {
		parent::__construct(SNEUtil::getSpecialPageLocal('SemanticQueryInfo'), '', true);
		SpecialPage::setGroup($this, 'sne');
	}

	// Here the inline output of the Special page will be created
	function execute($par) {
		global $wgOut, $wgUser;
		
		// show title
		$this->setHeaders();
		$this->returntitle = Title::makeTitle(NS_SPECIAL, SNEUtil::getSpecialPageLocal('SemanticQueryInfo'));
		
		$wgOut->setPagetitle(SNEUtil::getMsg('SemanticQueryInfoTitle'));
			
		if (!SNECoreConfig::tableExists('smwq_query')||!SNECoreConfig::tableExists('smwq_select')||!SNECoreConfig::tableExists('smwq_constraint')){
			$wgOut->addHTML(SNEUtil::getMsg('DBFail'));
			$admin = Title::makeTitle(NS_SPECIAL, SNECoreUtil::getSpecialPageLocal('Admin'));
			$link = HTML::rawElement('a', array('href' => htmlspecialchars($admin->getFullURL())), SNECoreUtil::getMsg('AdminTitle'));
			$wgOut->addHTML(SNEUtil::getMsg('RedirectToAdmin', $link));
			return true;
		}
		
		//if the form has been used attaches $POST variable and redirects to the real url
		if(isset($_POST['queryhash'])){
			$wgOut->redirect(htmlspecialchars($this->returntitle->getFullURL()) . '/'. $_POST['queryhash']);
		}
		
		if ($par == '') {
			// standard html output of config page	
			$par = $this->executeDefault();

		} else{
			// run queries
			self::executeAnalyseQuery($par);
		}
	}
	
	/**
	 * default method that loads the form to enter query ID or Hash
	 * @return		void				prints out html
	 */
	
	
	private function executeDefault(){//
		global $wgOut;//
		$wgOut->addHTML(SNEUtil::getMsg('SemanticQueryInfoWelcome'));
		
		$input = HTML::rawElement('input', array('name' => 'queryhash', 'type' => 'text', 'value' => ''));
		$button = HTML::rawElement('input', array('name' => 'submitbutton', 'type' => 'submit', 'value' => SNEUtil::getMsg('SemanticQueryInfoButton')));
		$form = HTML::rawElement('form', array('name' => 'typeQueryHash', 'action' => '', 'method' => 'POST'), $input.$button);
		$wgOut->addHTML($form);
	}
	
	/**
	 * given parameters in the textbox this function checks if they are valid
	 * (if an entry exists in the database and runs the appropriate methods)
	 * @param		$param				query hash or alias
	 */
	
	private function executeAnalyseQuery($param){
		global $wgOut;
		
		$wgOut->addHTML(HTML::rawElement('h2', array(), SNEUtil::getMsg('SemanticQueryInfoAdvancedQueryHeader')));

		/*
		 * QUERY
		 */
		
		$query = new SMWQQueryGateway();
		$query->setQid($param);
		$queries = SMWQQueryFinder::findByQid(array($query));
		$rows = '';
		foreach($queries as $key => $object){
			//we create a new SNESMWQQueryObject so that we can use the implemented 
			//new functions of that object
			$match = new SNESMWQQueryGateway();
			$match->instantiateFromObject($object);
			$headings = $match->iterateForHeadings();
			$rows .= HTML::rawElement('tr', array(), $headings);
			$rows .= HTML::rawElement('tr', array(), $match->iterateForRows());
		}
		$table = HTML::rawElement('table', array('border' => '1'), $rows);
		$wgOut->addHTML($table);
		
		/*
		 * CONSTRAINTS
		 */
		
		$wgOut->addHTML(HTML::rawElement('h3', array(), SNEUtil::getMsg('SemanticQueryInfoAdvancedConstraints')));
		$constraints = SNESMWQConstraintFinder::findByQid(array($query));
		$firstrun = true;
		$rows = '';
		foreach($constraints as $key => $object){
			//we create a new SNESMWQConstraintObject so that we can use the implemented 
			//new functions of that object
			$match = new SNESMWQConstraintGateway();
			$match->instantiateFromObject($object);
			if($firstrun){
				$headings = $match->iterateForHeadings();
				$rows .= HTML::rawElement('tr', array(), $headings);
				$firstrun = false;
			}
			$rows .= HTML::rawElement('tr', array(), $match->iterateForRows());
		}
		$table = HTML::rawElement('table', array('border' => '1'), $rows);
		$wgOut->addHTML($table);
		
		
		
		/*
		 * PRINTOUTS
		 */	

		$wgOut->addHTML(HTML::rawElement('h3', array(), SNEUtil::getMsg('SemanticQueryInfoAdvancedPrintouts')));
		$printouts = SMWQPrintoutFinder::findByQid(array($query));
		if(empty($printouts)){
			$wgOut->addHTML(SNEUtil::getMsg('SemanticQueryInfoAdvancedNoPrintouts'));
		}
		$firstrun = true;
		$rows = '';
		foreach($printouts as $key => $object){
			//we create a new SNESMWQConstraintObject so that we can use the implemented 
			//new functions of that object
			$match = new SNESMWQPrintoutGateway();
			$match->instantiateFromObject($object);
			if($firstrun){
				$headings = $match->iterateForHeadings();
				$rows .= HTML::rawElement('tr', array(), $headings);
				$firstrun = false;
			}
			$rows .= HTML::rawElement('tr', array(), $match->iterateForRows());
		}
		$table = HTML::rawElement('table', array('border' => '1'), $rows);
		$wgOut->addHTML($table);
		
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($this->returntitle->getFullURL())), SNEUtil::getMsg('SemanticMatchesTitleSimple'));
		$paragraph = HTML::rawElement('p', array(), SNEUtil::getMsg('GeneralReturnTo').' '.$link);
		$wgOut->addHTML($paragraph);
		$title = Title::makeTitle(NS_SPECIAL, SNEUtil::getSpecialPageLocal('AskLog'));
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($title->getFullURL())), $title->getText());
		$paragraph = HTML::rawElement('p', array(), SNEUtil::getMsg('GeneralGoTo').' '.$link);
		$wgOut->addHTML($paragraph);
		return true;
	}
}
?>