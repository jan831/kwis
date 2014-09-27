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


$queries = Array();
$questionBaseSelect = "select q.id as id, q.id as questionId, aes_decrypt(q.description, " . SALT  . ") as description, " . 
								" aes_decrypt(q.answer, " . SALT  . ") as answer, " .
								" aes_decrypt(q.answerExtra, " . SALT  . ") as answerExtra, " .
								" q.deleted, q.isSpecial, ".
								" q.creationUser, q.creationDate, q.modificationUser, q.modificationDate, " .
								" ifnull(q.difficulty, 0) as difficulty, q.parentId, ifnull(q.sequence, 0) as sequence, " .
								" q.correctAnswers, q.analysisInfo,  concat(floor( q.correctAnswers/coalesce(qz.numberOfTeams, q.correctAnswers) * 10) *10, ' %') as correctPercentage, " .
								" coalesce(qz.numberOfTeams, q.correctAnswers, 1) as numberOfTeams, " .
								" (select count(id) from " . TABLE_PREFIX_ . "question qc where qc.parentId = q.id and qc.deleted = 0 ) as childQuestions, " .
								" ifnull(t.id, -1)as themaId,  " .
								" ifnull( t.description, (select t2.description from " . TABLE_PREFIX_ . "thema t2 where t2.sequence = 0  and t2.quizId = qz.id limit 1 ) ) as thema, " .
								" ifnull(case when r.isSpecial = 1 then (ifnull(q.sequence, 0)) else t.sequence end, 99) as themaSequence, ".
								" ifnull(r.description, '-') as round, ".
								" ifnull(r.id, case when q.isSpecial then -1 else -2 end ) as roundId, " .
								" ifnull(r.sequence, 99) as roundSequence, " .
								" (select count(id) from " . TABLE_PREFIX_ . "task ts where ts.questionId = q.id) as taskCount, ".
								" (select count(id) from " . TABLE_PREFIX_ . "task ts where ts.questionId = q.id and done = false) as openTasks, ".
								" (select count(id) from " . TABLE_PREFIX_ . "image i where i.questionId = q.id) as imageCount ".
								" from " . TABLE_PREFIX_ . "question q " .
								" inner join " . TABLE_PREFIX_ . "quiz qz on q.quizId = qz.id " .
								" left outer join " . TABLE_PREFIX_ . "question qp on q.parentId = qp.id " .
								" left outer join " . TABLE_PREFIX_ . "round r on r.id = coalesce(qp.roundId, q.roundId) and r.quizId = qz.id" .
								" left outer join " . TABLE_PREFIX_ . "thema t on t.id = coalesce(q.themaId, qp.themaId)" ;

$queries["childQuestion"]["select"] = $questionBaseSelect .
								" where 1 = 1 ";
$queries["childQuestion"]["param"]["deleted"] = "q.deleted = ? ";
$queries["childQuestion"]["order"]["default"]  = "q.sequence";

$queries["question"]["select"] = $questionBaseSelect .
								" where 1 = 1 ";
$queries["question"]["prefix"] = "q.";

$queries["question"]["delete"][] = "update " . TABLE_PREFIX_ . "question set deleted = true where ? in (id, parentId) ";
$queries["question"]["undelete"][] = "update " . TABLE_PREFIX_ . "question set deleted = false where ? in (id, parentId) ";

$queries["question"]["order"]["thema_round"] = " ifnull(t.sequence, 99), ifnull(r.sequence, 99), q.sequence, q.id";
$queries["question"]["order"]["round_thema"] = " ifnull(r.sequence, 99), ifnull(case when r.isSpecial = 1 then (ifnull(q.sequence, 0) +1) else t.sequence+1 end, 99) asc, q.sequence, q.id";
$queries["question"]["order"]["difficulty_correctPercentage"] = " q.difficulty DESC, q.correctAnswers,  q.id";
$queries["question"]["order"]["correctPercentage_difficulty"] = " q.correctAnswers, q.difficulty DESC,  q.id";
$queries["question"]["order"]["modificationDate"] = " q.modificationDate DESC, q.id";
$queries["question"]["order"]["answer"] = " aes_decrypt(q.answer, " . SALT  . ")  ASC, q.id";

