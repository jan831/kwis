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
 <div class="panel panel-default selector">
	<div class="panel-heading"><h3 class="panel-title">Zoeken</h3></div>
	<div class="panel-body">
		<form method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]; ?>" >
			<div class="row">
				<div class="col-md-11 text-center">
					<?php
						$formBuilder->getSubmit("search", "Zoeken");
						$formBuilder->getSubmit("reset", "Zoekparameters verwijderen");
						$formBuilder->getHidden("table");?>
				</div>
				<div class="col-md-1" >
					<a href="<?php echo $_SERVER['REQUEST_URI'];?>" id="selectorLink" class="hasToolTip" title="link naar huidige zoek criteria, kan handig zijn om te mailen of als bookmark">link</a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("param.notReady", "toon alleen vragen die niet klaar zijn"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getCheckBox("param.notReady"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getLabel("param.hasRound", "toon alleen toegewezen aan rondes"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getCheckBox("param.hasRound"); ?></div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("param.taskCategory", "toon vragen <i>met</i> de volgende notities"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getSelect("param.taskCategory",$taskCategories, array("multiple" => true)); ?></div>
				<div class="col-md-3"> <?php $formBuilder->getLabel("param.thema", "thema"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getSelect("param.thema",$themas, array("multiple" => true)); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("param.taskCategoryNot","toon vragen <i>zonder</i> de volgende notities"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getSelect("param.taskCategoryNot",$taskCategories, array("multiple" => true)); ?></div>
				<div class="col-md-3"><?php $formBuilder->getLabel("param.round","Ronde"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getSelect("param.round",$rounds, array("multiple" => true)); ?></div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("param.deleted", "toon verwijderde vragen"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getCheckBox("param.deleted"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getLabel("order", "sorteer op"); ?></div>
				<div class="col-md-3">
					<?php $order = array();
							$order[] = array("id"=>"thema_round", "description"=> "thema, ronde");
							$order[] = array("id"=>"round_thema", "description"=> "ronde, thema");
							$order[] = array("id"=>"answer", "description"=> "antwoord");
							$order[] = array("id"=>"modificationDate", "description"=> "laatst gewijzigd");
							$order[] = array("id"=>"correctPercentage_difficulty", "description"=> "juiste antwoorden, moeilijkheid");
							$order[] = array("id"=>"difficulty_correctPercentage", "description"=> "moeilijkheid, juiste antwoorden");
		
		
					 $formBuilder->getSelect("order",$order, array("multiple" => false)); ?>
		
				</div>
			</div>
			<div class="row">
				<div class="col-md-3"><?php $formBuilder->getLabel("param.noImages", "toon alleen vragen zonder afbeeldingen"); ?></div>
				<div class="col-md-3"><?php $formBuilder->getCheckBox("param.noImages"); ?></div>
			</div>
		</form>
	</div>
</div>
