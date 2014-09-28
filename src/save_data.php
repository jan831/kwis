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
	$debug = true;

	debug("save_data.php _REQUEST", $_REQUEST);
	if( isset($_REQUEST["delete"]) && $_REQUEST["delete"] == 1 ){
		$_REQUEST["detail"]["action"] = "delete";
	}
	if(isset($_REQUEST["detail"])){
		$detail = $_REQUEST["detail"];
		if(isset($detail["action"])){
			$rows = updateWithAction($detail);
			echo "rows: " + $rows;
		}
		else{
			$id = update($detail);
			echo "id: " . $id;
		}
	}
	else{
		foreach($_REQUEST["data"] as $detail){
			if(isset($detail["action"])){
				$rows = updateWithAction($detail);
				echo "rows: " + $rows;
			}
			else{
				$id = update($detail);
				echo "id: " . $id . ",";
			}
		}
	}
?>