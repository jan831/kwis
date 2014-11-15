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
printHeader("Rondes");


global $debug;
$debug = false;
$rounds = selectRounds();
$quizId = getQuizId();
debug("quizId", $quizId);
debug("rounds", $rounds);

echo "<body >";
printMenu(true); 
?>
<div class="container-fluid" >
<div class="panel panel-default" >
	<div class="panel-heading"><h3 class="panel-title">Rondes</h3></div>
	<div class="panel-body medium-width">
		<?php
		if(count($rounds) == 0){  
			echo "<strong>maak eerst een ronde '-' of 'geen ronde' aan</strong>";
		}
					echo '<ul id="rounds" class="list-group" start="0">';
					$isfirst = true;
					foreach($rounds as $round){
						if(strlen(trim($round["description"])) == 0 ){
							$round["description"]= "&nbsp;&nbsp;&nbsp;";
						}
						if($isfirst == true){
							echo '<li id="round_' . $round["id"] .'" title="\'geen ronde\'" class="list-group-item disabled"> <span class="editableText" id="description_' . $round["id"] .'" >' . $round["description"]. "</span></li>";
							$isfirst=false;
						}
						else{
							echo '<li id="round_' . $round["id"] .'" class="list-group-item" data-sequence="' . $round["sequence"] .'" >';
							echo '<span class="glyphicon glyphicon-trash" id="delete_' . $round["id"] .'"></span>';
							echo '<span class="glyphicon glyphicon-arrow-up"></span><span class="glyphicon glyphicon-arrow-down"></span>&nbsp;&nbsp;&nbsp;';
							echo '<span><span class="editableText" id="description_' . $round["id"] .'" data-table="round" >' . $round["description"]. "</span></Span>\n";
							echo '<input type="checkbox" title="speciale ronde?" id="isSpecial_' . $round["id"] .'" ' . ($round["isSpecial"]?' checked="yes"':"") . ' onchange="changeIsSpecial(this)"/>';
							echo '<span class="glyphicon glyphicon-delete"></span>';
							echo "</li>\n";
						}
		
					}
					echo'</ul>';
		
		$round = Array();
		$round["id"]=-1;
		$round["sequence"] = selectMaxSequenceForRound()+1;
		$formBuilder = new DetailFormBuilder($round, "round");
		?>
	</div>
</div>
<div class="panel panel-default" >
	<div class="panel-heading"><h3 class="panel-title">Ronde toevoegen</h3></div>
	<div class="panel-body medium-width">
		<form id="detailForm" method="post" action="" >
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
					<?php $formBuilder->getLabel("param.description", "Nieuwe ronde"); ?>
				</div><div class="col-md-8">
					<?php $formBuilder->getText("param.description"); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<?php $formBuilder->getLabel("param.isSpecial", "Speciale ronde?", Array("class"=> "hasToolTip", "id" => "isSpecialLabel")); ?>
				</div><div class="col-md-8">
					<?php $formBuilder->getCheckBox("param.isSpecial"); ?>
					<div>Gewone rondes worden gesorteerd getoond per thema.   Speciale rondes kunnen handmatig gesorteerd worden in het overzicht, onafhankelijk van de thema's.</div>
				</div>
			</div>
		</table>
		</form>
	</div>
</div>
<script type="text/javascript">
var getUpdateData  = function(self, new_value){
	return getInlineEditData(self, new_value, 'round');
}

var saveFunction = function(auto) {
	var updateInfo = getFormData("#detailForm");


		$.post("save_data.php", updateInfo , function(data){
			var splitted =	data.split("id: ")
			debug("Data Loaded: " + data, "id:", splitted[splitted.length-1]);
			var description = $("#<?php echo $formBuilder->getId("param.description");?>")[0].value;
			$("#<?php echo $formBuilder->getId("param.description");?>")[0].value ="";
			var seq = $("#<?php echo $formBuilder->getId("param.sequence");?>")[0].value;
			var nextSeq = 1+parseInt(seq);
			$("#<?php echo $formBuilder->getId("param.sequence");?>")[0].value = nextSeq;
			var id =splitted[splitted.length-1];
			var now = new Date();

			var autoMessage = (auto == true?" automatisch ":"");
			showStatus("gegevens bewaard om " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds() );

			$("#rounds").append( 
					'<li id="round_' + id +'" class="list-group-item">' +
					'<span class="glyphicon glyphicon-arrow-up"></span><span class="glyphicon glyphicon-arrow-down">&nbsp;&nbsp;&nbsp;' +
					'<span class="editableText" id="description_' + id +'" >' + description + "</span>" +
					// '<input type="checkbox" title="speciale ronde?" id="isSpecial_' + id +'" ' . ($round["isSpecial"]?' checked="yes"':"") . ' onchange="changeIsSpecial(this)"/>' +
					'</li>');

		});

		return false;
    };

function deleteRound(event){
	var deleteButton = event.target;
	$(deleteButton).parent("li").addClass("active");
	var taskId=	deleteButton.id.split('_')[1];
	var confirmResult = confirm("Ronde verwijderen? \n vragen worden niet mee verwijderd.");
	$(deleteButton).parent("li").removeClass("active");
	if( !confirmResult){
		return false;
	}
	var updateInfo = { "detail[table]": 'round', "detail[id]": taskId, "detail[action]":"delete" };

	$.post("save_data.php", updateInfo , function(data){
			var splitted =	data.split("rows: ")
			rowId = "#row_" + taskId;
			$(rowId).hide();
			showStatus("Ronde verwijderd");
			debug("parent", $(deleteButton), $(deleteButton).parents("li"));
			$(deleteButton).parents("li").remove();
		});
		return false;
    };

function changeIsSpecial(checkBox){
	debug("changeIsSpecial", checkBox);
	var taskId=	checkBox.id.split('_')[1];
	var updateInfo = { "detail[table]": 'round', "detail[id]": taskId, "detail[param][isSpecial]":(checkBox.checked?"1":"0") };

	$.post("save_data.php", updateInfo , function(data){
		showStatus("ronde aangepast");
	});
}

 $(function() {
//		$( ".editableText" ).eip( "save_data.php", {
//			form_type: "text",
//			form_buttons		: '<span><input type="button" id="save-#{id}" class="#{savebutton_class}" value="#{savebutton_text}" /> <input type="button" id="cancel-#{id}" class="#{cancelbutton_class}" value="#{cancelbutton_text}" /><button id="delete-#{id}"  value="verwijderen" onclick="deleteRound(this);" >Verwijderen</button></span>',
//			editfield_class: "textInput"
//		} );
	$(".glyphicon-trash").click(deleteRound);
	installUpdateSequence('#rounds', "li", "round");
});

</script>

</body>
</html>
