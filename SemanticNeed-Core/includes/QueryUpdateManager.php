<?php
global $wgHooks;

$wgHooks['ArticleSave'][]									= 'sneBeforeArticleSave';
$wgHooks['SNE_LogQueryExecution'][]							= 'sneAfterArticleSave';
$wgHooks['ArticleSaveComplete'][]							= 'sneAfterArticleSaveUpdateDatabase';
$wgHooks['ArticleSaveComplete'][]							= 'sneAfterArticleSaveRefreshQueryResults';

/**called before saving a wikipage to retrieve and save the
 * queries on the page, queries that have the page as a result
 * and the properties on that page.
 * @param		&$article		Article Object
 * @param		&$user			User that commited the change
 * @param		&$text			Text of the article
 * @param		$minor			if it is marked as a minor edit
 * @param		$watchthis		...
 * @param		$sectionanchor	...
 * @param		&$flags			...
 * @param		&$status		...
 * @return		true			hook's return value that can either be true or false (continue/abort)
**/
	
function sneBeforeArticleSave(&$article, &$user, &$text, &$summary, $minor, $watchthis, $sectionanchor, &$flags, &$status){
	//sanity check on hooks to prevend wiki from crashing because of uninitialized extention
  	if(!QueryStorage::sanityCheck()){return true;};
	
	QueryUpdateManager::setQueriesOnPageBefore(SMWQQueryMapper::findByPage($article->getTitle()->getText()));
	QueryUpdateManager::setQueriesWithPageAsResult(QueryResolver::getQueryMatches($article->getTitle()));
	QueryUpdateManager::setPageProperties(QueryResolver::getPageProperties($article->getTitle()));
	return true;
}
	
function sneAfterArticleSave(&$smwqQuery, &$smwqConstraints, &$smwqPrintouts){
	QueryUpdateManager::addQueriesOnPageAfter($smwqQuery);
	return true;
}

/**called after an article is saved to activate/deactivate queries
 * on that page
 * @param		&$article		Article Object
 * @param		&$user			User that commited the change
 * @param		&$text			Text of the article
 * @param		$summary		summary
 * @param		$minoredit		if it is marked as a minor edit
 * @param		$watchthis		...
 * @param		$sectionanchor	...
 * @param		&$flags			...
 * @param		$revision		...
 * @param		&$status		...
 * @param		$baseRevId		...
 * @return		true			hook's return value that can either be true or false (continue/abort)
**/

function sneAfterArticleSaveUpdateDatabase(&$article, &$user, $text, $summary,
 $minoredit, $watchthis, $sectionanchor, &$flags, $revision, &$status, $baseRevId){
 	//sanity check on hooks to prevend wiki from crashing because of uninitialized extention
  	if(!QueryStorage::sanityCheck()){return true;};
  	
 	//received from the database
 	$queriesBefore = QueryUpdateManager::getQueriesOnPageBefore();
 	//received from QueryAnalyzer (has also SMWQConstraint and Printout Gateways)
 	$queriesAfter = QueryUpdateManager::getQueriesOnPageAfter();
 	
 	if(empty($queriesBefore) && empty($queriesAfter)){
 		return true;
 	}
	
 	$intersection = array_uintersect((array)$queriesBefore, (array)$queriesAfter, array('QueryUpdateManager', 'compareObjectsByQid'));
 	$queriesBefore = array_udiff((array)$queriesBefore, (array)$intersection, array('QueryUpdateManager', 'compareObjectsByQid'));
 	$queriesAfter = array_udiff((array)$queriesAfter, (array)$intersection, array('QueryUpdateManager', 'compareObjectsByQid'));
 	
 	foreach($queriesBefore as $key => $query){
 		SMWQQueryMapper::inactive($query);
 	}
 	
 	foreach($queriesAfter as $key => $object){
 		if($object instanceof SMWQQuery){
 			$constraints = false;
 			if(SMWQQueryMapper::exists($object)){
 				SMWQQueryMapper::active($object);
 			}
 			else{
 				//TODO ever here?
 				SMWQQueryMapper::insert($object);
 				//if query is not present mark constraints for insertion
 				$constraints = true;
 			}
 		}
 		elseif($constraints){
 			SMWQConstraintMapper::insert($object);
 		}
 		elseif(!$constraints){
 			//do nothing
 		}
 	}
 	//cleanup
	QueryUpdateManager::deleteQueriesOnPageBefore();
	QueryUpdateManager::deleteQueriesOnPageAfter();
	return true;
}