$queries["question"]["param"]["notReady"] = " exists (select 1 from " . TABLE_PREFIX_ . "task ts where ts.questionId = q.id and ts.done = false )";
$queries["question"]["param"]["hasRound"] = "(r.sequence >= 1 and (q.isSpecial or t.sequence >=1))"; 
$queries["question"]["param"]["taskCategory"] = " exists (select 1 from " . TABLE_PREFIX_ . "task ts where ts.questionId = q.id and ts.taskCategoryId in (%s) )";
$queries["question"]["param"]["taskCategoryNot"] = " not exists (select 1 from " . TABLE_PREFIX_ . "task ts where ts.questionId = q.id and ts.taskCategoryId in (%s) )";
$queries["question"]["param"]["thema"] = "t.id in (%s) ";
$queries["question"]["param"]["round"] = "r.id in (%s) ";
$queries["question"]["param"]["deleted"] = "q.deleted = ? ";
$queries["question"]["param"]["isSpecial"] = "r.isSpecial = 1 ";
$queries["question"]["param"]["isNotSpecial"] = "r.isSpecial = 0 ";
$queries["question"]["param"]["id"] = "q.id = ? ";
$queries["question"]["param"]["noImages"] = " not exists (select 1 from " . TABLE_PREFIX_ . "image i where i.questionId = q.id) ";


$queries["question"]["order"]["default"] = $queries["question"]["order"]["thema_round"];

$queries["question"]["historyTable"] = "question_history";

$queries["question"]["encrypted"]["answer"] = true;
$queries["question"]["encrypted"]["answerExtra"] = true;
$queries["question"]["encrypted"]["description"] = true;

$queries["question_history"]["isHistoryTable"] = true;
$queries["question_history"]["select"] = "select q.id as id, q.mainId as mainId, q.version, ".
								" aes_decrypt(q.description, " . SALT  . ") as description, " .
								" aes_decrypt(q.answer, " . SALT  . ") as answer, " .
								" aes_decrypt(q.answerExtra, " . SALT  . ") as answerExtra, " .
								" q.deleted, q.isSpecial, ".
								" q.creationUser, q.creationDate, " .
								" ifnull(q.difficulty, 0) as difficulty, q.parentId, ifnull(q.sequence, 0) as sequence, " .
								" q.correctAnswers, q.analysisInfo,  concat(floor( q.correctAnswers/coalesce(qz.numberOfTeams, q.correctAnswers) * 10) *10, ' %') as correctPercentage, " .
								" coalesce(qz.numberOfTeams, q.correctAnswers, 1) as numberOfTeams, " .
								" ifnull(t.id, -1)as themaId,  " .
								" ifnull( t.description, (select t2.description from " . TABLE_PREFIX_ . "thema t2 where t2.sequence = 0  and t2.quizId = qz.id limit 1 ) ) as thema, " .
								" ifnull(case when r.isSpecial = 1 then (ifnull(q.sequence, 0)) else t.sequence end, 99) as themaSequence, ".
								" ifnull(r.description, '-') as round, ".
								" ifnull(r.id, case when q.isSpecial then -1 else -2 end ) as roundId, " .
								" ifnull(r.sequence, 99) as roundSequence " .								
								" from " . TABLE_PREFIX_ . "question_history q " .
								" inner join " . TABLE_PREFIX_ . "quiz qz on q.quizId = qz.id " .
								" left outer join " . TABLE_PREFIX_ . "question qp on q.parentId = qp.id " .
								" left outer join " . TABLE_PREFIX_ . "round r on r.id = coalesce(qp.roundId, q.roundId) and r.quizId = qz.id" .
								" left outer join " . TABLE_PREFIX_ . "thema t on t.id = coalesce(q.themaId, qp.themaId)" .
								" where 1 = 1 " ;
$queries["question_history"]["prefix"] = "q.";
$queries["question_history"]["order"]["default"] = " q.version desc ";
$queries["question_history"]["param"]["id"] = "q.id = ? ";
$queries["question_history"]["param"]["ids"] = "q.id in ( %s) ";
$queries["question_history"]["param"]["mainId"] = "q.mainId = ? ";

$queries["question_history"]["encrypted"]["answer"] = true;
$queries["question_history"]["encrypted"]["answerExtra"] = true;
$queries["question_history"]["encrypted"]["description"] = true;


