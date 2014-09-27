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

function getSelector($name,  $paramSelector, $reset, $default){
	if(isset($reset)){
		$_SESSION['saved'][$name] = $default;
		return $default;
	}

	if(isset($paramSelector)){
		$_SESSION['saved'][$name] = $paramSelector;
		return $paramSelector;
	}

	if(isset($_SESSION['saved'][$name])){
		return $_SESSION['saved'][$name];
	}
	$_SESSION['saved'][$name] = $default;
	return $default;

}

function getDefaultSelectorForList($paramSelector, $reset){

	$default = Array();
	$default['order'] = 'thema_round';
	$default['table'] = "question";

	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}

function getDefaultSelectorForListTodo($paramSelector, $reset){

	$default = Array();
	$default['order'] = 'thema_round';
	$default['table'] = "question";
	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}

function getDefaultSelectorForListDelete($paramSelector, $reset){

	$default = Array();
	$default['order'] = 'thema_round';
	$default['table'] = "question";
	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}

function getDefaultSelectorForListAnalysis($paramSelector, $reset){

	$default = Array();
	$default['order'] = 'difficulty_correctPercentage';
	$default['param']['hasRound'] = 'hasRound';
	$default['table'] = "question";
	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}

function getDefaultSelectorForListAnalysisInput($paramSelector, $reset){

	$default = Array();
	$default['order'] = 'round_thema';
	$default['param']['hasRound'] = 'hasRound';
	$default['table'] = "question";
	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}


function getDefaultSelectorForListPrint($paramSelector, $reset){
	$default = Array();
	$default['ui']['showThema'] = true;
	$default['ui']['showQuestion'] = true;
	$default['ui']['showAnswer'] = true;
	$default['param']['hasRound'] = 'hasRound';

	$default['order'] = 'round_thema';
	$default['table'] = "question";

	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}


function getDefaultSelectorForTodo($paramSelector, $reset){

	$default = Array();
	$default['order'] = 'taskCategory_done';
	$default['param']['notReady'] = 'notReady';
	$default['table'] = "task";

	return getSelector(__FUNCTION__,$paramSelector, $reset, $default );
}



?>
