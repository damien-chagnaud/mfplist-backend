<?php

class CLIENTS {
    //PROPERTIES:
    private static $tablename = "clients";
    private static $primaryKey = "id";
    private $id;
    private $uuid;
    private $name;
    private $type;
    private $contact_manager;
    private $contact_tech;
    private $contact_accounting;
	private $contact_dj;
    private $address;
    private $phone;
    private $cel;
    private $email;
    private $flag1;
    private $flag2;
    private $flag3;
    private $flag4;
    private $flag5;

    //STATIC METHODS:
    public static function getTableName() { return self::$tablename; }
    public static function getPrimaryKey() { return self::$primaryKey; }

    //GETTERS:
    public function getId() { return $this->id; }
    public function getUuid() { return $this->uuid; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getContactManager() { return $this->contact_manager; }
    public function getContactTech() { return $this->contact_tech; }
    public function getContactAccounting() { return $this->contact_accounting; }
    public function getContactDj() { return $this->contact_dj; }
    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getCel() { return $this->cel; }
    public function getEmail() { return $this->email; }
    public function getFlag1() { return $this->flag1; }
    public function getFlag2() { return $this->flag2; }
    public function getFlag3() { return $this->flag3; }
    public function getFlag4() { return $this->flag4; }
    public function getFlag5() { return $this->flag5; }

    //SETTERS:
    public function setId($value) { $this->id = $value; }
    public function setUuid($value) { $this->uuid = $value; }
    public function setName($value) { $this->name = $value; }
    public function setType($value) { $this->type = $value; }
    public function setContactManager($value) { $this->contact_manager = $value; }
    public function setContactTech($value) { $this->contact_tech = $value; }
    public function setContactAccounting($value) { $this->contact_accounting = $value; }
    public function setContactDj($value) { $this->contact_dj = $value; }
    public function setAddress($value) { $this->address = $value; }
    public function setPhone($value) { $this->phone = $value; }
    public function setCel($value) { $this->cel = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setFlag1($value) { $this->flag1 = $value; }
    public function setFlag2($value) { $this->flag2 = $value; }
    public function setFlag3($value) { $this->flag3 = $value; }
    public function setFlag4($value) { $this->flag4 = $value; }
    public function setFlag5($value) { $this->flag5 = $value; }

    //TOOLS:
    public function __toString() {
        return(
            "CLIENTS: id=".$this->getId().
            ", uuid=".$this->getUuid().
            ", name=".$this->getName().
            ", type=".$this->getType().
            ", contact_manager=".$this->getContactManager().
            ", contact_tech=".$this->getContactTech().
            ", contact_accounting=".$this->getContactAccounting().
            ", contact_dj=".$this->getContactDj().
            ", address=".$this->getAddress().
            ", phone=".$this->getPhone().
            ", cel=".$this->getCel().
            ", email=".$this->getEmail().
            ", flag1=".$this->getFlag1().
            ", flag2=".$this->getFlag2().
            ", flag3=".$this->getFlag3().
            ", flag4=".$this->getFlag4().
            ", flag5=".$this->getFlag5()
        );  
    }
    
    public function toArray() {
        return(array(
            $this->getId(),
            $this->getUuid(),
            $this->getName(),
            $this->getType(),
            $this->getContactManager(),
            $this->getContactTech(),
            $this->getContactAccounting(),
            $this->getContactDj(),
            $this->getAddress(),
            $this->getPhone(),
            $this->getCel(),
            $this->getEmail(),
            $this->getFlag1(),
            $this->getFlag2(),
            $this->getFlag3(),
            $this->getFlag4(),
            $this->getFlag5()
        ));
    }

    public function fromJson($json) {
        $array = json_decode($json, true);
        $this->setId($array['id']);
        $this->setUuid($array['uuid']);
        $this->setName($array['name']);
        $this->setType($array['type']);
        $this->setContactManager($array['contact_manager']);
        $this->setContactTech($array['contact_tech']);
        $this->setContactAccounting($array['contact_accounting']);
        $this->setContactDj($array['contact_dj']);
        $this->setAddress($array['address']);
        $this->setPhone($array['phone']);
        $this->setCel($array['cel']);
        $this->setEmail($array['email']);
        $this->setFlag1($array['flag1']);
        $this->setFlag2($array['flag2']);
        $this->setFlag3($array['flag3']);
        $this->setFlag4($array['flag4']);
        $this->setFlag5($array['flag5']);
    }

    public function toJson() {
        return json_encode(array(
            'id' => $this->getId(),
            'uuid' => $this->getUuid(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'contact_manager' => $this->getContactManager(),
            'contact_tech' => $this->getContactTech(),
            'contact_accounting' => $this->getContactAccounting(),
            'contact_dj' => $this->getContactDj(),
            'address' => $this->getAddress(),
            'phone' => $this->getPhone(),
            'cel' => $this->getCel(),
            'email' => $this->getEmail(),
            'flag1' => $this->getFlag1(),
            'flag2' => $this->getFlag2(),
            'flag3' => $this->getFlag3(),
            'flag4' => $this->getFlag4(),
            'flag5' => $this->getFlag5()
        ));
    }
}  