$queries["thema"]["select"] = "select  t.id as id, t.id as themaId, t.description, t.sequence from " . TABLE_PREFIX_ . "thema t " .
								" inner join " . TABLE_PREFIX_ . "quiz qz on t.quizId = qz.id " .
								" where 1 = 1 ";
$queries["thema"]["order"]["default"] = " t.sequence ";
$queries["thema"]["prefix"] = "t.";
$queries["thema"]["delete"][] = "update " . TABLE_PREFIX_ . "question q set themaid = (select id FROM " . TABLE_PREFIX_ . "thema t where t.quizId= q.quizId and t.sequence=0)  where themaid = ?";
$queries["thema"]["delete"][] = "delete from " . TABLE_PREFIX_ . "thema where id = ?";

$queries["round"]["select"] = "select  r.id as id, r.id as roundId, r.description, r.sequence, r.isSpecial from " . TABLE_PREFIX_ . "round r " .
								" inner join " . TABLE_PREFIX_ . "quiz qz on r.quizId = qz.id " .
								" where 1 = 1 ";
$queries["round"]["order"]["default"] = " r.sequence ";
$queries["round"]["prefix"] = "r.";
$queries["round"]["delete"][] = "update " . TABLE_PREFIX_ . "question q set roundId = (select id FROM " . TABLE_PREFIX_ . "round r where r.quizId= q.quizId and r.sequence=0) where q.roundId = ?";
$queries["round"]["delete"][] = "delete from " . TABLE_PREFIX_ . "round where id = ?";

$queries["quiz"]["select"] = "select qz.id as id, qz.id as quizId, qz.description as description, " .
								" qz.numberOfTeams, qz.email, qz.quizDate, qz.encrypted " .
								" from " . TABLE_PREFIX_ . "quiz qz where 1 = 1 ";

$queries["quiz"]["param"]["password"] = " qz.password = ? ";
$queries["quiz"]["param"]["id"] = " qz.id = ?";
$queries["quiz"]["order"]["default"] = " qz.description ";

$queries["task"]["select"] = "select ts.id as id, ts.description, ts.done as done, tc.description as taskCategory, tc.id as taskCategoryId, " .
									" ts.creationUser, ts.creationDate, ts.modificationUser, ts.modificationDate, " .
									" ts.questionId as questionId, q.roundId as roundId, r.description as roundDescription, t.description as themaDescription ".
								" from " . TABLE_PREFIX_ . "task ts " .
								" inner join " . TABLE_PREFIX_ . "taskCategory tc on ts.taskCategoryId = tc.id  " .
								" inner join " . TABLE_PREFIX_ . "question q on ts.questionId = q.id " .
								" inner join " . TABLE_PREFIX_ . "quiz qz on q.quizId = qz.id " .
								" inner join " . TABLE_PREFIX_ . "round r on q.roundId = r.id " .
								" left outer join " . TABLE_PREFIX_ . "thema t on q.themaId = t.id " .
							" where 1 = 1 ";

$queries["task"]["param"]["hideDeletedQuestion"] = "q.deleted = false ";
$queries["task"]["order"]["default"] = " tc.id, ts.done ";
$queries["task"]["order"]["taskCategory_done"] = " tc.description, ts.done ";
$queries["task"]["order"]["done_taskCategory"] = " ts.done, tc.description ";
$queries["task"]["order"]["thema_round"] = "t.sequence, r.sequence";
$queries["task"]["order"]["round_thema"] = "r.sequence, t.sequence";
$queries["task"]["order"]["modificationDate"] = " ts.modificationDate DESC, t.id";
$queries["task"]["prefix"] = "ts.";

$queries["task"]["param"]["taskCategory"] = "tc.id in (%s) ";
$queries["task"]["param"]["thema"] = "t.id in (%s) ";
$queries["task"]["param"]["questionIds"] = "q.id in (%s) ";
$queries["task"]["param"]["notReady"] = " ts.done = false ";

$queries["task"]["delete"][] = "delete from " . TABLE_PREFIX_ . "task where id = ? ";


$queries["taskCategory"]["select"] =  "select tc.id as id, tc.description, tc.id as taskCategoryId from " . TABLE_PREFIX_ . "taskCategory tc  " .
										" inner join " . TABLE_PREFIX_ . "quiz qz on 1 = 1 " .
									" where 1 = 1 ";
$queries["taskCategory"]["order"]["default"] = "tc.id ";

