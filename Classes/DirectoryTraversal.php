<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DirectoryTraversal
 *
 * @author Prashant
 */
class DirectoryTraversal {
  public $parent;
  public $directory;
  public $child_directories =array();
  public $files = array();
  
  public function __construct($path = '/') {
    $dir=opendir($path);
    if (!$dir) {
      $path=getcwd();
      $dir=opendir($path);
    }
    $this->parent=dirname($path);
    $this->directory=$path;
    
    while (FALSE !== ($file = readdir($dir))) {
      if ($file != "." && $file != "..") {
        if (is_dir($path . "/" . $file)) {
          $this->child_directories[$file] = $path . "/" . $file;
        }
      }
    }
    $dir=opendir($path);
    while (FALSE !== ($file = readdir($dir))) {
      if ($file != "." && $file != "..") {
        if (!is_dir($path . "/" . $file)) {
          $this->files[$file] = $path . "/" . $file;
        }
      }
    }
    
    ksort($this->child_directories);
    ksort($this->files);
  }
  
  public function showallfiles($resursive=false){
    $result = array();
    foreach($this->files as $file){
      $result[] = $file;
    }
    
    if($resursive){
      foreach($this->child_directories as $dir_path){
        $dir = new DirectoryTraversal($dir_path);
        $arr_new = $dir->showallfiles(true);
        foreach ($arr_new as $file){
          $result[]=$file;
        }
      }
    }
    
    return $result;
  }
  //put your code here
}

?>
