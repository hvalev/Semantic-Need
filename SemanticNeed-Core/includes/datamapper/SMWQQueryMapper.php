<?php
/**
 * Finder class for SMWQConstraintGateway which has various functions
 * to retrieve arrays of SMWQQueryGateway Objects objects
 * @author valev
 *
 */

abstract class SMWQQueryMapper{// implements ISMWQMapper{
	
	/** Inserts the current SMWQQuery object in the Database 
	 * @param		void			
	 * @return		void				
	 */
	
	public static function insert(SMWQQuery $query){
		try{			
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_query);
			$array = $query->toArray();
			foreach($array as $key => $value){
				unset($array[$key]);
				$array[$table.'_'.$key] = $value;
			}
			$db->insert($smwq_query, $array); 
		}catch(Exception $e){
			throw new Exception('insertion of invalid query data');
		}
	}
	
	/** Updates the current SMWQQueryObject with the current values in the Database 
	 * @param		void			
	 * @return		void				
	 */
	
	public static function update(SMWQQuery $query){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_query);
			$array = $query->toArray();
			foreach($array as $key => $value){
				unset($array[$key]);
				$array[$table.'_'.$key] = $value;
			}
			$conds[$table.'_'.$query->getVarName($query->getQid())] = $query->getQid();
			$db->update($smwq_query, $array, $conds);
		}catch(Exception $e){
			throw new Exception('updating query with invalid data');
		}
	}
	
	/** Marks the current SMWQQueryObject as active in the Database 
	 * @param		void			
	 * @return		void				
	 */
	
	public static function active(SMWQQuery $query){
		try{
			$query->setActive(1);
			$query->setRemovedOn(null);
			SMWQQueryMapper::update($query);
		}catch(Exception $e){
			throw new Exception('updating query with invalid data');
		}
	}
	
	/** Marks the current SMWQQueryObject as inactive in the Database 
	 * @param		void			
	 * @return		void				
	 */
	
	public static function inactive(SMWQQuery $query){
		try{
			$db = SNECoreConfig::getDB();
			$query->setActive(0);
			$query->setRemovedOn($db->timestamp(time()));
			SMWQQueryMapper::update($query);
		}catch(Exception $e){
			throw new Exception('updating query with invalid data');
		}
	}
	
	/** Checks if the current SMWQQueryObject exists in the Database 
	 * @param		void			
	 * @return		void				
	 */

	public static function exists(SMWQQuery $query){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_query);
			$var[$table.'_'.$query->getVarName($query->getQid())] = $query->getQid();
			$sqlres = $db->select($smwq_query, '*' , $var);
			if($db->numRows($sqlres)==1){
				return true;
			}
			else{
				return false;
			}
		}catch(Exception $e){
			throw new Exception('updating query with invalid data');
		}
	}
	
	
	/**
	 * retrieves queries from given gateway objects qids
	 * @param	$gateway			array of SMWQGateway objects
	 * @return  $queries			array of SMWQConstraint objects
	 */
	
	public static function findByQid($gateways){
		try{
			//low level error handling
			if(empty($gateways)){
				return array();
			}

			$dummy = new SMWQQueryGateway();
			//really dumb roundabout way to complete the table name
			$dummy->setQid('this');
			$name = $dummy->getVarName('this');
			
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_query);
			
			//even dumber way to do it, but seeing how the Database
			//class is so inflexible and does not allow identical 
			//fields with the same name.. or to specify ANDs or ORs
			//in the select method...
			$vars = array();
			$str = '_toreplace_';
			$count = 0;
			foreach($gateways as $key => $gateway){
				$count++;
				$vars[$str.$count] = $gateway->getQid();
			}
			
			$list = $db->makeList($vars, LIST_OR);
			$list = preg_replace("/_toreplace_[0-9]*/", $table.'_'.$name, $list);		
			$sqlres = $db->select($smwq_query, '*' , $list);

			$queries = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$query = new SMWQQueryGateway();
				$query->instantiateFromDb($db->fetchobject($sqlres));
				$queries[] = $query;
			}
			return $queries;
		}catch(Exception $e){
			throw new Exception('');
		}
	}
	
	/** Finds an array of SMWQQuery Objects by the page name they are on
	 * @param		$page					name of the wikipage			
	 * @return		$queries				Array of SMWQQuery objects as returned from the DB
	 */
	
	public static function findByPage($page){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_query);
			
			$dummy = new SMWQQuery();
			$dummy->setPage($page);
			$dummy->setActive('1');
			
			$conds = array();
			$conds[$table.'_'.$dummy->getVarName($dummy->getPage())] = $dummy->getPage();
			$conds[$table.'_'.$dummy->getVarName($dummy->getActive())] = $dummy->getActive();
			
			$sqlres = $db->select($smwq_query, '*' , $conds);
			
			$queries = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$query = new SMWQQuery();
				$query->instantiateFromDb($db->fetchobject($sqlres));
				$queries[] = $query;
			}
			return $queries;
		}catch(Exception $e){ 
			throw new Exception('');
		}
	}
	
	/** Finds an array of SMWQQuery Objects through an array of SMWQConstraint objects
	 * @param		$constraints				Array of SMWQConstraint objects			
	 * @return		$queries					Array of SMWQQuery objects as returned from the DB
	 */
	
	public static function findByConstraints($constraints){
		try{
			if(empty($constraints)){
				
				return array();
			}

			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_constraint', 'smwq_query'));

			//prepare the array that is going to hold the constraints
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
			$dummy = new SMWQConstraint();
			$dummy->setQid('this');
			$sqlConstraints = $db->selectSQLText($smwq_constraint, $table.'_'.$dummy->getVarName($dummy->getQid()), $list);
			
			//create the sql to select only active queries
			$table = preg_replace("/\`/", '', $smwq_query);			
			$dummy = new SMWQQuery();
			$dummy->setQid('this');
			$dummy->setActive(1);
			$var[$table.'_'.$dummy->getVarName($dummy->getActive())] = $dummy->getActive();
			$sqlActive = $db->selectSQLText($smwq_query, $table.'_'.$dummy->getVarName($dummy->getQid()), $var);
			//add the IN clause to the subquery so that it would match constraint_qid with query_qid
			$sqlActive = $table.'_'.$dummy->getVarName($dummy->getQid()).' IN ('.$sqlActive.')';
			
			//combine both subqueries to prepare them for the main query
			$combinedList = array($sqlConstraints,$sqlActive);
			$subQuery = $db->makeList($combinedList, LIST_AND);
			
			//since the mw database class is so disgusting if you want to have complex
			//queries this is a roundabout way to get it working while 
			//maintaining the abstraction of the functions provided
			//by the same database class as much as possible...
			$sqlres = $db->select($smwq_query,'*',$table.'_'.$dummy->getVarName($dummy->getQid()).' IN '.$subQuery);
			$queries = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$query = new SMWQQuery();
				$query->instantiateFromDb($db->fetchobject($sqlres));
				$queries[] = $query;
			}
			return $queries;
		}catch(Exception $e){
			throw new Exception('');
		}
	}
}
?>