$queries["image"]["select"] = "select i.id as id, i.id as imageId, i.sequence as sequence, q.id as questionId, q.quizId as quizId, " .
										" i.creationUser, i.creationDate, i.modificationUser, i.modificationDate " .
										" from " . TABLE_PREFIX_ . "image i " .
										"  inner join " . TABLE_PREFIX_ . "question q on i.questionId = q.id" .
										"  inner join " . TABLE_PREFIX_ . "quiz qz on qz.id = i.quizId";
$queries["image"]["param"]["questionIds"] = " i.questionId in (%s) ";
$queries["image"]["order"]["default"] = " i.sequence ";
$queries["image"]["prefix"] = "i.";
$queries["image"]["delete"][] ="delete from " . TABLE_PREFIX_ . "image where id = ? ";


$queries["file"]["select"] = "select f.id as id, f.id as fileId, ".
		" filename as filename, originalFilename as originalFilename, " .
		" f.mimetype, f.description, " .
				" f.creationUser, f.creationDate,  f.modificationUser, f.modificationDate, " .
				" ifnull(r.description, '-') as round, ".
				" r.id as roundId, " .
				" ifnull(r.sequence, 99) as roundSequence " .
				" from " . TABLE_PREFIX_ . "file f " .
				" inner join " . TABLE_PREFIX_ . "quiz qz on f.quizId = qz.id " .
				" left outer join " . TABLE_PREFIX_ . "round r on r.id =f.roundId and r.quizId = qz.id" .
				" where 1 = 1 ";
$queries["file"]["prefix"] = "f.";

$queries["file"]["order"]["round"] = " ifnull(r.sequence, 99), originalFilename, f.modificationDate DESC";
$queries["file"]["order"]["modificationDate"] = " f.modificationDate DESC";


$queries["file"]["param"]["round"] = "r.id in (%s) ";
$queries["file"]["param"]["id"] = "f.id = ? ";

$queries["file"]["order"]["default"] = $queries["file"]["order"]["round"];
$queries["file"]["delete"][] = "delete from " . TABLE_PREFIX_ . "file where id = ? ";


function getUser(){
	return coalesce($_SESSION["user"], "");
}

function getQuizId(){
	global $debug;
	if(isset( $_SESSION["quizId"]))
		return $_SESSION["quizId"];
	else{
			return -1;
	}
}

function  setQuizId($params){
	global $quiz;
	if(is_array($params)){
		if( trim($params['user']) =="" || $params['user'] =="quiz"){
			return "gelieve een geldige gebruikersnaam in te geven (niet leeg laten, geen quiz als gebruikersnaam)";
		}
		$selector['table'] = 'quiz';
		$selector['param']['id'] = $params["id"];
		$selector['param']['password'] = "" . md5($params["password"]);
		$result= select($selector);
		if(sizeof($result) == 1){
			if (!headers_sent()) {
				setcookie("quizId", $result[0]['quizId'], time()+60*60*24*60, "/");
				setcookie("user", $params['user'], time()+60*60*24*60, "/");
			}
			else{
				debug("not setting header cookie, headers already sent...");
			}

			$_SESSION["quizId"] = $result[0]['quizId'];
			$_SESSION[SALT] =  "'" . sha1($params["password"]) . "'";
			$quiz = $result[0];
			
			updateEncryption($quiz);
			return;
		}
		else{
			return "foutief paswoord";
		}
	}
	else{
		unset($_SESSION['quizId']);
		unset($_SESSION['selector']);
	}
}

function saveQuiz($data){
	$data["detail"]["param"]["password"] = md5($data["detail"]["param"]["password"]);

	$id = update($data["detail"]);
	if($data["detail"]["id"] == -1){
		DBupdate("insert into " . TABLE_PREFIX_ . "round (quizId, sequence, description) values(" . $id . ", 0, '-')");
		DBupdate("insert into " . TABLE_PREFIX_ . "thema (quizId, sequence, description) values(" . $id . ", 0, '-')");
	}
	return $id;
}

function selectQuiz(){
	$selector['table'] = 'quiz';
	$selector['param']['id'] = 	getQuizId();
	$result= select($selector);

	if(count($result)>0)
		return $result[0];
	else
		return null;
}

