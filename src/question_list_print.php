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
$selector = getDefaultSelectorForListPrint(@$_REQUEST["selector"], @$_REQUEST["reset"]);
$rounds = selectRoundsForQuestionSelector();
$taskCategories = selectTaskCategory();
printHeader("vragen lijst"); ?>
<body>
<?php printMenu(); ?>
<div class="container-fluid" ">
<div class="panel panel-default selector" >
	<div class="panel-heading"><h3 class="panel-title">zoekparameters </h3></div>
	<div class="panel-body">
		<form method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" >
			<?php $formBuilder = new SelectorFormBuilder($selector, "selector"); ?>
			<div class="row">
				<div class="col-md-11 text-center">
					<input type="submit" value="Zoeken"/>
					<input type="submit" value="Zoekparameters verwijderen" name="reset"/>
					<input type="hidden" name="selector[table]" value="question"/>
				</div>
				<div class="col-md-1" >
					<a href="<?php echo $_SERVER['REQUEST_URI'];?>" id="selectorLink" class="hasToolTip" title="link naar huidige zoek criteria, kan handig zijn om te mailen of als bookmark">link</a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("param.hasRound", "toon alleen toegewezen aan rondes"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getCheckBox("param.hasRound"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getLabel("order", "sorteer op"); ?></div>
				<div class="col-md-3">
					<?php $order = array();
						$order[] = array("id"=>"thema_round", "description"=> "thema, ronde");
						$order[] = array("id"=>"round_thema", "description"=> "ronde, thema");
						$formBuilder->getSelect("order",$order, array("multiple" => false)); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">toon volgende velden</div>
				<div class="col-md-3">
				<?php
					$formBuilder->getCheckBox("ui.showThema");
					$formBuilder->getLabel("ui.showThema", "thema/ronde per vraag");
					echo "<br/>";
		
					$formBuilder->getCheckBox("ui.showDifficulty");
					$formBuilder->getLabel("ui.showDifficulty", "moeilijkheidsgraad");
					echo "<br/>";
		
					$formBuilder->getCheckBox("ui.showQuestion");
					$formBuilder->getLabel("ui.showQuestion", "vragen");
					echo "<br/>";
		
					$formBuilder->getCheckBox("ui.showAnswer");
					$formBuilder->getLabel("ui.showAnswer", "antwoorden");
					echo "<br/>";
				?>
				</div>
				<div class="col-md-3"><?php $formBuilder->getLabel("param.round","Ronde"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getSelect("param.round",$rounds, array("multiple" => true)); ?></div>
			</div>
			<div class="row">
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-3">
				<?php
					$formBuilder->getCheckBox("ui.showHiddenAnswer");
					$formBuilder->getLabel("ui.showHiddenAnswer", "\"verberg\" antwoorden");
					echo "<br/>";
		
					$formBuilder->getCheckBox("ui.showExtra");
					$formBuilder->getLabel("ui.showExtra", "extra uitleg");
					echo "<br/>";
					$formBuilder->getCheckBox("ui.showImage");
					$formBuilder->getLabel("ui.showImage", "afbeelding");
					echo "<br/>";
					?>
				</div>
				<div class="col-md-3">
					<?php $formBuilder->getLabel("param.taskCategory", "toon vragen <i>met</i> de volgende notities"); ?>
				</div>
				<div class="col-md-3">
					<?php $formBuilder->getSelect("param.taskCategory",$taskCategories, array("multiple" => true)); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("ui.comment","Toon volgende notitie(s)"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getSelect("ui.comment",$taskCategories, array("multiple" => true)); ?></div>
				<div class="col-md-3"><?php $formBuilder->getLabel("param.taskCategoryNot","toon vragen <i>zonder</i> de volgende notities"); ?></div>
				<div class="col-md-3">
					<?php $formBuilder->getSelect("param.taskCategoryNot",$taskCategories, array("multiple" => true)); ?>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-body">
		<?php
		global $debug;
		$debug = false;
		debug("selector", $selector	);

		$results = selectQuestionsForList($selector);

		$pageBreak = 2; // ever 2 rounds pagebreak
		if(@$selector["ui"]["showQuestion"])
			$pageBreak = 1; // every round if questions are printed
		?>


		<?
		if(is_array($results)){
			$count = 0;

			foreach($results as $title => $questionGroup){
				foreach($questionGroup["questions"] as $question){
					$count++;
					$count +=$question['childQuestions'];
				}
			}
			$count = 0;
			foreach($results as $title => $questionGroup){
				$pageBreakClass = "";
				if($count >0 && $count%$pageBreak == 0){
					$pageBreakClass= "pageBreaker";
					echo "<!--nextpage-->\n";
				}

				echo "<div class=\"$pageBreakClass groupLi\" >";
				echo "<h2 class=\"groupTitle clickeable\" >$title</h2>";
				echo"<ol  class=\"list_\">";
				foreach($questionGroup["questions"] as $question){
				?>
				<li class="questionLi">
				<div class="questionGroup">
					<?php
					$id  = $question["questionId"];

					if(@$selector["ui"]["showDifficulty"]){
						echo "<span class=\"editableSelect difficulty difficulty" . $question['difficulty'] ."\">". $question['difficulty'] ." - </span>";
					}
					if(@$selector["ui"]["showThema"]){
						if($selector["order"] == "thema_round"){
							echo "<span> Ronde " . $question["round"] . ". </span>";
						}

						if($selector["order"] == "round_thema"){
							echo "<span>" . $question["thema"] . ": </span>";
						}
					}
					if(@$selector["ui"]["showQuestion"]){
						echo "<div class=\"description\" id=\"description_$id\">" .str_replace("\n"," ",nl2br($question['description'])) ."</div>";
					}

					if(@$selector["ui"]["showImage"]){
						if($question['imageCount'] >0){
							foreach($question['images'] as $img){
								echo formatImage($img, $question);
							}
						}
					}

					if(@$selector["ui"]["showAnswer"]){
						$hiddenAnswer = "";
							if(isset($selector["ui"]["showHiddenAnswer"]))
								$hiddenAnswer = "hiddenAnswer";
						$title = $question["answer"];
						if($title != '' ){
							echo "<div class=\"$hiddenAnswer\" title=\"$title\">";
							echo formatAnswer($question, array("link"=> 0, "tasks" => 0, "difficulty" => 0));
							if(@$selector["ui"]["showExtra"]  && isNotBlank($question['answerExtra'])){
								echo "<div class=\"answerExtra\" id=\"answerExtra_$id\">". str_replace("\n"," ", nl2br($question['answerExtra'])) ."</div>";
							}
							if(is_array(@$selector["ui"]["comment"]) && $question['taskCount'] > 0){
								$hasComment = false;
								foreach($question['tasks'] as $task){
									if( in_array($task["taskCategoryId"], @$selector["ui"]["comment"])){
										if( !$hasComment){
											echo "<div class=\"commentGroup\">\n";
											$hasComment = true;
										}
										echo "<div>" . linkify($task['description']) . "</div>\n";
									}
								}
							
								if($hasComment){
									echo "</div>\n";
								}
							}
							
							echo "</div>";
						}
					}

					if($question['childQuestions'] > 0){
					echo '<ul  class="childQuestions">';
					foreach($question["children"]  as $subQuestion){
						echo '<li><div class="questionGroup">';
						$id  = $subQuestion["questionId"];
						if(@$selector["ui"]["showDifficulty"]){
							echo "<span class=\"editableSelect difficulty difficulty" . $subQuestion['difficulty'] ."\">". $subQuestion['difficulty'] ." - </span>";
						}
						if(@$selector["ui"]["showQuestion"]){
							echo "<div class=\"description\" id=\"description_$id\">" . str_replace("\r"," ",nl2br($subQuestion['description'])) ."</div>";
						}
						if(@$selector["ui"]["showImage"]){
							if($subQuestion['imageCount'] >0){
								foreach($subQuestion['images'] as $img){
									echo formatImage($img, $subQuestion);
								}
							}
						}

						if(@$selector["ui"]["showAnswer"] ){
							$hiddenAnswer = "";
							if(isset($selector["ui"]["showHiddenAnswer"]))
								$hiddenAnswer = "hiddenAnswer";
							$title = $subQuestion["answer"];
							echo "<div class=\"$hiddenAnswer\" title=\"$title\">";
							echo formatAnswer($subQuestion, array("link"=> 0, "tasks" => 0, "difficulty" => 0, "hiddenAnswer" => 0 ));

							if(@$selector["ui"]["showExtra"] && isNotBlank($subQuestion['answerExtra'])){
								echo "<div class=\"answerExtra\" id=\"answerExtra_$id\">". str_replace("\n"," ", nl2br($subQuestion['answerExtra'])) ."</div>";
							}
							if(is_array(@$selector["ui"]["comment"]) && $subQuestion['taskCount'] > 0){
								$hasComment = false;
								foreach($subQuestion['tasks'] as $task){
									if( in_array($task["taskCategoryId"], @$selector["ui"]["comment"])){
										if( !$hasComment){
											echo "<div class=\"commentGroup\">\n";
											$hasComment = true;
										}
										echo "<div>" . linkify($task['description']) . "</div>\n";
									}
								}
									
								if($hasComment){
									echo "</div>\n";
								}
							}
								
							echo "</div>";
						}

						echo '</div></li>';
					}
					echo'</ul>';
				} ?>
				</div>
			</li>
				<?php
				}
				echo"</ol></div>";
				$count++;
			}
		}
		debug("end", $_SESSION);
		?>
		</div>
	</div>
</div>


</body>
</html>
