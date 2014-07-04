<?php
class SNESemanticMatches  extends SpecialPage {
	
	function __construct() {
		parent::__construct(SNEUtil::getSpecialPageLocal('SemanticMatches'), '', true);
		SpecialPage::setGroup($this, 'sne');
	}

	// Here the inline output of the Special page will be created
	function execute($par) {
		global $wgOut, $wgUser;
		
		// show title
		$this->setHeaders();
		$this->returntitle = Title::makeTitle(NS_SPECIAL, SNEUtil::getSpecialPageLocal('SemanticMatches'));
	
		if (!SNECoreConfig::tableExists('smwq_query')||!SNECoreConfig::tableExists('smwq_select')||!SNECoreConfig::tableExists('smwq_constraint')){
			$wgOut->addHTML(SNEUtil::getMsg('DBFail'));
			$admin = Title::makeTitle(NS_SPECIAL, SNECoreUtil::getSpecialPageLocal('Admin'));
			$link = HTML::rawElement('a', array('href' => htmlspecialchars($admin->getFullURL())), SNECoreUtil::getMsg('AdminTitle'));
			$wgOut->addHTML(SNEUtil::getMsg('RedirectToAdmin', $link));
			return true;
		}
		
		//if the form has been used attaches $POST variable and redirects to the real url
		if(isset($_POST['pagename'])){
			$wgOut->redirect(htmlspecialchars($this->returntitle->getFullURL()) . '/'. $_POST['pagename']);
		}

		if ($par == '') {
			// standard html output of config page	
			$par = $this->executeDefault();

		} else{
			// run queries
			$this->executeAnalysePage($par);
		}
	}
	
	/**
	 * default method that loads the form to enter page name
	 * @return		void				prints out html
	 */
	
	private function executeDefault(){//
		global $wgOut;//
		$wgOut->setPagetitle(SNEUtil::getMsg('SemanticMatchesTitleSimple'));
		$wgOut->addHTML(SNEUtil::getMsg('SemanticMatchesWelcome'));
		
		$input = HTML::rawElement('input', array('name' => 'pagename', 'type' => 'text', 'value' => ''));
		$button = HTML::rawElement('input', array('name' => 'submitbutton', 'type' => 'submit', 'value' => SNEUtil::getMsg('SemanticMatchesButton')));
		$form = HTML::rawElement('form', array('name' => 'typePageName', 'action' => '', 'method' => 'POST'), $input.$button);
		$wgOut->addHTML($form);
	}
	
	/**
	 * analyze a page for semantic matches, missing and wanted properties and categories
	 * and generate the output as html
	 * @param			$wikiPageName			name of a wikipage as string
	 */
	
	private function executeAnalysePage($wikiPageName){
		global $wgOut;
		
		$wgOut->setPagetitle(SNEUtil::getMsg('SemanticMatchesTitleAdvanced', $wikiPageName));
		
		$title = Title::newFromText($wikiPageName);
		
		/*
		 * SEMANTIC QUERIES THAT MATCH THE PAGE
		 */
		
		$wgOut->addHTML(HTML::rawElement('h2', array(), SNEUtil::getMsg('SemanticMatchesQueries')));
		$queries = SNEQueryResolver::getQueryMatches($title);
		if(!empty($queries)){
			$firstrun = true;
			$rows = '';
			foreach($queries as $key => $object){
				//we create a new SNESMWQQueryObject so that we can use the implemented 
				//new functions of that object
				$match = new SNESMWQQueryGateway();
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
		}
		else{
			$wgOut->addHTML(SNEUtil::getMsg('SemanticMatchesMissingQueries'));
		}
		
		/*
		 * SEMANTIC QUERIES THAT ALMOST MATCH THE PAGE
		 */
		
		$wgOut->addHTML(HTML::rawElement('h2', array(), SNEUtil::getMsg('SemanticMatchesNearMatches')));
		$queries = SNEQueryResolver::getQueryNearMatches($title);
		if(!empty($queries)){
			$firstrun = true;
			$rows = '';
			foreach($queries as $key => $object){
				//we create a new SNESMWQQueryObject so that we can use the implemented 
				//new functions of that object
				$match = new SNESMWQQueryGateway();
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
		}
		else{
			$wgOut->addHTML(SNEUtil::getMsg('SemanticMatchesMissingQueries'));
		}
		
		/*
		 * MISSING PROPERTIES
		 */
		
		$wgOut->addHTML(HTML::rawElement('h2', array(), SNEUtil::getMsg('SemanticMatchesMissingProperties')));
		$properties = SNEQueryResolver::getPropertiesDifferences($title);
		if(!empty($properties)){
			$firstrun = true;
			$rows = '';
			foreach($properties as $key => $object){
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
		else{
			$wgOut->addHTML(SNEUtil::getMsg('SemanticMatchesNoMissingProperties'));
		}

		/*
		 * MISSING CATEGORIES
		 */

		$wgOut->addHTML(HTML::rawElement('h2', array(), SNEUtil::getMsg('SemanticMatchesMissingCategories')));
		$categories = SNEQueryResolver::getCategoriesDifferences($title);
		if(!empty($categories)){
			$firstrun = true;
			$rows = '';
			foreach($categories as $key => $object){
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
		else{
			$wgOut->addHTML(SNEUtil::getMsg('SemanticMatchesNoMissingCategories'));
		}
		
		/*
		 * WANTED PROPERTIES 
		 */

		$wgOut->addHTML(HTML::rawElement('h2', array(), SNEUtil::getMsg('SemanticMatchesWantedProperties')));
		$properties = SNEQueryResolver::getWantedProperties($title);
		if(!empty($properties)){
			$firstrun = true;
			$rows = '';
			foreach($properties as $key => $object){
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
		else{
			$wgOut->addHTML(SNEUtil::getMsg('SemanticMatchesNoWantedProperties'));
		}
		
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($this->returntitle->getFullURL())), SNEUtil::getMsg('SemanticMatchesTitleSimple'));
		$paragraph = HTML::rawElement('p', array(), SNEUtil::getMsg('GeneralReturnTo').' '.$link);
		$wgOut->addHTML($paragraph);
		$link = HTML::rawElement('a', array('href' => htmlspecialchars($title->getFullURL())), $wikiPageName);
		$paragraph = HTML::rawElement('p', array(), SNEUtil::getMsg('GeneralGoTo').' '.$link);
		$wgOut->addHTML($paragraph);
	}
}
?>