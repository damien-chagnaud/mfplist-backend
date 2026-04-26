<?php

/**
 * DAO (Data Access Object) class.
 *
 * Provides an abstract interface for MariaDB persistence using PHP PDO.
 *
 * Quick usage example:
 *
 * $dao = Dao::getInstance();
 *
 * // Create
 * $machine = new MACHINE();
 * $machine->setUuid('abc-123');
 * $machine->setName('Office MFP');
 * $dao->create($machine);
 *
 * // Read (as objects)
 * $machines = $dao->read(new MACHINE());
 *
 * // Read (as associative arrays)
 * $rows = $dao->read(new MACHINE(), 20, true, 'id');
 *
 * // Update by primary key or UUID
 * $machine->setId(1);
 * $dao->update($machine);
 * $dao->updateByUUID($machine);
 *
 * // Delete
 * $dao->delete($machine);
 *
 * Entity conventions expected by this DAO:
 * - The entity class must expose static getTableName().
 * - Entity getters are discovered automatically (getXyz).
 * - Row hydration expects matching setters (setXyz).
 * - Database columns should map to setter suffixes case-insensitively.
 * - update() uses the table primary key from INFORMATION_SCHEMA.
 * - updateByUUID() expects a uuid column and getUuid()/setUuid().
 */

require_once 'dbaccess.php';
require_once 'logger.php';

class Dao {
	/*------------------ SINGLETON ------------------*/
	private static $_instance = null;

