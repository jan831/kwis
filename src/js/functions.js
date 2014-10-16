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
	if (typeof console == "undefined" || typeof console.log == "undefined"){
	//do nothing for IE	
	}
	else{	
	  console.log(arguments);
	}
}


function updateDifficulties(){
	var cells = $("#questionMatrix").find(".matrixCell");

	$("#questionMatrix").find(".difficultyCell").each(function(){
		var roundId =  this.id.split('_')[1];
		var diffs = $(this).find("span");
		for(var i=0; i<diffs.length; i++){
		    var text = "0";
		    var classList = diffs[i].className.split(/\s+/);
			for (var c = 0; c < classList.length; c++) {
				var diff = classList[c];
				if (diff != 'difficulty' && diff.substring(0, 10) == 'difficulty') {
					var elements = $(".round_"+roundId).find("." + diff);
					text = elements.length;
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

	$.post("save_data.php", updateInfo , function(data){
				showStatus("vraag aangepast");
				updateDifficulties();
	});
}
function updateLayout(){
	$("tr").each(function(index, row){
		var isHidden = $.cookie("matrixHide_" + row.id);

		if(isHidden == "true" ){
			$(row).hide();
		}
	});

	$(".roundHeader").each(function(index, col){
		var isHidden = $.cookie("matrixHide_" + col.id);
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
		console.log("showStatus", message);
		 // $("#statusBox").glow('#E0E0E0', 2000);
		 // $("#statusBox").effect('highlight', {color: '#E0E0E0'}, 2000);
		//TODO
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
	
	debug(data,		updateInfo, $(formId));
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
	
	
//	$(listSelector).sortable({ 
//		update : function () {
//			updateSequence(listSelector, elementSelector, tableName);
//	  }
//	}); 
//	$(listSelector).disableSelection();
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
	$.post("save_data.php", updateInfo , function(data){
		showStatus("volgorde aangepast");
		debug("Data Loaded: " + data);
	});
}

function closeEditable(evt){
	console.log("close", evt.currentTarget);
	
	$(evt.currentTarget).parent().hide();
	$("#" + $(evt.currentTarget).parent().attr("id").replace("editable_", "")).show();
	return false;
}

function saveEditable(evt){
	var origElement = $("#" + $(evt.currentTarget).parent().attr("id").replace( "editable_", ""))[0];
	
	var value = $(evt.currentTarget).parent().find(".input").val();
	var table = $(origElement).data("table");
	var id = origElement.id.split('_')[1];
	var field =  origElement.id.split('_')[0];
	var updateInfo = { "detail[table]": table, "detail[id]": id };
	updateInfo["detail[param][" + field +"]"] = value;
	
	if($(origElement).html() != value){
		debug(updateInfo);
		$.post("save_data.php", updateInfo , function(data){
			console.log(origElement, "difficulty"+$(origElement).html());
			if($(origElement).hasClass("difficulty")){
				$(origElement).removeClass("difficulty"+$(origElement).html());
				$(origElement).addClass("difficulty"+value);
				
			}
			$(origElement).html(value);
			
			closeEditable(evt);
		});
	} else {
		closeEditable(evt);
	}
	return false;
}

var editableButtonsHtml = '<a href="#" class="saveBtn" title="Bewaren"><span class="glyphicon glyphicon-ok" ></span></a><a href="#" class="cancelBtn" title="Annuleren"><span class="glyphicon glyphicon-remove" ></span></a>';

function clickEditableTextArea(){
	$(".editableTextArea").on('click', function(){
		var id = "editable_" + this.id;
		if( $("#"+id).length == 0){
			$(this).parent().append("<div id='" + id + "' class='editable'><textarea class='input' rows='4' >" +  $(this).html() + "</textarea>" + editableButtonsHtml + "</div>");
			$(this).parent().find(".saveBtn").on("click", saveEditable);
			$(this).parent().find(".cancelBtn").on("click", closeEditable);
		} else {
			$("#"+id).show();
		}
		$(this).hide();
	});
	$(".editableText").on('click', function(){
		var id = "editable_" + this.id;
		if( $("#"+id).length == 0){
			console.log($(this).html().length);
			$(this).parent().append("<span id='" + id + "' class='editable'><input class='input' type='text' value='" +  $(this).html() + "' size='20'/>" + editableButtonsHtml + "</span>");
			$(this).parent().find(".saveBtn").on("click", saveEditable);
			$(this).parent().find(".cancelBtn").on("click", closeEditable);
		} else {
			$("#"+id).show();
		}
		$(this).hide();
	});
	$(".editableSelect").on('click', function(){
		var id = "editable_" + this.id;
		if( $("#"+id).length == 0){
			var selected = $(this).html();
			
			var html = "<span id='" + id + "' class='editable'><select class='input' type='text' value='" +  $(this).html() + "'>";
			$.each(editableSelectOptions, function(key, value){
				var sel = selected == key?"selected":"";
				console.log(selected, key, sel);
				html += "<option value='"+key + "' " + sel + ">"+value+ "</option>";
			});
					
			html += "</select>" + editableButtonsHtml + "</span>"
					
			$(this).parent().append(html);
			$(this).parent().find(".saveBtn").on("click", saveEditable);
			$(this).parent().find(".cancelBtn").on("click", closeEditable);
		} else {
			$("#"+id).show();
		}
		$(this).hide();
	});
}

	
$(document).ready(function(){
	$('.hasToolTip').tooltip({
		container:  "body",
		html: true,
		placement: 'auto',
	});
	
	$(".hasHtmlToolTip").tooltip({
		container:  "body",
		html: true,
		placement: 'auto',
		title : function() { 
			if( $("#tooltip_" + this.id).length == 1)
				return $("#tooltip_" + this.id).html(); 
			else{
				debug("no tooltip html found for ", this);
				return null;
			}
		} 
	});


	$( "#toggleSelector").click(function(){
		// alert($("#selectorTable").is(":visible") );
		$.cookie( 'visible',  ($("#selectorTable").is(":visible") )?"false":"true");
		$("#selectorTable").toggle();
		// console.log("done", $("#selectorTable").is(":visible") );
	});
	
	$( ".groupTitle").click(function(){
		$("#list_" + this.id).toggle();
	});
	
	var isVisible = $.cookie( 'visible');	
	// console.log(isVisible, isVisible == "false" || isVisible == false);
	if(isVisible != null && (isVisible == "false" || isVisible == false)){
		$("#selectorTable").hide();
	}
	
	clickEditableTextArea();
});
