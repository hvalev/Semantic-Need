<?php
/**
 * Finder class for SMWQConstraintGateway which has various functions
 * to retrieve arrays of SMWQQueryGateway Objects objects
 * @author valev
 *
 */

abstract class SMWQConstraintMapper{//{ implements ISMWQMapper{
	
	/** Inserts the current SMWQConstraint object in the Database 
	 * @param		void			
	 * @return		void				
	**/
	
	public static function insert(SMWQConstraint $constraint){
		try{
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_constraint'));
			$table = preg_replace("/\`/", '', $smwq_constraint);
			$array = $constraint->toArray();
			foreach($array as $key => $value){
				unset($array[$key]);
				$array[$table.'_'.$key] = $value;
			} 
			$db->insert($smwq_constraint,$array);
		}catch(Exception $e){
			throw new Exception('insertion of invalid query data');
		}
	}
}
?>