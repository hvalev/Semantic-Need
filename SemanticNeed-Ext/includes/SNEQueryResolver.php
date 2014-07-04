<?php
class SNEQueryResolver extends QueryResolver{
	
	/**
	 * retrieves smwq constraints containing properties and/or categories
	 * present on a given page
	 * @param 		$title				MW Title object
	 * @return		array				array of SMWQConstraintGateway objects
	 */	
	
	protected static function getQueriesConstraints($title){ 
		$c = self::getPageCategories($title);
		if (  is_null( $c ) )
		{
			$c  = array();
		}
		$p = self::getPageProperties($title);
		if ( is_null( $p ) )
		{
			$p = array();
		}
		$constraints = array_merge( $p, $c );
		return SNESMWQConstraintFinder::findByConstraints($constraints);
	}
	
	/**
	 * retrieves only the properties of the smwq queries
	 * present on a given page
	 * @param 		$title				MW Title object
	 * @return		array				array of SMWQConstraintGateway objects
	 */
			
	public static function getQueriesProperties($title){
		$constraints = self::getQueriesConstraints($title);
		$properties = array();
		foreach($constraints as $key => $constraint){
			if(!$constraint->getIsCategory() && !$constraint->getIsNamespace() &&
			   !$constraint->getIsSinglePage() && !$constraint->getIsConcept() &&
			   !$constraint->getIsSubquery()){
				$properties[] = $constraint;	
			}
		}
		return $properties;
	}
	
	/**
	 * retrieves only the categories of the smwq queries
	 * present on a given page
	 * @param 		$title				MW Title object
	 * @return		array				array of SMWQConstraintGateway objects
	 */
	
	protected static function getQueriesCategories($title){
		$constraints = self::getQueriesConstraints($title);
		$categories = array();
		foreach($constraints as $key => $constraint){
			if($constraint->getIsCategory()){
				$categories[] = $constraint;	
			}
		}
		return $categories;
	}

	/**
	 * retrieves the queries properties that do not match the
	 * page properties
	 * @param 		$title				MW Title object
	 * @return		array				array of SMWQConstraintGateway objects
	 */
	
	public static function getPropertiesDifferences($title){
		$pageProperties = self::getPageProperties($title);
		$queriesProperties = self::getQueriesProperties($title);
		$propertyDifferences = array_values(array_udiff($queriesProperties, $pageProperties, array('SNEQueryResolver','comparePropertiesByName')));
		$propertyDifferences = self::getDifferences($propertyDifferences);
		return $propertyDifferences;
	}
	
	/**
	 * compares SMWQConstraintGateway objects by the value stored in the $property variable
	 * @param 			 $a				SMWQConstraintGateway object
	 * @param			 $b				SMWQConstraintGateway object
	 */
	
	public static function comparePropertiesByName($a, $b){
		return strcasecmp($a->getProperty(),$b->getProperty());
	}
	
	/**
	 * retrieves the queries categories that do not match the page categories
	 * @param 		$title				MW Title object
	 * @return		array				array of SMWQConstraintGateway objects
	 */
	
	public static function getCategoriesDifferences($title){
		$queriesCategories = self::getQueriesCategories($title);
		if (  is_null( $queriesCategories  ) )
		{
			$queriesCategories = array();
		}
		$pageCategories = self::getPageCategories($title);
		if ( is_null( $pageCategories ) )
		{
			$pageCategories = array();
		}
		$categoryDifferences = array_values(array_udiff($queriesCategories, $pageCategories, array("SNEQueryResolver",'compareCategoriesByValue')));
		$categoryDifferences = self::getDifferences($categoryDifferences);
		return $categoryDifferences;
	}
	
	/**
	 * compares 2 SMWQConstraint objects representing a smw category(used by array_udiff)
	 * @param 		$queryCategory		SMWQConstraint object representing a property as from the db
	 * @param 		$pageCategory		SMWQConstraint object representing a property as seen on the wikipage
	 */	
	
	protected static function compareCategoriesByValue($queryCategory, $pageCategory){
		return strcasecmp($queryCategory->getValue(),$pageCategory->getValue());
	}
	
	/**
	 * transforms array of (SNE)SMWQConstraintGateway to SNEVariableDisplay objects
	 * @param 		$constraints		array of (SNE)SMWQConstraintGateway objects
	 * @return		$differences		SNEPrintouts Objects
	 */	
	
	private static function getDifferences($constraints){
		$differences = array();
		$firstrun = true;
		if ( !is_null($constraints) ) 
		{
			if( count($constraints) > 0)
			{
				foreach($constraints as $key => $value){
					$currentProperty = new SNEVariableDisplay(); 
					if($value->getIsCategory()){
						$currentProperty->setVariable(Category::newFromName($value->getValue()));
					}
					else{
						$currentProperty->setVariable(SMWDIProperty::newFromUserLabel($value->getProperty()));
					}
					if($firstrun){
						$actualProperty = new SNEVariableDisplay();
						if($value->getIsCategory()){
							$actualProperty->setVariable(Category::newFromName($value->getValue()));
						}
						else{
							$actualProperty->setVariable(SMWDIProperty::newFromUserLabel($value->getProperty()));
						}
						$actualProperty->addQuery($value->getQid());
						$actualProperty->addPage($value->getPage());
						$firstrun = false;
					}
					else{
						if($actualProperty->getVariable() != $currentProperty->getVariable()){
							$differences[] = $actualProperty;
							$actualProperty = $currentProperty;
							$actualProperty->addQuery($value->getQid());
							$actualProperty->addPage($value->getPage());
						}
						elseif($actualProperty->getVariable() == $currentProperty->getVariable()){
							$actualProperty->addQuery($value->getQid());
							$actualProperty->addPage($value->getPage());
						}
					}
					if($value == end($constraints)){
						$actualProperty->addQuery($value->getQid());
						$actualProperty->addPage($value->getPage());
						$differences[] = $actualProperty;
					}
				}
			}
		}
		return $differences;
	}
	
	/**
	 * searches for printouts in queries that have the page as a result
	 * and returns the constraints that have no value set for those printouts
	 * @param 			$title				MW Title object
	 */
	
	public static function getWantedProperties($title){	
		$properties = SNESMWQPrintoutFinder::findByQid(SNEQueryResolver::getQueryMatches($title));
		$pageProperties = self::getPageProperties($title);
		$wantedProperties = array_udiff($properties, $pageProperties, array('SNEQueryResolver','comparePropertiesByName'));
		$wantedProperties = self::getDifferences($wantedProperties);
		return $wantedProperties;
	}
	
	/**
	 * searches for category printouts in queries that have the page as a result
	 * and returns the constraints that have no value set for those printouts
	 * @param 			$title				MW Title object
	 */
	
	public static function getWantedCategories($title){
		//TODO actually scan category printouts and not just missing categories
		return self::getCategoriesDifferences($title);
	}
	
	/**
	 * returns queries that nearly match a page
	 * @param 			$title				MW Title object
	 */
	
	public static function getQueryNearMatches($title){
		$queries = self::getQueries($title);
		$matches = self::getQueryMatches($title);
		return array_udiff($queries, $matches, array('QueryUpdateManager', 'compareGatewaysByQid'));
	}
}
?>