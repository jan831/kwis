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

$themas = selectThemas();
$rounds = selectRoundsForQuestionSelector();
$formBuilder = new SelectorFormBuilder($selector, "selector");

 ?>
<form method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" >
<div id="selectorContainer">
<div id="toggleSelector" class="clickeable"> zoekparameters</div>
	<table id="selectorTable">
	<tr>
		<td width="75%" colspan="3">
			<?php
				$formBuilder->getSubmit("search", "Zoeken");
				$formBuilder->getSubmit("reset", "Zoekparameters verwijderen");
				$formBuilder->getHidden("table");?>
		</td>
		<td width="25%" align="right">
			<a href="<?php echo $_SERVER['REQUEST_URI'];?>" id="selectorLink" class="hasToolTip">link</a>
			<span id="tooltip_selectorLink" class="hidden">link naar huidige zoek criteria, kan handig zijn om te mailen of als bookmark</span>
		</td>
	</tr>
	<tr>
		<td><?php $formBuilder->getLabel("param.notReady", "toon alleen vragen die niet klaar zijn"); ?></td>
		<td><?php $formBuilder->getCheckBox("param.notReady"); ?></td>

		<td><?php $formBuilder->getLabel("param.hasRound", "toon alleen toegewezen aan rondes"); ?></td>
		<td><?php $formBuilder->getCheckBox("param.hasRound"); ?></td>

	</tr>
	<tr>
		<td>
			<?php $formBuilder->getLabel("param.taskCategory", "toon vragen <i>met</i> de volgende notities"); ?>
		</td><td>
			<?php $formBuilder->getSelect("param.taskCategory",$taskCategories, array("multiple" => true)); ?>
		</td>
		<td> <?php $formBuilder->getLabel("param.thema", "thema"); ?></td>
		<td>
			<?php $formBuilder->getSelect("param.thema",$themas, array("multiple" => true)); ?>
		</td>
	</tr>
	<tr>
		<td><?php $formBuilder->getLabel("param.taskCategoryNot","toon vragen <i>zonder</i> de volgende notities"); ?></td>
		<td>
			<?php $formBuilder->getSelect("param.taskCategoryNot",$taskCategories, array("multiple" => true)); ?>
		</td>
		<td><?php $formBuilder->getLabel("param.round","Ronde"); ?></td>
		<td>
			<?php $formBuilder->getSelect("param.round",$rounds, array("multiple" => true)); ?>
		</td>
	</tr>
	<tr>
		<td><?php $formBuilder->getLabel("param.deleted", "toon verwijderde vragen"); ?></td>
		<td><?php $formBuilder->getCheckBox("param.deleted"); ?></td>

		<td><?php $formBuilder->getLabel("order", "sorteer op"); ?></td>
		<td>
			<?php $order = array();
					$order[] = array("id"=>"thema_round", "description"=> "thema, ronde");
					$order[] = array("id"=>"round_thema", "description"=> "ronde, thema");
					$order[] = array("id"=>"answer", "description"=> "antwoord");
					$order[] = array("id"=>"modificationDate", "description"=> "laatst gewijzigd");
					$order[] = array("id"=>"correctPercentage_difficulty", "description"=> "juiste antwoorden, moeilijkheid");
					$order[] = array("id"=>"difficulty_correctPercentage", "description"=> "moeilijkheid, juiste antwoorden");


			 $formBuilder->getSelect("order",$order, array("multiple" => false)); ?>

		</td>
	<tr>
		<td><?php $formBuilder->getLabel("param.noImages", "toon alleen vragen zonder afbeeldingen"); ?></td>
		<td colspan="3"><?php $formBuilder->getCheckBox("param.noImages"); ?></td>
	</tr>
	</table>
</div>
</form>