/**called after an article is saved to refresh the wiki pages with queries
 * that are affected by the changed content
 * (changed/added/removed properties that affect either being (no longer) 
 * part of the result set or a printout of a query)
 * @param		&$article		Article Object
 * @param		&$user			User that commited the change
 * @param		&$text			Text of the article
 * @param		$summary		summary
 * @param		$minoredit		if it is marked as a minor edit
 * @param		$watchthis		...
 * @param		$sectionanchor	...
 * @param		&$flags			...
 * @param		$revision		...
 * @param		&$status		...
 * @param		$baseRevId		...
 * @return		true			hook's return value that can either be true or false (continue/abort)
**/

function sneAfterArticleSaveRefreshQueryResults(&$article, &$user, $text, $summary,
 $minoredit, $watchthis, $sectionanchor, &$flags, $revision, &$status, $baseRevId){
 	//sanity check on hooks to prevend wiki from crashing because of uninitialized extention
  	if(!QueryStorage::sanityCheck()){return true;};
 	//queries with page as a result before article save
 	$oldQueries = QueryUpdateManager::getQueriesWithPageAsResult();
 	//queries with page as a result after article save
	$newQueries = QueryResolver::getQueryMatches($article->getTitle());
	
	$removedQueries = array_udiff($oldQueries, $newQueries, array("QueryUpdateManager","compareObjectsByQid"));
	$addedQueries = array_udiff($newQueries, $oldQueries, array("QueryUpdateManager","compareObjectsByQid"));
	
	//queries that (no longer/now do) have that page as a result
	$queriesToRefresh = array_merge($removedQueries, $addedQueries);
	
	//queries that had and still do have that wikipage as part of their result set
	$queriesToInvestigate = array_udiff($newQueries, $queriesToRefresh, array("QueryUpdateManager","compareObjectsByQid"));
	$queriesToInvestigatePrintouts = SMWQPrintoutMapper::findByQid($queriesToInvestigate);

	
	/*
	 * comparing the properties on the changed page to the printouts of queries under investigation
	 * to see if there is a change affecting those printouts
	 */
	
	//properties on page before article save
	$oldProperties = QueryUpdateManager::getPageProperties();
	//properties on page after article save
	$newProperties = QueryResolver::getPageProperties($article->getTitle());
	//properties with modified content
	$addedProperties = array_udiff($newProperties, $oldProperties, array("QueryUpdateManager","comparePropertiesByQIDandValue"));
	$removedProperties = array_udiff($oldProperties, $newProperties, array("QueryUpdateManager","comparePropertiesByQIDandValue"));
	$propertiesToInvestigate = array_merge($addedProperties, $removedProperties);
	
	//changed Printouts
	$changedConstraints = array_uintersect($queriesToInvestigatePrintouts, $propertiesToInvestigate, array("QueryUpdateManager","comparePrintoutsToConstraintsByName"));
	
	$qidArray = array_merge($queriesToRefresh, SMWQQueryMapper::findByQid($changedConstraints));
	if(!empty($qidArray)){
		foreach($qidArray as $key => $object){
			$pageArray[] = $object->getPage();
		}
	 	$pageArray = array_unique($pageArray); 
 		$pageArray = array_filter(array_values($pageArray));
 		foreach($pageArray as $key => $page){
 			$title = Title::newFromText($page);
 			$article = Article::newFromID($title->getArticleID());
 			$article->purge();
 		}
	}
 	QueryUpdateManager::deleteQueriesWithPageAsResult();
	return true;
}

