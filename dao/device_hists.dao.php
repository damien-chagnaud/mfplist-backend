<?php
// route: dao/device_hists.dao.php
class DEVICE_HISTS {
    //PROPERTIES:
    private static $tablename = "device_hists";
    private static $primaryKey = "id";
    private $id;
    private $uuid;
    private $date;
    private $device_id;
    private $client_id;
    private $actions;
    private $state;
    private $attachments;
    private $notes;
    private $parts_used;

    //STATIC METHODS:
    public static function getTableName() { return self::$tablename; }
    public static function getPrimaryKey() { return self::$primaryKey; }

    //GETTERS:
    public function getId() { return $this->id; }
    public function getUuid() { return $this->uuid; }
    public function getDate() { return $this->date; }
    public function getDeviceId() { return $this->device_id; }
    public function getClientId() { return $this->client_id; }
    public function getActions() { return $this->actions; }
    public function getState() { return $this->state; }
    public function getAttachments() { return $this->attachments; }
    public function getNotes() { return $this->notes; }
    public function getPartsUsed() { return $this->parts_used; }

    //SETTERS:
    public function setId($value) { $this->id = $value; }
    public function setUuid($value) { $this->uuid = $value; }
    public function setDate($value) { $this->date = $value; }
    public function setDeviceId($value) { $this->device_id = $value; }
    public function setClientId($value) { $this->client_id = $value; }
    public function setActions($value) { $this->actions = $value; }
    public function setState($value) { $this->state = $value; }
    public function setAttachments($value) { $this->attachments = $value; }
    public function setNotes($value) { $this->notes = $value; }
    public function setPartsUsed($value) { $this->parts_used = $value; }

    //TOOLS:
    public function __toString() {
        return("DEVICE_HISTS: id=".$this->getId().", uuid=".$this->getUuid().", date=".$this->getDate().", device_id=".$this->getDeviceId().", client_id=".$this->getClientId().", actions=".$this->getActions().", parts_used=".$this->getPartsUsed().", state=".$this->getState().", attachments=".$this->getAttachments().", notes=".$this->getNotes());
    }

    public function toArray() {
        return(array(
            $this->getId(),
            $this->getUuid(),
            $this->getDate(),
            $this->getDeviceId(),
            $this->getClientId(),
            $this->getActions(),
            $this->getState(),
            $this->getAttachments(),
            $this->getNotes(),
            $this->getPartsUsed()
        ));
    }

    public function fromJson($data) {
        if (isset($data['uuid'])) $this->setUuid($data['uuid']);
        if (isset($data['date'])) $this->setDate($data['date']);
        if (isset($data['device_id'])) $this->setDeviceId($data['device_id']);
        if (isset($data['client_id'])) $this->setClientId($data['client_id']);
        if (isset($data['actions'])) $this->setActions($data['actions']);
        if (isset($data['state'])) $this->setState($data['state']);
        if (isset($data['attachments'])) $this->setAttachments($data['attachments']);
        if (isset($data['notes'])) $this->setNotes($data['notes']);
        if (isset($data['parts_used'])) $this->setPartsUsed($data['parts_used']);
    }

    public function toJson() {
        return json_encode(array(
            'id' => $this->getId(),
            'uuid' => $this->getUuid(),
            'date' => $this->getDate(),
            'device_id' => $this->getDeviceId(),
            'client_id' => $this->getClientId(),
            'actions' => $this->getActions(),
            'state' => $this->getState(),
            'attachments' => $this->getAttachments(),
            'notes' => $this->getNotes(),
            'parts_used' => $this->getPartsUsed()
        ));
    }
};