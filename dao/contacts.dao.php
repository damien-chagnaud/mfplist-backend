<?php

class CONTACTS {
    //PROPERTIES:
    private static $tablename = "contacts";
    private static $primaryKey = "id";
    private $id;
    private $uuid;
    private $company;
    private $firstname;
    private $lastname;
    private $surname;
    private $phone;
    private $cel;
    private $email;
    private $address;

    //STATIC METHODS:
    public static function getTableName() { return self::$tablename; }
    public static function getPrimaryKey() { return self::$primaryKey; }

    //GETTERS:
    public function getId() { return $this->id; }
    public function getUuid() { return $this->uuid; }
    public function getCompany() { return $this->company; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
    public function getSurname() { return $this->surname; }
    public function getPhone() { return $this->phone; }
    public function getCel() { return $this->cel; }
    public function getEmail() { return $this->email; }
    public function getAddress() { return $this->address; }

    //SETTERS:
    public function setId($value) { $this->id = $value; }
    public function setUuid($value) { $this->uuid = $value; }
    public function setCompany($value) { $this->company = $value; }
    public function setFirstname($value) { $this->firstname = $value; }
    public function setLastname($value) { $this->lastname = $value; }
    public function setSurname($value) { $this->surname = $value; }
    public function setPhone($value) { $this->phone = $value; }
    public function setCel($value) { $this->cel = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setAddress($value) { $this->address = $value; }

    //TOOLS:
    public function __toString() {
        return("CONTACTS: id=".$this->getId().", uuid=".$this->getUuid().", company=".$this->getCompany().", firstname=".$this->getFirstname().", lastname=".$this->getLastname().", surname=".$this->getSurname().", phone=".$this->getPhone().", cel=".$this->getCel().", email=".$this->getEmail().", address=".$this->getAddress());
    }

    public function toArray() {
        return(array(
            $this->getId(),
            $this->getUuid(),
            $this->getCompany(),
            $this->getFirstname(),
            $this->getLastname(),
            $this->getSurname(),
            $this->getPhone(),
            $this->getCel(),
            $this->getEmail(),
            $this->getAddress()
        ));
    }

    public function fromJson($json) {
        $data = json_decode($json, true);
        $this->setId($data['id']);
        $this->setUuid($data['uuid']);
        $this->setCompany($data['company']);
        $this->setFirstname($data['firstname']);
        $this->setLastname($data['lastname']);
        $this->setSurname($data['surname']);
        $this->setPhone($data['phone']);
        $this->setCel($data['cel']);
        $this->setEmail($data['email']);
        $this->setAddress($data['address']);
    }

    public function toJson() {
        return json_encode(array(
            'id' => $this->getId(),
            'uuid' => $this->getUuid(),
            'company' => $this->getCompany(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'surname' => $this->getSurname(),
            'phone' => $this->getPhone(),
            'cel' => $this->getCel(),
            'email' => $this->getEmail(),
            'address' => $this->getAddress()
        ));
    }
};

