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
require_once("dao.php");
require_once("selectors.php");
require_once("formBuilder.php");
require_once("db.php");

date_default_timezone_set("Europe/Brussels");
$numberOfQueries =0;
$debug = false;
$error = true;
$multiUser = false;

function printNumberOfQueriesDone(){
	global $numberOfQueries, $debug;
	if($debug){
		echo "<div>number of queries done: " . $numberOfQueries . "</div>";
	}
}

function select($selector){
	global $queries;


	$table = $selector['table'];
	$queryInfo = $queries[$table];
	$query = $queryInfo['select'];
	$queryParams = array();

	debug("selector", $selector);

	if($table != "quiz"){
		$queryParams[] = getQuizId();
		$query .= ' and qz.id = ? ';
	}
	else {
		debug("querying quiz table");
	}
	
	if(isset($selector['param']) &&  is_array($selector['param'])){
		foreach($selector['param'] as $key => $value){
			if(isset($queryInfo['param'][$key])){
				if(is_array($value)){
					$param=  implode(",", $value);
					$query .= " and " . sprintf($queryInfo['param'][$key], $param);
				}
				else{					
					$query .= " and " . $queryInfo['param'][$key];
					if(strpos($queryInfo['param'][$key], "?")){
						$queryParams[] = $value;
					}
				}
				
			}
			else if (isset($value)){
				// debug("adding slashes to -|" . $value . "|-");
//					$value = $dbLink->escape_string($value);
				$query .= " and " . $value;
			}
		}
	}

	if(isset($selector['order'])){
		$query .= (" order by " . $queryInfo["order"][$selector['order']]);
	}
	else{
		$query .= (" order by " . $queryInfo["order"]["default"]);
	}

	if(isset($selector['limit'])){
		$query .= " limit " . $selector['limit'];
	}

	return DbSelectStatement($query, $queryParams);
}



function updateWithAction($info){
	global $queries;
	if(isset($info['id'])){
		$id = $info['id'];
	}

	$counter = 0;
	if($id != null && $id >= 0){
		debug($info);
		$table = $info['table'];
		$statements = $queries[$table][$info["action"]];
		debug($statements);
		foreach($statements as $sql){
			$sql = sprintf($sql, $id);
			debug($sql);
			$counter += DbUpdateStatement($sql, $id );
		}
	}
	else{
		debug("updateWithAction(): id not set!", $info);
	}
	return $counter;
}


function update($info){
	global $queries;
	debug("info", $info);
	if(is_array($info['param']) && isset($info['id'])){
		$id = $info['id'];

		if($queries[ $info['table'] ]["isHistoryTable"] == true){
			$id = -1;
		}
		else if($id == null || $id <=0){
			$id = findIdByProperties($info);
		}
		debug("id: " . $id);
		
		$encryptedColumns = $queries[ $info['table'] ]["encrypted"];
		if($id > 0){
			$queryParams = Array();
			$query = "update " . TABLE_PREFIX_ . $info['table'] . " set";
			$query .= " modificationDate = ?,";
			$query .= " modificationUser = ?";
		
			$queryParams[] = date ("Y-m-d H:i:s");
			$queryParams[] = getUser();
			
			foreach($info['param'] as $key => $value){
				if($encryptedColumns[$key]){
					$query .= ", $key   = aes_encrypt(?, " . SALT . ")";
					$queryParams[] = $value;
				}
				else{
					$query .= ", $key   = ? ";
					$queryParams[] = $value;
				}
					
			}

			if($info['updateChild'] == 1 ){
				 $query .= ' where ( id = ? or parentId = ? ) ';
				 $queryParams[] = $id;
				 $queryParams[] = $id;
			}
			else{
				$query .= " where id = ? ";
				$queryParams[] = $id;
			}
			
			DbUpdateStatement($query, $queryParams);
		}
		else{
			$query = "insert into " . TABLE_PREFIX_ .  $info['table'] . " ";
			$columns = "creationUser, creationDate";
			$values =  "?, ? ";
			$queryParams[] = getUser();
			$queryParams[] = date ("Y-m-d H:i:s") ;
			
			if( $queries[ $info['table'] ]["isHistoryTable"] == true){
				$mainId = $info['mainId'];
				$columns .= ", mainId, version ";
				$maxVersion = DbSelectStatement("select max(version) as maxVersion from " . TABLE_PREFIX_ .  $info['table'] . " where mainId = ? ", $mainId );
				debug(" current version", $maxVersion);
				$tmp = DbSelectStatement("select * from " . TABLE_PREFIX_ .  $info['table'] . " where mainId = ? and version = ?",
							Array($mainId,coalesce($maxVersion[0]["maxVersion"], 0)));
				debug($tmp);
				$version = coalesce($maxVersion[0]["maxVersion"], 0)+1;
				$values  .= ", ?, ?";
				$queryParams[] = $mainId;
				$queryParams[] = $version;
				
				if(isset($tmp[0])){
					$currentRow = $tmp[0];
					foreach($currentRow as $key => $value){
						if(!isset($info['param'][$key]) && $value != null
							&& $key != "creationUser" && $key != "creationDate"
							&& $key != "id"  && $key != "quizId" && $key != "rownum"
							&& $key != "mainId"&& $key != "version" ){
							$columns .= ", $key ";
							$values .= ", ?";
							$queryParams[] = $value;
							debug("add existing data " . $key . "- " . $value);
						}
						else{
							debug("skip existing data " . $key . "- " . $value);
						}
					}
				}
			}	
			else{
				$columns .= ", modificationUser, modificationDate";
				$values  .= ", ?, ?";
				$queryParams[] = getUser();
				$queryParams[] = date ("Y-m-d H:i:s");
			}		
			// 
			// 

			foreach($info['param'] as $key => $value){
				$columns .= ", $key";
				if($encryptedColumns[$key]){
					$values .= ", aes_encrypt(?, " . SALT . ")";
					$queryParams[] = $value;
				}
				else{
					$values .= ", ? ";
					$queryParams[] = $value;
				}
			}
			if($info['table'] != "quiz"){
				$query .= '(' . $columns . ', quizId) values(' . $values . ', ?)';
				$queryParams[] = getQuizId();
			}
			else{
				$query .= '(' . $columns . ') values(' . $values . ')';
			}
			DbUpdateStatement($query, $queryParams);


			$id = findIdByProperties($info);

		}
		if( isset($queries[ $info['table'] ]["historyTable"])){
			$info2 = $info;
			$info2['mainId'] = $id;
			$info2['id'] = -1;
			$info2['table'] = $queries[ $info['table'] ]["historyTable"];
			update($info2);
		}
		return $id;
	}
	else{
		// debug("update(): param or id not set", $info['param'] , $info['id']);
		error("update(): param or id not set  -|" . $info['param'] . "| |" . $info['id'] . "|-");
	}

}


