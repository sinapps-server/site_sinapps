<?php

require_once 'myPDO.include.php';

class Appareil
{
	private $id_appareil = null;
	private $nom_appareil = null;
	private $mac_appareil = null;
	private $num_serie_appareil = null;
	private $id_parc = null;
	private $id_type_appareil = null;

	private function __construct() {}

	/******GETTER******/
	public function getIdAppareil()
	{
		return $this->id_appareil;
	}

	public function getNomAppareil()
	{
		return $this->nom_appareil;
	}

	public function getMacAppareil()
	{
		return $this->mac_appareil;
	}

	public function getSerieAppareil()
	{
		return $this->num_serie_appareil;
	}

	public function getIdParc()
	{
		return $this->id_parc;
	}

	public function getIdTypeAppareil()
	{
		return $this->id_type_appareil;
	}

	public function getAppareilByParc($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM APPAREIL
			WHERE id_parc = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		$array = $stmt->fetchAll();
		
		$html = "<div class='intro_box'><div class='box1'>";
		foreach($array as $ligne)
		{
			//$type = Type_Appareil::createPersFromId($ligne->getIdTypeAppareil());
			$i = $ligne->getIdAppareil();
			$html.=<<<HTML
				<div class = "row bordure fond">
					<div class="th2">Appareil n° {$ligne->getIdAppareil()}: {$ligne->getNomAppareil()}</div>
					<div class="txt">MAC: {$ligne->getMacAppareil()}</div>
					<div class="txt">Num. de série: {$ligne->getSerieAppareil()}</div>
				</div>
HTML;
		}
		$html.="</div></div>";
		return $html;
	}
	
	/******SETTER******/
	public function setNomAppareil($nom)
	{
		$this->nom_appareil = $nom;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE APPAREIL
			SET nom_appareil = :nom
			WHERE id_appareil = :id
SQL
		);	
		$stmt->bindValue(":nom", $nom);
		$stmt->bindValue(":id", $this->id_appareil);
		$stmt->execute();
	}

	public function setMacAppareil($mac)
	{
		$this->mac_appareil = $mac;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE APPAREIL
			SET mac_appareil = :mac
			WHERE id_appareil = :id
SQL
		);
		$stmt->bindValue(":mac", $mac);
		$stmt->bindValue(":id", $this->id_appareil);
		$stmt->execute();
	}

	public function setSerieAppareil($serie)
	{
		$this->num_serie_appareil = $serie;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE APPAREIL
			SET num_serie_appareil = :serie
			WHERE id_appareil = :id
SQL
		);
		$stmt->bindValue(":serie", $serie);
		$stmt->bindValue(":id", $this->id_appareil);
		$stmt->execute();
	}

	public function setParcAppareil($parc)
	{
		$this->id_parc = $parc;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE APPAREIL
			SET id_parc = :parc
			WHERE id_appareil = :id
SQL
		);
		$stmt->bindValue(":parc", $parc);
		$stmt->bindValue(":id", $this->id_appareil);
		$stmt->execute();
	}

	public function setTypeAppareil($type)
	{
		$this->id_type_appareil = $type;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE APPAREIL
			SET id_type_appareil = :type
			WHERE id_appareil = :id
SQL
		);
		$stmt->bindValue(":type", $type);
		$stmt->bindValue(":id", $this->id_appareil);
		$stmt->execute();
	}

	/******FONCTIONS******/
	public static function createAppareilFromId($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT * 
			FROM APPAREIL
			WHERE id_appareil = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		if (($object = $stmt->fetch()) !== false)
			return $object;
		else
			throw new Exception ("Appareil not found");
	}

	public static function createAppareil($id_parc = "", $id_type = "", $name = "")
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			INSERT INTO APPAREIL (nom_appareil, id_parc, id_type_appareil)
			VALUES (:nom, :parc, :type)
SQL
		);

		$stmt->bindValue(":nom", $name);
		$stmt->bindValue(":parc", $id_parc);
		$stmt->bindValue(":type", $id_type);
		$stmt->execute();
	}

	public static function deleteAppareil($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			DELETE FROM APPAREIL
			WHERE id_appareil = :id
SQL
		);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
	}
}
