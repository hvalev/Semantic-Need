<?php
global $wgHooks;
$wgHooks['SMW_AskQueryExecution'][]							= 'sneProcessAskQuery';

	/*
	 * hook that processes all queries on page when the refresh button is hit
	 * @param		&$query			SMW Query object of the current query
	 * @param		&$context		context of the query
	 * @param		&$format		format of the query
	 * @param		&$res			Query results (object)array
	 * @return		true			hook's return value that can either be true or false (continue/abort)
	 */


function sneProcessAskQuery(&$query, &$context, &$format, &$res, $isSubquery, $isConcept){
	global $wgTitle;
	global $wgArticle;
	global $sneTmpArticle;
	
	//Refresh call by Admin page
	if(isset($sneTmpArticle)){
		$article = $sneTmpArticle;
	}
	//Refresh call by a concept page
	elseif($wgArticle instanceof SMWConceptPage){
		$article = new Article($wgTitle);
	}
	//Refresh call by a normal wikipage via the refresh button or through the Article Save Process
	elseif($wgArticle instanceof Article){
		$article = $wgArticle;
	}
	else{ //exception								
		echo "Extiting sneProcessAskQuery due to probable SMW refresh call 2\n";
		// this seems to be a SMW refreshData call - ignore so far - TODO
		return true;
	}
	
	QueryAnalyzer::logSemanticQuery($query, $context, $format, $res, $article, $isSubquery, $isConcept);
	return true;
}

/**
 * holds functions that scan the whole wiki for semantic queries
 * and the purge script that is used to refresh those pages and
 * add those queries to the semantic need database
 * @author spell
 *
 */

class QueryMonitor{	
	
	/**
	 * Controller function for finding and purging Wikipages
	 */
	
	public static function crawlForPages(){
		//'0' is the main namespace in mediawiki where common articles are saved
		$pages = self::findPages(0);
		$count = self::purgePages($pages);
		return $count;
	}
	
	/**
	 * Controller function for finding and purging Concept pages
	 */
	
	public static function crawlForConcepts(){
		//SMW_NS_CONCEPT is a constant that describes the concept pages namespace
		$concepts = self::findPages(SMW_NS_CONCEPT);
		$count = self::purgePages($concepts);
		return $count;
	}
	
	/**
	 * function that accesses mediawiki's internal api to find specific wikipages
	 * based on the input parameters
	 * @param 		$namespace			namespace index
	 * @param		$limit				amount of wikipages retrieved
	 * @param 		$search				wikipage name prefix
	 * @return		$data				array containing the page names, index and namespace
	 */
	
	private static function findPages($namespace, $limit = 1000, $search = ''){
		$pages = new FauxRequest(
		 array(
        	'action' => 'query',
        	'list' => 'allpages',
        	'apnamespace' => $namespace,
        	'aplimit' => $limit,
        	'apprefix' => $search
		 	)					
		);

		$api = new ApiMain($pages);
		$api->execute();
		$data = $api->getResultData();
		
		return $data;
	}
	
	/**
	 * function that iterates all the pages returned by the mediawiki api
	 * and prepares each article to be purged
	 * @param 			$pages			array of wikipages as retrieved from MW API
	 * @return 			integer			number of refreshed pages
	 */
	
	private static function purgePages($pages){
		global $sneTmpArticle;
		foreach($pages['query']['allpages'] as $key => $value){
			$article = Article::newFromID($value['pageid']);
			$sneTmpArticle = $article;
			//$article->purge(); actually displays all the pages at once at the sne-admin page
			//with every refresh thats why a workaround with the function parseArticle has been implemented
			self::parseArticle($article);
		}
		return count($pages['query']['allpages']);
	}
	
	/**
	 * essentially purges an article in the wiki. (workaround for Article::purge();)
	 * @param 			$article		MW Article Object
	 * @param 			$stritags		striptags
	 * @return 			integer			number of refreshed pages
	 */
	
	private static function parseArticle($article, $striptags = true){
		global $wgParser, $wgOut;
		$parseroutput = $wgParser->parse($article->getContent(), $article->getTitle(), $wgOut->parserOptions());
		$content = $parseroutput->getText(); 
		if ($striptags) $content = strip_tags( $content );
		return $content;
	}
}