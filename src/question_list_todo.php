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
$selector = getDefaultSelectorForListTodo(@$_REQUEST["selector"], @$_REQUEST["reset"]);
$taskCategories = selectTaskCategory();
printHeader("vragen lijst");
echo "<body>";
printMenu();
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
			}

			if($count > 0){
			?>
			<div class="panel panel-default" id="questions">
				<div class="panel-heading"><h3 class="panel-title">Notitie toevoegen</h3></div>
				<div class="panel-body">
					<form id="newTaskForm" class="medium-width">
						<div class="row">
							<div class="col-md-12">
								<input type="submit" class="newTaskButton" value="Bewaren"/>
								<input type="reset"  value="Beginwaarden"/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<select name="taskCategoryId" id="taskCategoryId">
								<?php foreach($taskCategories as $taskCategory){
									echo '<option value = "' . $taskCategory['id'] . '" >' . $taskCategory['description'] . '</option>';
								} ?>
								</select>
							</div>
							<div class="col-md-6">
								<select name="done" id="done" title="deze taak markeren dat er nog extra werk nodig is, die klaar gemeld kan worden">
								<option value="0" selected>actie nodig</option>
								<option value="1" >geen actie nodig</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<textarea name="description" rows="2" cols="80" class="textInput"></textarea>
							</div>
						</div>
					</form>
			<?php
			}

			echo "<h3>" . $count . " vragen</h3>";
			echo "<br/><input type=\"checkbox\"  id=\"checkAll\" /><label for=\"checkAll\"> alles (de)selecteren</label>";
			echo "<ul>";
			foreach($results as $title => $questionGroup){
				echo "<li >";
				echo "<h3 class=\"groupTitle clickeable\" >$title</h3>";
				echo"<ul  class=\"list_\">";
				foreach($questionGroup["questions"] as $question){
				?>
				<li>
				<div class="questionGroup">
						<?php
					$id  = $question["questionId"];
					echo '<input type="checkbox" name="' . $id . '" id="check_' . $id  . '" class="questionCheck" />';
					echo '<label for="check_' . $id  .'">' . formatAnswer( $question, array("editable" => 0, "link"=> true, "hiddenQuestion" => true, "tasks" => true, "difficulty" => true) ) . "</label>";



					if($question['childQuestions'] > 0){
					echo '<ul  class="childQuestions">';
					foreach($question["children"]  as $subQuestion){
						$id  = $subQuestion["questionId"];
						echo '<li><div class="questionGroup">';
						echo '<input type="checkbox" name="' . $id . '" id="check_' . $id  . '" class="questionCheck" />';
						echo '<label for="check_' . $id  .'">' . formatAnswer( $subQuestion, array("editable" => 0, "link"=> true, "hiddenQuestion" => true, "tasks" => true, "difficulty" => true) ) . "</label>";
												  echo '</div></li>';
					}
					echo'</ul>';
				}
					?>

				</div>
				</li>
				<?php
				}
				echo"</ul></li>";
			}
			echo"</ul>";
		}
?>
<script type="text/javascript">


	var saveTaskFunction = function() {
      // validate and process form here
		var data =$("#newTaskForm").serializeArray();

			var updateInfo = { multiple: true, data: null};
			updateInfo.data = Array();

			var questionSelection = $(".questionCheck");
		var numSelected=0;
			for(var i=0; i< questionSelection.length; i++){
				if(questionSelection[i].checked){
					var questionId = questionSelection[i].id.split('_')[1];
				numSelected++;
				updateInfo["data[" +i +"][table]"] = 'task';
				updateInfo["data[" +i +"][id]"] = -1;
				updateInfo["data[" +i +"][param][questionId]"] = questionId;
				$.each(data, function() {
					updateInfo["data[" +i +"][param]["+this.name +"]"] = this.value;
				});
			}
		}

		 $.post("save_data.php", updateInfo , function(data){
			 showStatus("toegevoegd voor " + numSelected + " vragen");
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


    $(function() {
	    $(".newTaskButton").click(saveTaskFunction);
	    $("#checkAll").change(selectAll);
	 });
</script>
</body>
</html>
