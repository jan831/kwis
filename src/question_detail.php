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
printHeader("vraag detail");

global $debug;

$debug = false;
$question = selectQuestionById($_GET["id"]);
if(isset($_GET['parentId'])){
	$question["parentId"] = $_GET['parentId'];
}

$prevNext = getPrevAndNextQuestionId($question);


$themas = selectThemas();
$difficulty = selectDifficulty();
$taskCategories = selectTaskCategory();
$formBuilder = new DetailFormBuilder($question, "question");
$rounds = selectRoundsForQuestionSelector();
$history = selectQuestionHistory($question["id"]);

debug($formBuilder);
?>
<body >
<?php printMenu(false, $question["id"]); ?>
<div class="container-fluid" id="question_detail">
<div class="panel panel-default" id="question">
	<div class="panel-body">
		<form id="detailForm" method="post" action="">
			<div class="row">
				<div class="col-sm-1">
				<?php if (isset($prevNext[0])){
					echo '<a href="question_detail.php?id='. $prevNext[0] . '"><span class="glyphicon glyphicon-chevron-left" title="vorige"></span></a>';
				 } ?>&nbsp;
				</div>
				<div class="col-sm-10 text-center">
					<input type="submit" class="button" value="Bewaren" onclick="return saveFunction();"/>
					<input type="reset"  value="Beginwaarden" onclick="return resetId();"/>
					<input type="submit" id="deleteButton" value="Verwijderen" onclick="return deleteFunction();"/>
					
					<?php if($question["deleted"] == true) { ?>
						<input type="submit" id="undeleteButton" value="Undelete" onclick="return undeleteFunction();"/>
					<?php } else if ($question["id"] >0){ ?>
					<?php } ?>
						<input type="checkbox" checked="checked" id="autoSave"/><label for="autoSave" title="alle wijzigingen worden elke minuut automatisch bewaard">vraag automatisch bewaren</label>
					
					<?php $formBuilder->getHidden("id");
					 $formBuilder->getHidden("table");
					 $formBuilder->getHidden("param.isSpecial", "0");
					 $formBuilder->getHidden("param.deleted", "0");
					 if($question['parentId'] >0) {
					 $formBuilder->getHidden("param.parentId");
					 } ?>
				</div>
				<div class="col-sm-1">&nbsp;
				<?php if (isset($prevNext[1])){
					echo '<a href="question_detail.php?id='. $prevNext[1] . '"><span class="glyphicon glyphicon-chevron-right" title="volgende"></span></a>';
				} ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 text-center">
					&nbsp;
					<?php
					if($question["parentId"] >0){
						echo '<a href="question_detail.php?id=' . $question["parentId"] . '" >naar bovenliggende vraag</a>';
					}
					?>
					<br/>
					&nbsp;<span id="statusBox" style="padding: 2px;"></span>&nbsp;
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-sm-3">
					<?php $formBuilder->getLabel("param.roundId", "Ronde"); ?>
				</div><div class="col-md-2 col-sm-3">
					<?php $formBuilder->getSelect("param.roundId",$rounds); ?>
				</div><div class="col-md-2 col-sm-3">
					<?php $formBuilder->getLabel("param.themaId", "thema"); ?>
				</div><div class="col-md-2 col-sm-3">
					<?php $formBuilder->getSelect("param.themaId", $themas); ?>
				</div><div class="col-md-2 col-sm-3">
					<?php $formBuilder->getLabel("param.difficulty", "moeilijkheidsgraad"); ?>
				</div><div class="col-md-2 col-sm-3">
					<?php $formBuilder->getSelect("param.difficulty",$difficulty); ?>
				</div>
			</div>
			<?php if($question["isSpecial"]){ ?>
				<div class="row">
					<div class="col-md-2 col-sm-3">
						<?php $formBuilder->getLabel("param.sequence", "volgorde"); ?>
					</div><div class="col-md-2 col-sm-3">
					<?php $formBuilder->getText("param.sequence", 1); ?>
					</div>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-2">
					<?php $formBuilder->getLabel("param.description", "vraag"); ?>
				</div><div class="col-md-10">
					<?php $formBuilder->getTextArea("param.description", 10); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<?php $formBuilder->getLabel("param.answer", "antwoord"); ?>
				</div><div class="col-md-10">
				<?php $formBuilder->getTextArea("param.answer", 1); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<?php $formBuilder->getLabel("param.answerExtra", "extra uitleg"); ?>
				</div><div class="col-md-10">
					<?php $formBuilder->getTextArea("param.answerExtra", 2); ?>
				</div>
			</div>
			<div class="row">
				<?php if(isset($question["answer"]) && isNotBlank($question["answer"])){ ?>
					<div class="col-md-6 text-center">
						<?php
						echo '<a target="_new" href="http://nl.wikipedia.org/wiki/' . str_replace(' ', '_', $question['answer']) . '">Wikipedia artikel</a> &nbsp;&nbsp;';
						echo '<a target="_new" href="http://nl.wikipedia.org/wiki/Speciaal:Zoeken/' . $question['answer'] . '">Wikipedia zoeken</a>&nbsp;&nbsp;';
						echo '<a target="_new" href="http://www.google.be/search?q=' . str_replace(' ', '+', $question['answer']) . '">Google zoeken</a> &nbsp;&nbsp;';
						?>
					</div>
				<?php } ?>
				<?php if( $question["id"] >= 0) { ?> 
					<div class="col-md-3 text-center">
						Aangemaakt door <i><?php echo $formBuilder->getValue("param.creationUser"); ?></i>
						op <i><?php echo formatDate($formBuilder->getValue("param.creationDate")); ?></i>
					</div>
					<div class="col-md-3 text-center">
						Laatst gewijzigd door <?php echo $formBuilder->getValue("param.modificationUser"); ?></i>
						op <i><?php echo formatDate($formBuilder->getValue("param.modificationDate")); ?></i>
					</div>
				<?php } ?>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default" id="children">
	<div class="panel-heading"><h3 class="panel-title">Deelvragen</h3></div>
	<div class="panel-body">
		<?php
			if($question['questionId'] > 0){
				echo '<a href="question_detail.php?id=-1&parentId=' . $question["questionId"] .'">nieuwe deelvraag</a>';
			}
			if($question['childQuestions'] > 0){
				echo "<h3>deelvragen:</h3>";
	
				echo '<ul id="childQuestions" class="childQuestions">';
				foreach($question["children"]  as $subQuestion){
	
					echo '<li id="childQuestion_' . $subQuestion["questionId"] .'" class="moveable">' . formatAnswerForDetail($subQuestion) . '</li>';
				}
				echo'</ul>';
			}
		?>
	</div>
</div>
<div class="panel panel-default" id="notes">
	<div class="panel-heading"><h3 class="panel-title">Notities</h3></div>
	<div class="panel-body">
		<?php
		echo '<table class="table" id="tasks">';
		if($question['taskCount'] > 0){
			foreach($question['tasks'] as $task){
					$readyButton = '';
					if($task['done'] == false){
						$readyButton = '<button id="taskButton_' . $task['id'] . '" class="taskButton">klaar melden </button>';
					}
					$deleteButton = '<button id="deleteTask_' . $task['id'] . '" class="deleteTaskButton">verwijderen</button>';
					$rowId= "row_" . $task['id'];
					echo "<tr id=\"$rowId\">";
					echo "<td  class='col-md-2'>" . formatAuditInfo($task) . "</td>";
					if(isNotBlank($task['description'])){
						$taskDescr = linkify($task['description']);
					} else {
						$taskDescr = "&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					$editableClass = "";
					if(stripos($taskDescr, "a href") == false){
						$editableClass= "editableTextArea"; 
					}
					echo "<td class='col-md-2'>" . $task['taskCategory'] . "</td><td  class='col-md-5'><div class=\"$editableClass\" id=\"description_" .$task["id"] ."\" data-table='task'>" . $taskDescr . "</div></td>";
					echo "<td class='col-md-2'>&nbsp;$readyButton</td><td class='col-md-1'>$deleteButton</td></tr>";
			}
		}
		echo "</table>";
	
		$task = array();
		$task["questionId"] = $question['questionId'];
		$task["id"] = -1;
		$task["done"] = 1;
		$taskFormBuilder = new DetailFormBuilder($task, "task");
		debug("task form builder", $taskFormBuilder);
		?>
		<br/>
		<form id="newTaskForm"  >
			<div class="row">
				<div class="col-md-12 text-center">
					<input type="submit" class="newTaskButton" value="Bewaren"/>
					<input type="reset"  value="Beginwaarden"/>
					<?php
					$taskFormBuilder->getHidden("id");
					$taskFormBuilder->getHidden("table");
					$taskFormBuilder->getHidden("param.questionId");
					?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
				<?php $taskFormBuilder->getLabel("param.taskCategoryId", "categorie"); ?>
				</div><div class="col-md-3">
				<?php $taskFormBuilder->getSelect("param.taskCategoryId", $taskCategories); ?>
				</div><div class="col-md-3">
				<?php $taskFormBuilder->getLabel("param.done", "Actie nodig?"); ?>
				</div><div class="col-md-3">
				<?php
					$actions = Array();
					$actions[] = array("id"=>"0", "description"=> "Ja");
					$actions[] = array("id"=>"1", "description"=> "Neen");
		
					$taskFormBuilder->getSelect("param.done", $actions, array("title"=>"deze taak markeren dat er nog extra werk nodig is, die klaar gemeld kan worden"));
				?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php $taskFormBuilder->getTextArea("param.description", 2); ?>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default" id="images">
	<div class="panel-heading"><h3 class="panel-title">Afbeeldingen</h3></div>
	<div class="panel-body medium-width">
		<form method="post" action="upload.php" enctype="multipart/form-data" onsubmit="return checkImage(this)" >
			<input type="hidden" value="<?php echo $question['questionId'];?>"  name="questionId" id="imageQuestionId"/>
			<div class="row">
				<div class="col-md-6"><label for="image_file">bestand</label></div>
				<div class="col-md-6"><input type="file" name="image" id="image_file"/></div>
			</div>
			<div class="row">
				<div class="col-md-6"><label for="image_url" title="bijvoorbeeld http://www.goolge.com/logo.jpeg">web link</label></div>
				<div class="col-md-6"><input type="text" name="url"	id="image_url" /></div>
			</div>
			<div class="row">
				<div class="col-md-12"><input type="submit" value="Afbeelding toevoegen" ></div>
			</div>
			
		</form>
		<table class="table" id="images">
		<?php
			$images = coalesce($question['images'], Array());
			debug("images", $images);
			foreach($images as $img){
				echo '<tr><td id="image_' . $img["id"] .'">';
				echo formatImage($img, $question);
				echo "</td><td>";
				echo formatAuditInfo($img);
				echo "</td><td>";
				echo '<form method="post" action="upload.php" enctype="multipart/form-data">';
				echo '<input type="hidden" value="' . $img['questionId'] . '"  name="questionId" />';
				echo '<input type="hidden" value="' . $img['id'] . '"  name="id"/>';
				echo '<input type="hidden" value="100"  name="sequence"/>';
				echo '<input type="submit" value="Afbeelding verwijderen" />';
				echo '<input type="hidden" name="delete" value="1" />';
				echo '</form>';
				echo '</td></tr>';
			}
		?>
		</table>
	</div>
</div>
<div class="panel panel-default" id="diff">
	<div class="panel-heading"><h3 class="panel-title">Geschiedenis</h3></div>
	<div class="panel-body medium-width">
		<form name="questionHistory" action="question_diff.php" >
			<input type="hidden" name="mainId" value='<?php echo $question["id"]?>' />
				<?php 
				$history = coalesce($history, Array());
				debug("history", $history);
				$histLen = sizeof($history);
				if($histLen>0){
				?>
					<table class="table" id="versions">
					<tr><td colspan="6" valign="middle"><input type="submit" value="Vergelijk versies"/></td></tr>
					<?php 
					for($i=0; $i<$histLen; $i++){
						$histQuestion = $history[$i];
						$checkedLeft = "";
						if($i==0){
							$checkedLeft= ' checked="checked" ';
						}
						$checkedRight = "";
						if($i==1){
							$checkedRight= ' checked="checked" ';
						}
						
						echo '<tr>';
						echo '<td class="versionRadioButton"><input type="radio" name="left"  ' . $checkedLeft  . ' value="' . $histQuestion['id'] . "\" /></td>\n";
						echo '<td class="versionRadioButton"><input type="radio" name="right" ' . $checkedRight . ' value="' . $histQuestion['id'] . "\" /></td>\n";
						echo '<td>' . $histQuestion["version"] . " </td><td> " . $histQuestion["creationUser"] . "</td>";
						echo "<td>" . formatDate($histQuestion["creationDate"]) . "</td>\n";
						if( $i < $histLen-1){
							echo "<td>" . getChangedField($histQuestion, $history[$i+1]) . "</td>\n";
						} else {
							echo "<td>&nbsp;</td>\n";
						}
						echo "</tr>\n";
					}
					?>
					<tr><td colspan="6" valign="middle"><input type="submit" value="Vergelijk versies"/></td></tr>
				</table>
				<?php 
			}
			?>
		</form>
	</div>
</div>
<script type="text/javascript">
function resetId(){
	$("#<?php echo $formBuilder->getId("id");?>")[0].value ='<?php echo $question['questionId'];?>';
}


var updateIsSpecial = function(){
	var roundIds = new Array(-2, -3,
	<?php
	foreach($rounds as $round){
		if($round["isSpecial"]){
			echo $round["id"] .",";
		}
	}
	?>-1);
	var roundId =  $("#<?php echo $formBuilder->getId("param.roundId");?>")[0].value;
	$("#<?php echo $formBuilder->getId("param.isSpecial");?>")[0].value = "0";
	for(var i=0;i<roundIds.length;i++) {
		if(roundId == roundIds[i]){
			$("#<?php echo $formBuilder->getId("param.isSpecial");?>")[0].value = "1";
			return;
		}
	}
};

var oldUpdateInfo = getFormData("#detailForm");

var saveFunction = function(auto) {
	debug(auto, $("#autoSave")[0], $("#autoSave")[0].checked);
	updateIsSpecial();
	if(auto == true && $("#autoSave")[0].checked == false)
		return false;

	var formId = "#detailForm";

	updateInfo = getFormData(formId);

	if( !updateInfoChanged(updateInfo, oldUpdateInfo))
		return false;

	oldUpdateInfo = updateInfo;

	$.post("save_data.php", updateInfo , function(data){
		var splitted =	data.split("id: ");
		debug(data);

		var questionId = splitted[splitted.length-1];
		debug("Data Loaded: ", data, "id:", questionId);
		if(!isNaN(questionId)){
			$("#<?php echo $formBuilder->getId("id");?>")[0].value =  questionId;
			$("#<?php echo $taskFormBuilder->getId("param.questionId");?>")[0].value =  questionId;
			$("#imageQuestionId")[0].value =  questionId;
			var now = new Date();
			showStatus("gegevens bewaard om " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds() );
		}
		else{
			showStatus("probleem bij het bewaren van de vraag, probeer opnieuw te bewaren ");
		}

	});
	return false;
};

var undeleteFunction = function(){
	$("#<?php echo $formBuilder->getId("param.deleted");?>")[0].value = "0";
	var answerId = $("#<?php echo $formBuilder->getId("id");?>")[0].value;

	var updateInfo = { "detail[table]": 'question', "detail[id]": answerId, "detail[action]":"undelete" };
	$.post("save_data.php", updateInfo , function(data){
		debug(data);
	});
	$("#undeleteButton")[0].disabled = true;
	return false;
};

var deleteFunction = function() {
	// validate and process form here

	var answerId = $("#<?php echo $formBuilder->getId("id");?>")[0].value;
	var updateInfo = { "detail[table]": 'question', "detail[id]": answerId, "detail[action]":"delete" };
	var confirmResult = confirm("Vraag verwijderen?  alle deelvragen en notities worden mee verwijderd!");

	if( !confirmResult){
		return false;
	}

	$("#autoSave")[0].checked = false;

	$.post("save_data.php", updateInfo , function(data){
			// debug("Data Loaded: " + data, "id:", splitted[splitted.length-1]);
			$("#autoSave")[0].checked = false;
			alert("Vraag verwijderd");
			window.location="./list.php";
		});
		return false;
	};

var deleteTaskFunction = function() {

	// validate and process form here
	var buttonId = this.id;
	var taskId=	buttonId.split('_')[1];

	var updateInfo = { "detail[table]": 'task', "detail[id]": taskId, "detail[action]":"delete" };

	var confirmResult = confirm("Notitie verwijderen?");

	if( !confirmResult){
		return false;
	}

	$.post("save_data.php", updateInfo , function(data){
			rowId = "#row_" + taskId;
			$(rowId).hide();
			showStatus("Taak verwijderd");
		});
		return false;
	};

var updateTaskFunction = function(){
	var buttonId = this.id;
	var taskId=	buttonId.split('_')[1];
	var updateInfo = { "detail[table]": 'task', "detail[id]": taskId };
	updateInfo["detail[param][done]"] = 1;

	$.post("save_data.php", updateInfo , function(data){
			console.log("task updated, hiding " + buttonId);
			$("#" + buttonId).hide();
	});
};

var updateSavedTaskFunction = function(formId){
	var form =		$("#newTaskForm")[0];
	var doneBox =  $("#<?php echo $taskFormBuilder->getId("param.done");?>")[0];
	var done = doneBox.options[doneBox.selectedIndex].text;
	var description = $("#<?php echo $taskFormBuilder->getId("param.description");?>")[0].value;

	var catBox = $("#<?php echo $taskFormBuilder->getId("param.taskCategoryId");?>")[0];
	var cat = '';
	if(catBox.selectedIndex >=0){
		cat = catBox.options[catBox.selectedIndex].text;
	}
	form.reset();
	$('table#tasks').append('<tr><td>&nbsp;</td><td>' + cat +'</td><td>' + description + '</td><td>' + done + '</td><td>&nbsp;</td></tr>');
};

var saveTaskFunction = function() {
	var formId = "#newTaskForm";

	// validate and process form here
	if(	$("#<?php echo $taskFormBuilder->getId("param.questionId");?>")[0].value <= 0 ){
			alert("Taak kan niet bewaard worden, gelieve eerst de vraag te bewaren.");
		return false;
	}
	var data =$(formId).serializeArray();
	var updateInfo = {};

	$.each(data, function() {
		debug(this.name, this.value);
		updateInfo[this.name] = this.value;
	});

	debug(data,		updateInfo, $(formId));
	$.post("save_data.php", updateInfo , function(data){
		var splitted =	data.split("id: "),
			taskId = splitted[splitted.length-1];
		debug("Data Loaded: " + data, "id:", taskId);
		if(!isNaN(taskId)){
		// $("#<?php echo $taskFormBuilder->getId("id");?>")[0].value =  splitted[splitted.length-1];
		showStatus("notitie toegevoegd");
		updateSavedTaskFunction(formId);
		}
		else{
			showStatus("fout bij het bewaren van de taak, probeer opnieuw te bewaren");
		}

	});
	return false;
};


function checkImage(form){
	debug(form);
	if(form.questionId.value <=0 ){
		alert("Afbeelding kan niet bewaard worden, gelieve eerst de vraag te bewaren.");
		return false;
	}
	return true;
};

function checkFormDirty(e){
	var formId = "#detailForm";
	updateInfo = getFormData(formId);
	
	if( updateInfoChanged(updateInfo, oldUpdateInfo) &&  !(updateInfo["detail[id]"] != oldUpdateInfo["detail[id]"] && oldUpldateInfo["detail[id]"] == "")){
		if(navigator.userAgent.indexOf("Firefox") !=-1){
			debug("checkFormDirty firefox,return false");
			return false;
		}
		else {
			debug("checkFormDirty, return string");
			return "de vraag is niet bewaard, bent u zeker dat u deze pagina wil verlaten zonder de wijzigingen te bewaren?";
		} 
	}
	else{
		return;
	}
}

$(document).ready(function() {
	$(".taskButton").click(updateTaskFunction);
	$(".deleteTaskButton").click(deleteTaskFunction);
	$(".newTaskButton").click(saveTaskFunction);

	window.onbeforeunload = checkFormDirty;   

	// installUpdateSequence('#childQuestions', ".answer", "question");
	// installUpdateSequence('#images', "li", "image");

	//TODO
//		$( ".editableTextArea" ).eip( "save_data.php", {
//			form_type: "textarea",
//			editfield_class: "textInput",
//			getUpdateData: updateTaskDescription
//		} );
	
	debug("end loading doc");

});
setInterval("saveFunction(true)", 60000 * 2);
</script>
</div>
</body>
</html>
