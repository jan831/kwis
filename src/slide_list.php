<?php
/*
 * Copyright (C) 2011 Jan Marien
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

include_once("php/header.php");
printHeader("Slides");

global $debug;
$debug = false;

$selector = Array();
$selector['table'] = "round";
// $selector['param'][] = 'r.isSpecial = false';
$selector['param'][] = 'r.sequence >0';
$rounds = select($selector);

?>
<body >

<?php printMenu(true); ?>
<div class="container-fluid">
	<div class="panel panel-default" id="questions">
		<div class="panel-heading"><h3 class="panel-title">Slides</h3></div>
		<div class="panel-body">
			<div class="well">
				<h4>Hoe de slides te downloaden:</h4>
				<p>
				Download alle files hier onder naar een lokale map (slides bv.).  Dit zijn odp files, te openen als presentatie met <a href="http://libreoffice.org/">LibreOffice</a>.
				</p>
				<p>
				De afbeeldingen moeten apart gedownload worden, omdat de files anders te groot zijn.  Download alle afbeeldingen naar een submap "images", deze map moet bestaan in dezelfde map als waar de slides zelf staan (slides/images dus).  Via de <a href="slide_images.php">afbeeldingen</a> link kan je alle afbeeldingen tegelijk downloaden (save as complete web page).
				</p>
			</div>
			
<?php
			echo '<ul id="rounds" class="list-group" start="0">';
			$isfirst = true;
			foreach($rounds as $round){
				echo '<li id="round_' . $round["id"] .'" class="list-group-item"> <a href="slide_generate.php?roundId=' . $round["id"] .'">slides ronde ' . $round["description"]. "</a><br/><br/></li>";

			}
			echo'</ul>';
?>
<a href="slide_images.php">alle afbeeldingen</a>
</body>
</html>
