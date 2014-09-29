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
 */ include_once("php/header.php");

$isInput = coalesce(@$_GET["input"], 0);
if($isInput)
	$selector = getDefaultSelectorForListAnalysisInput(@$_REQUEST["selector"], @$_REQUEST["reset"]);
else
	$selector = getDefaultSelectorForListAnalysis(@$_REQUEST["selector"], @$_REQUEST["reset"]);

$taskCategories = selectTaskCategory();
printHeader("Analyze"); ?>
<body>
<?php printMenu(true); ?>
	<div class="container-fluid">
	<?include ("php/question_selector.php"); ?>
	<div class="panel panel-default" id="questions">
		<div class="panel-heading"><h3 class="panel-title">Analyze</h3></div>
		<div class="panel-body">
			<?php 
			if($isInput){
				?>
				<br/>
						&nbsp;<span id="statusBox" style="padding: 2px;"></span>&nbsp;
						<br/>&nbsp;
				<button onclick="save();">Bewaren</button>
				<?php
			}
			else{
				echo "<a href=\"question_list_analysis.php?input=1\">punten ingeven</a>";
			}
			global $debug;
			$debug = false;
		
			$results = selectQuestions($selector,false, false, false);

			if(is_array($results)){
				echo "<table class=\"table-bordered table-hover\" style='width: 100%;'><thead><tr>".
						"<td class=\"header clickeable \"  title=\"klik om te verbergen\" >ronde</td>".
						"<td class=\"header clickeable \"  title=\"klik om te verbergen\" >thema</td>".
						"<td class=\"header clickeable \"  title=\"klik om te verbergen\" >antwoord</td>".
						"<td class=\"header clickeable \"   title=\"klik om te verbergen\" >moeilijkheidsgraad</td>".
						"<td class=\"header clickeable \"   title=\"klik om te verbergen\" >juiste antwoorden</td>";
						if( !$isInput){
							echo "<td ALIGN=\"right\">percentage</td>";
						}
				echo	"<td class=\"header clickeable \"  title=\"klik om te verbergen\" >extra analyze info</td>".
						"</tr></thead>";
				foreach($results as $question){
					if( rtrim($question["answer"]) <> ""){
						global $isInput;
						echo "<tr>";
						echo "<td>".$question["round"] . "</td>";
						echo "<td>".$question["thema"] . "</td>";
						echo "<td>".$question["answer"] . "</td>";
						echo "<td ALIGN=\"right\" ><span class=\"difficulty difficulty" . $question['difficulty'] . "\">". $question["difficulty"] . "</span></td>";
	
						if($isInput){
							echo "<td><input type=\"text\" class=\"correctAnswers\" id=\"correctAnswers_" . $question["id"] . "\" value=\"" . $question["correctAnswers"] . "\"></td>";
							echo "<td><input type=\"text\" class=\"analysisInfo\" id=\"analysisInfo_" . $question["id"] . "\" value=\"" . $question["analysisInfo"] . "\"></td>";
						}
						else{
							echo "<td ALIGN=\"right\">".coalesce($question["correctAnswers"], "0") . "</td>";
							echo "<td ALIGN=\"right\">".round($question["correctAnswers"] / $question["numberOfTeams"], 4)*100 . "</td>";
							echo "<td>". coalesce($question["analysisInfo"], "&nbsp;") . "</td>";
						}
						echo "</tr>";
					}
				}
				echo"</table>";
			}
			
			if($isInput){
				echo "<button onclick='save();'>Bewaren</button>";
				$formBuilder = new DetailFormBuilder(selectQuiz(), "quiz");
				?><br/><br />
				<div class="center-block medium-width">
					<form id=numberOfTeamsForm method="post" action="">
						<?php
							$formBuilder->getHidden("id");
							$formBuilder->getHidden("table");
						?>
						<div class="row ">
							<div class="col-md-4">
								<?php $formBuilder->getLabel("param.numberOfTeams", "Aantal ploegen"); ?>
							</div>
							<div class="col-md-8">
								<?php $formBuilder->getText("param.numberOfTeams"); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<input type="submit" class="button" value="Aantal ploegen bewaren" onclick="return saveTeams();"/>
							</div>
						</div>
					</form>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
var save = function(){
	var updateInfo = { multiple: true, data: null};
		updateInfo.data = Array();
	$(".correctAnswers").each(function(){
		var elementId = this.id.split('_')[1];
		debug(this, elementId);
		if(this.value != this.defaultValue){
			updateInfo["data[" +elementId +"][table]"] = 'question';
			updateInfo["data[" +elementId +"][id]"] = elementId;
			updateInfo["data[" + elementId +"][param][correctAnswers]"] = this.value;
		}
	});
	$(".analysisInfo").each(function(){
		var elementId = this.id.split('_')[1];
		debug(this, elementId);
		if(this.value != this.defaultValue){
			updateInfo["data[" +elementId +"][table]"] = 'question';
			updateInfo["data[" +elementId +"][id]"] = elementId;
			updateInfo["data[" + elementId +"][param][analysisInfo]"] = this.value;
		}
	});
	debug(updateInfo);
	$.post("save_data.php", updateInfo , function(data){
		showStatus("vragen aangepast");
		debug("Data Loaded: " + data);
	});
}

var saveTeams = function(){

	var formId = "#numberOfTeamsForm";

	updateInfo = getFormData(formId);

	$.post("save_data.php", updateInfo , function(data){
		var splitted =	data.split("id: ");
		debug(data);

		var questionId = splitted[splitted.length-1];
		debug("Data Loaded: ", data, "id:", questionId);
		showStatus("aantal ploegen aangepast" );

	});
	return false;
}

$(document).ready(function(){
	$(".header").click(function(){
		debug(this);
		$('#questionMatrix td:nth-child(' + (this.cellIndex +1) + ')').hide();
	});
	});
</script>
</body>
</html>
