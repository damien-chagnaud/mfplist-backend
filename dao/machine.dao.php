<?php

class MACHINE {
	//PROPERTIES:
	public static $tablename = "machine";
	private static $primaryKey = "id";
	private $id;
	private $uuid;
	private $sn;
	private $name;
	private $brand;
	private $type;
	private $installdate;
	private $geopos;
	private $clientname;
	private $clientsubname;

	//STATIC METHODS:
	public static function getTableName() { return self::$tablename; }
	public static function getPrimaryKey() { return self::$primaryKey; }

	//GETTERS:
	public function getId() { return $this->id; }
	public function getUuid() { return $this->uuid; }
	public function getSn() { return $this->sn; }
	public function getName() { return $this->name; }
	public function getBrand() { return $this->brand; }
	public function getType() { return $this->type; }
	public function getInstalldate() { return $this->installdate; }
	public function getGeopos() { return $this->geopos; }
	public function getClientname() { return $this->clientname; }
	public function getClientsubname() { return $this->clientsubname; }

	//SETTERS:
	public function setId($value) { $this->id = $value; }
	public function setUuid($value) { $this->uuid = $value; }
	public function setSn($value) { $this->sn = $value; }
	public function setName($value) { $this->name = $value; }
	public function setBrand($value) { $this->brand = $value; }
	public function setType($value) { $this->type = $value; }
	public function setInstalldate($value) { $this->installdate = $value; }
	public function setGeopos($value) { $this->geopos = $value; }
	public function setClientname($value) { $this->clientname = $value; }
	public function setClientsubname($value) { $this->clientsubname = $value; }

	//TOOLS:
	public function __toString() {
		return(
			"MACHINE: id=".$this->getId().
			", uuid=".$this->getUuid().
			", sn=".$this->getSn().
			", name=".$this->getName().
			", brand=".$this->getBrand().
			", type=".$this->getType().
			", installdate=".$this->getInstalldate().
			", geopos=".$this->getGeopos().
			", clientname=".$this->getClientname().
			", clientsubname=".$this->getClientsubname()
		);
	}

	public function toArray() {
		return array(
			$this->getId(),
			$this->getUuid(),
			$this->getSn(),
			$this->getName(),
			$this->getBrand(),
			$this->getType(),
			$this->getInstalldate(),
			$this->getGeopos(),
			$this->getClientname(),
			$this->getClientsubname()
		);
	}

	public function fromJson($json) {
		$data = is_array($json) ? $json : json_decode($json, true);
		if (!is_array($data)) {
			return;
		}

		$this->setId($data['id'] ?? null);
		$this->setUuid($data['uuid'] ?? null);
		$this->setSn($data['sn'] ?? null);
		$this->setName($data['name'] ?? null);
		$this->setBrand($data['brand'] ?? null);
		$this->setType($data['type'] ?? null);
		$this->setInstalldate($data['installdate'] ?? null);
		$this->setGeopos($data['geopos'] ?? null);
		$this->setClientname($data['clientname'] ?? null);
		$this->setClientsubname($data['clientsubname'] ?? null);
	}

	public function toJson() {
		return json_encode(array(
			'id' => $this->getId(),
			'uuid' => $this->getUuid(),
			'sn' => $this->getSn(),
			'name' => $this->getName(),
			'brand' => $this->getBrand(),
			'type' => $this->getType(),
			'installdate' => $this->getInstalldate(),
			'geopos' => $this->getGeopos(),
			'clientname' => $this->getClientname(),
			'clientsubname' => $this->getClientsubname()
		));
	}
}

