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
debug($_POST);

printHeader(); ?>
<body >
<?php printMenu(true);
?>
<div class="container-fluid">
<?php 
if(getQuizId() != -1){ 
	global $debug;
	
	$nbrOfEntries = coalesce(@$_REQUEST["nbrOfEntries"], 15);
	$questions = selectQuestionsForOverview($nbrOfEntries);
	$tasks = selectTasksForOverview($nbrOfEntries);
	echo "<h3>laatst gewijzigde vragen: </h3>";
	echo"<ul id=\"list_0\" class=\"list_\">";
	foreach($questions as $question){
		echo '<li>'. formatAuditInfo($question). ': '. formatAnswer( $question, array("editable" => 0, "link"=> true, "hiddenQuestion" => true, "tasks" => true, "difficulty" => false) ) . '</li>';
	}
	echo"</ul>";

	echo "<h3>laatst gewijzigde notities: </h3>";
	echo"<ul id=\"list_0\" class=\"list_\">";
	foreach($tasks as $task){
		$description = '';
		if(strlen($task['description'])>0 ){
			$description = linkify($task['description']) . " - ";
		}
		echo '<li>'. formatAuditInfo($task). ': '. $task['taskCategory'] . " - " . $description .  formatAnswer( $task["question"], array("editable" => 0, "link"=> true, "hiddenQuestion" => true, "tasks" => true, "difficulty" => false) ) . '</li>';
	}
	echo"</ul>";
	
	$moreEntries = $nbrOfEntries*2;
	echo "<a href='index.php?nbrOfEntries=" . $moreEntries . "'>toon meer</a>";} ?>
</div>
</body>
</html>
