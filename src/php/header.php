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
	require_once("config.php");
	require_once("functions.php");
	session_start();
	global $debug;

	global $errorMessage;
	if(isset($_POST['id'])  && isset($_POST['password'])){
		$errorMessage = setQuizId($_POST);
	}
	else if(isset($_GET['logoff'])){
		setQuizId(-1);
		$_SESSION['saved'] = null;
	}
	debug($_POST, $errorMessage);
	if(isset($_POST["user"])){
		$_SESSION["user"] = $_POST["user"];
	}
	$quiz = selectQuiz();

function printHeader($title = null){
 header("Content-type: text/html; charset=utf-8");
 ?>
 <html lang="en">
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<link rel="shortcut icon" type="image/png" href="images/icon.png">
	<title> <?php echo $title; ?></title>

	<!-- Bootstrap -->
	<link href="lib/bootstrap-3.2.0-dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="lib/bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>

	<!--  jQuery plugins -->
	<script src="lib/jquery.cookie.js"></script>
	
	<!-- custom css & javascript -->
	<link href="css/style.css" rel="stylesheet"></link>
	<link href="css/print.css" rel="stylesheet" media="print"></link>
	<script src="js/functions.js"></script>
	
  </head>
<? }

function printMenu($printSubMenu = false, $questionId = null){
global $quiz;
$questionAnchor = "";
if($questionId != null)
	$questionAnchor = "#question$questionId";
?>
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="index.php" class="navbar-brand <?php echo isCurPage('index.php');?>" id="menu_main" >
			<?php echo coalesce($quiz["description"], "inloggen"); ?>
		</a>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
		<?php if(getQuizId() != -1){ ?>
			<li class="<?php echo isCurPage('question_list.php');?>">
				<a href="question_list.php<?php echo $questionAnchor;?>"  id="menu_list" >vragen lijst</a>
			</li>
			<li class="<?php echo isCurPage('todo_list.php');?>">
				<a href="todo_list.php"  id="menu_todo" >notities</a>
			</li>
			<li class="<?php echo isCurPage('matrix.php');?>">
				<a href="matrix.php"  id="menu_matrix" >overzicht</a>
			</li>
			<li class="<?php echo isCurPage('question_detail.php?id=-1');?>">
				<a href="question_detail.php?id=-1" id="menu_newQuestion" >nieuwe vraag</a>
			</li>
			<li class="<?php echo isCurPage('question_list_print.php');?>">
				<a href="question_list_print.php" id="menu_print" >print lijst</a>
			</li>
			<li class="divider"></li>
			<li class="<?php echo isCurPage('round_list.php');?>">
				<a href="round_list.php" >rondes</a>
			</li>
			<li class="<?php echo isCurPage('thema_list.php');?>">
				<a href="thema_list.php">thema's</a>
			</li>
			<li class="<?php echo isCurPage('file_list.php');?>">
				<a href="file_list.php">files</a>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Extra <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li class="<?php echo isCurPage('question_list_delete.php');?>">
						<a href="question_list_delete.php">vragen verwijderen</a>
					</li>
					<li class="<?php echo isCurPage('slide_list.php');?>">
						<a href="slide_list.php">slides</a>
					</li>
					<li class="<?php echo isCurPage('question_list_analysis.php');  echo isCurPage('question_list_analysis.php?input=1');?>">
						<a href="question_list_analysis.php">analyze</a>
					</li>
				</ul>
			</li>
			<li><a href="index.php?logoff">uitloggen</a></li>
		<?php } ?>
			
		</ul>
	</div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<?php if(getQuizId() == -1){
$action = coalesce($_SERVER["REQUEST_URI"], "index.php");
if(endsWith($action, "logoff")){
	$action = "index.php";
}
?>
<div class="container-fluid">
<form method="post" action="<?php echo $action; ?>" name="login" >
<table>
<?php
	global $errorMessage;
	if(isset($errorMessage)){?>
		<tr><td colspan="2"> <?php echo $errorMessage; ?></td></tr>
	<?php }?>
	<tr><td>quiz:</td><td>
	<select name="id" >
		<?php
		$quizes = selectAllQuizes();
		foreach($quizes as $quiz){
			$cookieId = coalesce(@$_COOKIE['quizId'], -1);
			$selected = ( $cookieId ==  $quiz['quizId']?'selected="selected"':'');
			echo '<option value = "' . $quiz['quizId'] . '" '  . $selected . '>' . $quiz['description'] . '</option>';
		} ?>
	</select>
	<?php
	debug("quizes", $quizes);
	debug("cookie", $_COOKIE);
	
	$user = coalesce(@$_COOKIE['user'], '');
	?>
	</td></tr>
	<tr><td>gebruikersnaam</td><td>
	<input type="text"			name="user" value="<?php echo $user ?>" />
	</td></tr>
	<tr><td>paswoord</td><td>
	<input type="password"	name="password" />
	</td></tr>
	<tr><td colspan="2">
	<input type="submit" value="inloggen"/>
	</td></tr>
</table>
</form>
<?php
if(isNotBlank($user)){
	$focusField="password";
}
else{
	$focusField="user";
}
 ?>
<script type="text/javascript">
	$('input[name=<?php echo $focusField?>]')[0].focus();
 </script>
</div>
</body>
</html>

<?php die(); 
	}

}

