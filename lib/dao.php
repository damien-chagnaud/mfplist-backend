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
            $query = "SELECT COUNT(*) FROM $tablename";
			
            $resQuery = self::query($query);
			$stmt = self::$dbHandle->prepare($query);
			$stmt->execute();
			
            $tot = self::countRow($stmt);
            return($tot);
        }catch(Exception $e) {
            return(false);
        }
    }

    public function create($obj){
        
        try{
            $varTab = array();
            $valTab = array();
            $objValue = self::objToValue($obj);
            $tablename = $obj::getTableName();

            foreach($objValue as $key => $value){
                $varTab[] = $key;
                $valTab[] = "'".$value."'";
            }

            $query = "INSERT INTO ".self::dbFilterIn($tablename)." (".implode(",", $varTab).") VALUES ( ".implode(",", $valTab).");";
			
			$stmt = self::$dbHandle->prepare($query);
			$stmt->execute();
            
            return ($stmt!=FALSE)? true:false;
  
        }catch(Exception $e) {
            return(false);
        }
        return false;
    }
    
    public function read($objDB, $limit = false,$returnTab = false, $orderBy=false, $whereClause=false){
        $query = '';
        $i = 0;
        $tabelemnt = array();
        
        try{
            $tablename = $objDB::getTableName();
            $objValue = self::objToValue($objDB);
            $query .= "SELECT * FROM $tablename ";

            if(count($objValue)>0){
                $query .= 'WHERE ';
                foreach($objValue as $name => $value){
                    $sep = ($i>0)?' AND ':' ';
                    $query .= $sep.self::dbFilterIn($name)." LIKE '".self::dbFilterIn($value)."'";
                    $i++;
                }
            }elseif(is_string($whereClause)){/*************************** VERY BAD SYSTEM *********************/
				 $query .= 'WHERE '.$whereClause;
			}

            if(is_string($orderBy)){
                $query .= ' ORDER BY '.self::dbFilterIn($orderBy).' DESC ';
            }

            if(is_numeric($limit)){
                $query .= ' LIMIT ' . self::dbFilterIn($limit);
            }elseif(is_array($limit)){
                $query .= ' LIMIT ' . self::dbFilterIn($limit[0]).', '.self::dbFilterIn($limit[1]);
            }

            $query .= ';';

            $result = false;

            $stmt = self::$dbHandle->prepare($query);
            $result = $stmt->execute();
            if(!$result){
                return false;
            }

            if($returnTab){
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){$tabelemnt[] = $row;}
				return($tabelemnt);
            }else{
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
                if(method_exists($obj, 'get'.$prim)){
                    $primVal = call_user_func(array($obj, 'get'.$prim));
					
                    if($primVal<>''){
                        $objValue = self::objToValue($obj);

                        $query = "UPDATE ".self::dbFilterIn($tablename)." SET ";
                        $i=0;
                        foreach($objValue as $key => $value){ 
							if($value!=null ){
							   $comma = ($i>0)? ',': '';
							   $query .= $comma.self::dbFilterIn($key)." = '".self::dbFilterIn($value)."'";  
							   $i++;
							}
                        }
                        
                        $query .= " WHERE ".self::dbFilterIn($prim)." = '".self::dbFilterIn($primVal)."';";
						//print($query);
                        $res = self::query($query);
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

                    $query = "UPDATE ".self::dbFilterIn($tablename)." SET ";
                    $i=0;
                    foreach($objValue as $key => $value){ 
                        if($value!=null ){
                            $comma = ($i>0)? ',': '';
                            $query .= $comma.self::dbFilterIn($key)." = '".self::dbFilterIn($value)."'";  
                            $i++;
                        }
                    }
                    
                    $query .= " WHERE ".self::dbFilterIn($primaryTab)." = '".self::dbFilterIn($primVal)."';";
                    //print($query);
                    $res = self::query($query);
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
                       
                        $query = "DELETE FROM ".self::dbFilterIn($tablename)." WHERE ".self::dbFilterIn($prim)." = '".self::dbFilterIn($primVal)."' ;";

						
                        $res = self::query($query);
						
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
	
    /*-------------------- PROTECTED --------------------*/

    protected function dbFilterIn($value){//TO => DB
        $valueCln = '';
        $valueCln = addslashes($value);
        

        return($valueCln);
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
                $value = self::dbFilterOut(call_user_func(array($obj, $method)));
                if(strlen($value)>0){
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