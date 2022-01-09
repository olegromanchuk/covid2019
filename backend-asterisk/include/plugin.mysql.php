<?php

class CorePlugin__mysql
{
	var $encoding = 'cp1251';
	
	var $link_id = NULL;
	
	var $database = '';
	
	var $shutdown_queries = array();
	
	var $lastquery;
	
	var $QueriesExecuted = 0;
	
	var $Ready;
	
	function CorePlugin__mysql()
	{
		$this->Ready = TRUE;
	}
	
	function Connect($server,$user,$password,$database,$pconnect=FALSE)
	{
		global $core;
		
		if($this->link_id) return TRUE;
		
		$evalCode = array();
		
		$evalCode = '($server,$user'.(!empty($password)?',$password':'').')';
		
		$evalFuncName = ($pconnect?'mysql_pconnect':'mysql_connect');
		
		$evalCode = '@'.$evalFuncName.$evalCode;
		
		$evalCode = '$this->link_id = '.$evalCode.';';
		
		eval($evalCode);

		if(!$this->link_id)
		{
			$core->log("mysql","Failed to connect to database");
			return FALSE;
		}
		
		if(@mysql_select_db($database,$this->link_id))
		{
			$this->database = $database;
		}
		else
		{
			$core->log("mysql","cannot use database: $database");
			return FALSE;
		}
		
		mysql_query("set character_set_client='{$this->encoding}'",$this->link_id);
		mysql_query("set character_set_results='{$this->encoding}'",$this->link_id);
		mysql_query("set collation_connection='{$this->encoding}_general_ci'",$this->link_id);
		
		return TRUE;
	}
	
	function query_unbuffered($query)
	{
		return $this->query($query,'mysql_unbuffered_query');
	}
	
	function query_shutdown($query,$key='')
	{
		if(is_array($query))
		{
			$query = call_user_method_array('prepare',$this,$array);
		}
		
		if($key)
		{
			$this->shutdown_queries[$key] = $query;
		}
		else
		{
			$this->shutdown_queries[] = $query;
		}
		
		return TRUE;
	}
	
	function query_first($query,$type=MYSQL_ASSOC)
	{
		if(!$query_id = $this->query($query))
		{
			return FALSE;
		}
		
		$result = $this->fetch_array($query_id,$type);
		
		$this->free_result($query_id);
		
		if($result === FALSE) $result = NULL;
		elseif(count($result) === 1) $result = array_first($result);
		
		return $result;
	}
	
	function query_last($query,$increment=NULL,$type=MYSQL_ASSOC)
	{
		if(!$query_id = $this->query($query))
		{
			return FALSE;
		}
		
		$result = array();
		
		while(($row = $this->fetch_array($query_id,$type)) !== FALSE)
		{
			$i = ( $increment && isset($row[$increment]) ? $row[$increment] : NULL );
			
			if(preg_match('/^\d+$/s',$i)) $i = (int) $i;
			
			eval('$result['.$i.'] = $row;');
		}
		
		return $result;
	}
	
	function query($query,$query_function='mysql_query')
	{
		global $core;
		
		if(is_array($query))
		{
			if(count($query) > 1)
			{
				$query = call_user_method_array('__prepare',$this,$array);
			}
			else
			{
				$query = first_value($query);
			}
		}
		
		$query_function = ( ($tree_args = (func_num_args() > 2)) ? 'mysql_query' : $query_function );
		
		if(!function_exists($query_function))
		{
			$core->log("mysql","function is not exists: $query_function");
			$query_function = 'mysql_query';
		}
		
		$query_id = $query_function($query,$this->link_id);
		
		$this->lastquery = $query;
		
		if(!$query_id)
		{
			$error = array(
				"Error.Status" => "Bad query",
				"MySQL.Error" => mysql_error($this->link_id),
				"MySQL.Query" => $this->lastquery,
			);
			
			if($tree_args) {
				$error["Caller.File"] = func_get_arg(1);
				$error["Caller.Line"] = func_get_arg(2);
			}
			debug($error);
			$log_uid = $core->log("mysql",$error);
			$core->report(array_merge($error,array("log_uid"=>$log_uid)));
			
			if($tree_args) {
				critical_error($error);
			}
			
			return FALSE;
		}
		
		$this->QueriesExecuted++;
		
		return $query_id;
	}
	
	function prepare()
	{
		if(!$args = func_get_args()) return FALSE;
		
		$tmpl =& $args[0];
		
		$tmpl = str_replace("%", "%%", $tmpl);
		
		$tmpl = str_replace("?", "%s", $tmpl);
		
		foreach($args as $i=>$v)
		{
			if(!$i) continue;
			
			if(is_int($v)) continue;
			
			$args[$i] = "'".mysql_escape_string($v)."'";
		}
		
		for($i=$c=count($args)-1; $i<$c+20; $i++)
		{
			$args[$i+1] = "UNKNOWN_PLACEHOLDER_$i";
		}
		
		return call_user_func_array("sprintf", $args);
	}
	
	function fetch_array($query_id,$type=MYSQL_ASSOC)
	{
		return @mysql_fetch_array($query_id,$type);
	}
	
	function free_result($query_id)
	{
		return @mysql_free_result($query_id);
	}
	
	function data_seek($query_id,$pos)
	{
		return @mysql_data_seek($query_id, $pos);
	}
	
	function num_rows($query_id)
	{
		return mysql_num_rows($query_id);
	}
	
	function num_fields($query_id)
	{
		return mysql_num_fields($query_id);
	}

	function field_name($query_id, $columnnum)
	{
		return mysql_field_name($query_id, $columnnum);
	}

	function insert_id()
	{
		return mysql_insert_id($this->link_id);
	}
	
	function close()
	{
		return mysql_close($this->link_id);
	}
	
	function shutdown()
	{
		
	}
}

?>

