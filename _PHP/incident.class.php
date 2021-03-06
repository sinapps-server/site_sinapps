<?php

require_once 'myPDO.include.php';

class Incident
{
	private $id_incident = null;
	private $nom_incident = null;
	private $description_incident = null;
	private $id_personne = null;
	private $id_type_incident = null;
	private $date_incident = null;
	private $statut_incident = null;
	private $id_parc_incident = null;
	private $id_entreprise_incident = null;
	
	private function __construct()
	{
		
	}

	public function getIdIncident()
	{
		return $this->id_incident;
	}

	public function getNomIncident()
	{
		return $this->nom_incident;
	}

	public function getDescriptionIncident()
	{
		return $this->description_incident;
	}

	public function getIdPersonne()
	{
		return $this->id_personne;
	}

	public function getIdType()
	{
		return $this->id_type_incident;
	}

	public function getDateIncident() {
		return $this->date_incident;
	}

	public function getStatutIncident() {
		return $this->statut_incident;
	}

	public function getIdParcIncident() {
		return $this->id_parc_incident;
	}

	public function getIdEntpIncident() {
		return $this->id_entreprise_incident;
	}

	public function setStatutIncident($valeur) {
		$this->statut_incident = $valeur;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE INCIDENT
			SET statut_incident = :val
			WHERE id_incident = :id_inc
SQL
		);	
		$stmt->bindValue(":val", $valeur);
		$stmt->bindValue(":id_inc", $this->id_incident);
		$stmt->execute();		
	}

	public function setDateIncident($date) {
		$this->date_incident = $date;
	}

	public function setDescriptionIncident($desc)
	{
		$this->description_incident = $desc;
	}

	public function setTypeIncident($type)
	{
		$this->id_type_incident = $type;
	}

	public static function getNbIncidents() 
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT COUNT(*) AS nb
			FROM INCIDENT
SQL
		);
		$stmt->execute();
		$count = $stmt->fetch();
		return $count['nb'];
	}

	public static function getNbIncidentsByPers($id) 
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT COUNT(*) AS nb
			FROM INCIDENT
			WHERE id_personne = :id
SQL
		);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		$count = $stmt->fetch();
		return $count['nb'];
	}
	
		public static function getNbIncidentsActifsByPers($id) 
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT COUNT(*) AS nb
			FROM INCIDENT
			WHERE id_personne = :id
			AND statut_incident = 1
SQL
		);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		$count = $stmt->fetch();
		return $count['nb'];
	}

	public static function getNbIncidentsActifs() 
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT COUNT(*) AS nb
			FROM INCIDENT
			WHERE statut_incident = 1
SQL
		);
		$stmt->execute();
		$count = $stmt->fetch();
		return $count['nb'];
	}

	public static function getNbIncidentsByIdEntp($id) 
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT COUNT(*) AS nb
			FROM INCIDENT
			WHERE id_entreprise_incident = :id_entp
SQL
		);
		$stmt->bindValue(":id_entp", $id);
		$stmt->execute();
		$count = $stmt->fetch();
		return $count['nb'];
	}

		public static function getNbIncidentsActifsByIdEntp($id) 
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT COUNT(*) AS nb
			FROM INCIDENT
			WHERE statut_incident = 1
			AND id_entreprise_incident = :id_entp