function isCurPage($page) {
 if(substr($_SERVER["REQUEST_URI"],strrpos($_SERVER["REQUEST_URI"],"/")+1) == $page){
		return "active";
 }
 else{
		return "";
 }
}

function formatImage($image, $question, $options = null){
	$showLink = coalesce($options['link'], true);
	$quizImage = coalesce($options['small'], true);

	$linkPrefix = '';
	$linkSuffix = '';
	if($showLink){
		$linkPrefix = '<a href="upload/' . $image["quizId"] . '-'  . $image['imageId'] . '.jpeg" class="linkImage">';
		$linkSuffix = '</a>';
	}

	$id = "img" . $question["roundSequence"] . "_" . $question["themaSequence"] . "_" . $question["sequence"]. "_" . $image["sequence"];
	debug("img id: ", $id);
	return  $linkPrefix  .'<img src="upload/' .  $image["quizId"] . '-' .$image['imageId'] . '.jpeg" class="quizImage" title="' . $id . '" />' . $linkSuffix ;
}

function formatAnswer( $question, $options  = null){
 //, $editable = false,  $showLink = true, $showHiddenQuestion = false, $showTasks = true, $showDifficulty=true){
debug($options);

	$editable= coalesce(@$options['editable'], false);
	$showLink = coalesce(@$options['link'], true);
	$showHiddenQuestion = coalesce(@$options['hiddenQuestion'], false);
	$showTasks = coalesce(@$options['tasks'], true);
	$showDifficulty = coalesce(@$options['difficulty'], true);
	$hiddenAnswer = coalesce(@$options['hiddenAnswer'], false);

	if($showLink)
		$detailLink = '<a  href="question_detail.php?id=' . $question["questionId"] . '"><span class="glyphicon glyphicon-edit"></span></a>';
	else
		$detailLink = '';

	// if($question['childQuestions'] == 0){
	if($editable){
		$editableStyle="editableText";
	} else {
		$editableStyle = "";
	}
	if(isNotBlank($question['answer']))
		$answer = $question['answer'];
	else{
		$answer = '&nbsp;&nbsp;&nbsp;&nbsp;';
	}

	$description = "";
	$tooltipClass = "";
	if($showHiddenQuestion == true){
		$description = '<div class="hidden" id="tooltip_answer_' . $question["questionId"]  .'">' .nl2br( $question['description']) . '</div>';
		$tooltipClass = "hasHtmlToolTip";
	}

	$hiddenAnswerClass = "";
	$hiddenAnswerToolTip = "";
	if($hiddenAnswer == true){
		$hiddenAnswerClass="hiddenAnswer";
		$hiddenAnswerToolTip = " title=\"$answer\" ";
	}

	$taskIndicator = "";
	if($showTasks && $question['openTasks'] >0){
		$taskDescr = '';
		foreach($question['tasks'] as $task){
			if($task['done'] == false){
				if(strlen($taskDescr) >0){
					$taskDescr .= ',';
				}
				$taskDescr= $taskDescr . ' ' . $task['taskCategory'];
			}
		}
			$taskDescr = 'Open notities:' .$taskDescr;
		$taskIndicator = '<a  href="question_detail.php?id=' . $question["questionId"] . '#notes"><span class="glyphicon glyphicon-tag hasToolTip" data-toggle="tooltip" title="' . $taskDescr . '" ></span></a>';
	}

	if($showDifficulty)
		$difficulty = "difficulty difficulty" . $question['difficulty'] ;
	else
		$difficulty = "";

	$difficultyEditable = "";
	if($editable && $showDifficulty){
		$difficultyEditable="<span><span class=\"editableSelect $difficulty \"  id=\"difficulty_" . $question["questionId"] ."\" data-table='question'>". $question['difficulty'] ."</span></span>";
		$difficulty="";
	} else {
		$difficultyEditable="<span class=\"$difficulty \"  id=\"difficulty_" . $question["questionId"] ."\">&nbsp;</span>";
	}


	return "$difficultyEditable<span><span class=\"$editableStyle $tooltipClass answer $hiddenAnswerClass \" id=\"answer_" . $question["questionId"] ."\"  $hiddenAnswerToolTip data-table='question'>" . $answer .'</span> </span>' . $detailLink  . $taskIndicator . '' . $description;
	/*
	}
	else{
		return '<div><span class="answer" id="answer_' . $question["questionId"]  .'" >&nbsp;</span>' . $detailLink . '</div>';
	}
	*/
}

