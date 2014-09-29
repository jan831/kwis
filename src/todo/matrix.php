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
printHeader("vragen overzicht");

$print = coalesce($_GET["print"], $_SESSION["print"], 0);
$_SESSION["print"] = $print;
?>
<body>
<?php printMenu(); ?>
&nbsp;<span id="statusBox" style="padding: 2px;"></span>&nbsp;
<br/>&nbsp;
<?php
global $debug;
$debug = false;

$questionsWithoutRound = selectQuestionsWithoutRoundForMatrix();
$themas = selectThemasForMatrix();
$rounds = selectRoundsForMatrix();
$specialRounds = selectSpecialRoundsForMatrix();
$roundNull = selectRoundNull();
$difficulties = selectDifficulty();
$questions = selectQuestionsForMatrix($themas);

debug("rounds", $rounds);
debug("themas", $themas);
debug("questions", $questions);
echo '<div id="drag"><table width="100%"  class="matrix" id="questionMatrix"	style="min-width: 100%">';

$cols = 0;
echo "<thead><tr id=\"headerRow\"><td class=\"forbid matrixCell\"  id=\"col" . ($cols++) . "\">\n";

echo '<span onclick="resetLayout();" class="clickeable">reset layout</span>';
echo '</td>';
foreach($rounds as $round) {
	echo "<td class=\"forbid matrixHeader clickeable roundHeader\"  title=\"klik om ronde te verbergen\" id=\"col" . ($cols++) . "\">" . $round["description"]. "</td>\n";
}
echo "</tr></thead>";

$drag = $print?"":"drag";

$rows = 0;
foreach($questions as $sequence => $tmp){
	$thema = $themas[$sequence];
	debug("thema ", $thema);
	echo "<tr id=\"row". $rows++ ."\">";
	echo '<td class="forbid matrixCell clickeable rowHeader" title="klik om rij te verbergen">' .$sequence . ". ". $thema['description'] . "</td>\n";

	foreach($rounds as $round) {
		$extraClass='forbid';
		if($round["isSpecial"]){
			$id = "cell_" . $round["roundId"] . '_-1_' . $sequence;
			$extraClass ="dropable";
		}
		else{
			$id = "cell_" . $round["roundId"] . '_' . $thema["themaId"];
			if(isset($thema)){
				$extraClass ="dropable";
			}
		}
		echo '<td class="' . $extraClass. ' matrixCell" id="' . $id .'">';

		debug("roundId roundDescr, sequence " . $round["roundId"] .' '. $round["description"] . " " . $sequence);
		if(is_array($questions[$sequence][$round["roundId"]])){
			foreach($questions[$sequence][$round["roundId"]]  as $question) {
				$qId = 'draghandle_' . $question["questionId"] . '_'. $question["themaId"];
				if($question["isSpecial"]){
					$qId.= '_'. $question["roundId"];
				}
				echo  '<div class="'. $drag .'" id="' . $qId .'" >' . formatAnswerForMatrix($question, $print);
				if($question['childQuestions'] > 0){
					echo '<ul class="childQuestions">';
					foreach($question["children"]  as $subQuestion){
						echo '<li>' . formatAnswerForMatrix($subQuestion, $print) . '</li>';
					}
					echo'</ul>';
				}
				echo '</div>';
			}
		}
		echo "</td>";
	}
	echo "</tr>";
}

if(!$print){
	$sequence++;
	echo "<tr id=\"extraSequenceTR\"><td class=\"forbid matrixCell clickeable\" id=\"extraSequenceTD\" title=\"klik om te verbergen\">&nbsp</td>\n";
	foreach($rounds as $round) {
		$id = "cell_" . $round["roundId"].'_-1_' . $sequence;
		$extraClass='';
		if($round["isSpecial"]){
			$extraClass ="dropable";
		}
		else{
			$extraClass = "forbid";
		}
		echo '<td class="'. $extraClass .' matrixCell" id="' . $id .'">';
		debug("roundId roundDescr, sequence " . $round["roundId"] .' '. $round["description"] . " " . $sequence);
		echo '</td>';
	}
	echo "</tr>";
}

echo "<tfoot><tr id=\"difficultyFooter\"><td class=\"forbid matrixCell clickeable\" id=\"toggleDifficulty\">verberg samenvatting</td>\n";
foreach($rounds as $round) {
	$id = "difficulty_" . $round["roundId"] ;
	echo '<td class="forbid difficultyCell"  id="' . $id .'">';
	echo '<ul class="difficultyList">';
	foreach($difficulties as $diff){
		if($diff[id] >0){
			echo '<li><span class="difficulty difficulty' . $diff["id"] . '">&nbsp;</span>' .  $diff["description"] . '</li>';
		}
	}
	echo "</ul>";
	echo "</td>";
}
echo "</tr></tfoot>";

echo '</table><br/><br />';

echo "<table  class=\"matrix\"><thead><tr><td class=\"forbid matrixHeader\" colspan=\"2\">niet toegewezen vragen</td></tr></thead>\n";
echo "<tr><td class=\"forbid matrixCell\"> rondenummer verwijderen, bestaand thema behouden</td>";
	echo '<td class="dropable matrixCell" id="cell_' . $roundNull["id"] . '"></td></tr>';

foreach($questionsWithoutRound as $title => $questionGroup){
	$id = "cell_" . $roundNull["id"] . '_' . $questionGroup["id"];
	echo '<tr><td class="forbid matrixCell">' . $title  . '</td>';
	echo '<td class="dropable matrixCell" id="' . $id .'">';

	foreach($questionGroup["questions"] as $question){
				echo  '<div class="'. $drag .'" id="draghandle_' . $question["questionId"] . '_'. $question["themaId"] .'">' . formatAnswerForMatrix($question, $print);
				if($question['childQuestions'] > 0){
					echo '<ul  class="childQuestions">';
					foreach($question["children"]  as $subQuestion){
						echo '<li>' . formatAnswerForMatrix($subQuestion, $print) . '</li>';
					}
					echo'</ul>';
				}
				echo '</div>';
	}
	echo '</td></tr>';
}

echo '</table></div>';




?>

<script type="text/javascript">

var hover_color = '#E8E8E8';
// used for myhandlers demo to display events
function message(text){
//	debug( text);
}


$(document).ready(function(){
	/*
	var newOptions = {
	    expiresAt: new Date( <?php  echo (date("Y") +1 ). ",  " . date("m, d"); ?> )
	};
	$.cookies.setOptions(newOptions);
*/

		updateDifficulties();
	updateLayout();

	$( "#toggleDifficulty").click(function(){
		$("#difficultyFooter").hide();
		$.cookie("matrixHide_difficultyFooter", true);
	});
		$( "#extraSequenceTD").click(function(){
		$("#extraSequenceTR").hide();
		$.cookie("matrixHide_extraSequenceTR", true);
	});
		$( ".rowHeader").click(function(){
	debug("matrixHide_"+$(this.parentNode)[0].id);
		$(this.parentNode).hide();
		$.cookie("matrixHide_"+$(this.parentNode)[0].id, true);
	});
	$(".roundHeader").click(function(){
		debug(this);
		$('#questionMatrix td:nth-child(' + (this.cellIndex +1) + ')').hide();
		$.cookie("matrixHide_" + this.id, true);
	});
});

</script>
<?php printNumberOfQueriesDone(); ?>
</body>
</html>
