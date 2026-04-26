<?php

class TIMESTAMP{
    //PROPERTIES:
	public static $tablename="timestamp";
    private static $primaryKey = "id";
    private $id;
    private $uuid;
    private $name;
    private $refresh;

    //STATIC METHODS:
    public static function getTableName(){ return self::$tablename; }
    public static function getPrimaryKey(){ return self::$primaryKey; }

    //GETTERS:
    public function getId(){ return $this->id; }   
    public function getUuid(){ return $this->uuid; } 
    public function getName(){ return $this->name; }
    public function getRefresh(){ return $this->refresh; }

    //SETTERS:
    public function setId($value){ $this->id = $value; }
    public function setUuid($value){ $this->uuid = $value; }
    public function setName($value){ $this->name = $value; }
    public function setRefresh($value){ $this->refresh = $value; }

    //TOOLS:
    public function __toString(){
        return("TIMESTAMP: id=".$this->getId().", uuid=".$this->getUuid().", name=".$this->getName().", refresh=".$this->getRefresh());
    }
    public function toArray(){
        return(array($this->getId(), $this->getUuid(), $this->getName(), $this->getRefresh()));
    }


}