function formatAnswerForMatrix($question, $print){
		$link = !$print;

		return formatAnswer($question,  array("link"=> $link, "hiddenQuestion" => $link, "tasks"=> $link));
 }

 function formatAnswerForDetail($question){
 return formatAnswer($question,  array("link"=> true, "hiddenQuestion" => true));
 }

 function formatDate($date){
		$ts = strtotime($date);
		if($ts==0)
			return "";
		else
			return date ('d/m/Y H:i:s', $ts);
 }

 function formatAuditInfo($object){
	$title = 'Aangemaakt door ' . $object["creationUser"] . " op " . formatDate($object["creationDate"]);
	if($object["modificationDate"] != $object["creationDate"]){
		$title .= '<br/>Laatst gewijzigd door ' . $object["modificationUser"] . " op " . formatDate($object["modificationDate"]);
	}
	return '<span id="audit_'.$object["id"] .'" class="hasToolTip" data-toggle="tooltip" data-html="true" title="'. $title . '"><i>' .  coalesce($object["modificationUser"], '&nbsp;&nbsp;') . '</i></span>';
 }
 
 $questionFields["description"] = "vraag";
 $questionFields["answer"] = "antwoord";
 $questionFields["answerExtra"] = "extra uitleg";
 $questionFields["round"] = "ronde";
 $questionFields["thema"] = "thema";
 $questionFields["difficulty"] = "moeilijkheidsgraad";
 function getChangedField($question1, $question2){
		if($question1 ==null || $question2 == null){
			return "";
		} 
		
		global $questionFields;
		$changedFields ="";
		debug("getChangedField");
		foreach ($questionFields as $key => $value){
			debug("field " . $value, $question1[$key] , $question2[$key]);
			if($question1[$key] != $question2[$key]){
				if(strlen($changedFields) >0){
				$changedFields .= ', ';
			}
			$changedFields .= $value;
			}
		}
		return $changedFields;
 }
 ?>
