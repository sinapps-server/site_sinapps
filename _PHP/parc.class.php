<?php

require_once 'myPDO.include.php';
require_once 'Personne.class.php';

class Parc
{
	private $id_parc = null;
	private $nom_parc = null;
	private $lieu_parc = null;
	private $id_entreprise = null;
	private $id_personne_resp = null;

	private function __construct() {}

	/******GETTER******/
	public function getIdParc()
	{
		return $this->id_parc;
	}

	public function getNomParc()
	{
		return $this->nom_parc;
	}

	public function getLieuParc()
	{
		return $this->lieu_parc;
	}

	public function getIdEntreprise()
	{
		return $this->id_entreprise;
	}

	public function getIdResponsable()
	{
		return $this->id_personne_resp;
	}

	public function getParcByEntreprise($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM PARC
			WHERE id_entreprise = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		$array = $stmt->fetchAll();
		
		$html = "<div class='box1'>";
		foreach($array as $ligne)
		{
			$pers = Personne::createPersFromId($ligne->getIdResponsable());
			$i = $ligne->getIdParc();
			$html.=<<<HTML
				<div class = "row bordure fond">
					<div class="th2">Parc n°{$ligne->getIdParc()} : {$ligne->getNomParc()}</div>
					<div class="row">Responsable: {$pers->getNomPers()} {$pers->getPrenomPers()}</div>
					<button onclick="location.href='./parc.php?i={$i}'" type="submit" class="button">Voir en détail</button>
				</div>
HTML;
		}
		$html.="</div>";
		return $html;
	}
	
	public function getParcByIdEntreprise($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT *
			FROM PARC
			WHERE id_entreprise = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	/******SETTER******/
	public function setNomParc($nom)
	{
		$this->nom_parc = $nom;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE PARC
			SET nom_parc = :nom
			WHERE id_parc = :id
SQL
		);	
		$stmt->bindValue(":nom", $nom);
		$stmt->bindValue(":id", $this->id_parc);
		$stmt->execute();
	}

	public function setLieuParc($lieu)
	{
		$this->lieu_parc = $lieu;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE PARC
			SET lieu_parc = :lieu
			WHERE id_parc = :id
SQL
		);
		$stmt->bindValue(':lieu', $lieu);
		$stmt->bindValue(':id', $this->id_parc);
		$stmt->execute();
	}

	public function setIdEntreprise($id)
	{
		$this->lieu_parc = $id;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE PARC
			SET id_entreprise = :id_entreprise
			WHERE id_parc = :id
SQL
		);
		$stmt->bindValue(':id_entreprise', $id);
		$stmt->bindValue(':id', $this->id_parc);
		$stmt->execute();
	}

	public function setResponsable($id)
	{
		$this->lieu_parc = $id;
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			UPDATE PARC
			SET id_personne_resp = :id_personne_resp
			WHERE id_parc = :id
SQL
		);
		$stmt->bindValue(':id_personne_resp', $id);
		$stmt->bindValue(':id', $this->id_parc);
		$stmt->execute();
	}

	/******FONCTIONS******/
	public static function createParcFromId($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT * 
			FROM PARC
			WHERE id_parc = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		if (($object = $stmt->fetch()) !== false)
			return $object;
		else
			throw new Exception ("Parc not found");
	}

	public static function createParc($id_entp = "", $id_resp = "", $name = "")
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			INSERT INTO PARC (nom_parc, id_entreprise, id_personne_resp)
			VALUES (:nom, :entp, :resp)
SQL
		);

		$stmt->bindValue(":nom", $name);
		$stmt->bindValue(":entp", $id_entp);
		$stmt->bindValue(":resp", $id_resp);
		$stmt->execute();
	}

	public static function deleteParc($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			DELETE FROM PARC
			WHERE id_parc = :id
SQL
		);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
	}
}