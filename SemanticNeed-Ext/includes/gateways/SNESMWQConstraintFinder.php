<?php
/**
 * Finder class for SMWQConstraintGateway which has various functions
 * to retrieve arrays of SMWQConstraintGateway objects
 * @author valev
 *
 */

abstract class SNESMWQConstraintFinder extends SNESMWQConstraintGateway{
	
	/**
	 * retrieves constraints from given gateway objects qids
	 * @param	$gateway			array of SMWQGateway objects
	 * @return  $constraints		array of SMWQConstraint objects
	 */
	
	public static function findByQid($gateways){
		try{
			if(empty($gateways)){
				return array();
			}
			$dummy = new SMWQConstraintGateway();
			$dummy->setQid('this');
			$name = $dummy->getVarName('this');
			
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_constraint', 'smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_constraint);
			
			$vars = array();
			$str = '_toreplace_';
			$count = 0;
			foreach($gateways as $key => $gateway){
				$count++;
				$vars[$str.$count] = $gateway->getQid();
			}
			
			$list = $db->makeList($vars, LIST_OR);
			$list = preg_replace("/_toreplace_[0-9]*/", $table.'_'.$name, $list);
			
			$sqlres = $db->select($smwq_constraint, '*' , $list);
			$constraints = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$constraint = new SMWQConstraintGateway();
				$constraint->instantiateFromDb($db->fetchobject($sqlres));
				$constraints[] = $constraint;
			}
			return $constraints;
		}catch(Exception $e){
			throw new Exception('');
		}
	}
	
	/** Finds a List of SMWQConstraintGateway Objects by a list of SMWQConstraints
	 * @param		$constraints			Array of SMWQConstraint objects			
	 * @return		$constraints			Array of SMWQConstraints as returned from the DB
	**/
	
	public static function findByConstraints($constraints){
		try{
			if(empty($constraints)){
				return array();
			}
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_constraint', 'smwq_query'));
			
			$list = array();
			foreach($constraints as $key => $constraint){

				if($constraint->getIsCategory()){
					//remove the category namespace tag because it
					//messes with the sql code
					$constraint->setProperty(null);
				}
				else{
					//remove the property value
					//because it messes with the sql code
					$constraint->setValue(null);
				}
				//get all variables that are set in the object
				$variables = $constraint->getSetVariables();
				
				//generating sql for the constraints
				//i.e. isCategory = 1 AND value = Awesome + etc
				$list[] = $db->makeList($variables, LIST_AND);
			}
			//creating the query for all the constraints
			//(isProperty=1 AND ...) OR (isCategory = 1 AND .. ) OR etc..
			$list = $db->makeList($list, LIST_OR);
			//its not very beautiful i know but thats the way to get all the
			//constraints to be like WHERE ((constraint1 props) OR (constraint2 props) OR etc..)
			$list = '('.$list.')';
			
			//create the sql for the constraints
			$table = preg_replace("/\`/", '', $smwq_constraint);
			$dummy = new SMWQConstraintGateway();
			$dummy->setQid('this');
			$sqlConstraints = $db->selectSQLText($smwq_constraint, $table.'_'.$dummy->getVarName($dummy->getQid()), $list);
			
			//create the sql to select only active queries
			$table = preg_replace("/\`/", '', $smwq_query);			
			$dummy = new SMWQQueryGateway();
			$dummy->setQid('this');
			$dummy->setActive(1);
			$var[$table.'_'.$dummy->getVarName($dummy->getActive())] = $dummy->getActive();
			$sqlActive = $db->selectSQLText($smwq_query, $table.'_'.$dummy->getVarName($dummy->getQid()), $var);
			//add the IN clause to the subquery so that it would match constraint_qid with query_qid
			$table = preg_replace("/\`/", '', $smwq_constraint);
			$dummy = new SMWQConstraintGateway();
			$dummy->setQid('this');
			$sqlActive = $table.'_'.$dummy->getVarName($dummy->getQid()).' IN ('.$sqlActive.')';
			
			//combine both subqueries to prepare them for the main query
			$combinedList = array($sqlConstraints,$sqlActive);
			$subQuery = $db->makeList($combinedList, LIST_AND);
			
			//set the options
			$dummy->setProperty('hey');
			$dummy->setValue('yall');
			$options['ORDER BY'] = $table.'_'.$dummy->getVarName($dummy->getProperty());
			$options['ORDER BY'] .= ','.$table.'_'.$dummy->getVarName($dummy->getValue());
			//print_r($options);
			//self::rr();
			
			//set tables to select
			$tables = array();
			$tables[] = 'const.*';
			$table = preg_replace("/\`/", '', $smwq_query);	
			$dummy = new SMWQQueryGateway();
			$dummy->setPage('this');
			$tables[] = 'query.'.$table.'_'.$dummy->getVarName($dummy->getPage());
			
			//set what to select exactly (Mysql: FROM ???)
			//$table = preg_replace("/\`/", '', $smwq_constraint);
			$vars = "$smwq_constraint AS const";
			//since the database class is gay as hell this is the only way
			//to perform an inner join.. yeah baby.. kill me now.. :( 
			$vars .= " INNER JOIN $smwq_query AS query ON ";
			$table = preg_replace("/\`/", '', $smwq_query);
			$dummy->setQid('rawr');
			$vars .= $table.'_'.$dummy->getVarName($dummy->getQid()).' = ';
			$table = preg_replace("/\`/", '', $smwq_constraint);
			$dummy = new SMWQConstraintGateway();
			$dummy->setQid('rawr');
			$vars .= $table.'_'.$dummy->getVarName($dummy->getQid());
			//smwq_query_qid = smwq_constraint_qid";
			$sqlres = $db->select($vars, $tables, $table.'_'.$dummy->getVarName($dummy->getQid()).' IN '.$subQuery,'DatabaseBase::select', $options);
			
			$constraints = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$constraint = new SNESMWQConstraintGateway();
				$constraint->instantiateFromDb($db->fetchobject($sqlres));
				$constraints[] = $constraint;
			}
			return $constraints;
			
		}catch(Exception $e){ 
			throw new Exception('');
		}
	}
}