function findIdByProperties($info){
		global $queries;
		$selector["table"] = $info['table'] ;
		$selector["params"] = Array();
		$prefix = $queries[ $info['table'] ]["prefix"];
		$encryptedColumns = $queries[ $info['table'] ]["encrypted"];
		$query = "select id from " . TABLE_PREFIX_ . $info['table']  . " where 1 = 1 ";
		foreach( $info['param'] as $key => $value){
			if($encryptedColumns[$key]){
				$query .= " and $key = aes_encrypt(?, " . SALT . ")";
			}
			else{
				$query .= " and $key = ?";
			}
			$queryParams[] = $value;
		}
		if($info['table'] != "quiz"){
			$query .= " and quizId = ? ";
			$queryParams[] = getQuizId();
		}
		
		$result = DbSelectStatement($query, $queryParams);

		debug($result);
		return $result[0]["id"];
}

function endsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strrpos($Haystack, $Needle) === strlen($Haystack)-strlen($Needle);
}

function getExtension($filename){
	return substr($filename, strrpos($filename, '.')+1);
}

function coalesce() {
  $args = func_get_args();
//  debug($args);
  foreach ($args as $arg) {
    if (isset($arg)) {
      return $arg;
    }
  }
  return NULL;
}

function redirect($to){
	global $debug;
	if($debug){
		echo '<b>not refreshing because of debug setting</b>';
		echo '<a href="' . $to . '">' . $to . '</a>';
		return;
	}

  $schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
  $host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
  if (headers_sent()){
	echo '<a href="' . $to . '">' . $to . '</a>';
	exit();
  }
  else
  {
    //header("HTTP/1.1 301 Moved Permanently")
    // header("HTTP/1.1 302 Found")
    header("HTTP/1.1 303 See Other");
    header("Location: $to");
    exit();
  }
}

function isNotBlank($str){
	return !($str ==null || strlen(trim(str_replace('&nbsp;', '',$str))) ==0);
}

function linkify($text) {
	return preg_replace('#(\A|[^=\]\'"a-zA-Z0-9])(http[s]?://(.+?)/[^()<>\s]+)#i', '\\1<a href="\\2" target="_blank" >\\2</a>', $text);
//		return  preg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\">\\0</a>", $text);
}

function debug($var){
	global $debug;
	if($debug){
		echo "<pre>";
		$args = func_get_args ();
		foreach($args as $arg){
			echo "\n";
			print_r($arg);
		}
		echo "</pre>";
	}
}

function error($var){
	global $error;
	if($error){
		echo "<pre style='margin-left: 2px solid red;'>ERROR:\n";
		$args = func_get_args ();
		foreach($args as $arg){
			echo "\n";
			if($arg instanceof Exception){
				echo $arg->getTraceAsString();				
			}
			else{
				print_r($arg);
			}
		}
		echo "</pre>";
	}
	die();
}
?>
