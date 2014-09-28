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
	$selector = getDefaultSelectorForTodo($_REQUEST["selector"], $_REQUEST["reset"]);
	$themas = selectThemas();
 ?>

<form method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" >
<div id="selectorContainer">
<div id="toggleSelector" class="clickeable"> zoekparameters</div>
<table id="selectorTable">
<tr>
<td>
	<input type="submit" value="Zoeken"/>
	<input type="submit" value="Zoekparameters verwijderen" name="reset"/>
	<input type="hidden" name="selector[table]" value="task"/>

</td>
</tr>
	<tr >
		<?php $selected =  isset($selector['param']['notReady'])?"checked":""; ?>
		<td width="25%"><label for="notReady">toon alleen notities die niet klaar zijn</label></td>
		<td width="25%"><input type="checkbox" name="selector[param][notReady]" value="notReady" id="notReady" <?php echo $selected; ?>/></td>
		<td width="25%"> &nbsp;</td>
		<td width="25%" align="right">
			<a href="<?php echo $_SERVER['REQUEST_URI'];?>" id="selectorLink" class="hasToolTip">link</a>
			<span id="tooltip_selectorLink" class="hidden">link naar huidige zoek criteria, kan handig zijn om te mailen of als bookmark</span>
		</td>
	</tr>
	<tr>
		<td>
			<label for="taskCategory">toon volgende notities</label>
		</td><td>
			<select name="selector[param][taskCategory][]" id="taskCategory" multiple>
			<?php foreach($taskCategories as $taskCategory){
				if(isset($selector['param']['taskCategory']))
					$selected = in_array($taskCategory['id'], $selector['param']['taskCategory'])?"selected":"";
				else
					$selected = '';
				echo '<option value = "' . $taskCategory['id'] . '" ' . $selected . '>' . $taskCategory['description'] . '</option>';
			} ?>
			</select>
		</td>
		<td> <label for="themaId">thema</label></td>
		<td>
			<select name="selector[param][thema][]" id="themaId" multiple>
			<?php foreach($themas as $thema){
				if(isset($selector['param']['thema']))
					$selected = in_array($thema['themaId'], $selector['param']['thema'])?"selected":"";
				else
					$selected = "";
				echo '<option value = "' . $thema['themaId'] . '" '  . $selected . '>' . $thema['description'] . '</option>';
			} ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>sorteer op</td>
		<td>
			<?php $selected =  $selector['order']; ?>
				<select name="selector[order]">
				  <option value="taskCategory_done" <?php echo $selected=="taskCategory_done"?"selected":""; ?> >categorie</option>
				  <option value="done_taskCategory" <?php echo $selected=="done_taskCategory"?"selected":""; ?> >niet klaar, categorie</option>
  				  <option value="thema_round" <?php echo $selected=="thema_round"?"selected":""; ?> >vraag (thema, ronde)</option>
  				  <option value="round_thema" <?php echo $selected=="round_thema"?"selected":""; ?> >vraag (ronde, thema)</option>
				</select>
		</td>
	</tr>
</table>
</div>
</form>
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

			echo "<table>";
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
