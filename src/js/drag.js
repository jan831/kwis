$(document).ready(function(){

	$(".dropable" ).droppable({ accept: ".drag",
		drop: function( event, ui ) {
			console.log(event, ui);
			$(event.target).append(ui.draggable);
			$(ui.draggable).css({
		        top: "0px",
		        left: "0px"
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
	$(".drag").draggable({
		// snap: "td",
		start: function(){
			$('.hasToolTip').tooltip('disable');
			$(".hasHtmlToolTip").tooltip('disable');
		},
		stop: function(){
			console.log("stop", arguments);
			$('.hasToolTip').tooltip('enable');
			$(".hasHtmlToolTip").tooltip('enable');
		},
		revert: function(){
			console.log("revert", arguments);
			return false;
		},
		
	});
});