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
	require_once("php/functions.php");
	session_start();

	global $debug;
	$debug = false;
	debug($_POST);

	debug($_FILES);

	if($_POST['delete'] == 1){
		$imageId = $_POST['id'];
		$questionId = $_POST['questionId'];
		$update = Array();
		$update['table'] = 'image';
		$update['id'] = $imageId;
		$update['action'] = "delete";
		updateWithAction($update);
		unlink( "upload/" . getQuizId() . '-' . $imageId . '.jpeg');
		redirect("question_detail.php?id=$questionId#images");
	}
	else{
		if(isNotBlank ($_POST['url'])){
			$filename = tempnam(sys_get_temp_dir(), 'qz');
			file_put_contents($filename, file_get_contents($_POST['url']));
			$image_type = mime_content_type ($filename);
			debug("filename: " . $filename);
		}
		else{
			$filename = $_FILES["image"]["tmp_name"];
			$image_type = $_FILES["image"]["type"];
			
		}
		
		
		debug("image_type", $image_type);
		if( $image_type == "image/jpeg" || $image_type == "image/pjpeg" ) {
			 debug("doing jpg $filename");
			 $image = imagecreatefromjpeg($filename) or die ("imagecreatefromjpeg");
		} elseif( $image_type == "image/gif" ) {
			debug("doing gif $filename");
			$image = imagecreatefromgif($filename) or die ("imagecreatefromjpeg");
		} elseif( $image_type == "image/png" || $image_type == "image/x-png" ) {
			debug("doing png $filename");
			$image = imagecreatefrompng($filename) or die ("imagecreatefromjpeg");
		}
		else{
			debug($image_type) ;
			die("unkown image time $image_type");
		}
	    debug("image", $image);

		$questionId = $_POST['questionId'];

		if($image && $questionId > 0){
			$update = Array();
			$update['param']['questionid'] = $questionId;
			$update['param']['sequence'] = 1+ selectMaxImageSequenceForQuestion($questionId);
			$update['table'] = 'image';
			$update['id'] = -1;
			$id = update($update);

			$newFilename = "upload/" . getQuizId() . '-' . $id . '.jpeg';
			if( $image_type == "image/jpeg" ) {
				if(isNotBlank ($_POST['url'])){
					debug("moving remote file,  not saving the image: " + $filename);
					rename($filename, $newFilename) or die ('cannot move tmp file');
				}
				else {
				debug("moving uploaded file,  not saving the image: " . $filename);
				debug("cwd: " . getCwd());
				move_uploaded_file($filename, $newFilename )
				 or die('cannot move_uploaded_file ');
					
				}
				
			}
			else{
				debug("saving file $id ");
				imagejpeg ($image, $newFilename , 85)
					or die('cannot imagejpeg ');
			}

			redirect("question_detail.php?id=$questionId#images");
		}
		else{
			die("image upload error");
			//TODO
		}
	}
?>