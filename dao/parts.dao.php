<?php

class PARTS{
	//PROPERTIES:
	private static $tablename="stock_parts";
	private static $primaryKey = "id";
	private $id;
	private $uuid;
	private $brand;
	private $cat;
	private $devices;
	private $name;
	private $ref;
	private $stock;

    //STATIC METHODS:
	public static function getTableName(){ return self::$tablename; }
	public static function getPrimaryKey(){ return self::$primaryKey; }
	
 	//GETTERS:
	public function getId(){ return $this->id;}
	public function getUuid(){ return $this->uuid;}
 	public function getBrand(){ return $this->brand;}
	public function getCat(){ return $this->cat;}	
	public function getDevices(){ return $this->devices;}
	public function getName(){ return $this->name;}
	public function getRef(){ return $this->ref;}
	public function getStock(){ return $this->stock;}

	
	//SETTERS:
	public function setId($value){ $this->id = $value;}
	public function setUuid($value){ $this->uuid = $value;}
 	public function setBrand($value){ $this->brand = $value;}
 	public function setCat($value){ $this->cat = $value;}
 	public function setDevices($value){ $this->devices = $value;}
 	public function setName($value){ $this->name = $value;}
 	public function setRef($value){ $this->ref = $value;}
 	public function setStock($value){ $this->stock = $value;}
	
	//TOOLS:
	public function __toString(){return("PARTS: id=".$this->getId().", uuid=".$this->getUuid().", brand=".$this->getBrand().", cat=".$this->getCat().", devices=".$this->getDevices().", name=".$this->getName().", ref=".$this->getRef().", stock=".$this->getStock());}
	public function toArray(){return(array($this->getId(),$this->getUuid(),$this->getBrand(),$this->getCat(),$this->getDevices(),$this->getName(),$this->getRef(),$this->getStock()));}

	public function fromJson($json) {
		$data = json_decode($json, true);
		$this->setId($data['id']);
		$this->setUuid($data['uuid']);
		$this->setBrand($data['brand']);
		$this->setCat($data['cat']);
		$this->setDevices($data['devices']);
		$this->setName($data['name']);
		$this->setRef($data['ref']);
		$this->setStock($data['stock']);
	}

	public function toJson() {
		return json_encode(array(
			'id' => $this->getId(),
			'uuid' => $this->getUuid(),
			'brand' => $this->getBrand(),
			'cat' => $this->getCat(),
			'devices' => $this->getDevices(),
			'name' => $this->getName(),
			'ref' => $this->getRef(),
			'stock' => $this->getStock()
		));
	}
};
