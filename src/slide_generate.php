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
 include_once("php/header.php");

function printSlide( $question, $image, $isSubQuestion, $parentQuestion = null){

	if($isSubQuestion){
		if($parentQuestion["themaSequence"] > 0){
			$title = htmlspecialchars($parentQuestion["themaSequence"] . ". " . ($question["sequence"] +1) );
		}
		else{
			$title = ($question["sequence"] +1);
		}
	}
	else if($question["isSpecial"]){
		$title =  ($question["sequence"] +1);
	}
	else{
		$title = htmlspecialchars($question["themaSequence"] . ". " .  $question["thema"]);
	}
	$img = "../images/" .  $image["quizId"] . '-' .$image["id"] . ".jpeg";

	$width = 10;
	$height = 15;
	if($image != null){
		$size = getimagesize("upload/" . $image["quizId"] . '-' . $image["id"] . ".jpeg");

		$width = $size[0];
		$height = $size[1];
		$ratio = $height / $width;

		if($ratio > 1){
			//  height is larger then width (portrait)
			$height = 15; // calculate with ratio
			$width= $height/$ratio;
		}
		else{
			// width larger then height (landscape)
			$height = 13; // calculate with ratio
			$width= $height/$ratio;
		}

		debug($filename, $size, $ratio, $width, $height);
	}
	$x = (28 - $width)/2;
	$y  = 4.5 + ((15.5-$height)/2);

	$xmlContent = '<draw:page draw:name="'. $title . '" draw:style-name="dp1" draw:master-page-name="Standaard" presentation:presentation-page-layout-name="AL2T19"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/>'.
	'<draw:frame presentation:style-name="pr4" draw:text-style-name="P1" draw:layer="layout" svg:width="25.199cm" svg:height="3.256cm" svg:x="1.4cm" svg:y="0.962cm"
	presentation:class="title"><draw:text-box><text:p text:style-name="P1">' . $title . '</text:p></draw:text-box></draw:frame>'.
	'<draw:frame draw:style-name="gr2" draw:text-style-name="P3" draw:layer="layout" svg:width="'.$width.'cm" svg:height="'.$height.'cm" svg:x="'.$x.'cm" svg:y="'.$y.'cm">' .
	'<draw:image			xlink:href="' . $img .'"		xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"><text:p text:style-name="P1"/></draw:image></draw:frame>';

	if($question["imageCount"] >1){
		$xmlContent.= '<draw:frame draw:style-name="gr3" draw:text-style-name="P4" draw:layer="layout" svg:width="2.161cm" svg:height="0.962cm" svg:x="0.339cm" svg:y="20.038cm"><draw:text-box>' .
		' <text:p text:style-name="P4">' . $image["sequence"] . "/" . sizeof($question["images"]) . '</text:p></draw:text-box></draw:frame>';
	}

	$xmlContent  .= '<presentation:notes draw:style-name="dp2"> <draw:page-thumbnail draw:style-name="gr1" draw:layer="layout" svg:width="14.848cm" svg:height="11.136cm" svg:x="3.075cm" svg:y="2.257cm" draw:page-number="2" presentation:class="page"/><draw:frame presentation:style-name="pr5" draw:text-style-name="P2" draw:layer="layout"  svg:x="2.1cm" svg:y="14.107cm" presentation:class="notes" presentation:placeholder="true"><draw:text-box/></draw:frame></presentation:notes></draw:page>';

	return $xmlContent;
}

$tmp = tempnam ( sys_get_temp_dir(), "zip");
copy("slides/default.odp", $tmp);
$zip = new ZipArchive();

$zip->open($tmp);

global $debug;
$debug = false;
$roundId = $_GET['roundId'];
$round = selectRoundById($roundId);

debug("round", $round);
$results = selectQuestionsForSlides($round["id"]);


