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
function debug(){
	return;
	if (typeof console == "undefined" || typeof console.log == "undefined"){
	//do nothing for IE	
	}
	else{	
	  console.log(arguments);
	}
}


function updateDifficulties(){
	var cells = $("#questionMatrix").find(".matrixCell");

	var difficulties = {};
	cells.each(function(){
		children = $(this).find(".answer");
		var roundId =  this.id.split('_')[1];
		for(var i=0; i<children.length; i++){
			var classList = children[i].className.split(/\s+/);
			for (var c = 0; c < classList.length; c++) {
				var diff = classList[c];
				if (diff != 'difficulty' && diff.substring(0, 10) == 'difficulty') {
				    if(difficulties[roundId] == null)
		                        difficulties[roundId] = {};
				    if(difficulties[roundId][diff] == null)
				        difficulties[roundId][diff] = 0;

				    difficulties[roundId][diff]++;
				}
			}

		}
	});
	debug("difficulties", difficulties);
	$("#questionMatrix").find(".difficultyCell").each(function(){
		var roundId =  this.id.split('_')[1];
		var diffs = $(this).find("span");
		for(var i=0; i<diffs.length; i++){
		    var text = "0";
		    var classList = diffs[i].className.split(/\s+/);
			for (var c = 0; c < classList.length; c++) {
				var diff = classList[c];
				if (diff != 'difficulty' && diff.substring(0, 10) == 'difficulty') {
				    if(difficulties[roundId] != null && difficulties[roundId][diff] != null){
				        text = difficulties[roundId][diff];
				    }
				    else{
				    }
				}
			}
		    $(diffs[i]).html(text + "&nbsp;");
		}
	});
}

function myhandler_dropped(node, cellNode){
	var answerNode = $(node).find(".answer")[0];
//	var cellNode = $(answerNode).parent("td")[0];

	var answerId = answerNode.id.split('_')[1];
	var roundId =  cellNode.id.split('_')[1];
	var themaId =  cellNode.id.split('_')[2];
	var sequence =  cellNode.id.split('_')[3];
	var updateInfo;
	if(sequence != null){
		updateInfo = { "detail[table]": 'question', "detail[id]": answerId , "detail[updateChild]": 0, "detail[param][isSpecial]": 1, "detail[param][roundId]": roundId, "detail[param][sequence]": sequence-1  };
	}
	else if(themaId == null)	{
		updateInfo = { "detail[table]": 'question', "detail[id]": answerId , "detail[updateChild]": 1, "detail[param][isSpecial]": 0, "detail[param][roundId]": roundId };
	}
	else{
		updateInfo = { "detail[table]": 'question', "detail[id]": answerId , "detail[updateChild]": 1, "detail[param][isSpecial]": 0, "detail[param][roundId]": roundId, "detail[param][themaId]": themaId };
	}
	debug(answerNode, cellNode,  updateInfo );

	$.post("save.php", updateInfo , function(data){
 			showStatus("vraag aangepast");
		 	updateDifficulties();
	});
}
function updateLayout(){
	$("tr").each(function(index, row){
		var isHidden = $.cookie("matrixHide_" + row.id);
		debug("updateLayout","matrixHide_" + row.id, row, (isHidden == true), (isHidden == "true"));

		if(isHidden == "true" ){
			$(row).hide();
		}
	});

	$(".roundHeader").each(function(index, col){
		var isHidden = $.cookie("matrixHide_" + col.id);
		debug("updateLayout", isHidden);
		if(isHidden == "true"){
			$('#questionMatrix td:nth-child(' + (col.cellIndex +1) + ')').hide();
		}
	});

	if(navigator.userAgent.indexOf("MSIE") != -1){
		$('img[alt=detail]').hide();
	}
}

