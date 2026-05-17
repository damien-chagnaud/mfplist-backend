<?php

class UsersDao{
	//PROPERTIES:
	public static $tablename = "cred_users";
	private static $primaryKey = "id";
	private $id;
	private $uuid;
	private $uid;
	private $username;
	private $email;
	private $password;
	private $token;
	private $token_created_at;
	private $level;

	//STATIC METHODS:
	public static function getTableName() { return self::$tablename; }
	public static function getPrimaryKey() { return self::$primaryKey; }

	//GETTERS:
	public function getId() { return $this->id; }
	public function getUuid() { return $this->uuid; }
	public function getUid() { return $this->uid; }
	public function getUsername() { return $this->username; }
	public function getEmail() { return $this->email; }
	public function getPassword() { return $this->password; }
	public function getToken() { return $this->token; }
	public function getToken_created_at() { return $this->token_created_at; }
	public function getLevel() { return $this->level; }

	//SETTERS:
	public function setId($value) { $this->id = $value; }
	public function setUuid($value) { $this->uuid = $value; }
	public function setUid($value) { $this->uid = $value; }
	public function setUsername($value) { $this->username = $value; }
	public function setEmail($value) { $this->email = $value; }
	public function setPassword($value) { $this->password = $value; }
	public function setToken($value) { $this->token = $value; }
	public function setToken_created_at($value) { $this->token_created_at = $value; }
	public function setLevel($value) { $this->level = $value; }

	//TOOLS:
	public function __toString() {
		return(
			"USER: id=".$this->getId().
			", uuid=".$this->getUuid().
			", uid=".$this->getUid().
			", username=".$this->getUsername().
			", email=".$this->getEmail().
			", password=".$this->getPassword().
			", token=".$this->getToken().
			", token_created_at=".$this->getToken_created_at().
			", level=".$this->getLevel()
		);
	}

	public function toArray() {
		return array(
			$this->getId(),
			$this->getUuid(),
			$this->getUid(),
			$this->getUsername(),
			$this->getEmail(),
			$this->getPassword(),
			$this->getToken(),
			$this->getToken_created_at(),
			$this->getLevel()
		);
	}

	public function fromJson($json) {
		$data = is_array($json) ? $json : json_decode($json, true);
		if (!is_array($data)) {
			return;
		}

		$this->setId($data['id'] ?? null);
		$this->setUuid($data['uuid'] ?? null);
		$this->setUid($data['uid'] ?? null);
		$this->setUsername($data['username'] ?? null);
		$this->setEmail($data['email'] ?? null);
		$this->setPassword($data['password'] ?? null);
		$this->setToken($data['token'] ?? null);
		$this->setToken_created_at($data['token_created_at'] ?? null);
		$this->setLevel($data['level'] ?? null);
	}

	public function toJson() {
		return json_encode(array(
			'id' => $this->getId(),
			'uuid' => $this->getUuid(),
			'uid' => $this->getUid(),
			'username' => $this->getUsername(),
			'email' => $this->getEmail(),
			'password' => $this->getPassword(),
			'token' => $this->getToken(),
			'token_created_at' => $this->getToken_created_at(),
			'level' => $this->getLevel()
		));
	}
}
