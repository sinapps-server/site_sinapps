<?php

$pdo = myPDO::getInstance();

$stmt = $pdo->prepare(<<<SQL
	SELECT *
	FROM TYPE_PRESTATION
SQL
);

$stmt->execute();

$tableau = $stmt->fetchAll();

function createBox($i)
{
	return <<< HTML
	<div class = "box_container">
			<div class = "presta box1">
				<div class = "th3">{$tableau['nom_prestation'][$i]}</div>
				<div class = "img_presta">
					<img id="logo_ordi" src="{$tableau['path_prestation'][$i]}" alt="logo1"/>
				</div>
				<div class = "border_logo"></div>
				<div class = "txt_box">{$tableau['description_prestation'][$i]}</div>
				<div class = "more">
					<a href="">En savoir plus &rsaquo;</a>
				</div>
			</div>
		</div>
HTML;
}