function resetLayout(){	
	$("tr").each(function(index, row){
		$.cookie("matrixHide_" + row.id, false);
		
		$(row).show();
	});
	var i=0;
	var value = $.cookie("matrixHide_row"+i);
	while(value !=null){
		if(value == true){
			$.cookie("matrixHide_row" + i, false);
		}
		i++;
		value = $.cookie("matrixHide_row"+i);
	}

	$(".roundHeader").each(function(index, col){
		$.cookie("matrixHide_" + col.id, false);
		debug("resetLayout", col, col.cellIndex );
		$('#questionMatrix td:nth-child(' + (col.cellIndex +1) + ')').show();
	});
}

function showStatus(message){
		$("#statusBox").text(message);
		 // $("#statusBox").glow('#E0E0E0', 2000);
		 $("#statusBox").effect('highlight', {color: '#E0E0E0'}, 2000);
	}
	
function updateDifficulty(className, newDifficulty){
  var re = new RegExp("difficulty[0-9]", "g");
  return className.replace(re, "difficulty"+newDifficulty); 
}

function getFormData(formId){
    // validate and process form here
	var data =$(formId).serializeArray();
	var updateInfo = {};		

	$.each(data, function() {
		debug(this.name, this.value);
		updateInfo[this.name] = this.value;
	});
	
	debug(data, 	updateInfo, $(formId));
	return updateInfo;
}

function updateInfoChanged(updateInfo, oldUpdateInfo){
	for (var key in updateInfo){
		debug("updateInfoChanged", key, updateInfo[key], oldUpdateInfo[key]);
		if(updateInfo[key] != oldUpdateInfo[key]){
			debug("updateInfoChanged changed", key);
			return true;
		}
	}
	debug("updateInfoChanged no change");
	return false;
}

function getInlineEditData(element, new_value, tableName){
	var themaId = element.id.split('_')[1];
	var field =  element.id.split('_')[0];
	var updateInfo = { "detail[table]": tableName, "detail[id]": themaId };
	updateInfo["detail[param][" + field +"]"] = new_value;
	return updateInfo;
}

function installUpdateSequence(listSelector, elementSelector, tableName){
	$(listSelector).sortable({ 
		update : function () {
			updateSequence(listSelector, elementSelector, tableName);
	  }
	}); 
	$(listSelector).disableSelection();
};
	
function updateSequence(listSelector, elementSelector, tableName){
	var order = $(listSelector).find(elementSelector);	
		  
	var updateInfo = { multiple: true, data: null};
	updateInfo.data = Array();

	for(var i=0; i< order.length; i++){
		var elementId = order[i].id.split('_')[1];
		updateInfo["data[" +i +"][table]"] = tableName;
		updateInfo["data[" +i +"][id]"] = elementId;
		updateInfo["data[" +i +"][param][sequence]"] = i ;
	}

	debug(order, updateInfo);
	$.post("save.php", updateInfo , function(data){
		showStatus("volgorde aangepast");
		debug("Data Loaded: " + data);
	});
}

function scrollToContent(){
	debug("hash |" + window.location.hash +"|" );
	if(window.location.hash == "") {
		$('html,body').animate({
			scrollTop : $(".menuContainer").next().offset().top - 5
		}, 0);
	}
}
	
$(document).ready(function(){
	$(".hasToolTip").tooltip({
		items:  ".hasToolTip",
		content : function() { 
			if( $("#tooltip_" + this.id).length == 1)
				return $("#tooltip_" + this.id).html(); 
			else
		  		return null;
		} 
	});


	$( "#toggleSelector").click(function(){
		// alert($("#selectorTable").is(":visible") );
		$.cookie( 'visible',  ($("#selectorTable").is(":visible") )?"false":"true");
		$("#selectorTable").toggle();
		// console.log("done", $("#selectorTable").is(":visible") );
	});
	
	$( "#showAll").click(function(){
		$(".list_").show();
	});
	$( "#hideAll").click(function(){
		$(".list_").hide();
	});			
	$( ".groupTitle").click(function(){
		$("#list_" + this.id).toggle();
	});
	
	var isVisible = $.cookie( 'visible');	
	// console.log(isVisible, isVisible == "false" || isVisible == false);
	if(isVisible != null && (isVisible == "false" || isVisible == false)){
		$("#selectorTable").hide();
	}
	scrollToContent();
});
