<?php

require_once 'myPDO.include.php';

class Action
{
	private $id_action = null;
	private $nom_action = null;
	private $date_action = null;
	private $derniere_action = null;
	private $id_type_action = null;
	private $id_incident = null;
	private $id_personne_intervenant = null;

	private function __construct()
	{
		
	}

	public function getIdAction() {
		return $this->id_action;
	}

	public function getNomAction()
	{
		return $this->nom_action;
	}

	public function getDateAction()
	{
		return $this->date_action;
	}

	public function estDerniereAction()
	{
		return ($derniere_action == 1) ? true : false;
	}

	public function getIdTypeAction()
	{
		return $this->id_type_action;
	}

	public function getIdIncident()
	{
		return $this->id_incident;
	}

	public function getIdPersonneInt()
	{
		return $this->id_personne_intervenant;
	}

	public function setNomAction($nom)
	{
		$this->nom_action = $nom;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE ACTION
			SET nom_action = :nom
			WHERE id_action = :action 
SQL
		);
		$stmt->bindValue(":nom", $nom);
		$stmt->bindValue(":action", $this->id_action);
		$stmt->execute();
	}	

	public function setDerniereAction($val) {
		$this->derniere_action = $val;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE ACTION
			SET derniere_action = :val
			WHERE id_action = :action 
SQL
		);
		$stmt->bindValue(":val", $val);
		$stmt->bindValue(":action", $this->id_action);
		$stmt->execute();
	}
	
	public function setIdTypeAction($val) {
		$this->id_type_action = $val;
	}





	public static function getActionsByIdIncident($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM ACTION
			WHERE id_incident = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		$array = $stmt->fetchAll();

		$html = <<<HTML
			<div class = "box1">
HTML;
		foreach ($array as $action)
		{
			$html.=<<<HTML
				<div class = "row">
					<div class = "th1">{$action->getNomIncident()}</div>
					<div class = "th2">{$action->getDescriptionIncident()}</div>
				</div>
HTML;
		}
		$html.=<<<HTML
			</div>
HTML;
		return $html;
	}


	public static function createAction($lastAction, $user, $id_incident, $id_type_action)
	{
		require_once 'type_action.class.php';
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			INSERT INTO ACTION (nom_action, date_action, derniere_action, id_personne_intervenant, id_incident, id_type_action)
			VALUES (:nom, :date_action, :lastAction, :id_pers, :id_inc, :id_type_act)
SQL
		);
		$type_action = Type_action::createFromId($id_type_action);
		$nom_action = $type_action->getNomTypeAction();
		$stmt->bindValue(":nom", $nom_action);
		$stmt->bindValue(":date_action", date("Le d-m-Y à H:i") );
		$stmt->bindValue(":lastAction", $lastAction);
		$stmt->bindValue(":id_pers", Personne::createFromSession()->getIdPers());
		$stmt->bindValue(":id_inc", $id_incident);
		$stmt->bindValue(":id_type_act", $id_type_action);		
		$stmt->execute();
	}

	/*
	Permet la suppression d'un incident
	*/
	public static function deleteAction($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			DELETE FROM ACTION
			WHERE id_action = :id
SQL
		);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
	}
	
	
	
	
	
	
	
	
}