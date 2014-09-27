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

abstract class AbstractFormBuilder{
   // property declaration
    public $object;
	public $param;
	public $identifier;

	function __construct($object, $param ="%s", $identifier = ''){
		$this->object = $object;
		$this->param = $param;
		$this->identifier = $identifier;

	}

	public function getLabel($field, $text, $options = Array()){
		$class = coalesce(@$options["class"], "");
		$id="";
		if(isset($options["id"])){
			$id = " id=\"" . $options["id"] . "\" ";
		}
		echo "<label for=\"" . $this->getId($field) . "\"  class=\"". $class . "\"  $id >$text</label>\n";
	}

	public function getCheckBox($field, $options = Array()){
		$value = $this->getValue($field);
		$selected =  (isset($value)?'checked=checked':"");
		$class = coalesce(@$options["class"], "");
		if($value == '')
			$value = "1";
		echo "<input type=\"checkbox\" name=\"" . $this->getName($field) . "\"  value=\"$value\" id=\"" . $this->getId($field) . "\"  $selected class=\"$class\" >\n";
	}

	public function getSelect($field, $list, $options = Array()){
		$multiple = coalesce(@$options["multiple"], false);
		$param = coalesce(@$options["param"], $this->param);

		$title ="";
		if(isset($options["title"])){
			$title= ' title="'. $options["title"] .'"';
		}

		$isMultiple  = ($multiple?"multiple":"");
		$nameMultiple = ($multiple?"[]":"");


		echo "<select name=\"" . $this->getName($field) . $nameMultiple . "\" id=\"" . $this->getId($field) . "\" $isMultiple $title>\n";
		$value = $this->getValue($field);
		foreach($list as $item){
			if(isset($value)){
				if($multiple)
					$selected = in_array($item['id'], $value)?"selected":"";
				else
					$selected = ($item['id'] == $value?"selected":"");
			}
			else
				$selected = '';
			echo '<option value = "' . $item['id'] . '" ' . $selected . '>' . $item['description'] . "</option>\n";
		}
		echo "</select>\n";
	}

	public function getHidden($field, $defaultValue = null){
		$value = $this->getValue($field);
		$value = coalesce($value, $defaultValue);
		echo "<input type=\"hidden\" name=\"" . $this->getName($field) . "\"  value=\"$value\" id=\"" . $this->getId($field) . "\"  >\n";
	}

	public function getSubmit($name, $label){
		echo "<input type=\"submit\" value=\"$label\" name=\"$name\"/>\n";
	}

	public function getText($field, $options = null){
		$value = $this->getValue($field);
		$id = $this->getId($field);
		$listRef = "";
		if($options != null && is_array($options)){
			$listRef = " autocomplete=\"off\" list=\"" .  $id . "_list\" ";
			echo "<datalist id='" . $id . "_list'>";
			foreach($options as $option){
				echo "<option value= \"$option\">";
			}
			echo "</datalist>";
		}

		echo "<input type=\"text\" name=\"" . $this->getName($field) . "\"  value=\"$value\" id=\"" . $id . "\" $listRef >\n";
	}

	public function getPassword($field){
		$value = $this->getValue($field);
		echo "<input type=\"password\" name=\"" . $this->getName($field) . "\"  value=\"$value\" id=\"" . $this->getId($field) . "\" >\n";
	}

	public function getTextArea($field, $rows, $cols = "80"){
		$value = $this->getValue($field);
		echo "<textarea name=\"" . $this->getName($field) . "\" id=\"" . $this->getId($field) . "\" rows=\"$rows\" cols=\"$cols\">$value</textarea>\n";
	}

	public function getValue($field){
		$path = explode(".", $field);
		$value = $this->object;
		foreach($path as $element){
			$value = @$value[$element];
		}
		return $value;
	}

	public function getName($field){
		$path = explode(".", $field);
		$joined = "[" . implode("][", $path) . "]";
		return $this->param . $joined;

	}

	public function getId($field){
		return str_replace('.', "_", $this->identifier . "." . $this->param . "." . $field);
	}
}

class SelectorFormBuilder extends AbstractFormBuilder
{

}

class DetailFormBuilder extends AbstractFormBuilder{
	function __construct($object, $table, $param ="detail"){
		 parent::__construct(array("table"=>$table, "param"=>$object, "id"=>$object["id"]), $param, $table);
	}
}
?>
