<?php 

require_once 'webpage.class.php';
require_once 'myPDO.include.php';

$pdo = myPDO::getInstance();

$stmt = $pdo->prepare(<<<SQL
	SELECT nom_personne AS nom_pers, prenom_personne AS p_pers, image_personne AS img_pers, emploi_personne AS emp_pers
	FROM PERSONNE
	WHERE id_entreprise_pers = 1
SQL
);

$stmt->execute();

//var_dump($sinapps['img_pers']);

$p = new WebPage("Notre équipe - Sinapp's");

$p->appendContent(<<<HTML
	<div class="content">	
		<div class="infos_equipe">
			<div class="row">
				<div class="th1">
					L'équipe
				</div>
				<div class="th2">Qui sommes-nous...</div>
			</div>
			<div class="row">
HTML
);


while (($sinapps = $stmt->fetch()) !== false) {

	$nom = strtoupper($sinapps['nom_pers']);
	$prenom = ucfirst($sinapps['p_pers']);
	$p->appendContent(<<<HTML
				<div class="container">
					<div class="membre_equipe">
						<div class="image_membre">
							<img src="{$sinapps["img_pers"]}" alt="Image membre" class="path_img_membre">
						</div>
						<div class="barre_membre"></div>
						<div class="infos_membre">
							{$nom} {$prenom}
						</div>
						<div class="poste_membre">
							{$sinapps['emp_pers']}
						</div>	
					</div>
				</div>
HTML
	);
}

/*
$p->appendContent(<<<HTML					
			</div>	
		</div>	
	</div>
HTML
);*/

echo $p->toHTML();