SQL
		);
		$stmt->bindValue(":id_entp", $id);
		$stmt->execute();
		$count = $stmt->fetch();
		return $count['nb'];
	}
	
	/*
	Récupération de tous les incidents en fonction de l'ID d'une personne
	*/
	public static function getIncidentByPers($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM INCIDENT
			WHERE id_personne = :id
			ORDER BY statut_incident, date_incident
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		$array = $stmt->fetchAll();
		$html = <<<HTML
			<div class = "box1">
HTML;
		foreach ($array as $ligne)
		{
			$i = $ligne->getIdIncident();
			$status = "";

			$type_inc = Type_incident::createTypeIncidentFromId($ligne->getIdType());
			$parc = Parc::createParcFromId($ligne->getIdParcIncident());
			switch($ligne->getStatutIncident()) {
				case 0 : 
					$status = "<div class=\"status nt\">Non traité (Type {$type_inc->getDescType()})</div>";
					break;
				case 1 :
					$status = "<div class=\"status ec\">En cours de traitement (Type {$type_inc->getDescType()})</div>";
					break;
				case 2 :
					$status = "<div class=\"status t\">Résolu (Type {$type_inc->getDescType()})</div>";
					break;		
			}

			$html.=<<<HTML
				<div class = "row bordure fond">
					<div class = "th2">{$ligne->getNomIncident()}</div>
					<div class = "th3">{$ligne->getDescriptionIncident()}</div>
					{$status}
					<div class = "coupable">Déclaré le {$ligne->getDateIncident()} (Parc : {$parc->getNomParc()})</div>
					<div class = "boutons_objet">
						<button type="submit" class="button" onclick="effacer({$ligne->getIdIncident()})">Supprimer</button>					
						<button onclick="location.href='./incident.php?i={$i}'" type="submit" class="button">Voir en détail</button>
					</div>
				</div>
HTML;
		}
		$html.=<<<HTML
			</div>
HTML;
		$html .="<script>
			function effacer(num)
			{
				var confirm = window.confirm(\"Voulez-vous supprimer cet incident ?\");
				if (confirm)
					document.location.href=\"./incidents.php?i=\" + num + \"&delete=yes\";
			};
		</script>";

		return $html;
	}
	

	/*
	Récupération de tous les incidents de toutes les personnes
	*/
	public static function getAllIncident()
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM INCIDENT
			ORDER BY statut_incident, date_incident
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->execute();
		$array = $stmt->fetchAll();
		$html = <<<HTML
			<div class = "box1">
HTML;
		foreach ($array as $ligne)
		{
			$i = $ligne->getIdIncident();
			$status = "";
			$type_inc = Type_incident::createTypeIncidentFromId($ligne->getIdType());
			$parc = Parc::createParcFromId($ligne->getIdParcIncident());

			switch($ligne->getStatutIncident()) {
				case 0 : 
					$status = "<div class=\"status nt\">Non traité (Type {$type_inc->getDescType()})</div>";
					break;
				case 1 :
					$status = "<div class=\"status ec\">En cours de traitement (Type {$type_inc->getDescType()})</div>";
					break;
				case 2 :
					$status = "<div class=\"status t\">Résolu (Type {$type_inc->getDescType()})</div>";
					break;		
			}
			try
			{
				$pers = Personne::createPersFromId($ligne->getIdPersonne());
			}
			catch(Exception $e)
			{
				$stmt = myPDO::getInstance()->prepare(<<<SQL
					UPDATE INCIDENT
					SET id_personne = null
					WHERE id_personne = :val
SQL
				);
				$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
				$stmt->bindValue(":val", $ligne->getIdPersonne());
				$stmt->execute();
				$ligne->id_personne = null;
				
				$pers = null;
				$entp = null;
			}
			
			if(!is_null($pers) && $pers instanceof Personne) {
				if(!is_null($pers->getIdEntpPers())) {
					$entp = Entreprise::createEntrepriseFromId($pers->getIdEntpPers());
				}
			}
			
			$txt = $ligne->getDescriptionIncident();
			/*
			if(strlen($txt) > 128)
				$txt = substr($txt, 0, 128);
			*/
			$description = self::cleanCut($ligne->getDescriptionIncident(), 150);

			if(is_null($pers))
				$prenom = 'Inconnu';
			else
				$prenom = $pers->getPrenomPers();

			if(is_null($pers))
				$nom = '';
			else
				$nom = $pers->getNomPers();

			if(is_null($entp))
				$ent = '';
			else
				$ent = $entp->getNomEntreprise();


			$html.=<<<HTML
				<div class = "row bordure fond">
					<div class = "th2">{$ligne->getNomIncident()}</div>
					<div class = "th3">{$description}</div>
					{$status}
					<div class = "coupable">{$ligne->getDateIncident()} par {$prenom} {$nom} ({$ent} : Parc {$parc->getNomParc()})</div>
					<div class = "boutons_objet">
						<button type="submit" class="button" onclick="effacer({$ligne->getIdIncident()})">Supprimer</button>					
						<button onclick="location.href='./incident.php?i={$i}'" type="submit" class="button">Voir en détail</button>
					</div>
				</div>
HTML;
		}
		$html.=<<<HTML
			</div>
HTML;

		$html .="<script>
			function effacer(num)
			{
				var confirm = window.confirm(\"Voulez-vous supprimer cet incident ?\");
				if (confirm)
					document.location.href=\"./incidents.php?i=\" + num + \"&delete=yes\";
			};
		</script>";
		
		return $html;
	}


		/*
	Récupération de tous les incidents pour une entreprise
	*/
	public static function getIncidentsByIdEntp($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM INCIDENT
			WHERE id_entreprise_incident = :id_entp
			ORDER BY statut_incident, date_incident
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id_entp", $id);
		$stmt->execute();
		$array = $stmt->fetchAll();
		$html = <<<HTML
			<div class = "box1">
HTML;
		foreach ($array as $ligne)
		{
			$i = $ligne->getIdIncident();
			$status = "";
			$type_inc = Type_incident::createTypeIncidentFromId($ligne->getIdType());
			$parc = Parc::createParcFromId($ligne->getIdParcIncident());

			switch($ligne->getStatutIncident()) {
				case 0 : 
					$status = "<div class=\"status nt\">Non traité (Type {$type_inc->getDescType()})</div>";
					break;
				case 1 :
					$status = "<div class=\"status ec\">En cours de traitement (Type {$type_inc->getDescType()})</div>";
					break;
				case 2 :
					$status = "<div class=\"status t\">Résolu (Type {$type_inc->getDescType()})</div>";
					break;		
			}
			try
			{
				$pers = Personne::createPersFromId($ligne->getIdPersonne());
			}
			catch(Exception $e)
			{
				$stmt = myPDO::getInstance()->prepare(<<<SQL
					UPDATE INCIDENT
					SET id_personne = null
					WHERE id_personne = :val
SQL
				);
				$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
				$stmt->bindValue(":val", $ligne->getIdPersonne());
				$stmt->execute();
				$ligne->id_personne = null;
				
				$pers = null;
				$entp = null;
			}
			
			if(!is_null($pers) && $pers instanceof Personne) {
				if(!is_null($pers->getIdEntpPers())) {
					$entp = Entreprise::createEntrepriseFromId($pers->getIdEntpPers());
				}
			}
			
			$txt = $ligne->getDescriptionIncident();
			/*
			if(strlen($txt) > 128)
				$txt = substr($txt, 0, 128);
			*/
			$description = self::cleanCut($ligne->getDescriptionIncident(), 150);

			if(is_null($pers))
				$prenom = 'Inconnu';
			else
				$prenom = $pers->getPrenomPers();

			if(is_null($pers))
				$nom = '';
			else
				$nom = $pers->getNomPers();

			if(is_null($entp))
				$ent = '';
			else
				$ent = $entp->getNomEntreprise();


			$html.=<<<HTML
				<div class = "row bordure fond">
					<div class = "th2">{$ligne->getNomIncident()}</div>
					<div class = "th3">{$description}</div>
					{$status}
					<div class = "coupable">{$ligne->getDateIncident()} par {$prenom} {$nom} ({$ent} : Parc {$parc->getNomParc()})</div>
					<div class = "boutons_objet">
						<button type="submit" class="button" onclick="effacer({$ligne->getIdIncident()})">Supprimer</button>					
						<button onclick="location.href='./incident.php?i={$i}'" type="submit" class="button">Voir en détail</button>
					</div>
				</div>
HTML;
		}
		$html.=<<<HTML
			</div>
HTML;

		$html .="<script>
			function effacer(num)
			{
				var confirm = window.confirm(\"Voulez-vous supprimer cet incident ?\");
				if (confirm)
					document.location.href=\"./incidents.php?i=\" + num + \"&delete=yes\";
			};
		</script>";
		
		return $html;
	}

	/*
	Récupération de tous les incidents liés à une entreprise
	*/

	/*
	Récupération des actions effectuées sur un incident
	*/
	public static function getActionsIncident($id_incident)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM ACTION
			WHERE id_incident = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id_incident);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	/*
	Permet la déclaration d'un incident
	*/
	public static function createIncident($nom_incident, $description = "", $id_type_incident, $id_parc_incident, $id_entp)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			INSERT INTO INCIDENT (nom_incident, description_incident, id_personne, id_type_incident, date_incident, id_parc_incident, id_entreprise_incident)
			VALUES (:nom, :description, :id_pers, :id_type, :date_incident, :id_parc, :id_entp)
SQL
		);
		$stmt->bindValue(":nom", $nom_incident);
		$stmt->bindValue(":description", $description);
		$stmt->bindValue(":id_pers", Personne::createFromSession()->getIdPers());
		$stmt->bindValue(":id_type", $id_type_incident);
		$stmt->bindValue(":date_incident", "Posté le " . date("d-m-Y") . " à " . date("H:i") );
		$stmt->bindValue(":id_parc", $id_parc_incident);
		$stmt->bindValue(":id_entp", $id_entp);
		$stmt->execute();
	}

	public static function createIncidentFromId($id) {
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT * 
			FROM INCIDENT
			WHERE id_incident = :id_inc
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id_inc", $id);
		$stmt->execute();
		if (($object = $stmt->fetch()) !== false) {
			return $object;
		}
		else throw new Exception ("Incident not found");
	}

	/*
	Permet la suppression d'un incident
	*/
	public static function deleteIncident($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			DELETE FROM INCIDENT
			WHERE id_incident = :id
SQL
		);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
	}
	
	
	
	
	
	
	
	public static function cleanCut($string, $length, $cutString = '...')
	{
		if(strlen($string) <= $length)
			return $string;
		$str = substr($string,0,$length-strlen($cutString)+1);
		return substr($str,0,strrpos($str,' ')).$cutString;
	}
}