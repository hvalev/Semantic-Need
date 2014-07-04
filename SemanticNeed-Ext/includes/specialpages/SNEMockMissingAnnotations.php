<?php
/*******************************************************************************
*	This file is part of Woogle4MediaWiki
*   (http://www.mediawiki.org/wiki/Extension:Woogle4MediaWiki)
*
*	Copyright (c) 2007 - 2009 Hans-Jörg Happel and
*	FZI Forschungszentrum Informatik and der Universität Karlsruhe (TH)
*
*   Woogle4MediaWiki is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Woogle4MediaWiki is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Woogle4MediaWiki.  If not, see <http://www.gnu.org/licenses/>.
*******************************************************************************/ 

class SNEMockMissingAnnotations  extends SpecialPage {
	
	function __construct() {
		parent::__construct(SNEUtil::getSpecialPageLocal('MockMissingAnnotations'), '', true);
		SpecialPage::setGroup($this, 'sne');
	}

	// Here the inline output of the Special page will be created
	function execute($par) {
		global $wgOut, $wgUser;

		
		// show title
		$this->setHeaders();
		$this->returntitle = Title::makeTitle(NS_SPECIAL, SNEUtil::getSpecialPageLocal('MockMissingAnnotations'));
		
		$wgOut->setPagetitle("Special:MissingAnnotations");
		
		
		$html = '<h1>List of Wiki pages and their missing property values</h1>';
		$html .= "<table width='100%' border='1'><tr><th>Wiki Page</th><th>Value missing for property</th><th>Value requested by</th></tr>";
		$html .= '<tr><td rowspan="3"><a href="http://www.fzi.de">Nigeria</a></td><td>Area</td><td><a href="http://www.fzi.de">6 Queries on 3 pages</a></td></tr>';
		$html .= '<tr><td>Currency</td><td><a href="http://www.fzi.de">3 Queries on 2 pages</a></td></tr>';
		$html .= '<tr><td>Population</td><td><a href="http://www.fzi.de">1 Queries on 1 pages</a></td></tr>';
		$html .= '<tr><td rowspan="2"><a href="http://www.fzi.de">Zimbabwe</a></td><td>Area</td><td><a href="http://www.fzi.de">6 Queries on 3 pages</a></td></tr>';
		$html .= '<tr><td>Currency</td><td><a href="http://www.fzi.de">3 Queries on 2 pages</a></td></tr>';
		$html .= "</table>";
		
		$wgOut->addHTML($html);
		

	}
}
?>