/**
 * class that holds variables necessary to compare old data on a
 * wikipage and new data, so that the hooks in the class can refresh
 * or insert queries
 * @author spell
 *
 */

abstract class QueryUpdateManager{
	//before saving an article all those values are saved
	//so that after an article is saved analysis can be made
	private static $sneQueriesOnPageBefore;
	private static $sneQueriesOnPageAfter;
	private static $sneQueriesWithPageAsResult;
	private static $snePageProperties;
	
	public static function getQueriesOnPageBefore(){
		return self::$sneQueriesOnPageBefore;
	}
	
	public static function setQueriesOnPageBefore($queries){
		self::$sneQueriesOnPageBefore = $queries;
	}
	
	public static function deleteQueriesOnPageBefore(){
		self::$sneQueriesOnPageBefore = null;
	}
	
	public static function getQueriesOnPageAfter(){
		return self::$sneQueriesOnPageAfter;
	}
	
	public static function addQueriesOnPageAfter($query){
		if(is_array($query)){
			self::$sneQueriesOnPageAfter = array_merge(self::$sneQueriesOnPageAfter, $query);
		}
		elseif(is_object($query)){
			self::$sneQueriesOnPageAfter[] = $query;
		}
		else{
			//dummy for debugging
		}
	}
	
	public static function deleteQueriesOnPageAfter(){
		self::$sneQueriesOnPageAfter = null;
	}
	
	public static function getQueriesWithPageAsResult(){
		return self::$sneQueriesWithPageAsResult;
	}
	
	public static function setQueriesWithPageAsResult($queries){
		self::$sneQueriesWithPageAsResult = $queries;
	}
	
	public static function deleteQueriesWithPageAsResult(){
		self::$sneQueriesWithPageAsResult = null;
	}
	
	public static function getPageProperties(){
		return self::$snePageProperties;
	}
	
	public static function setPageProperties($properties){
		self::$snePageProperties = $properties;
	}
	
	public static function deletePageProperties(){
		self::$snePageProperties = null;
	}
	
	/**
	 * compares SMWQPrintout to SMWQConstraint Objects by name (used by array_uintersect)
	 * @param $select				SMWQConstraint Object
	 * @param $constraint			SMWQConstraint Object
	 * @return		0/1		1 when gateways match, 0 when not
	 */
		
	public static function comparePrintoutsToConstraintsByName($a, $b){
		if($a instanceof SMWQPrintout && $b instanceof SMWQConstraint){
			return strcasecmp($a->getVariable(),$b->getProperty());
		}
		elseif($a instanceof SMWQConstraint && $b instanceof SMWQPrintout){
			return strcasecmp($a->getProperty(),$b->getVariable());
		}
		elseif($a instanceof SMWQConstraint && $b instanceof SMWQConstraint){
			return strcasecmp($a->getProperty(),$b->getProperty());
		}
		elseif($a instanceof SMWQPrintout && $b instanceof SMWQPrintout){
			return strcasecmp($a->getVariable(),$b->getVariable());
		}
	}
	
	/**
	 * compares SMWQConstraint Objects by qid and value (used by array_udiff)
	 * @param $a			SMWQConstraint Object
	 * @param $b			SMWQConstraint Object
	 * @return		0/1		1 when gateways match, 0 when not
	 */
	
	public static function comparePropertiesByQIDandValue($a, $b){
		if(strcasecmp($a->getQid(),$b->getQid()) == 0){
			return strcasecmp($a->getValue(),$b->getValue());
		}
		else{
			return strcasecmp($a->getQid(),$b->getQid());
		}
	}
	
	/**
	 * compares 2 SMWQ Gateway objects only by qid
	 * @param 		$a				SMWQ gateway object
	 * @param 		$b				SMWQ gateway object
	 * @return		0/1				1 when gateways match, 0 when not
	 */	
	
	public static function compareObjectsByQid($a, $b){
		return strcasecmp($a->getQid(),$b->getQid());
	}
}
?>