$xmlContent =  '<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:smil="urn:oasis:names:tc:opendocument:xmlns:smil-compatible:1.0" xmlns:anim="urn:oasis:names:tc:opendocument:xmlns:animation:1.0" xmlns:field="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:field:1.0" office:version="1.1"><office:scripts/>';
$xmlContent .='<office:automatic-styles><style:style style:name="dp1" style:family="drawing-page"><style:drawing-page-properties presentation:background-visible="true" presentation:background-objects-visible="true" presentation:display-footer="true" presentation:display-page-number="false" presentation:display-date-time="true"/></style:style><style:style style:name="dp2" style:family="drawing-page"><style:drawing-page-properties presentation:display-header="true" presentation:display-footer="true" presentation:display-page-number="false" presentation:display-date-time="true"/></style:style><style:style style:name="gr1" style:family="graphic"><style:graphic-properties style:protect="size"/></style:style><style:style style:name="gr2" style:family="graphic" style:parent-style-name="standard"><style:graphic-properties draw:stroke="none" draw:fill="none" draw:textarea-horizontal-align="center" draw:textarea-vertical-align="middle" draw:color-mode="standard" draw:luminance="0%" draw:contrast="0%" draw:gamma="100%" draw:red="0%" draw:green="0%" draw:blue="0%" fo:clip="rect(0cm, 0cm, 0cm, 0cm)" draw:image-opacity="100%" style:mirror="none"/></style:style><style:style style:name="gr3" style:family="graphic" style:parent-style-name="standard"><style:graphic-properties draw:stroke="none" svg:stroke-color="#000000" draw:fill="none" draw:fill-color="#008000" draw:textarea-horizontal-align="left" draw:auto-grow-height="true" draw:auto-grow-width="true" fo:min-height="0cm" fo:min-width="0cm"/></style:style><style:style style:name="pr1" style:family="presentation" style:parent-style-name="Standaard-title"><style:graphic-properties draw:fill-color="#ffffff" draw:auto-grow-height="true" fo:min-height="3.507cm"/></style:style><style:style style:name="pr2" style:family="presentation" style:parent-style-name="Standaard-subtitle"><style:graphic-properties draw:fill-color="#ffffff" fo:min-height="13.609cm"/></style:style><style:style style:name="pr3" style:family="presentation" style:parent-style-name="Standaard-notes"><style:graphic-properties draw:fill-color="#ffffff" fo:min-height="13.114cm"/></style:style><style:style style:name="pr4" style:family="presentation" style:parent-style-name="Standaard-title"><style:graphic-properties draw:fill-color="#ffffff" fo:min-height="3.256cm"/></style:style><style:style style:name="pr5" style:family="presentation" style:parent-style-name="Standaard-notes"><style:graphic-properties draw:fill-color="#ffffff" draw:auto-grow-height="true" fo:min-height="13.365cm"/></style:style><style:style style:name="P1" style:family="paragraph"><style:paragraph-properties fo:margin-left="0cm" fo:margin-right="0cm" fo:text-indent="0cm"/></style:style><style:style style:name="P2" style:family="paragraph"><style:paragraph-properties fo:margin-left="0.6cm" fo:margin-right="0cm" fo:text-indent="-0.6cm"/></style:style><style:style style:name="P3" style:family="paragraph"><style:paragraph-properties fo:text-align="center"/></style:style><style:style style:name="P4" style:family="paragraph"><style:text-properties fo:color="#280099"/></style:style><style:style style:name="T1" style:family="text"><style:text-properties fo:color="#280099"/></style:style><text:list-style style:name="L1"><text:list-level-style-bullet text:level="1" text:bullet-char="●"><style:list-level-properties/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="2" text:bullet-char="●"><style:list-level-properties text:space-before="0.6cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="3" text:bullet-char="●"><style:list-level-properties text:space-before="1.2cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="4" text:bullet-char="●"><style:list-level-properties text:space-before="1.8cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="5" text:bullet-char="●"><style:list-level-properties text:space-before="2.4cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="6" text:bullet-char="●"><style:list-level-properties text:space-before="3cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="7" text:bullet-char="●"><style:list-level-properties text:space-before="3.6cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="8" text:bullet-char="●"><style:list-level-properties text:space-before="4.2cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="9" text:bullet-char="●"><style:list-level-properties text:space-before="4.8cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet><text:list-level-style-bullet text:level="10" text:bullet-char="●"><style:list-level-properties text:space-before="5.4cm" text:min-label-width="0.6cm"/><style:text-properties fo:font-family="StarSymbol" style:use-window-font-color="true" fo:font-size="45%"/></text:list-level-style-bullet></text:list-style></office:automatic-styles>';

$xmlContent .= '<office:body><office:presentation>
<draw:page draw:name="1" draw:style-name="dp1" draw:master-page-name="Standaard" presentation:presentation-page-layout-name="AL1T0"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/>
<draw:frame presentation:style-name="pr1" draw:text-style-name="P1" draw:layer="layout" svg:width="25.199cm" svg:height="3.507cm" svg:x="1.4cm" svg:y="0.837cm" presentation:class="title" presentation:placeholder="true"><draw:text-box/></draw:frame><draw:frame presentation:style-name="pr2" draw:text-style-name="P1" draw:layer="layout" svg:width="25.199cm" svg:height="13.609cm" svg:x="1.4cm" svg:y="5.039cm" presentation:class="subtitle"><draw:text-box> <text:p text:style-name="P1">' . $round["description"] . '</text:p> </draw:text-box></draw:frame><presentation:notes draw:style-name="dp2"> <draw:page-thumbnail draw:style-name="gr1" draw:layer="layout" svg:width="14.848cm" svg:height="11.136cm" svg:x="3.075cm" svg:y="2.257cm" draw:page-number="1" presentation:class="page"/><draw:frame presentation:style-name="pr3" draw:text-style-name="P2" draw:layer="layout" svg:width="16.799cm" svg:height="13.114cm" svg:x="2.1cm" svg:y="14.107cm" presentation:class="notes" presentation:placeholder="true"><draw:text-box/></draw:frame></presentation:notes></draw:page>';

	foreach($results as $title => $questionGroup){
		foreach($questionGroup["questions"] as $question){
			$images = $question['images'];
			debug("images", $images);

			$imgSeq = 1;
			foreach($images as $img){
				$img["sequence"] = $imgSeq;
				$xmlContent .= printSlide($question, $img, false);
				$imgSeq++;
			}
			if($question['childQuestions'] > 0){
				$seq = 0;
				foreach($question["children"]  as $subQuestion){
					$images = $subQuestion['images'];
					$subQuestion["sequence"] = $seq;
					$seq++;

					$imgSeq = 1;
					foreach($images as $img){
						$img["sequence"] = $imgSeq;
						$xmlContent .= printSlide($subQuestion, $img, true, $question);
						$imgSeq++;
					}
					$imgSeq++;
				}
			}
		}
	}
$xmlContent .= '<presentation:settings presentation:mouse-visible="false"/></office:presentation></office:body></office:document-content>';

if($debug){
	print $xmlContent;
	return;
}

$zip->addFromString("content.xml", $xmlContent);
$zip->close();

header("Content-Type: application/vnd.oasis.opendocument.presentation");
header("Content-disposition: attachment; filename=" . $round["description"] .".odp");
readfile($tmp);

unlink($tmp);
?>
