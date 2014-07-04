<?php
class SNECoreLangGerman implements SNECoreLang {
	
	public function getIsoCode() {
		return 'de';
	}

	public function getSpecialPages() {
		return array(						// url-name									// description (shown on Special:SpecialPages)
			'Admin'							=> array('SNEAdmin',						'Administrator Einstellungen'),
		);
	}
	
	public function getMWMessages(){
		return array(
			'specialpages-group-sneCore'			=> 'Semantischer Bedarf Kern',
		);
	}

	public function getMessages() {
		return array(
			//Main
			'Title'								=>	'Semantischer Bedarf Kern',
		
			//SNEAdmin
			'AdminTitle'						=>	'Semantischer Bedarf Konfiguration Seite',
			'AdminWelcome'						=> 	'Hier können Sie den SNE Erweiterung einrichten! :)',
			'AdminDBRunning'					=> 	'Tabellen erstellt',
			'AdminDBNotRunning'					=> 	'Tabellen nicht erstellt',
			'AdminDBDrop'						=> 	'Löschen der SemanticNeed Datenbank',
			'AdminDBDropMsg'					=> 	'$1 {{plural:$1|Tabelle|Tabellen}} sind aus dem Datenbank entfernt worden.',
			'AdminDBInit'						=> 	'Inizialisierung der SemanticNeed Datenbank',
			'AdminDBInitMsg'					=> 	'$1 {{plural:$1|Tabelle|Tabellen}} sind zu dem Datenbank eingefügt.',
			'AdminReindex'						=> 	'Alle Seiten indizieren',
			'AdminReindexMsg'					=> 	'Alle Wikiseiten wurden erfrischen. Bitte haben Sie Geduld.<br />',
			'AdminFindWikiPagesTime'			=> 	'Startzeit: $1<br />',
			'AdminFindWikiPagesExtractPages'	=> 	'Abfragen von Seiten aus der Datenbank...<br />',
			'AdminFindWikiPagesParsingPages'	=> 	'Parsing $1 {{plural:$1|Seite|Seiten}} für ASK-Abfragen<br />',
			'AdminPurgeWikiPagesComplete'		=> 	'$1 {{plural:$1|wikiseite|wikiseiten}} {{plural:$1|würde|würden}} indiziert<br />',
			'AdminPurgeWikiConceptsComplete'	=> 	'$1 {{plural:$1|konzept|konzepte}} {{plural:$1|würde|würden}} indiziert',
		
			//General
			'GeneralReturnTo'					=> 	'Rückkehr zu',		
			
			//Errors
			'ErrorAdminDBRights'				=> 	'Datenbank Fehler - Überprüfen Sie Ihre Benutzerrechte: $1'
		);
	}
}
?>