function updateEncryption($quiz){
	if($quiz["encrypted"] != 1){
		$quizId = $quiz["id"];
		DBupdate("update " . TABLE_PREFIX_ . "quiz set encrypted = 1 where id = $quizId");
		DBupdate("update " . TABLE_PREFIX_ . "question set " .
					" description = aes_encrypt(description, " . SALT . "), " .
					" answer 	  = aes_encrypt(answer, " . SALT . "), " .
					" answerExtra = aes_encrypt(answerExtra, " . SALT . ") " .
					" where quizId = $quizId");
		DBupdate("update " . TABLE_PREFIX_ . "question_history set " .
					" description = aes_encrypt(description, " . SALT . "), " .
					" answer 	  = aes_encrypt(answer, " . SALT . "), " .
					" answerExtra = aes_encrypt(answerExtra, " . SALT . ") " .
					" where quizId = $quizId");
	}
}

function selectAllQuizes(){
	$selector['table'] = 'quiz';
	$result= select($selector);
	return  select($selector);
}


function selectQuestions($selector, $groupByOrder = true, $hierarchicalParentChild = true, $fetchImages = true){
	if($hierarchicalParentChild)
		$selector['param'][] = " (q.parentId is null or q.parentId <=0) ";

	$showDeleted = false;
	if(isset($selector['param']['deleted'])){

	}
	else if( !isset($selector['param']['id'])) {
		$selector['param']['deleted'] = 0;
	}

	$questions =  select($selector);

	$result = Array();
	if(isset($selector['order']) && strpos($selector['order'], '_') > 0){
		$grouping = explode('_', $selector['order']);
	// debug($questions);
		$grouping = $grouping[0];
	}

	$tmp = Array();
	$questionsWithImages = Array();
	$questionsWithTasks = Array();
	foreach($questions as $question){
		if($question['childQuestions'] > 0){
			$childSelector = Array();
			$childSelector['table'] = 'childQuestion';
			$childSelector['param'][] = 'q.parentId = ' . $question["questionId"];
			$childSelector['param']['deleted'] = $question['deleted'];
			debug("=== " . $question["questionId"] . ': ' . $question['childQuestions'] );
			$question["children"] = selectQuestions( $childSelector, false, false, $fetchImages);
		}

		if($question['taskCount'] > 0){
			$questionsWithTasks[] = $question["id"];
		}
		if($question['imageCount']>0){
			$questionsWithImages[] = $question["id"];
		}
		$question['images'] = array();
		$question['tasks'] = array();

		$tmp[] = $question;
	}
	$questions = $tmp;
	$tmp = Array();
	debug("questionsWithImages before", $questionsWithImages, " fetchImages? " . ($fetchImages?"true":"false"));
	if(sizeof($questionsWithImages) > 0 && $fetchImages){
		$images = selectImagesForQuestion($questionsWithImages);
		foreach($questions as $question){
			if($question['imageCount']>0){
				foreach($images as $image){
					if($image["questionId"] == $question["id"]){
						$question['images'][] = $image;
						debug("adding image to question " . $image["id"] . "-" . $image["questionId"] . " " . $question["id"]);
					}
				}
			}
			$tmp[] = $question;
		}
		$questions = $tmp;
		$tmp = Array();
	}
debug("questionsWithTasks before", $questionsWithTasks);
	if(sizeof($questionsWithTasks) > 0 ){
		$childSelector = Array();
		$childSelector['table'] = 'task';
		$childSelector['param']["questionIds"] = $questionsWithTasks;
		$tasks = select( $childSelector);
		foreach($questions as $question){
			if($question['taskCount']>0){
				foreach($tasks as $task){
					if($task["questionId"] == $question["id"]){
						$question['tasks'][] = $task;
						debug("adding task to question " . $task["id"] . "-" . $task["questionId"] . " " . $question["id"]);
					}
				}
			}
			$tmp[] = $question;
		}
		debug("questionsWithTasks after", $tmp);
		$questions = $tmp;
		$tmp = Array();
	}

	if($groupByOrder){
		foreach($questions as $question){
			debug("selectQuestions grouping by " . $grouping . ': ' . $question[$grouping] . "  title: " . $question[$grouping . "Id"] );
			$result[$question[$grouping]]["questions"][]= $question;
		}
	}
	else{
		$result = $questions;
	}

	debug("result", $result);
	return $result;
}

function selectQuestionsForList($selector){
	$selector['param'][] = "q.parentId is null";
	return selectQuestions($selector, true, true,  coalesce(@$selector["ui"]["showImage"], false));
}

