<?php
class EVENTS	{
	private $_db;
	private $_query_type = "insert";
	private $_update_columns;
	private $_filter = array();
	private $_id , $_name , $_start , $_end , $_remarks , $_inactive ;

	public function __construct($id ,$db=NULL) {
		if($db==NULL)	{
			$this->_db = new MySQLDataBase();
		}else	{
			$this->_db = $db;
		}
		$this->loadData($id );
	}

	private function loadData($id ) {
		$query = "SELECT * FROM `events`  WHERE `id` = '$id'";
		$this->_db->query($query);
		if($this->_db->num_rows==1)	{
			$row = $this->_db->last_result[0];

			$this->_id = $row->id;
			$this->_name = $row->name;
			$this->_start = $row->start;
			$this->_end = $row->end;
			$this->_remarks = $row->remarks;
			$this->_inactive = $row->inactive;
			$this->_query_type = "update";
		}
	}

	public function filter()	{
		$query = "SELECT  `id`  FROM `events` WHERE ";
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

	public function getName()	{
		return $this->_name;
	}

	public function setName($value)	{
		$this->_name = $value;
		$this->_update_columns['name'] = "$value";
	}

	public function filterName($value)	{
		$this->_filter['name'] = "$value";
	}

	public function getStart()	{
		return $this->_start;
	}

	public function setStart($value)	{
		$this->_start = $value;
		$this->_update_columns['start'] = "STR_TO_DATE('$value','%d/%m/%Y')";
	}

	public function filterStart($value)	{
		$this->_filter['start'] = "$value";
	}

	public function getEnd()	{
		return $this->_end;
	}

	public function setEnd($value)	{
		$this->_end = $value;
		$this->_update_columns['end'] = "STR_TO_DATE('$value','%d/%m/%Y')";
	}

	public function filterEnd($value)	{
		$this->_filter['end'] = "$value";
	}

	public function getRemarks()	{
		return $this->_remarks;
	}

	public function setRemarks($value)	{
		$this->_remarks = $value;
		$this->_update_columns['remarks'] = "$value";
	}

	public function filterRemarks($value)	{
		$this->_filter['remarks'] = "$value";
	}

	public function getInactive()	{
		return $this->_inactive;
	}

	public function setInactive($value)	{
		$this->_inactive = $value;
		$this->_update_columns['inactive'] = "$value";
	}

	public function filterInactive($value)	{
		$this->_filter['inactive'] = "$value";
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
				if($indx=='start' || $indx=='end' )	{
					$values.=" ".$value .",";
				}else	{
					$values.=" '" . str_replace("'", "''", $value) . "',";
				}
			}
			$values = substr($values, 0, strlen($values) - 1);
			$query = "INSERT INTO `events` ($cols) VALUES ($values)";
		}else	{
			$query = "UPDATE `events` SET ";
			$cols = "";
			foreach($keys as $col){
				if($this->_update_columns[$col]==''){
					$cols.= " `$col` = NULL,";
				}else	{
					if($col=='start' || $col=='end' )	{
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