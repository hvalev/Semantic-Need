<?php
/**
 *	This file lists all MediaWiki hooks (http://www.mediawiki.org/wiki/Manual:Hooks) consumed
 *  by the SNE core code
 *  
 *  Each hook is assigned a callback function in this file, which will be called at runtime if
 *  the hook fires.
 *  
 */
global $wgHooks;
global $sneBox;


$wgHooks['SkinTemplateToolboxEnd'][] 						= 'sneShowSemanticMatchesLink';
$wgHooks['OutputPageBeforeHTML'][]							= 'sneAddFacts';
$wgHooks['OutputPageBeforeHTML'][]							= 'sneGenerateSNEBox';
$wgHooks['SkinAfterContent'][]								= 'sneAddSNEBoxAfterArticleEnd';


	/*
	 * hook that adds a link in the toolbox that links the current article to the SNE:SemanticMatches special page
	 * @param		$skintemplate	skin object?
	 * @return		true			hook's return value that can either be true or false (continue/abort)
	 */

function sneShowSemanticMatchesLink($skintemplate) {
	global $wgTitle;
    if($skintemplate->data['isarticle']) {
    	//get the Title Object for the SNEAdmin Special Page
		$spadmin = Title::makeTitle(NS_SPECIAL, SNEUtil::getSpecialPageLocal('SemanticMatches'));
        $browselink = SMWInfolink::newInternalLink(SNEUtil::getMsg('ToolboxLink', $wgTitle->getText()),$spadmin->getNsText().':'.$spadmin->getText().'/'.$wgTitle->getText(),false,array());
  	  echo "<li id=\"t-snesemanticmatcheslink\">" . $browselink->getHTML() . "</li>";
    }
    return true;
}

	/**
	 * adds properties or categories to the page inserted through the SNEBox
	 * @param $out
	 * @param $parseroutput
	 */

function sneAddFacts(&$out, $parseroutput){
	global $wgArticle;
	global $wgTitle;
	global $wgOut;
	$shouldRedirect = false;
	
	//check if the submit button on the SNEbox has been clicked
	if(!empty($_POST['submitbutton'])){
		//init arrays for categories and properties
		$properties = '';
		$categories = '';
		$section = '<!-- facts added by semantic need -->';
		foreach($_POST as $key => $value){
			if(!empty($value)){
				$array = explode(':', $key);
				if($array[0] == 'property'){
					$properties .= $array[1].'='.$value.'|';
				}
				elseif($array[0] == 'category'){
					$category = Category::newFromName($array[1]);
					$categories .= '[['.$category->getTitle()->getNsText().':'.$array[1].']]';
				}
			}
		}
		//init sectiontext variable
		$sectiontext = '';
		if(!empty($properties)){
			$sectiontext .= '{{#set:'.$properties.'}}';
		}
		if(!empty($categories)){
			$sectiontext .= $categories;
		}
		
		
		$articleContent = $wgArticle->fetchContent();
		if(stripos($articleContent, $section) != false){
			$articleContent = str_ireplace($section, $section.$sectiontext, $articleContent);
			$wgArticle->doEdit($articleContent, $section);
		}
		else{
			$wgArticle->doEdit($articleContent.$section.$sectiontext, $section);
		}
		$wgOut->redirect($wgArticle->getTitle()->getFullURL());
	}
	return true;
}

	/**
	 * generates the SNEbox HTML code
	 * @param $out
	 * @param $parseroutput
	 */

