<?php
/**
 * class that retrieves printouts from the database
 * @author spell
 *
 */

abstract class SNESMWQPrintoutFinder extends SNESMWQPrintoutGateway{
	
	/**
	 * retrieves printouts by qid from gateway objects
	 * @param $gateways
	 */
	
	public static function findByQid($gateways){
		try{
			if(empty($gateways)){
				return array();
			}
			$db = SNEConfig::getDB();
			extract($db->tableNames('smwq_select', 'smwq_query'));
			
			
			//TODO dummy object to use getDbField
			$sql = 'SELECT smwq_select_qid, smwq_select_variable, smwq_query_page FROM ' . $smwq_select . '';
			$sql .= ' INNER JOIN ' . $smwq_query . ' ON smwq_query_qid = smwq_select_qid WHERE ';
		
			foreach($gateways as $key => $gateway){
				$sql .= 'smwq_select_qid = "'.$gateway->getQID().'"';
				if($gateway == end($gateways)){
					//do nothing
				}
				else{
					$sql .= ' OR ';
				}
			}
			$sql .= ' ORDER BY smwq_select_variable';
			
			$printouts = array();
			$sqlres = $db->query($sql);
			for($i=0;$i<$db->numRows($sqlres);$i++){
				$stdObject = (array) $db->fetchobject($sqlres);
				$currentPrintout = new SNESMWQConstraintGateway();
				$currentPrintout->setQid($stdObject['smwq_select_qid']);
				$currentPrintout->setProperty($stdObject['smwq_select_variable']);
				$currentPrintout->setPage($stdObject['smwq_query_page']);
				$printouts[] = $currentPrintout;
			}
			return $printouts;
		}catch(Exception $e){
			//TODO 
			throw new Exception('');
		}
	}
}

?>