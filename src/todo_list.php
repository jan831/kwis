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
printHeader("notities");
?>
<body>
<?php

	printMenu();
	$taskCategories = selectTaskCategory();
	$selector = getDefaultSelectorForTodo(@$_REQUEST["selector"], @$_REQUEST["reset"]);
	$themas = selectThemas();
 ?>
<div class="container-fluid" ">
<div class="panel panel-default" >
	<div class="panel-heading"><h3 class="panel-title">zoekparameters</h3></div>
	<div class="panel-body">
		<form method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" >
		<div class="row">
			<div class="col-md-11">
				<input type="submit" value="Zoeken"/>
				<input type="submit" value="Zoekparameters verwijderen" name="reset"/>
				<input type="hidden" name="selector[table]" value="task"/>
			</div>
			<div class="col-md-1" >
				<a href="<?php echo $_SERVER['REQUEST_URI'];?>" id="selectorLink" class="hasToolTip" title="link naar huidige zoek criteria, kan handig zijn om te mailen of als bookmark">link</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<label for="taskCategory">toon volgende notities</label>
			</div>
			<div class="col-md-3">
				<select name="selector[param][taskCategory][]" id="taskCategory" multiple>
				<?php foreach($taskCategories as $taskCategory){
					if(isset($selector['param']['taskCategory']))
						$selected = in_array($taskCategory['id'], $selector['param']['taskCategory'])?"selected":"";
					else
						$selected = '';
					echo '<option value = "' . $taskCategory['id'] . '" ' . $selected . '>' . $taskCategory['description'] . '</option>';
				} ?>
				</select>
			</div>
			<div class="col-md-3"> <label for="themaId">thema</label></div>
			<div class="col-md-3">
				<select name="selector[param][thema][]" id="themaId" multiple>
				<?php foreach($themas as $thema){
					if(isset($selector['param']['thema']))
						$selected = in_array($thema['themaId'], $selector['param']['thema'])?"selected":"";
					else
						$selected = "";
					echo '<option value = "' . $thema['themaId'] . '" '  . $selected . '>' . $thema['description'] . '</option>';
				} ?>
				</select>
			</div>
		</div>
		<div class="row">
			<?php $selected =  isset($selector['param']['notReady'])?"checked":""; ?>
			<div class="col-md-3"><label for="notReady">toon alleen notities die niet klaar zijn</label></div>
			<div class="col-md-3"><input type="checkbox" name="selector[param][notReady]" value="notReady" id="notReady" <?php echo $selected; ?>/></div>
			<div class="col-md-3">sorteer op</div>
			<div class="col-md-3">
				<?php $selected =  $selector['order']; ?>
					<select name="selector[order]">
					  <option value="taskCategory_done" <?php echo $selected=="taskCategory_done"?"selected":""; ?> >categorie</option>
					  <option value="done_taskCategory" <?php echo $selected=="done_taskCategory"?"selected":""; ?> >niet klaar, categorie</option>
						  <option value="thema_round" <?php echo $selected=="thema_round"?"selected":""; ?> >vraag (thema, ronde)</option>
						  <option value="round_thema" <?php echo $selected=="round_thema"?"selected":""; ?> >vraag (ronde, thema)</option>
					</select>
			</div>
		</div>
		</form>
	</div>
</div>
<div class="panel panel-default" >
	<div class="panel-body">
		<a href="question_list_todo.php">nieuwe notities toevoegen</a>
		<?php
			// echo "<pre>";
				$results = selectTasksForList($selector);
		
			// echo "</pre>";
		
				?>
		
		
				<?
				if(is_array($results)){
					$count = count($results);
		
		
		
					echo "<h2>" . $count . " notities</h2>";
		
					echo "<table class='table table-hover'>";
					foreach($results as $task){
						$readyButton = '';
						if($task['done'] == false){
							$readyButton = '<button id="task_' . $task['id'] . '" class="taskButton">klaar melden </button>';
						}
						echo "<tr><td>" . formatAuditInfo($task) . "</td>";
						echo "<td>" . $task['taskCategory'] . "</td><td>" . linkify($task['description']) . "</td>";
						echo "<td>" . formatAnswer( $task["question"],  array("editable" => 0, "link"=> 1, "hiddenQuestion" => 1, "tasks" => 1, "difficulty" => 1)) . "</td>";
		
						echo "<td>&nbsp;$readyButton</td></tr>";
		
		
					}
					echo"</table>";
			?>
		</div>
	</div>
			<script type="text/javascript">


				var updateTaskFunction = function(){
					var buttonId = this.id;
					var taskId=	buttonId.split('_')[1];
					var updateInfo = { "detail[table]": 'task', "detail[id]": taskId };
					updateInfo["detail[param][done]"] = 1;

					$.post("save_data.php", updateInfo , function(data){
							$("#" +  buttonId).hide();
					});
				};
			    $(".taskButton").click(updateTaskFunction);
			</script>
			<?php
		}

 printNumberOfQueriesDone(); ?>
</body>
</html>
