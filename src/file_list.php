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
printHeader("files");
echo "<body>";
printMenu(true);
echo '<div class="container-fluid">';
	global $debug;
// 	$debug = true;
	$rounds = selectRounds();
	$results = selectFilesForList();
	$difficulties = selectDifficulty();

		if(is_array($results)){
			?>
			<div class="panel panel-default" id="questions">
			<div class="panel-heading"><h3 class="panel-title"><?php echo sizeof($results); ?> bestanden</h3></div>
				<div class="panel-body">
					<?php 
			echo "<ul>";
			foreach($results as $title => $fileGroup){
				$groupId = $fileGroup["id"];
				echo "<li >";
				echo "<h3 class=\"groupTitle clickeable\" id=\"$groupId\">$title</h3>";
				echo"<ol id=\"list_$groupId\" class=\"list_\">";
				foreach($fileGroup["files"] as $file){
					$id  = $file["fileId"];
					?>
					<li >
					<div class="questionGroup" id="<?php echo "file$id"; ?>">
						<?php
						echo "<div>";
						echo formatAuditInfo($file) .  "<div class=\"editableText originalFilename\" id=\"originalFilename_$id\">" .$file['originalFilename'] ."</div>";
						echo "<div class=\"editableTextArea description\" id=\"description_$id\">" .$file['description'] ."</div></div>";
						echo "<a href=\"file_download.php?id=$id\" title='" . $file['originalFilename'] . "'>download</a> <a href=\"#\" onclick=\"deleteFile($id)\">verwijderen</a>";
					//	echo "<div><div class=\"editableTextArea answerExtra\" id=\"answerExtra_$id\">". $file['answerExtra'] ."</div></div>";

						
					?>

				</div>
				</li>
				<?php
				}
				echo"</ol></li>";
			}
			echo"</ul>";
			echo "</div>";
			echo "</div>";
		} ?>
		<div class="panel panel-default" >
			<div class="panel-heading"><h3 class="panel-title">File toevoegen</h3></div>
			<div class="panel-body medium-width">
			<form method="post" action="file_upload.php" enctype="multipart/form-data" id="uploadForm">
				<input type="hidden" id="form_delete" 	name="delete" value="">
				<input type="hidden" id="form_id"		name="id" value="">
				<div class="row">
					<div class="col-md-4 text-center"><input type="submit" value="File toevoegen" ></div>
				</div>
					<div class="row">
						<div class="col-md-4">Bestand</div>
						<div class="col-md-8"><input type="file" name="file" /></div>
					</div>
					<div class="row">
						<div class="col-md-4">Omschrijving</div>
						<div class="col-md-8"><textarea rows="3" cols="80" name="description"></textarea></div>
					</div>
					<div class="row">
						<div class="col-md-4">Ronde</div>
						<div class="col-md-8">
							<select name="roundId" id="roundId">
							<?php foreach($rounds as $round){
								echo '<option value = "' . $round['id'] . '" >' . $round['description'] . '</option>';
							} ?>
							</select>
						</div>
					</div>
				</table>
			</form>
			</div>
		</div>
		<script type="text/javascript">
			var getUpdateData  = function(self, new_value){
				var fileId = self.id.split('_')[1];
				var field =  self.id.split('_')[0];
				var updateInfo = { "detail[table]": 'file', "detail[id]": fileId };
				updateInfo["detail[param][" + field +"]"] = new_value;
				return updateInfo;
			}

			var deleteFile = function(fileId){
				var confirmResult = confirm("File verwijderen?");

				if( !confirmResult){
					return false;
				}

				$("#form_delete")[0].value = 1;
				$("#form_id")[0].value = fileId;
				$("#uploadForm")[0].submit();
			}

// 			$(document).ready(function(){
// 				$( ".editableTextArea" ).eip( "save_data.php", {
// 					form_type: "textarea",
// 					editfield_class: "textInput",
// 					getUpdateData: getUpdateData
// 				} );

// 				$( ".editableText" ).eip( "save_data.php", {
// 					form_type: "text",
// 					editfield_class: "textInput",
// 					getUpdateData: getUpdateData
// 				} );
// 			});


			</script>
		<?php 
printNumberOfQueriesDone();
?>

</body>
</html>
