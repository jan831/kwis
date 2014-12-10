function getHoverColor(cell, obj){
	var cellThemaId =  cell.id.split('_')[2];
	var objThemaId =  obj.id.split('_')[2];

	if(cellThemaId == -1){
		cellThemaId = cell.id.split('_')[1];
		objThemaId =  obj.id.split('_')[3];
	}
	
	console.log(cell.id, obj.id, cellThemaId, objThemaId);
	if(cellThemaId == null || objThemaId == cellThemaId)
		return "dragGood";
	else
		return "dragBad";
}

$(document).ready(function(){
	$(".drag").draggable({
		// snap: "td",
		start: function(){
			$('.hasToolTip').tooltip('disable');
			$(".hasHtmlToolTip").tooltip('disable');
		},
		stop: function(event, ui){
			$('.hasToolTip').tooltip('enable');
			$(".hasHtmlToolTip").tooltip('enable');
			$(ui.helper).css({
		        top: "0px",
		        left: "0px",
		    });
			$(".dragGood").removeClass("dragGood");
			$(".dragBad").removeClass( "dragBad");
		},
	});
	$(".dropable" ).droppable({ accept: ".drag",
		activeClass: "difficulty2",
		hoverClass: "difficulty2",
		over: function(event, ui){
			$(event.target).addClass(getHoverColor(event.target, ui.helper[0]));
		},
		out: function(event, ui){
			$(event.target).removeClass("dragGood").removeClass( "dragBad");
		},
		drop: function( event, ui ) {
			console.log("drop", event, ui);
			$(event.target).append(ui.draggable);
			$(ui.draggable).css({
		        top: "0px",
		        left: "0px",
		    });
			
			var answerNode = $(ui.draggable).find(".answer")[0];
			var cellNode = $(event.target).closest("td")[0];

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
			
		},
	});
});