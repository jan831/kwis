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
include_once("php/finediff.php");
printHeader("vraag detail");

global $debug;

$debug = false;
$questions = selectQuestionHistoryById($_GET["left"], $_GET["right"]);
debug($questions);
if($questions[0]["id"] == $_GET["left"]){
	$left = $questions[1];
	$right = $questions[0];
}
else {
	$right = $questions[1];
	$left = $questions[0];
}

?>

<body>
<?php printMenu(false, $left["id"]); ?>
<div class="container-fluid" id="question_diff">
<a href="question_detail.php?id=<?php echo $_GET["mainId"]; ?>">terug naar vraag</a>
<table class="table" id="audit_info">
	<tr>
		<td><?php echo $left["version"] ?> </td><td><i><?php echo $left["creationUser"]; ?></i></td><td>op <i><?php echo formatDate($left["creationDate"]); ?></i></td>
	</tr>
	<tr>
		<td><?php echo $right["version"] ?> </td><td><i><?php echo $right["creationUser"]; ?></i> </td><td>op <i><?php echo formatDate($right["creationDate"]); ?></i></td>
	</tr>
</table>
<br/><br/>
<table>
<?php
	global $questionFields;
 	$changedFields ="";
 	debug("getChangedField");
 	foreach ($questionFields as $key => $value){
		$from_text = str_replace('&nbsp;', ' ', $left[$key]);
    		$to_text = str_replace('&nbsp;', ' ', $right[$key]);
		$diff = new FineDiff($from_text, $to_text, FineDiff::$wordGranularity);
		echo "<tr><td style='width: 200px;'>" . $value . "</td><td colspan='2'>" . $diff->renderDiffToHTML() . "</td></tr>\n";
	}
?>
</table>

</body>
</html>
