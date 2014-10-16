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
$selector = getDefaultSelectorForList(@$_REQUEST["selector"], @$_REQUEST["reset"]);
printHeader("vragen lijst");
echo "<body>";
printMenu();
echo '<div class="container-fluid">';
$taskCategories = selectTaskCategory();
include ("php/question_selector.php");


	global $debug;
	$debug = false;
	$results = selectQuestionsForList($selector);
	$difficulties = selectDifficulty();

		if(is_array($results)){
			$count = 0;
			foreach($results as $title => $questionGroup){
				foreach($questionGroup["questions"] as $question){
					$count++;
					$count +=$question['childQuestions'];
				}
			}
?>
<div class="panel panel-default" id="questions">
	<div class="panel-heading"><h3 class="panel-title"><?php echo $count?> vragen</h3></div>
	<div class="panel-body">
		<?php 
		echo "<ul>";
		foreach($results as $title => $questionGroup){
			echo "<li >";
			echo "<h3 class=\"groupTitle clickeable\" >$title</h3>";
			echo"<ol class=\"list_\">";
			foreach($questionGroup["questions"] as $question){
				$id  = $question["questionId"];
				?>
				<li >
				<div class="questionGroup" id="<?php echo "question$id"; ?>">
					<?php
					echo "<div>";
					if($selector["order"] == "thema_round" && $question["roundSequence"] > 0){
						echo "<span> Ronde " . $question["round"] . " </span> - ";
					}
					
					if($selector["order"] == "round_thema"  && $question["themaSequence"] > 0){
						echo "<span>" . $question["thema"] . " </span>  - ";
					}
					echo formatAuditInfo($question) ."<div class=\"editableTextArea description\" id=\"description_$id\" data-table='question'>" .$question['description'] ."</div></div>";
					echo formatAnswer($question, array("editable"=> true));
					echo "<div><div class=\"editableTextArea answerExtra\" id=\"answerExtra_$id\" data-table='question' data-table='question'>". $question['answerExtra'] ."</div></div>";

					if($question['childQuestions'] > 0){
					echo '<ul  class="childQuestions">';
					foreach($question["children"]  as $subQuestion){
						$id  = $subQuestion["questionId"];
						echo '<li ><div  id="question' . $id . '" class="questionGroup">';
						echo "<div>".formatAuditInfo($subQuestion) ."<div class=\"editableTextArea description\" id=\"description_$id\" data-table='question'>" .$subQuestion['description'] ."</div></div>";
						echo formatAnswer($subQuestion, array("editable"=> true));
						echo "<div><div class=\"editableTextArea answerExtra\" id=\"answerExtra_$id\" data-table='question'>". $subQuestion['answerExtra'] ."</div></div>";
						echo '</div></li>';
					}
					echo'</ul>';
				}
				?>

			</div>
			</li>
			<?php
			}
			echo"</ol></li>";
		}
		echo"</ul>";
		?>
		<script type="text/javascript">
		var getUpdateData  = function(self, new_value){
			var answerId = self.id.split('_')[1];
			var field =  self.id.split('_')[0];
			var updateInfo = { "detail[table]": 'question', "detail[id]": answerId };
			updateInfo["detail[param][" + field +"]"] = new_value;
			debug(updateInfo);
			return updateInfo;
		}

//			$(document).ready(function(){
//				$( ".editableTextArea" ).eip( "save_data.php", {
//					form_type: "textarea",
//					editfield_class: "textInput",
//					getUpdateData: getUpdateData
//				} );

//				$( ".editableText" ).eip( "save_data.php", {
//					form_type: "text",
//					editfield_class: "textInput",
//					getUpdateData: getUpdateData
//				} );

//				$( ".editableSelect" ).eip( "save_data.php", {
//					form_type: "select",
//					select_options: {
					<?php
//					  for($i = 0; $i < count($difficulties)-1; $i++) {
//							$diff = $difficulties[$i];
//							echo $diff["id"] . ": '". $diff["description"] . "',";
//						}
//						$diff = $difficulties[count($difficulties)-1];
//							echo $diff["id"] . ": '". $diff["description"] ."'";
//						?>
//					},
//					getUpdateData: getUpdateData,
//					after_save: function(self){
//						self.className =updateDifficulty(self.className, $(self).html());
//					}
//				} );
//			});


			var editableSelectOptions = {
					<?php
							  for($i = 0; $i < count($difficulties)-1; $i++) {
									$diff = $difficulties[$i];
									echo $diff["id"] . ": '". $diff["description"] . "',";
								}
								$diff = $difficulties[count($difficulties)-1];
									echo $diff["id"] . ": '". $diff["description"] ."'";
								?>
							};
			</script>
			<?php
		}
printNumberOfQueriesDone();
?>
</div>
</body>
</html>
