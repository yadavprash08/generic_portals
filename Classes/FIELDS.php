<?php
class FIELDS	{
	private $_db;
	private $_query_type = "insert";
	private $_update_columns;
	private $_filter = array();
	private $_id , $_page , $_name , $_display_name , $_max_length , $_type , $_order , $_remarks , $_mandatory ;

	public function __construct($id ,$db=NULL) {
		if($db==NULL)	{
			$this->_db = new MySQLDataBase();
		}else	{
			$this->_db = $db;
		}
		$this->loadData($id );
	}

	private function loadData($id ) {
		$query = "SELECT * FROM `fields`  WHERE `id` = '$id'";
		$this->_db->query($query);
		if($this->_db->num_rows==1)	{
			$row = $this->_db->last_result[0];

			$this->_id = $row->id;
			$this->_page = $row->page;
			$this->_name = $row->name;
			$this->_display_name = $row->display_name;
			$this->_max_length = $row->max_length;
			$this->_type = $row->type;
			$this->_order = $row->order;
			$this->_remarks = $row->remarks;
			$this->_mandatory = $row->mandatory;
			$this->_query_type = "update";
		}
	}

	public function filter()	{
		$query = "SELECT  `id`  FROM `fields` WHERE ";
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

	public function getPage()	{
		return $this->_page;
	}

	public function setPage($value)	{
		$this->_page = $value;
		$this->_update_columns['page'] = "$value";
	}

	public function filterPage($value)	{
		$this->_filter['page'] = "$value";
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

	public function getDisplay_name()	{
		return $this->_display_name;
	}

	public function setDisplay_name($value)	{
		$this->_display_name = $value;
		$this->_update_columns['display_name'] = "$value";
	}

	public function filterDisplay_name($value)	{
		$this->_filter['display_name'] = "$value";
	}

	public function getMax_length()	{
		return $this->_max_length;
	}

	public function setMax_length($value)	{
		$this->_max_length = $value;
		$this->_update_columns['max_length'] = "$value";
	}

	public function filterMax_length($value)	{
		$this->_filter['max_length'] = "$value";
	}

	public function getType()	{
		return $this->_type;
	}

	public function setType($value)	{
		$this->_type = $value;
		$this->_update_columns['type'] = "$value";
	}

	public function filterType($value)	{
		$this->_filter['type'] = "$value";
	}

	public function getOrder()	{
		return $this->_order;
	}

	public function setOrder($value)	{
		$this->_order = $value;
		$this->_update_columns['order'] = "$value";
	}

	public function filterOrder($value)	{
		$this->_filter['order'] = "$value";
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

	public function getMandatory()	{
		return $this->_mandatory;
	}

	public function setMandatory($value)	{
		$this->_mandatory = $value;
		$this->_update_columns['mandatory'] = "$value";
	}

	public function filterMandatory($value)	{
		$this->_filter['mandatory'] = "$value";
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
			$query = "INSERT INTO `fields` ($cols) VALUES ($values)";
		}else	{
			$query = "UPDATE `fields` SET ";
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