function selectQuestionsForMatrix($themas){
	$selector['table'] = "question";
	$selector['order'] = "thema_round";
	$selector['param'][] = 'r.id is not null and r.sequence > 0';
	$selector['param'][] = '( (t.id is not null and t.sequence > 0) or r.isSpecial = true) ';
	$selector['param'][] = 'q.parentId is null';
	// $selector['param'][] = 'r.isSpecial = false';

	$questions = selectQuestions($selector, false, true, false);

	$result = Array();
	foreach($themas as $thema){
		$result[$thema["sequence"]] = Array();
	}

	foreach($questions as $question){
		debug($question["id"] . " " . $question["themaSequence"] . "-" . $question["themaId"] . "  -- " . $question["sequence"]);
		if($question["isSpecial"]){
			$result[$question['sequence']+1] [$question['roundId']][] = $question;
		}
		else{
			$result[$question["themaSequence"]] [$question['roundId']][] = $question;
		}
	}

	if(sizeof($result)>0){
		$max = max(array_keys($result));
		debug("selectQuestionsForMatrix max before check", $max);
		if( !is_numeric($max) || $max <= 0){
			$max = 1;
		}
		$max = min(100, $max);
		debug("selectQuestionsForMatrix max", $max, $result);
		for($i=1; $i<=$max; $i++){
			if( !isset($result[$i])){
				// debug("setting value for $i");
				$result[$i] = Array();
			}
		}
		ksort($result);
		debug("selectQuestionsForMatrix after", $max, $result);
	}
	else{
		debug("selectQuestionsForMatrix no results");
	}
	return $result;

}

function selectQuestionsForSlides($round){
	$selector['table'] = "question";
	$selector['order'] = "round_thema";
	$selector['param'][] =  "r.id = $round ";
	return selectQuestions($selector);
}

function selectQuestionsWithoutRoundForMatrix(){
	$selector['table'] = "question";
	$selector['order'] = "thema_round";
	$selector['param'][] = ' (  r.id is null  or t.id is null or r.sequence = 0 or t.sequence = 0) ';
	$selector['param'][] = ' ifnull(r.isSpecial, false) = false';

	return selectQuestions($selector, true, true, false);
}

function selectQuestionsForOverview(){
	$selector['table'] = "question";
	$selector['order'] = "modificationDate";

	$questions = selectQuestions($selector, false, false, true);
	return array_slice($questions, 0,15);
}


function selectQuestionById($id){
	if( $id >= 0){
		$selector['table'] = "question";
		$selector['order'] = "thema_round";
		$selector['param']["id"] = $id;
	
		$questions = selectQuestions($selector, false, false, true);
		return $questions[0];
	} else {
		return array("id" => -1, "parentId" => -1, "questionId" => -1, "isSpecial" => false, "deleted" => false, "children" => array(), "childQuestions" => 0, "tasks" => array(), "taskCount" => 0, "images" => array());
	}
}

function getPrevAndNextQuestionId($question){
	if( $question["id"] < 0){
		return array();
	}
	if($question["parentId"] >0){
		$parent = selectQuestionById($question["parentId"]);
		$questions = $parent["children"];
	}
	else{
		$selector = getDefaultSelectorForList(null, null);
		$selector['param'][] = " (q.parentId is null or q.parentId <=0) and q.deleted = false ";
		$questions = select($selector);
	}

	$ids = Array();
	$found = false;
	if(is_array($questions)){
		foreach($questions as $q){
			if($question["id"] == $q["id"]){
				$found = true;
			}
			else if( !$found){
				$ids[0] = $q["id"];
			}
			else{
				$ids[1] = $q["id"];
				break;
			}
		}
	}
	debug("getPrevAndNextQuestionId, all ids: " . sizeof($questions), $ids);
	return $ids;
}

function selectQuestionHistory($questionId){
	if(coalesce($questionId, -1) >0){
		$selector['table'] = "question_history";
		$selector['param']['mainId'] = $questionId;
		$questionHist = select($selector);
		return $questionHist;
	}
}

function selectQuestionHistoryById($id1, $id2){
	$selector['table'] = "question_history";	
	$selector['param']["ids"] = Array($id1, $id2);

	$questions = select($selector);
	return $questions;
}

