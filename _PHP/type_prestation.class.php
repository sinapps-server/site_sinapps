<?php

require_once 'myPDO.include.php';

class Type_Prestation
{
	private $id_type_prestation = null;
	private $description_prestation = null;
	private $nom_prestation = null;
	private $prix_forfaitaire = null;
	private $prix_preferentiel = null;
	private $path_logo = null;
	private $id_offre = null;

	private function __construct()
	{

	}

	public static function getIdTypePrestation()
	{
		return $this->id_type_prestation;
	}

	public static function getDescriptionTypePrestation()
	{
		return $this->description_prestation;
	}

	public static function getNomPrestation()
	{
		return $this->nom_prestation;
	}

	public static function getPrixForfaitaire()
	{
		return $this->prix_forfaitaire;
	}

	public static function getPrixPreferentiel()
	{
		return $this->prix_preferentiel;
	}

	public static function getLogo()
	{
		return $this->path_logo;
	}
	
	public static function getIdOffre()
	{
		return $this->id_offre;
	}

	public static function setDescriptionTypePrestation($desc)
	{
		$this->description_prestation = $desc;
	}

	public static function createTypePrestationFromId($id)
	{
		$stmt = myPDO::getInstance()->prepare(<<<SQL
			SELECT * 
			FROM TYPE_PRESTATION
			WHERE id_type_prestation = :id
SQL
		);
		$stmt->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
		$stmt->bindValue(":id", $id);
		$stmt->execute();
		if (($object = $stmt->fetch()) !== false)
		{
			return $object;
		}
		else throw new Exception ("Type Prestation not found");
	}

	public function printTypePrestation()
	{
		$prestation = <<<HTML
			<div class = "row">
				<div class = "th3">{$this->nom_prestation}</div>
				<div class = "img">
					<img id="logo_ordi" src="{$this->path_logo}" alt="logo1"/>
				</div>
				<div class = "border_logo"></div>
				<div class = "txt">{$this->description_prestation}</div>
				<div class = "more">
					<a href="./prestation.php?i={$this->id_type_prestation}">En savoir plus &rsaquo;</a>
				</div>
			</div>
HTML;
		return $prestation;
	}

	public function printTypePrestaComplete()
	{
		$prestation = <<<HTML
			<div class = 'row'>
				<div class = "th1">{$this->nom_prestation}</div>
				<div class = "img">
					<img id="logo_ordi" src="{$this->path_logo}" alt="logo1"/>
				</div>
				<div class = 'border'></div>
			</div>
			<div class = 'row'>
				<div class = 'txt'>{$this->description_prestation}</div>
				<div class = 'prix'>A partir de {$this->prix_forfaitaire}€</div>
				<div class = 'prix'>Pour les gens gentils comme il faut : {$this->prix_preferentiel}€</div>
			</div>
HTML;
		return $prestation;
	}
}