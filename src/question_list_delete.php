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
$selector = getDefaultSelectorForListDelete(@$_REQUEST["selector"], @$_REQUEST["reset"]);
$taskCategories = selectTaskCategory();

printHeader("vragen verwijderen");
echo "<body>";
printMenu(true);
echo '<div class="container-fluid">';
include ("php/question_selector.php");

$results = selectQuestionsForList($selector);

	if(is_array($results)){
		$count = 0;
		foreach($results as $title => $questionGroup){
			foreach($questionGroup["questions"] as $question){
				$count++;
				$count +=$question['childQuestions'];
			}
		} ?>
	<div class="panel panel-default" id="questions">
		<div class="panel-heading"><h3 class="panel-title"><?php echo $count?> vragen</h3></div>
		<div class="panel-body">
			<?php
			if($count > 0){
				?>
				<br/>
				&nbsp;<span id="statusBox" style="padding: 2px;"></span>&nbsp;
				<br/>&nbsp;<button class="deleteQuestions">verwijderen</button>
				<?php
			}
	
			echo "<ul>";
			foreach($results as $title => $questionGroup){
				echo "<li >";
				echo "<h3 class=\"groupTitle clickeable\" >$title</h3>";
				echo"<ul class=\"list_\">";
				foreach($questionGroup["questions"] as $question){
				?>
				<li>
				<div class="questionGroup">
					<?php
					$id  = $question["questionId"];
					echo '<input type="checkbox" name="' . $id . '" id="check_' . $id  . '" class="questionCheck" />';
					echo '<label for="check_' . $id  .'">' . formatAnswer( $question, array("editable" => false, "link"=> true, "hiddenQuestion" => true, "tasks" => true, "difficulty" => true) ) . "</label>";
	
	
	
					if($question['childQuestions'] > 0){
					echo '<ul  class="childQuestions">';
					foreach($question["children"]  as $subQuestion){
						$id  = $subQuestion["questionId"];
						echo '<li><div class="questionGroup">';
						echo '<input type="checkbox" name="' . $id . '" id="check_' . $id  . '" class="questionCheck" />';
						echo '<label for="check_' . $id  .'">' . formatAnswer( $subQuestion, array("editable" => false, "link"=> true, "hiddenQuestion" => true, "tasks" => true, "difficulty" => true) ) . "</label>";						  echo '</div></li>';
					}
					echo'</ul>';
				}
					?>
				</div>
				</li>
				<?php
				}
				echo"</ul></li>";
			} ?>
			</ul>
			<br/>&nbsp;<button class="deleteQuestions">verwijderen</button>
		</div>
	</div>
	<?php } ?>
<script type="text/javascript">


	var deleteQuestions = function() {
      // validate and process form here

			var updateInfo = { multiple: true, data: null};
			updateInfo.data = Array();

			var confirmResult = confirm("Vragen verwijderen?  alle deelvragen en notities worden mee verwijderd!");

		if( !confirmResult){
			return false;
		}
		
			var questionSelection = $(".questionCheck");
		var numSelected=0;
			for(var i=0; i< questionSelection.length; i++){
				if(questionSelection[i].checked){
					var questionId = questionSelection[i].id.split('_')[1];
				numSelected++;
				updateInfo["data[" +i +"][table]"] = 'question';
				updateInfo["data[" +i +"][action]"] = 'delete';
				updateInfo["data[" +i +"][id]"] = questionId;
			}
		}

		 $.post("save_data.php", updateInfo , function(data){
				alert(numSelected + " vragen verwijderd");
			window.location="./question_list_delete.php";
		});

		return false;
    };

    var selectAll = function(event){
		var checked = event.currentTarget.checked;
			var questionSelection = $(".questionCheck");
			for(var i=0; i< questionSelection.length; i++){
				questionSelection[i].checked = checked;
			}
	};

	var selectAll = function(event){
		var checked = event.currentTarget.checked;
			var questionSelection = $(".questionCheck");
			for(var i=0; i< questionSelection.length; i++){
				questionSelection[i].checked = checked;
			}
	};


    $(function() {
	    $(".deleteQuestions").click(deleteQuestions);
	    $("#checkAll").change(selectAll);
	 });
</script>
</body>
</html>
