<?php 
class TAB_1_PAGE_1	{
	private $_db;
	private $_query_type = "insert";
	private $_update_columns;
	private $_filter = array();
	private $_id , $_applicant , $_first_name , $_middle_name , $_last_name , $_date_of_birth ;

	public function __construct($id ,$db=NULL) {
		if($db==NULL)	{
			$this->_db = new MySQLDataBase();
		}else	{
			$this->_db = $db;
		}
		$this->loadData($id );
	}

	private function loadData($id ) {
		$query = "SELECT `id` , `applicant` , `first_name` , `middle_name` , `last_name` , DATE_FORMAT(`date_of_birth`,'%d/%m/%Y') as 'date_of_birth'  FROM `tab_1_page_1`  WHERE `id` = '$id'";
		$this->_db->query($query);
		if($this->_db->num_rows==1)	{
			$row = $this->_db->last_result[0];

			$this->_id = $row->id;
			$this->_applicant = $row->applicant;
			$this->_first_name = $row->first_name;
			$this->_middle_name = $row->middle_name;
			$this->_last_name = $row->last_name;
			$this->_date_of_birth = $row->date_of_birth;
			$this->_query_type = "update";
		}
	}

	public function filter()	{
		$query = "SELECT  `id`  FROM `tab_1_page_1` WHERE ";
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

	public function getApplicant()	{
		return $this->_applicant;
	}

	public function setApplicant($value)	{
		$this->_applicant = $value;
		$this->_update_columns['applicant'] = "$value";
	}

	public function filterApplicant($value)	{
		$this->_filter['applicant'] = "$value";
	}

	public function getFirst_name()	{
		return $this->_first_name;
	}

	public function setFirst_name($value)	{
		$this->_first_name = $value;
		$this->_update_columns['first_name'] = "$value";
	}

	public function filterFirst_name($value)	{
		$this->_filter['first_name'] = "$value";
	}

	public function getMiddle_name()	{
		return $this->_middle_name;
	}

	public function setMiddle_name($value)	{
		$this->_middle_name = $value;
		$this->_update_columns['middle_name'] = "$value";
	}

	public function filterMiddle_name($value)	{
		$this->_filter['middle_name'] = "$value";
	}

	public function getLast_name()	{
		return $this->_last_name;
	}

	public function setLast_name($value)	{
		$this->_last_name = $value;
		$this->_update_columns['last_name'] = "$value";
	}

	public function filterLast_name($value)	{
		$this->_filter['last_name'] = "$value";
	}

	public function getDate_of_birth()	{
		return $this->_date_of_birth;
	}

	public function setDate_of_birth($value)	{
		$this->_date_of_birth = $value;
		$this->_update_columns['date_of_birth'] = "STR_TO_DATE('$value','%d/%m/%Y')";
	}

	public function filterDate_of_birth($value)	{
		$this->_filter['date_of_birth'] = "$value";
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
				if($indx=='date_of_birth' )	{
					$values.=" ".$value .",";
				}else	{
					$values.=" '" . str_replace("'", "''", $value) . "',";
				}
			}
			$values = substr($values, 0, strlen($values) - 1);
			$query = "INSERT INTO `tab_1_page_1` ($cols) VALUES ($values)";
		}else	{
			$query = "UPDATE `tab_1_page_1` SET ";
			$cols = "";
			foreach($keys as $col){
				if($this->_update_columns[$col]==''){
					$cols.= " `$col` = NULL,";
				}else	{
					if($col=='date_of_birth' )	{
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
