<?php 
class APPLICANT_1	{
	private $_db;
	private $_query_type = "insert";
	private $_update_columns;
	private $_filter = array();
	private $_id , $_username , $_password , $_status , $_cr_time ;

	public function __construct($id ,$db=NULL) {
		if($db==NULL)	{
			$this->_db = new MySQLDataBase();
		}else	{
			$this->_db = $db;
		}
		$this->loadData($id );
	}

	private function loadData($id ) {
		$query = "SELECT `id` , `username` , `password` , `status` , DATE_FORMAT(`cr_time`,'%d/%m/%Y') as 'cr_time'  FROM `applicant_1`  WHERE `id` = '$id'";
		$this->_db->query($query);
		if($this->_db->num_rows==1)	{
			$row = $this->_db->last_result[0];

			$this->_id = $row->id;
			$this->_username = $row->username;
			$this->_password = $row->password;
			$this->_status = $row->status;
			$this->_cr_time = $row->cr_time;
			$this->_query_type = "update";
		}
	}

	public function filter()	{
		$query = "SELECT  `id`  FROM `applicant_1` WHERE ";
		$keys = array_keys($this->_filter);
		if(count($keys)){
			$query.="`$keys[0]` = '". str_replace("'", "''", $this->_filter[$keys[0]]) ."'";
			for($i=1;$i<count($keys);$i++){
				$query.=" AND `$keys[$i]` = '". str_replace("'", "''", $this->_filter[$keys[$i]]) ."'";				
			}
		}else{
			$query.= "1";
		}
		$this->_db->query($query);
		$return = array();
		if($this->_db->num_rows){
			foreach($this->_db->last_result as $row){
				$row_arr = (array)$row;
				$return[] = $row_arr;
			}
		}
		return $return;
	}

	public function clearFilter()	{
		$this->_filter = array();
	}

	public function getId()	{
		return $this->_id;
	}

	public function setId($value)	{
		$this->_id = $value;
		$this->_update_columns['id'] = "$value";
	}

	public function filterId($value)	{
		$this->_filter['id'] = "$value";
	}

	public function getUsername()	{
		return $this->_username;
	}

	public function setUsername($value)	{
		$this->_username = $value;
		$this->_update_columns['username'] = "$value";
	}

	public function filterUsername($value)	{
		$this->_filter['username'] = "$value";
	}

	public function getPassword()	{
		return $this->_password;
	}

	public function setPassword($value)	{
		$this->_password = $value;
		$this->_update_columns['password'] = "$value";
	}

	public function filterPassword($value)	{
		$this->_filter['password'] = "$value";
	}

	public function getStatus()	{
		return $this->_status;
	}

	public function setStatus($value)	{
		$this->_status = $value;
		$this->_update_columns['status'] = "$value";
	}

	public function filterStatus($value)	{
		$this->_filter['status'] = "$value";
	}

	public function getCr_time()	{
		return $this->_cr_time;
	}

	public function setCr_time($value)	{
		$this->_cr_time = $value;
		$this->_update_columns['cr_time'] = "STR_TO_DATE('$value','%d/%m/%Y')";
	}

	public function filterCr_time($value)	{
		$this->_filter['cr_time'] = "$value";
	}

	public function saveChanges()	{
		$keys = array_keys($this->_update_columns);
		if ($this->_query_type == "insert")	{
			$cols = "";
			foreach ($keys as $col)	{
				$cols.="`$col` ,";
			}
			$cols = substr($cols, 0, strlen($cols) - 1);
			$values = "";
			foreach ($this->_update_columns as $indx => $value) {
				if($indx=='cr_time' )	{
					$values.=" ".$value .",";
				}else	{
					$values.=" '" . str_replace("'", "''", $value) . "',";
				}
			}
			$values = substr($values, 0, strlen($values) - 1);
			$query = "INSERT INTO `applicant_1` ($cols) VALUES ($values)";
		}else	{
			$query = "UPDATE `applicant_1` SET ";
			$cols = "";
			foreach($keys as $col){
				if($this->_update_columns[$col]==''){
					$cols.= " `$col` = NULL,";
				}else	{
					if($col=='cr_time' )	{
						$cols.= " `$col` = ".$this->_update_columns[$col].",";
					}else	{
						$cols.= " `$col` = '".str_replace("'", "''", $this->_update_columns[$col]) ."',";
					}
				}
			}
			$cols = substr($cols, 0,  strlen($cols)-1);
			$query .= $cols;
			$query .= " WHERE ";
			$query.=" `id` = '$this->_id'";
		}
		$this->_db->query($query);
		if($this->_db->last_error!=""){
			return false;
		}
		$this->_query_type = "update";
		return true;
		}
}
?> 
