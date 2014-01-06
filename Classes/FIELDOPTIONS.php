<?php
class FIELDOPTIONS	{
	private $_db;
	private $_query_type = "insert";
	private $_update_columns;
	private $_filter = array();
	private $_id , $_field , $_option_name , $_option_value ;

	public function __construct($id ,$db=NULL) {
		if($db==NULL)	{
			$this->_db = new MySQLDataBase();
		}else	{
			$this->_db = $db;
		}
		$this->loadData($id );
	}

	private function loadData($id ) {
		$query = "SELECT * FROM `fieldoptions`  WHERE `id` = '$id'";
		$this->_db->query($query);
		if($this->_db->num_rows==1)	{
			$row = $this->_db->last_result[0];

			$this->_id = $row->id;
			$this->_field = $row->field;
			$this->_option_name = $row->option_name;
			$this->_option_value = $row->option_value;
			$this->_query_type = "update";
		}
	}

	public function filter()	{
		$query = "SELECT  `id`  FROM `fieldoptions` WHERE ";
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

	public function getField()	{
		return $this->_field;
	}

	public function setField($value)	{
		$this->_field = $value;
		$this->_update_columns['field'] = "$value";
	}

	public function filterField($value)	{
		$this->_filter['field'] = "$value";
	}

	public function getOption_name()	{
		return $this->_option_name;
	}

	public function setOption_name($value)	{
		$this->_option_name = $value;
		$this->_update_columns['option_name'] = "$value";
	}

	public function filterOption_name($value)	{
		$this->_filter['option_name'] = "$value";
	}

	public function getOption_value()	{
		return $this->_option_value;
	}

	public function setOption_value($value)	{
		$this->_option_value = $value;
		$this->_update_columns['option_value'] = "$value";
	}

	public function filterOption_value($value)	{
		$this->_filter['option_value'] = "$value";
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
				if(FALSE)	{
					$values.=" ".$value .",";
				}else	{
					$values.=" '" . str_replace("'", "''", $value) . "',";
				}
			}
			$values = substr($values, 0, strlen($values) - 1);
			$query = "INSERT INTO `fieldoptions` ($cols) VALUES ($values)";
		}else	{
			$query = "UPDATE `fieldoptions` SET ";
			$cols = "";
			foreach($keys as $col){
				if($this->_update_columns[$col]==''){
					$cols.= " `$col` = NULL,";
				}else	{
					if(FALSE)	{
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