function selectTasksForList($selector){
	$selector["param"]["hideDeletedQuestion"] = 1;
	$tasks = select($selector);

	$result = Array();
	foreach($tasks as $task){
		$task["question"] = selectQuestionById($task["questionId"]);
		$result[] = $task;
	}
	return $result;
}

function selectTasksForOverview(){
	$selector["table"] = "task";
	$selector["order"] = "modificationDate";
	$tasks = selectTasksForList($selector);
	return array_slice($tasks, 0, 10);
}


function selectDifficulty(){
	$result = Array();
	$result[] = Array("id"=>0, "description"=>"");
	$result[] = Array("id"=>1, "description"=>"*");
	$result[] = Array("id"=>2, "description"=>"**");
	$result[] = Array("id"=>3, "description"=>"***");
	$result[] = Array("id"=>4, "description"=>"****");
	$result[] = Array("id"=>5, "description"=>"*****");

	return $result;
}


function selectTaskCategory(){
	$selector['table'] = 'taskCategory';

	return select($selector);
}

function selectThemas(){
	$selector['table'] = 'thema';

	return select($selector);
}

function selectThemasForMatrix(){
	$selector['table'] = 'thema';
	$selector['param'][] = ' t.sequence >0';
	$tmp= select($selector);
	$result = Array();
	foreach($tmp as $thema){
		$result[$thema["sequence"]] = $thema;
	}
	return $result;
}

function selectImagesForQuestion($questionId){
	$selector['table'] = "image";
	
	if( !is_array($questionId)){
		$questionId = Array($questionId);
	}
	$selector['param']["questionIds"] = $questionId;

	return select($selector);
}

function selectRounds(){
	$selector['table'] = "round";
	// $selector['param'][] = 'r.isSpecial = false';
	$list = select($selector);
	return $list;
}

//TODO is this used ?
function selectRoundsForQuestionSelector(){
	$list = selectRounds();
	$selector['param'][] = 'r.isSpecial = false';
	//TODO add at 0 dummy round to get all rounds
	// array_splice($array,$position,0,array($insert));
	return $list;
}

function selectRoundNull(){
	$selector['table'] = "round";
	$selector['param'][] = 'r.isSpecial = false';
	$selector['param'][] = 'r.sequence =0';
	$list = select($selector);
	return $list[0];
}

function selectRoundsForMatrix(){
	$selector['table'] = "round";
//	$selector['param'][] = 'r.isSpecial = false';
	$selector['param'][] = 'r.sequence >0';
	$list = select($selector);
	return $list;
}

function selectSpecialRoundsForMatrix(){
	$selector['table'] = "round";
	$selector['param'][] = 'r.isSpecial = true';
	$selector['param'][] = 'r.sequence >0';
	$list = select($selector);
	return $list;
}

function selectRoundById($roundId){
	$selector['table'] = "round";
	$selector['param'][] = "r.id =  $roundId ";
	$list = select($selector);
	return $list[0];
}


function selectMaxImageSequenceForQuestion($questionId){
	$tmp = DbSelectStatement("select max(sequence) as maxSequence from " . TABLE_PREFIX_ . "image where questionId = ? ", $questionId);
	return (int)coalesce($tmp[0]["maxSequence"], -1);
}

function selectMaxSequenceForThema(){
	$tmp = DbSelectStatement("select max(sequence) as maxSequence from " . TABLE_PREFIX_ . "thema where quizId = ?" , getQuizId());
	debug("selectMaxSequenceForThema", $tmp[0]["maxSequence"]);
	return (int)coalesce($tmp[0]["maxSequence"], -1);
}

function selectMaxSequenceForRound(){
	$tmp = DbSelectStatement("select max(sequence) as maxSequence from " . TABLE_PREFIX_ . "round where quizId = ? " , getQuizId());
	debug("selectMaxSequenceForRound", $tmp[0]["maxSequence"]);
	return (int)coalesce($tmp[0]["maxSequence"], -1);
}

function selectFilesForList(){
	$selector = Array();
	$selector['order'] = 'round';
	$selector['table'] = "file";
	
	$list = select($selector);
	$result = Array();
	foreach($list as $file){
		$result[$file["round"]]["id"] = $file["roundId"];
		$result[$file["round"]]["files"][] = $file;
	}
	debug("selectFilesForList", $result);
	return $result;
}

?>
