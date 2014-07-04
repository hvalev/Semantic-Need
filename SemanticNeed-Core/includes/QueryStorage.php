<?php

abstract class QueryStorage {
	//TODO move get/setdb from SNECoreConfig?
	public static function getDb(){
		$db = SNECoreConfig::getDB();
		return $db;
	}
	
	/**
	 * drop db tables
	 * @return void
	 */
	
	public static function dropDbTables($db){
		$count = 0;
		extract($db->tableNames('smwq_query', 'smwq_select', 'smwq_constraint'));
		if ($db->tableExists($smwq_query)) {
			$sql = 'DROP TABLE ' . $smwq_query . ';';
			$db->query($sql);
			$count++;
		}
		if ($db->tableExists($smwq_select)) {
			$sql = 'DROP TABLE ' . $smwq_select . ';';
			$db->query($sql);
			$count++;
		}
		if ($db->tableExists($smwq_constraint)) {
			$sql = 'DROP TABLE ' . $smwq_constraint . ';';
			$db->query($sql);
			$count++;
		}
		return $count;
	}
	
	
	/**
	 * create loggging tables
	 * @return void
	 */
	
	
	public static function createDbTables($db){
		$count = 0;
		//connection and database
		extract($db->tableNames('smwq_query', 'smwq_select', 'smwq_constraint'));
		
		if ($db->tableExists($smwq_query) == false) {
			$sql = 'CREATE TABLE ' . $smwq_query . ' (' .
					'smwq_query_id				INTEGER		UNSIGNED					NOT NULL	AUTO_INCREMENT, ' .
					'smwq_query_qid				VARCHAR(200)							NOT NULL, ' .
					'smwq_query_type			INTEGER		UNSIGNED					NOT NULL, ' .
					'smwq_query_format			VARCHAR(200)							NOT NULL, ' .
					'smwq_query_limit			INTEGER									NOT NULL, ' .
					'smwq_query_results			INTEGER		UNSIGNED					NOT NULL, ' .
					'smwq_query_page			VARCHAR(200)							NOT NULL, ' .
					'smwq_query_queryString		VARCHAR(1000)							NOT NULL, ' .
					'smwq_query_link			VARCHAR(1000)							NOT NULL, ' .
					'smwq_query_alias			VARCHAR(200)								NULL, ' .
					'smwq_query_isConcept		BOOL									NOT	NULL, ' .
					'smwq_query_isSubquery		BOOL									NOT	NULL, ' .
					'smwq_query_createdOn		TIMESTAMP 	DEFAULT CURRENT_TIMESTAMP	NOT NULL, ' .
					'smwq_query_removedOn		TIMESTAMP 									NULL, ' .
					'smwq_query_userId			VARCHAR(200)							NOT NULL, ' .
					'smwq_query_active			INTEGER		UNSIGNED					NOT NULL, ' .
					'PRIMARY KEY(smwq_query_qid), ' .
					'UNIQUE INDEX(smwq_query_id))';
			$db->query($sql);
			$count++;
		}
		
		if ($db->tableExists($smwq_select) == false) {
			$sql = 'CREATE TABLE ' . $smwq_select . ' (' .
					'smwq_select_id				INTEGER		UNSIGNED	NOT NULL	AUTO_INCREMENT, ' .
					'smwq_select_qid			VARCHAR(200)			NOT NULL, ' .
					'smwq_select_variable		VARCHAR(200)			NOT NULL, ' .
					'UNIQUE INDEX(smwq_select_id))';
			$db->query($sql);
			$count++;
		}
		
		if ($db->tableExists($smwq_constraint) == false) {
			$sql = 'CREATE TABLE ' . $smwq_constraint . ' (' .
					'smwq_constraint_id				INTEGER		UNSIGNED	NOT NULL	AUTO_INCREMENT, ' .
					'smwq_constraint_qid			VARCHAR(200)			NOT NULL, ' .
					'smwq_constraint_order			INTEGER					NOT NULL, ' .
					'smwq_constraint_andor			VARCHAR(200)				NULL, ' .
					'smwq_constraint_isCategory		BOOL					NOT NULL, ' .
					'smwq_constraint_isNamespace	BOOL					NOT NULL, ' .
					'smwq_constraint_isSinglePage	BOOL					NOT NULL, ' .
					'smwq_constraint_isConcept		BOOL					NOT NULL, ' .
					'smwq_constraint_isSubquery		BOOL					NOT NULL, ' .
					'smwq_constraint_property		VARCHAR(200)				NULL, ' .
					'smwq_constraint_expression		VARCHAR(200)				NULL, ' .
					'smwq_constraint_value			VARCHAR(200)				NULL, ' .
					'PRIMARY KEY(smwq_constraint_id), ' .
					'UNIQUE INDEX(smwq_constraint_id))';
			$db->query($sql);
			$count++;
		}
		return $count;
	}
	
	/* Checks if the db has been set
	 * 
	 */
	
	public static function sanityCheck(){
		if(!SNECoreConfig::tableExists('smwq_query')||!SNECoreConfig::tableExists('smwq_select')||!SNECoreConfig::tableExists('smwq_constraint')){
			return false;
		}
		else{
			return true;
		}
	}
}
?>