	private static $dbHandle;

	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new Dao();
		}

		return self::$_instance;
	}

	private function __construct() {
		$dbAccess = new DbAccess();
		self::$dbHandle = $dbAccess->open();
	}

	/*-------------------- PUBLIC --------------------*/

	public function count($obj) {
		try {
			$tablename = $obj::getTableName();
			$query = "SELECT COUNT(*) FROM " . self::quoteIdentifier($tablename);
			$stmt = self::$dbHandle->prepare($query);
			$stmt->execute();

			return (int) $stmt->fetchColumn();
		} catch (Exception $e) {
			return false;
		}
	}

	public function create($obj) {
		try {
			$objValue = self::objToValue($obj);
			$tablename = $obj::getTableName();

			if (count($objValue) === 0) {
				return false;
			}

			$columns = array_keys($objValue);
			$quotedColumns = array_map(function ($column) {
				return self::quoteIdentifier($column);
			}, $columns);

			$placeholders = array();
			$params = array();
			foreach ($columns as $index => $column) {
				$placeholder = ':val' . $index;
				$placeholders[] = $placeholder;
				$params[$placeholder] = self::dbFilterIn($objValue[$column]);
			}

			$query = "INSERT INTO " . self::quoteIdentifier($tablename) . " (" . implode(',', $quotedColumns) . ") VALUES (" . implode(',', $placeholders) . ")";

			$stmt = self::$dbHandle->prepare($query);
			$res = $stmt->execute($params);

			return ($res != false) ? true : false;
		} catch (Exception $e) {
			Logger::safeError('create failed.', array('exception' => $e->getMessage()));
			return false;
		}
	}

	public function read($objDB, $limit = false, $returnTab = false, $orderBy = false, $whereClause = false) {
		$query = '';
		$tabElement = array();
		$params = array();

		try {
			$tablename = $objDB::getTableName();
			$objValue = self::objToValue($objDB);
			$query .= "SELECT * FROM " . self::quoteIdentifier($tablename) . " ";

			if (count($objValue) > 0) {
				$query .= 'WHERE ';
				$whereParts = array();
				$index = 0;
				foreach ($objValue as $name => $value) {
					$placeholder = ':where' . $index;
					$whereParts[] = self::quoteIdentifier($name) . ' = ' . $placeholder;
					$params[$placeholder] = self::dbFilterIn($value);
					$index++;
				}

				$query .= implode(' AND ', $whereParts) . ' ';
			} elseif (is_string($whereClause) && trim($whereClause) !== '') {
				// Keep API compatibility but reject raw SQL fragments for safety.
				return false;
			}

			if (is_string($orderBy) && trim($orderBy) !== '') {
				$query .= ' ORDER BY ' . self::quoteIdentifier($orderBy) . ' DESC ';
			}

			if (is_numeric($limit)) {
				$query .= ' LIMIT ' . (int) $limit;
			} elseif (is_array($limit) && count($limit) >= 2) {
				$offset = (int) $limit[0];
				$size = (int) $limit[1];
				$query .= ' LIMIT ' . $offset . ', ' . $size;
			}

			$query .= ';';

			$stmt = self::$dbHandle->prepare($query);
			$result = $stmt->execute($params);
			if (!$result) {
				return false;
			}

			if ($returnTab) {
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$tabElement[] = $row;
				}

				return $tabElement;
			}

			$className = get_class($objDB);
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$tabElement[] = self::valueToObj($row, $className);
			}

			return $tabElement;
		} catch (Exception $e) {
			Logger::safeError('read failed.', array('exception' => $e->getMessage()));
			return false;
		}
	}

	public function update($obj) {
		$tablename = $obj::getTableName();
		try {
			$primaryTab = self::getPrimary($tablename);
			if (count($primaryTab) > 0) {
				$prim = $primaryTab[0];
				if (method_exists($obj, 'get' . ucfirst($prim))) {
					$primVal = call_user_func(array($obj, 'get' . ucfirst($prim)));
					if ($primVal !== '') {
						$objValue = self::objToValue($obj);

						$query = "UPDATE " . self::quoteIdentifier($tablename) . " SET ";
						$updateParts = array();
						$params = array();
						$i = 0;
						foreach ($objValue as $key => $value) {
							if ($value !== null) {
								$placeholder = ':set' . $i;
								$updateParts[] = self::quoteIdentifier($key) . " = " . $placeholder;
								$params[$placeholder] = self::dbFilterIn($value);
								$i++;
							}
						}

						if (count($updateParts) === 0) {
							return false;
						}

						$query .= implode(', ', $updateParts);
						$query .= " WHERE " . self::quoteIdentifier($prim) . " = :primaryValue;";
						$params[':primaryValue'] = $primVal;

						$stmt = self::$dbHandle->prepare($query);
						$res = $stmt->execute($params);
						return ($res != false) ? true : false;
					}
				}
			}
		} catch (Exception $e) {
			Logger::safeError('update failed.', array('exception' => $e->getMessage()));
			return false;
		}

		return false;
	}

	public function updateByUUID($obj) {
		$tablename = $obj::getTableName();
		try {
			$primaryTab = 'uuid';
			if (method_exists($obj, 'getUuid')) {
				$primVal = call_user_func(array($obj, 'getUuid'));
				if ($primVal !== '') {
					$objValue = self::objToValue($obj);

					$query = "UPDATE " . self::quoteIdentifier($tablename) . " SET ";
					$updateParts = array();
					$params = array();
					$i = 0;
					foreach ($objValue as $key => $value) {
						if ($value !== null) {
							$placeholder = ':set' . $i;
							$updateParts[] = self::quoteIdentifier($key) . " = " . $placeholder;
							$params[$placeholder] = self::dbFilterIn($value);
							$i++;
						}
					}

					if (count($updateParts) === 0) {
						return false;
					}

					$query .= implode(', ', $updateParts);
					$query .= " WHERE " . self::quoteIdentifier($primaryTab) . " = :primaryValue;";
					$params[':primaryValue'] = $primVal;

					$stmt = self::$dbHandle->prepare($query);
					$res = $stmt->execute($params);
					return ($res != false) ? true : false;
				}
			}
		} catch (Exception $e) {
			Logger::safeError('updateByUUID failed.', array('exception' => $e->getMessage()));
			return false;
		}

		return false;
	}

	public function delete($obj) {
		$tablename = $obj::getTableName();
		try {
			$primaryTab = self::getPrimary($tablename);
			if (count($primaryTab) > 0) {
				$prim = $primaryTab[0];
				if (method_exists($obj, 'get' . ucfirst($prim))) {
					$primVal = call_user_func(array($obj, 'get' . ucfirst($prim)));
					if ($primVal !== '') {
						$query = "DELETE FROM " . self::quoteIdentifier($tablename) . " WHERE " . self::quoteIdentifier($prim) . " = :primaryValue;";
						$stmt = self::$dbHandle->prepare($query);
						$res = $stmt->execute(array(':primaryValue' => $primVal));

						return ($res != false) ? true : false;
					}
				}
			}
		} catch (Exception $e) {
			Logger::safeError('delete failed.', array('exception' => $e->getMessage()));
			return false;
		}

		return false;
	}

	/*--------------------- PRIVATE ---------------------*/

	private static function quoteIdentifier($identifier) {
		if (!is_string($identifier) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
			throw new InvalidArgumentException('Invalid SQL identifier.');
		}

		return '`' . $identifier . '`';
	}

	private static function getPrimary($tableName) {
		$query = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table AND COLUMN_KEY = :columnKey';
		$stmt = self::$dbHandle->prepare($query);
		$stmt->execute(array(
			':db' => configuration::$dbConfig->getDbName(),
			':table' => $tableName,
			':columnKey' => 'PRI'
		));

		$primaryKeys = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$primaryKeys[] = $row['COLUMN_NAME'];
		}

		return $primaryKeys;
	}

	/*-------------------- PROTECTED --------------------*/

	protected static function dbFilterIn($value) {
		if (is_string($value)) {
			return trim($value);
		}

		return $value;
	}

	protected static function dbFilterOut($value) {
		if (is_string($value)) {
			return htmlentities(stripslashes($value));
		}

		if (is_numeric($value)) {
			return intval($value);
		}

		return $value;
	}

	protected static function getGetters($obj) {
		$getters = array();
		if (is_object($obj)) {
			$methods = get_class_methods($obj);
			foreach ($methods as $value) {
				if (substr($value, 0, 3) == 'get') {
					$getters[] = $value;
				}
			}
		}

		return $getters;
	}

	protected static function objToValue($obj) {
		$valueTab = array();
		$getters = self::getGetters($obj);
		foreach ($getters as $method) {
			if (method_exists($obj, $method) && $method != 'getTableName' && $method != 'getPrimaryKey') {
				$value = call_user_func(array($obj, $method));
				if ($value !== null && $value !== '') {
					$valueTab[strtoupper(substr($method, 3))] = $value;
				}
			}
		}

		return $valueTab;
	}

	protected static function valueToObj($valueTab, $className) {
		if (!class_exists($className)) {
			return false;
		}

		$obj = new $className();
		foreach ($valueTab as $name => $value) {
			$method = 'set' . ucfirst(strtolower($name));
			if (method_exists($obj, $method) && strtolower($name) != 'tablename') {
				call_user_func(array($obj, $method), self::dbFilterOut($value));
			}
		}

		return $obj;
	}
}

?>
