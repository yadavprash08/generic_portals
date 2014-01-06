<?php
require_once 'DEFINE_PARAM.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MySQLDataBase
 *
 * @author Prashant
 */
class MySQLDataBase {
  //put your code here
  
  /**
   *Stores the mysql host machine name
   * 
   * @var String
   */
  private $DB_HOST = "";
  /**
   *Stores the mysql username
   * 
   * @var String 
   */
  private $DB_USER = "";
  /**
   *
   * @var String 
   */
  private $DB_PASSWORD = "";
  /**
   *
   * @var String 
   */
  private $DB_NAME = "";
  /**
   *
   * @var String
   * @access private
   */
  private $DB_CONNECTION;
  /**
   * Holds the last query of the database
   *
   * @var String 
   * @access public
   */
  public $last_query='';
  /**
   *
   * @var Query 
   * @access public
   */
  public $last_error = '';
  /**
   *
   * @var Integer
   * @access public
   */
  public $num_queries=0;
  /**
   *
   * @var integer
   * @access public 
   */
  public $num_rows=0;
  /**
   *
   * @var type 
   * @access public
   */
  public $rows_affected=0;
  /**
   *
   * @var integer
   * @access public 
   */
  public $insert_id=0;
  /**
   *
   * @var object
   * @access public 
   */
  public $last_result;
  /**
   *
   * @var object
   * @access public 
   */
  public $col_info;
  /**
   *
   * @var array
   * @access public 
   */
  public $queries;
  
  /**
   *
   * @param string $user
   * @param string $password
   * @param string $host
   * @param string $database 
   */
  public function __construct($user=DB_USER ,$password=DB_PASSWORD ,$host=DB_HOST ,$database=DB_NAME ) {
    $this->DB_HOST = $host;
    $this->DB_NAME = $database;
    $this->DB_PASSWORD = $password;
    $this->DB_USER = $user;
    
    $this->_connectDB();
  }
  
  /**
   * 
   */
  private function _connectDB(){
    if(!$this->DB_CONNECTION){
      $this->DB_CONNECTION = mysql_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD);
      if(!$this->DB_CONNECTION){
          header('Location: createDefinitions.php');
          die();
      }
      if(!mysql_select_db($this->DB_NAME, $this->DB_CONNECTION)){
          header('Location: createDefinitions.php');
          die();
      }
    }
  }
  /**
   *
   * @param type $dbname 
   */
  public function changeDatabase($dbname){
    $this->DB_NAME = $dbname;
    $this->_connectDB();
    mysql_select_db($this->DB_NAME);
  }
  /**
   * 
   */
  private function flush(){
    $this->last_result = array();
    $this->col_info = null;
    $this->last_query = null;
  }
  
  /**
   *
   * @param string $query
   * @return int 
   */
  public function query($query){
    $this->_connectDB();
    $this->flush();
    $this->last_query = $query;
    $this->num_queries++;
    
    $start_time = microtime();
    $this->result = mysql_query($query,  $this->DB_CONNECTION);
    $stop_time = microtime();
    $this->last_error = mysql_error($this->DB_CONNECTION);
    $this->queries[] = array($query,$start_time,($stop_time-$start_time));
    $return_val = 0;
    
    if(preg_match("/^\s*(create|alter|truncate|drop) /i", $query)){
      $return_val = $this->result;
    }elseif(preg_match("/^\s*(insert|delete|update|replace) /i", $query)){
      $this->rows_affected = mysql_affected_rows($this->DB_CONNECTION);
      if ( preg_match( '/^\s*(insert|replace) /i', $query ) ) {
				$this->insert_id = mysql_insert_id($this->DB_CONNECTION);
			}
      $return_val = $this->rows_affected;
    }else{
      $this->col_info = array();
      $i=0;
      while($i<  mysql_num_fields($this->result)){
        $this->col_info[$i]=mysql_fetch_field($this->result);
        $i++;
      }
      $num_rows =0;
      while($row = mysql_fetch_object($this->result)){
        $this->last_result[$num_rows] = $row;
        $num_rows++;
      }
      mysql_free_result($this->result);
      $this->num_rows = $num_rows;
      $return_val = $num_rows;
    }
    
    return $return_val;
  }
  
}

?>