<?php
/**
 * finder class that searches the database and returns objects of type
 * (SNE)SMWQQueryGateway
 * @author spell
 *
 */

abstract class SNESMWQQueryFinder extends SNESMWQQueryGateway{
	
	/**
	 * retrieves all queries from the database
	 * @return  $queries			array of SMWQQuery objects
	 */
	
	public static function findAll($offset = null, $limit = null){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));

			$options = array();
			if(isset($offset)){
				$options['OFFSET'] = $offset;
			}
			if(isset($limit)){
				$options['LIMIT'] = $limit;
			}

			$sqlres = $db->select($smwq_query, '*', '', 
								'DatabaseBase::select', $options);
			$queries = array();
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$query = new SNESMWQQueryGateway();
				$query->instantiateFromDb($db->fetchobject($sqlres));
				$queries[] = $query;
			}
			return $queries;
		}catch(Exception $e){
			throw new Exception('');
		}
	}
	
	/**
	 * retrieves number of queries from the database
	 * @return  $queries			array of SMWQQuery objects
	 */
	
	public static function findAllCount(){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$sqlres = $db->select($smwq_query, 'COUNT(*)');
			$count = (array) $db->fetchobject($sqlres);
			return $count['COUNT(*)'];
		}catch(Exception $e){
			throw new Exception('');
		}
	}
}
?>