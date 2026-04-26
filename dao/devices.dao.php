<?php

class DEVICES {
    //PROPERTIES:
    private static $tablename = "devices";
    private static $primaryKey = "id";
    private $id;
    private $uuid;
    private $client_id;
    private $name;
    private $type;
    private $brand;
    private $sn;

    //STATIC METHODS:
    public static function getTableName() { return self::$tablename; }
    public static function getPrimaryKey() { return self::$primaryKey; }

    //GETTERS:
    public function getId() { return $this->id; }
    public function getUuid() { return $this->uuid; }
    public function getClientId() { return $this->client_id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getBrand() { return $this->brand; }
    public function getSn() { return $this->sn; }

    //SETTERS:
    public function setId($value) { $this->id = $value; }
    public function setUuid($value) { $this->uuid = $value; }
    public function setClientId($value) { $this->client_id = $value; }
    public function setName($value) { $this->name = $value; }
    public function setType($value) { $this->type = $value; }
    public function setBrand($value) { $this->brand = $value; }
    public function setSn($value) { $this->sn = $value; }

    //TOOLS:
    public function __toString() {
        return("DEVICES: id=".$this->getId().", uuid=".$this->getUuid().", client_id=".$this->getClientId().", name=".$this->getName().", type=".$this->getType().", brand=".$this->getBrand().", sn=".$this->getSn());
    }

    public function toArray() {
        return(array(
            $this->getId(),
            $this->getUuid(),
            $this->getClientId(),
            $this->getName(),
            $this->getType(),
            $this->getBrand(),
            $this->getSn()
        ));
    }

    public function fromJson($json) {
        $data = json_decode($json, true);
        $this->setId($data['id']);
        $this->setUuid($data['uuid']);
        $this->setClientId($data['client_id']);
        $this->setName($data['name']);
        $this->setType($data['type']);
        $this->setBrand($data['brand']);
        $this->setSn($data['sn']);
    }

    public function toJson() {
        return json_encode(array(
            'id' => $this->getId(),
            'uuid' => $this->getUuid(),
            'client_id' => $this->getClientId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'brand' => $this->getBrand(),
            'sn' => $this->getSn()
        ));
    }
};