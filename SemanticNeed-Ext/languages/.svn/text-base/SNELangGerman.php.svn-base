<?php
class SNELangGerman implements SNELang {
	
	public function getIsoCode() {
		return 'de';
	}

	public function getSpecialPages() {
		return array(						// url-name									// description (shown on Special:SpecialPages)
			'MockMissingAnnotations'		=> array('SNEMockMissingAnnotations',		'Pseudofehlenden Annotationen'),
			'MockSemanticMatches'			=> array('SNEMockSemanticMatches',			'Pseudosemantische Übereinstimmungen'),
			'Admin'							=> array('SNEAdmin',						'Administrator Einstellungen'),
			'AskLog'						=> array('SNEAskLog',						'Frage Protokoll'),
			'SemanticMatches'				=> array('SNESemanticMatches',				'Semantische Übereinstimmungen'),
			'SemanticQueryInfo'				=> array('SNESemanticQueryInfo',			'Information über Semantische Abfragen'),
			'SpecialPages'					=> array('SNESpecialPages',					'Dieser Text soll versteckt sein'),
		);
	}
	
	public function getMWMessages(){
		return array(
			'specialpages-group-sne'			=> 'Semantischer Bedarf',
		);
	}

	public function getMessages() {
		return array(
			//Main
			'Title'								=>	'Semantischer Bedarf',
		
			//SNEAskLog
			'AskLogTitle'						=> 	'Liste aller Abfragen in der Wiki',
			'AskLogQueriesWithResultsHeader'	=> 	'Semantische Abfragen',
			'AskLogSearchBoxMainLabel'			=> 	' Suche Abfragen nach ',
			'AskLogSearchBoxSubLabelGeneral'	=> 	' Allgemein ',
			'AskLogSearchBoxNumRes'				=> 	' Anzahl der Ergebnisse ',
			'AskLogSearchBoxOnPage'				=> 	' auf Seite ',
			'AskLogSearchBoxSubLabelProperties'	=> 	' Eigenschaften ',
			'AskLogSearchBoxSubLabelCategories'	=> 	' Kategorien ',
			'AskLogSearchBoxButton'				=> 	'Suche',
			'AskLogBrowsingBegin'				=>	'Anfang',
			'AskLogBrowsingEnd'					=>	'Ende',
			'AskLogBrowsingShow'				=>	'Zeige',
			'AskLogBrowsingPrevious'			=>	'vorherige',
			'AskLogBrowsingNext'				=>	'nächste',
			'AskLogQueriesWithResults'			=>	'Momentan gibt es keine semantische Abfragen in der Wiki',
			'AskLogQueriesWithNoResultsHeader'	=>  'Semantische Abfragen ohne Ergebnisse',
			'AskLogQueriesWithNoResults'		=> 	'Momentan gibt es keine semantische Abfragen ohne Ergebnisse in der Wiki',
			'AskLogDBFail'						=> 	'Datenbanktabellen existieren nicht. Bitte inizialisieren Sie die Tabellen und indizieren Sie alle Wikiseiten.',
			'AskLogRedirectToAdmin'				=> 	'Auf diese Seite können sie damit fortfahren',
			
			//SNESemanticMatches
			'SemanticMatchesTitleSimple'		=> 	'Semantische Übereinstimmungen',
			'SemanticMatchesWelcome'			=> 	'Geben Sie bitte die Name einer Wikiseite:',
			'SemanticMatchesTitleAdvanced'		=> 	'Semantische Übereinstimmungen die $1 betreffen',
			'SemanticMatchesButton'				=> 	'Seite untersuchen',
			'SemanticMatchesQueries'			=> 	'Teil des Ergebnisses',
			'SemanticMatchesMissingQueries'		=> 	'keine Abfragen können angezeigt werden',
			'SemanticMatchesNearMatches'		=> 	'Fast ein Teil des Ergebnisses',
			'SemanticMatchesMissingProperties'	=> 	'Fehlende Eigenschaften',
			'SemanticMatchesNoMissingProperties'=>	'keine fehlende Eigenschaften für diese Seite',
			'SemanticMatchesMissingCategories'	=>	'Fehlende Kategorien',
			'SemanticMatchesNoMissingCategories'=>	'keine fehlende Kategorien für diese Seite',
			'SemanticMatchesWantedProperties'	=> 	'Erwünschte Eigenschaften',
			'SemanticMatchesNoWantedProperties'	=>	'keine erwünschte Eigenschaften für diese Seite',
			'SemanticMatchesWantedProperties'	=> 	'Erwünschte Kategorien',
			'SemanticMatchesNoWantedProperties'	=>	'keine erwünschte Kategorien für diese Seite',
			
			//SNESemanticQueryInfo
			'SemanticQueryInfoTitle'			=> 	'Information über Semantische Abfragen',
			'SemanticQueryInfoWelcome'			=> 	'Geben Sie bitte einen Abfrage-Hashwert oder einen Abfrage-Alias:',
			'SemanticQueryInfoButton'			=> 	'Abfrage untersuchen',
			'SemanticQueryInfoQueryFail'		=> 	'Die Abfrage existiert nicht im Datenbank',
			'SemanticQueryInfoBasicQueryHeader'	=>	'Grundabfrageinformation',
			'SemanticQueryInfoQueryInternalFail'=>	'Abfrage scheint fehlerhaft zu sein',
			'SemanticQueryInfoAdvancedQueryHeader'=>'Fortgeschrittene Abfrageinformation',
			'SemanticQueryInfoAdvancedConstraints'=>'Einschränkungen',
			'SemanticQueryInfoAdvancedPrintouts'=>	'Ausdrucke',
			'SemanticQueryInfoAdvancedNoPrintouts'=>'da gibt es keine Ausdrücke für diese Semantische Abfrage',
			
			//SNEVariableDisplay
			'VariableDisplayHeadlineProperty'	=> 	'fehlender Wert für Eingeschaft',
			'VariableDisplayHeadlineCategory'	=> 	'fehlender Wert für Kategorie',
			'VariableDisplayHeadlineValue'		=> 	'Wert angefordert durch',
			'VariableDisplayValue'				=> 	'$1 {{plural:$1|Abfrage|Abfragen}} auf $2 {{plural:$2|Seite|Seiten}}',
		
			//Toolbox
			'ToolboxLink'						=> 	'Semantische Übereinstimmungen für $1 untersuchen',
			
			//SNEBox
			'SNEBoxHeader'						=> 	'Fehlende Eigenschaften/Kategorien auf $1',
			'SNEBoxLink'						=> 	'angefordert von $1 {{plural:$1|Abfrage|Abfragen}} auf $2 {{plural:$2|Seite|Seiten}}',
			'SNEBoxButton'						=> 	'Speichern',
		
			//General
			'GeneralReturnTo'					=> 	'Rückkehr zu',
			'GeneralGoTo'						=> 	'zu',
			
			//Errors
			'ErrorAdminDBRights'				=> 	'Datenbank Fehler - Überprüfen Sie Ihre Benutzerrechte: $1'
		);
	}
}
?>
