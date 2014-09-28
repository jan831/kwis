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
printHeader("Alle afbeeldingen");

global $debug;
$debug = false;

$selector = Array();
$selector['table'] = "question";
$selector['order'] = "round_thema";
$selector['param'][] = 'q.roundId is not null and r.sequence > 0';
$questions = selectQuestions($selector, false, true, true);

	echo "<body >";

	printMenu(true);
	?>
	<div class="container-fluid">
		<div class="panel panel-default" id="questions">
			<div class="panel-heading"><h3 class="panel-title">Alle afbeeldingen</h3></div>
			<div class="panel-body">
				<?php 
				foreach($questions as $question){
					if($question['imageCount'] >0){
						foreach($question['images'] as $img){
							echo '<span  class="slideImages">' .formatImage($img, $question) . '</span>';
						}
					}
					if($question["childQuestions"]>0){
						foreach($question["children"] as $subQuestion){
							if($subQuestion['imageCount'] >0){
								foreach($subQuestion['images'] as $img){
									echo '<span class="slideImages">' .formatImage($img, $subQuestion) . '</span>';
								}
							}
						}
					}
				}
				?>
			</div>
		</div>
	</div>

</body>
</html>
