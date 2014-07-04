<?php
/**
 * Finder class for SMWQPrintoutGateway which has various functions
 * to retrieve arrays of SMWQPrintoutGateway Objects objects
 * @author valev
 *
 */

abstract class SMWQPrintoutMapper{// implements ISMWQMapper{
	
	/** Inserts the current SMWQQuery object in the Database 
	 * @param		void			
	 * @return		void				
	 */
	
	public static function insert(SMWQPrintout $printout){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_select'));
			$table = preg_replace("/\`/", '', $smwq_select);
			$array = $printout->toArray();
			foreach($array as $key => $value){
				unset($array[$key]);
				$array[$table.'_'.$key] = $value;
			}   
			$db->insert($smwq_select,$array);
		}catch(Exception $e){
			throw new Exception('insertion of invalid query data');
		}
	}
	
	/** Finds an array of SMWQPrintout Objects through SMWQGateway objects
	 * @param		$gateways					Array of SMWQGateway objects			
	 * @return		$printouts					Array of SMWQPrintout objects as returned from the DB
	 */	

	public static function findByQid($gateways){
			try{
			//low level error handling
			if(empty($gateways)){
				return array();
			}

			$dummy = new SMWQQuery();
			//really dumb roundabout way to complete the table name
			$dummy->setQid('this');
			$name = $dummy->getVarName('this');
			
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_select'));
			$table = preg_replace("/\`/", '', $smwq_select);
			
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
			$sqlres = $db->select($smwq_select, '*' , $list);

			$queries = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$query = new SMWQPrintout();
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