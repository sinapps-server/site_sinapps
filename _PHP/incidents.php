<?php

require_once 'webpage.class.php';
require_once 'Personne.class.php';
require_once 'Incident.class.php';

$p = new WebPage("Incidents - Sinapp's");


try {
    // Lecture depuis les données de session
    $user = Personne::createFromSession();
    switch ($user->getIdHabilitation()) {
		// Si l'utilisateur est un administrateur
		case 1:
		$droit = 1;
		break;
		//Si l'utilisateur n'est qu'un membre lambda sans droits
		case 2:
		$droit = 2;
		break;
	}

	$p->appendContent(<<<HTML
		<div class="content">
			<div class = "th1">Liste des incidents</div>
HTML
	);

	if ($droit == 1) {
		$p->appendContent(Incident::getAllIncident());
	}
	else {
		$p->appendContent(Incident::getIncidentByPers($user->getIdPers()));
	}

	$p->appendContent(<<<HTML
			<div class="box1">
				<div class="row">
					<button onclick="location.href='./auth.php?action=logout'">Déconnexion</button>
				</div>
			</div>
		</div>
HTML
	);
}

catch (notInSessionException $e) {
    // Pas d'utilisateur connecté
    header("Location: ./connexion.php") ;
    die() ;
}

echo $p->toHTML();