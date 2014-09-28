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


require_once("php/config.php");
session_start();

global $debug;
// $debug = true;
debug($_POST);

debug($_FILES);

if($_POST['delete'] == 1){
	$id = $_POST['id'];
	$update = Array();
	$update['table'] = 'file';
	$update['id'] = $id;

	$file = select($update)[0];
	debug($file);
	$update['action'] = "delete";
	updateWithAction($update);
	unlink( $file['filename']);
	redirect("file_list.php");
}
else{
	$filename = $_FILES["file"]["name"];
	$roundId = $_POST['roundId'];
	$fileId = $_POST['fileId'];
	$description = $_POST['description'];
	$tmpFilename =$_FILES["file"]["tmp_name"]; 
		
	$file_type = $_FILES["file"]["type"];
	$random = substr(md5(rand()), 0, 7);
	$ext = getExtension($filename);
		
	$newFilename = "upload/" . getQuizId() . '-' . $random . '.' . $ext;
		
	$update = Array();
	$update['param']['roundid'] = $roundId;
	$update['param']['originalFilename'] = $filename;
	$update['param']['filename'] = $newFilename;
	$update['param']['mimetype'] = $file_type;
	$update['param']['description'] = $description;
	$update['table'] = 'file';
	$update['id'] = -1;
		

		
	$id = update($update);
	debug("moving uploaded file,  |" . $tmpFilename . "| -> |" . $newFilename . "|");
	move_uploaded_file($tmpFilename, $newFilename )
	or die('cannot move_uploaded_file ');
		
	redirect("file_list.php");
}

?>