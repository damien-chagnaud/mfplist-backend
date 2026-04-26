<?php

/**
 * Dao Class
 * 
 * This class provides methods for database operations such as create, read, update, and delete.
 * It uses a singleton pattern to ensure only one instance of the Dao class exists.
 */
class Dao {
	/*------------------ SNGl MECHA ------------------*/
	private static $_instance = null;
	
	private static $dbHandle;
	
	public static function getInstance() {
 
		if(is_null(self::$_instance)) {
		   self::$_instance = new Dao();  
		}
		return self::$_instance;
    }
	
	private function __construct() { 
        // Initialize the database connection
	    $dbConfig = configuration::$dbConfig;
        $dbAccess = new DbSQL($dbConfig);
		self::$dbHandle = $dbAccess->open();
	}
	
    /*-------------------- PUBLIC --------------------*/

    public function count($obj){
        try{
            $tablename = $obj::getTableName();
            $query = "SELECT COUNT(*) FROM " . self::quoteIdentifier($tablename);
			$stmt = self::$dbHandle->prepare($query);
			$stmt->execute();
			
            return (int) $stmt->fetchColumn();
        }catch(Exception $e) {
            return(false);
        }
    }

    public function create($obj){
        
        try{
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
                $params[$placeholder] = $objValue[$column];
            }

            $query = "INSERT INTO " . self::quoteIdentifier($tablename) . " (" . implode(',', $quotedColumns) . ") VALUES (" . implode(',', $placeholders) . ")";

			$stmt = self::$dbHandle->prepare($query);
			$stmt->execute($params);
            
            return ($stmt!=FALSE)? true:false;
  
        }catch(Exception $e) {
            return(false);
        }
        return false;
    }
    
    public function read($objDB, $limit = false,$returnTab = false, $orderBy=false, $whereClause=false){
        $query = '';
        $tabelemnt = array();
        $params = array();
        
        try{
            $tablename = $objDB::getTableName();
            $objValue = self::objToValue($objDB);
            $query .= "SELECT * FROM " . self::quoteIdentifier($tablename) . " ";

            if(count($objValue)>0){
                $query .= 'WHERE ';
                $whereParts = array();
                $index = 0;
                foreach($objValue as $name => $value){
                    $placeholder = ':where' . $index;
                    $whereParts[] = self::quoteIdentifier($name) . ' = ' . $placeholder;
                    $params[$placeholder] = $value;
                    $index++;
                }

                $query .= ' ' . implode(' AND ', $whereParts) . ' ';


            }elseif(is_string($whereClause) && trim($whereClause) !== ''){
				 return false;
			}

            if(is_string($orderBy)){
                $query .= ' ORDER BY '.self::quoteIdentifier($orderBy).' DESC ';
            }

            if(is_numeric($limit)){
                $query .= ' LIMIT ' . (int) $limit;
            }elseif(is_array($limit)){
                $offset = (int) $limit[0];
                $size = (int) $limit[1];
                $query .= ' LIMIT ' . $offset . ', ' . $size;
            }

            $query .= ';';

            $result = false;

            $stmt = self::$dbHandle->prepare($query);
            $result = $stmt->execute($params);
            if(!$result){
                return false;
            }

            if($returnTab){
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){$tabelemnt[] = $row;}
				return($tabelemnt);
            }else{
                $className = get_class($objDB);
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){$tabelemnt[] = self::valueToObj($row,$className);}
				return($tabelemnt);
            }

        }catch(Exception $e) {
            return(false);
        }
    }

    public function update($obj){
        $tablename = $obj::getTableName();
        try{
            $primaryTab = self::getPrimary($tablename);
            if(count($primaryTab)>0){//SINGLE PRIMARY
                $prim = $primaryTab[0];
                if(method_exists($obj, 'get'.ucfirst($prim))){
					$primVal = call_user_func(array($obj, 'get'.ucfirst($prim)));
					
                    if($primVal<>''){
                        $objValue = self::objToValue($obj);

                        $query = "UPDATE " . self::quoteIdentifier($tablename) . " SET ";
                        $updateParts = array();
                        $params = array();
                        $i = 0;
                        foreach($objValue as $key => $value){ 
							if($value !== null){
							   $placeholder = ':set' . $i;
							   $updateParts[] = self::quoteIdentifier($key) . " = " . $placeholder;
							   $params[$placeholder] = $value;
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
                        return ($res!=FALSE)? true:false;
                    }
                }
            }
        }catch(Exception $e) {
            return(false);
        }
        return false;
    }

    public function updateByUUID($obj){
        $tablename = $obj::getTableName();
        try{
            $primaryTab = 'uuid';
            if(method_exists($obj, 'getUuid')){
                $primVal = call_user_func(array($obj, 'getUuid'));
                
                if($primVal<>''){
                    $objValue = self::objToValue($obj);

                    $query = "UPDATE " . self::quoteIdentifier($tablename) . " SET ";
                    $updateParts = array();
                    $params = array();
                    $i=0;
                    foreach($objValue as $key => $value){ 
                        if($value !== null ){
                            $placeholder = ':set' . $i;
                            $updateParts[] = self::quoteIdentifier($key) . " = " . $placeholder;
                            $params[$placeholder] = $value;
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
                    return ($res!=FALSE)? true:false;
                }
            }
        }catch(Exception $e) {
            return(false);
        }
        return false;
    }

    public function delete($obj){
        $tablename = $obj::getTableName();
        try{
            $primaryTab = self::getPrimary($tablename);
            if(count($primaryTab)>0){//SINGLE PRIMARY
                $prim = $primaryTab[0];
                if(method_exists($obj, 'get'.ucfirst($prim))){
                    $primVal = call_user_func(array($obj, 'get'.ucfirst($prim)));
                    if($primVal<>''){

                        $query = "DELETE FROM " . self::quoteIdentifier($tablename) . " WHERE " . self::quoteIdentifier($prim) . " = :primaryValue ;";
						$stmt = self::$dbHandle->prepare($query);
						$res = $stmt->execute(array(':primaryValue' => $primVal));
						
                        return ($res!=FALSE)? true:false;
                    }
                }
            }
        }catch(Exception $e) {
            return(false);
        }
        return false;
    }

	/*--------------------- PRIVATE ---------------------*/
	
	private function query($query){
		$result = false;
		
		try{
			$stmt = self::$dbHandle->prepare($query);
			$result = $stmt->execute();
		}catch(Exception $e){
		}
		return($result);
	}
	
	private function fetch_array($retQuery){ 
		$result = false;
		try{
			$result = $retQuery->fetch(PDO::PDO::FETCH_NUM);	
		}catch(Exception $e){
		}
		return($result);
	}
	
	private function fetch_assoc($retQuery){
		$result = false;
		try{
			$result = $retQuery->fetch(PDO::FETCH_ASSOC);
		}catch(Exception $e){

		}
		return($result);
		
	}
	
	private function countRow($retQuery){
		return(count(self::fetch_array($retQuery)));
	}

    private function quoteIdentifier($identifier) {
        if (!is_string($identifier) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid SQL identifier.');
        }

        return '`' . $identifier . '`';
    }

    private function getPrimary($tableName) {
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

    protected function dbFilterIn($value){//TO => DB
        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    protected function dbFilterOut($value){//TO => PHP
        $valueCln = '';
        if(is_string($value)){
            $valueCln =htmlentities(stripslashes($value));//   
        }elseif(is_numeric($value)){
			$valueCln = intval($value);
		}
        return($valueCln);
    }

    protected function getGetters($obj){
        $getters = array();
        if(is_object($obj)){
            $methods = get_class_methods($obj);      
            foreach($methods as $value){            
                if(substr($value, 0, 3)=='get'){
                    $getters[] = $value;
                }
            }
           
        }
        return($getters);
    }
    
    protected function getSetters($obj){
        $setters = array();
        if(is_object($obj)){
            $methods = get_class_methods($obj);
            foreach($methods as $value){
                if(substr($value, 0, 3)=='set'){
                    $setters[] = $value;
                }
            }
        }
        return($setters);
    }

    protected function objToValue($obj){
        $valueTab = array();
        $getters = self::getGetters($obj);
        foreach($getters as $method){
            if(method_exists($obj, $method) && $method != 'getTableName'){
                $value = call_user_func(array($obj, $method));
                if($value !== null && $value !== ''){
                    $valueTab[strtoupper(substr($method,3))] = $value;
                } 
            }
        }
        return($valueTab);
    }
    
    protected function valueToObj($valueTab,$className){

        if(class_exists($className)){
            $obj = new $className();

            foreach($valueTab as $name => $value){
                $method = 'set'.ucfirst($name);
                if(method_exists($obj, $method) && $name != 'tablename'){
                    call_user_func(array($obj, $method),self::dbFilterOut($value));

                }
            }
            return($obj);
        }else{
            return(false);
        }
    }

}
?>