function sneGenerateSNEBox(&$out, $parseroutput){
	global $wgArticle;
	global $wgOut;
	global $smwgShowFactbox;
	
	if(!SNECoreConfig::tableExists('smwq_query')||!SNECoreConfig::tableExists('smwq_select')||!SNECoreConfig::tableExists('smwq_constraint')){
		return true;
	}
	
	if(!empty($wgArticle)){
		$properties = SNEQueryResolver::getWantedProperties($wgArticle->getTitle());
		$categories = SNEQueryResolver::getWantedCategories($wgArticle->getTitle());
	}
	
	if(!empty($properties) || !empty($categories)){
		global $sfgScriptPath;
    	global $smwgScriptPath;
    	global $sfgYUIBase;	
    	$links = array(
	        			array(
					        'rel' => 'stylesheet',
					        'type' => 'text/css',
					        'media' => "screen",
					        'href' => $sfgScriptPath . '/skins/SF_main.css'
					    ),
	        			array(
					        'rel' => 'stylesheet',
					        'type' => 'text/css',
					        'media' => "screen",
					        'href' => $sfgYUIBase . "autocomplete/assets/skins/sam/autocomplete.css"
					    ),
	        			array(
					        'rel' => 'stylesheet',
					        'type' => 'text/css',
					        'media' => "screen",
					        'href' => $sfgScriptPath . '/skins/SF_yui_autocompletion.css'
					    ),
	        			array(
					        'rel' => 'stylesheet',
					        'type' => 'text/css',
					        'media' => "screen",
					        'href' => $sfgScriptPath . '/skins/floatbox.css'
	       				 ),
	        			array(
					        'rel' => 'stylesheet',
					        'type' => 'text/css',
					        'media' => "screen",
					        'href' => $smwgScriptPath . '/skins/SMW_custom.css'
	        			),  
        			);
		foreach($links as $link) {            
	        $wgOut->addLink($link);
		}
		
		$rows = array();
		foreach($properties as $key => $property){
			$url = HTML::rawElement('a', array('href' => $property->getVariable()->getDiWikiPage()->getTitle()->getFullUrl()), $property->getVariable()->getLabel());
			$col1 = HTML::rawElement('td', array('class' => 'smwpropname'), $url);
			
			$input = HTML::rawElement('input', array('name' => 'property:'.$property->getVariable()->getLabel(), 'type' => 'text', 'value' => ''));
			$col2 = HTML::rawElement('td', array('class' => 'smwprops'), $input);
			
			$url = HTML::rawElement('a', array('href' => ''), SNEUtil::getMsg('SNEBoxLink', count($property->getQueries()), count($property->getPages())));
			$col3 = HTML::rawElement('td', array(), $url);
			
			$rows[] = HTML::rawElement('tr', array(), $col1.$col2.$col3);
		}
		foreach($categories as $key => $category){
			$url = HTML::rawElement('a', array('href' => $category->getVariable()->getTitle()->getFullUrl()),  $category->getVariable()->getName());
			$col1 = HTML::rawElement('td', array('class' => 'smwpropname'), $url);
			
			$input = HTML::rawElement('input', array('name' => 'category:'.$category->getVariable()->getName(), 'type' => 'checkbox', 'value' => ''));
			$col2 = HTML::rawElement('td', array('class' => 'smwprops'), $input);
			
			$url = HTML::rawElement('a', array('href' => ''), SNEUtil::getMsg('SNEBoxLink', count($category->getQueries()), count($category->getPages())));
			$col3 = HTML::rawElement('td', array(), $url);
			
			$rows[] = HTML::rawElement('tr', array(), $col1.$col2.$col3);
		}
	
		$table = HTML::rawElement('table', array('class' => 'smwfacttable'), implode('', $rows));
			
		$save = HTML::rawElement('input', array('name' => 'submitbutton', 'type' => 'submit', 'value' => SNEUtil::getMsg('SNEBoxButton')));
		$form = HTML::rawElement('form', array('name' => 'save', 'action' => '', 'method' => 'POST'), $table.$save);
		
		$span = HTML::rawElement('span', array('class' => 'smwfactboxhead'), SNEUtil::getMsg('SNEBoxHeader', $wgArticle->getTitle()->getText()));
		$div = HTML::rawElement('div', array('class' => 'smwfact'), $span.$form);

		global $sneBox;
		$sneBox = $div;
	}
	return true;
}

	/**
	 * adds the SNEBox HTML code in the end of the article
	 * @param $data
	 */

function sneAddSNEBoxAfterArticleEnd(&$data){
	global $sneBox;
	if(!empty($sneBox)){
		$data .= $sneBox;	
	}
	return true; 
}
?>