<?php 
$dbLink = null;
function getDbLink(){
	global $dbLink;
	if($dbLink == null){
		/* Connecting, selecting database */
		$dbLink = new mysqli(MYSQL_HOST,MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
		
		if ($dbLink->connect_errno) {
			error("Could not connect : " . $dbLink->connect_errno . ") " . $dbLink->connect_error);
			
		}
		$dbLink->set_charset('utf8');
	}
	return $dbLink;
}

$conn = null;
function getDbConn(){
	global $conn;
	if($conn == null){
		try{
			$conn = new PDO("mysql:host=" . MYSQL_HOST .";dbname=".MYSQL_DATABASE,MYSQL_USER,MYSQL_PASSWORD);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch (PDOException $e) {
			error($e);
		}
	}
	return $conn;
}

// the same, but for update/insert-queries
function DBupdate($query)
{
	$dbLink = getDbLink();

	debug("DBupdate", $query);

	try {
		$query = str_replace(SALT, $_SESSION[SALT], $query);

		/* Performing SQL query */
		$result = $dbLink->query($query);
		if(!$result){
			throw new Exception("Query failed : " . $dbLink->error . ")\n" . $query);
		}

		$rows = $dbLink->affected_rows;
		debug("updated rows: " . $rows);
	}
	catch(Exception $e){
		error($e);
	}
	return $rows;
}

function DbUpdateStatement($query, $queryParams = null){
	debug("DbUpdateStatement", $query, $queryParams);
	$query = str_replace(SALT, $_SESSION[SALT], $query);
	
	if(!is_array($queryParams)){
		$queryParams = Array($queryParams);
	}
	
	$dbLink = getDbConn();
	try{
		$stmt = $dbLink->prepare($query);
	
		$stmt->execute($queryParams);
		$rows =  $stmt->rowCount();

		debug("updated rows: " . $rows);
		return $rows;
	} catch (PDOException $e) {
		error($e);
	}
}


function DbSelectStatement($query, $queryParams = null){
	debug("DbSelectStatement", $query,  $queryParams);
	if(isset( $_SESSION["quizId"])){
		$query = str_replace(SALT, $_SESSION[SALT], $query);
	}
	$dbLink = getDbConn();
	
	if(!is_array($queryParams)){
		$queryParams = Array($queryParams);
	}
	
	try{
		$stmt = $dbLink->prepare($query);
		$stmt->execute($queryParams);
		
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		error($e);
	}
	/*	
	}
	else{
		$result = $dbLink->query($query);
	}
*/
	$i = 0;
	$res = Array();
	foreach($result as $line){
		$line["rownum"] = $i;
		$res[] = $line;
		$i++;
	}
	if(isset($res))
	{
		return $res;
	}
	else
	{
		// make life easy, return empty array instead of 0 (nice on array-handling functions)
		return array();
	}
}

function executeScript($fileName){
	$dbLink = getDbConn();

	debug("executing " . $fileName);
	$handle = fopen($fileName, "r") or die("Could not open $fileName");

	$query = "";
	while(!feof($handle)) {
		$sql_line = fgets($handle);
		if (trim($sql_line) != "" && strpos($sql_line, "--") === false) {
			$query .= $sql_line;
			$sql_line = null;
			if (preg_match("/.*;\s*\$/", $query)) {
				$query = str_replace("TABLE_PREFIX_", TABLE_PREFIX_, $query);
				debug($query) ;
				$result = $dbLink->query($query) or die($dbLink->error . "\n" . $query);
				$query = "";
			}
		}
	}
}

?>