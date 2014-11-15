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
printHeader("thema's");


global $debug;
$debug = false;
$themas = selectThemas();
$quizId = getQuizId();
debug("quizId", $quizId);

echo "<body >";

printMenu(true);
?>
<div class="container-fluid" >
<div class="panel panel-default" >
	<div class="panel-heading"><h3 class="panel-title">Thema's</h3></div>
	<div class="panel-body medium-width">
		<?php
		if(count($themas) == 0){  
			echo "<strong>maak eerst een thema '-' of 'geen thema' aan</strong>";
		}
		echo '<ol id="themas" class="list-group" start="0">';
		$isfirst = true;
		foreach($themas as $thema){
			if(strlen(trim($thema["description"])) == 0 ){
				$thema["description"]= "&nbsp;&nbsp;&nbsp;";
			}
			if($isfirst == true){
				echo '<li id="thema_' . $thema["id"] .'" title="\'geen thema\'" class="list-group-item disabled"> <span class="editableText" id="description_' . $thema["id"] .'" >' . $thema["description"]. "</span></li>";
				$isfirst=false;
			}
			else{
				echo '<li id="thema_' . $thema["id"] .'" class="list-group-item" data-sequence=' . $thema["sequence"] . '>';
				echo '<span class="glyphicon glyphicon-trash" id="delete_' . $thema["id"] .'"></span>';
				echo '<span class="glyphicon glyphicon-arrow-up"></span><span class="glyphicon glyphicon-arrow-down"></span>&nbsp;&nbsp;&nbsp;';
				echo '<span><span class="editableText" id="description_' . $thema["id"] .'" data-table="thema">' . $thema["description"]. "</span></span>";
				echo "</li>";
			}
		
		}
		echo'</ol>';
		$thema = Array();
		$thema["id"]=-1;
		$thema["sequence"] = selectMaxSequenceForThema()+1;
		$formBuilder = new DetailFormBuilder($thema, "thema");
		?>
	</div>
</div>
<div class="panel panel-default" >
	<div class="panel-heading"><h3 class="panel-title">Thema toevoegen</h3></div>
	<div class="panel-body medium-width">
		<form id="detailForm" method="post" action="">
		<?php
		 $formBuilder->getHidden("id");
		 $formBuilder->getHidden("table");
		 $formBuilder->getHidden("param.sequence");
		 ?>
			<div class="row">
				<div class="col-md-4 text-center">
					<input type="submit" class="button" value="Bewaren" onclick="return saveFunction();"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<?php $formBuilder->getLabel("param.description", "Nieuw thema"); ?>
				</div>
				<div class="col-md-8">
					<?php
						$defaultThemas = Array("Actua", "Geschiedenis", "Aardrijkskunde", "Sport", "Muziek", "Media & TV", "Wetenschap", "Kunst & Cultuur", "Literatuur", "Eten & Drinken","Strips", "Fauna & Flora", "Globo", "3pts vraag");
							$formBuilder->getText("param.description", $defaultThemas);
					 ?>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
var getUpdateData  = function(self, new_value){
	return getInlineEditData(self, new_value, 'thema');
}


var saveFunction = function(auto) {

	var updateInfo = getFormData("#detailForm");

		$.post("save_data.php", updateInfo , function(data){
			var splitted =	data.split("id: ")
			debug("Data Loaded: " + data, "id:", splitted[splitted.length-1]);
			var description = $("#<?php echo $formBuilder->getId("param.description");?>")[0].value;
			$("#<?php echo $formBuilder->getId("param.description");?>")[0].value ="";
			var sequence = $("#<?php echo $formBuilder->getId("param.sequence");?>")[0].value;
			// debug($("#<?php echo $formBuilder->getId("param.sequence");?>")[0], seq, nextSeq);
			var nextSeq = 1+parseInt(sequence);
			$("#<?php echo $formBuilder->getId("param.sequence");?>")[0].value = nextSeq;
			var id =splitted[splitted.length-1];
			var now = new Date();

			var autoMessage = (auto == true?" automatisch ":"");
			showStatus("gegevens bewaard om " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds() );

			$("#themas").append(
							'<li id="thema_' + id +'" class="list-group-item" data-sequence=' + nextSeq +'>' +
							'<span class="glyphicon glyphicon-arrow-up"></span><span class="glyphicon glyphicon-arrow-down">&nbsp;&nbsp;&nbsp;' +
							'<span class="editableText" id="description_' + id +'" >' + description + "</span></li>");

		});

		return false;
    };

function deleteThema(event){
	var deleteButton = event.target;
	$(deleteButton).parent("li").addClass("active");
	
	var taskId=	deleteButton.id.split('_')[1];
	var confirmResult = confirm("Thema verwijderen?\n vragen worden niet mee verwijderd.");
	$(deleteButton).parent("li").removeClass("active");
	
	if( !confirmResult){
		return false;
	}
	var updateInfo = { "detail[table]": 'thema', "detail[id]": taskId, "detail[action]":"delete" };


	$.post("save_data.php", updateInfo , function(data){
			var splitted =	data.split("rows: ")
			rowId = "#row_" + taskId;
			$(rowId).hide();
			showStatus("Thema verwijderd");
			debug("parent", $(deleteButton), $(deleteButton).parents("li"));
			$(deleteButton).parents("li").remove();
		});
		return false;
    };


 $(function() {
//		$( ".editableText" ).eip( "save_data.php", {
//			form_type: "text",
//			form_buttons		: '<span><input type="button" id="save-#{id}" class="#{savebutton_class}" value="#{savebutton_text}" /> <input type="button" id="cancel-#{id}" class="#{cancelbutton_class}" value="#{cancelbutton_text}" /><button id="delete-#{id}"  value="verwijderen" onclick="deleteThema(this);" >Verwijderen</button></span>',
//			editfield_class: "textInput"
//		} );

	$(".glyphicon-trash").click(deleteThema);
	installUpdateSequence('#themas', "li", "thema");
});

</script>

</body>
</html>
