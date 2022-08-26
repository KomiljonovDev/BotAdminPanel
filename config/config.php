<?php
	include './config/dbConfig.php';

	class dbmysqli extends Dbconfig {

	    public $connectionString;
	    public $dataSet;
	    private $sqlQuery;
	    
	    protected $databaseName;
	    protected $hostName;
	    protected $userName;
	    protected $passCode;

	    protected $token;
	    function bot($method, $datas=[]){
			$url = "https://api.telegram.org/bot" . "BOT TOKEN" . "/".$method;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
			$res = curl_exec($ch);
			if (curl_error($ch)) {
				var_dump(curl_error($ch));
			}else{
				return json_decode($res);
			}
		}
	    function dbmysqli() {
	        $this -> connectionString = NULL;
	        $this -> sqlQuery = NULL;
	        $this -> dataSet = NULL;

	        $dbPara = new Dbconfig();
	        $this -> databaseName = $dbPara -> dbName;
	        $this -> hostName = $dbPara -> serverName;
	        $this -> userName = $dbPara -> userName;
	        $this -> passCode = $dbPara ->passCode;
	        $dbPara = NULL;
	    }
	  	
	    function dbConnect()    {
	    	$dbPara = new Dbconfig();
	        $this -> connectionString = mysqli_connect($dbPara -> serverName,$dbPara -> userName,$dbPara -> passCode, $dbPara -> dbName);
	        return $this -> connectionString;
	    }

	    function dbDisconnect() {
	        $this -> connectionString = NULL;
	        $this -> sqlQuery = NULL;
	        $this -> dataSet = NULL;
	        $this -> databaseName = NULL;
	        $this -> hostName = NULL;
	        $this -> userName = NULL;
	        $this -> passCode = NULL;
	    }

	    function selectAll($tableName)  {
	        $this -> sqlQuery = 'SELECT * FROM '.$this -> databaseName.'.'.$tableName;
	        $this -> dataSet = mysqli_query($this -> sqlQuery,$this -> connectionString);
	        return $this -> dataSet;
	    }
	    function selectWhere($tableName,$conditions, $extra="")   {
	        $this -> sqlQuery = 'SELECT * FROM '.$tableName.' WHERE ';
	        if (gettype($conditions) == "array") {
	        	foreach ($conditions as $keys => $values) {
		        	foreach ($values as $key => $value) {
		        		if ($key !== 'cn') {
		        			$this -> sqlQuery .= mysqli_real_escape_string($this -> connectionString, $key) . " " . $values['cn'] . "'";
			        		$this -> sqlQuery .= mysqli_real_escape_string($this -> connectionString, $values[$key]);
			        		$this -> sqlQuery .= "' and ";
		        		}
		        	}
		        }
		        $this -> sqlQuery = substr($this -> sqlQuery, 0,strlen($this -> sqlQuery)-4);
	        }else{
	        	$this -> sqlQuery .= $conditions;
	        }
	        $this -> sqlQuery .= $extra;
	        $this -> dataSet = mysqli_query($this -> connectionString, $this -> sqlQuery);
	        $this -> sqlQuery = NULL;
	        return $this -> dataSet;
	        // return $this -> sqlQuery;
	    }

	    function insertInto($tableName,$values=[]) {
	        $i = NULL;
	        $this -> sqlQuery = 'INSERT INTO '.$tableName;
	        $columns = "(";
	        $VALUES = "(";
	        foreach ($values as $key => $value) {
	        	$columns .= mysqli_real_escape_string($this -> connectionString,$key) . ',';
	        	$VALUES .= "'";
	        	$VALUES .= mysqli_real_escape_string($this -> connectionString,$value);
	        	$VALUES .= "',";
	        }
	        $columns = substr($columns, 0,strlen($columns)-1);
	        $VALUES = substr($VALUES, 0,strlen($VALUES)-1);
	        $columns .= ")";
	        $VALUES .= ")";
	        $this -> sqlQuery .= $columns . " VALUES " . $VALUES;
	        mysqli_query($this ->connectionString,$this -> sqlQuery);
	        return $this -> sqlQuery;
	    }
	    function delete($tableName,$conditions=[])   {
	    	$this -> sqlQuery = 'DELETE FROM '.$tableName.' WHERE ';
	    	foreach ($conditions as $keys => $values) {
	        	foreach ($values as $key => $value) {
		          	if ($key !== 'cn') {
		            	$this -> sqlQuery .= mysqli_real_escape_string($this -> connectionString, $key) . " " . $values['cn'] . "'";
		            	$this -> sqlQuery .= mysqli_real_escape_string($this -> connectionString, $values[$key]);
		            	$this -> sqlQuery .= "' and ";
		          	}
	        	}
	      	}
	      	$this -> sqlQuery = substr($this -> sqlQuery, 0,strlen($this -> sqlQuery)-4);
	      	$this -> dataSet = mysqli_query($this -> connectionString, $this -> sqlQuery) or die(mysqli_error($this -> connectionString));
	      	// $this -> sqlQuery = NULL;
	      	return $this -> dataSet;
	      	// return $this -> sqlQuery;
      	}
      	function update($tableName,$values=[], $conditions=[],$extra="") {
	        $this -> sqlQuery = 'UPDATE '.$tableName . ' SET ';
	        foreach ($values as $key => $value) {
	        	$this -> sqlQuery .=  mysqli_real_escape_string($this -> connectionString,$key);
	        	$this -> sqlQuery .=  "='";
	        	$this -> sqlQuery .=  mysqli_real_escape_string($this -> connectionString,$value);
	        	$this -> sqlQuery .=  "',";
	        }
	        $this -> sqlQuery = substr($this -> sqlQuery, 0,strlen($this -> sqlQuery)-1);
	        $this -> sqlQuery .= " WHERE ";
	        foreach ($conditions as $key => $value) {
	        	if ($key !== 'cn') {
	        		$this -> sqlQuery .= mysqli_real_escape_string($this -> connectionString, $key) . " " . $conditions['cn'] . "'";
	        		$this -> sqlQuery .= mysqli_real_escape_string($this -> connectionString, $conditions[$key]);
	        		$this -> sqlQuery .= "' and ";
	        	}
	        }
	        $this -> sqlQuery = substr($this -> sqlQuery, 0,strlen($this -> sqlQuery)-4);
	        $this -> sqlQuery .= $extra;
	        mysqli_query($this ->connectionString,$this -> sqlQuery);
	        return $this -> sqlQuery;
	    }
	    function selectFreeRun($query) {
	        $this -> dataSet = mysqli_query($query,$this -> connectionString);
	        return $this -> dataSet;
	    }

	    function freeRun($query) {
	        return mysqli_query($query,$this -> connectionString);
	    }
	}
?>