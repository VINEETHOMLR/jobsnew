<?php

namespace src\traits;

trait ControllerFunctionTrait
{

    public function validateNumeric($str) {
       return  is_numeric($str)?true:false;
    }
    
    public function validateStrlength($str,$length) {
       return   (strlen($str)==$length)?true:false;
    }
    public function checkFileminsize($File,$min_size) {
        return ($File['size']>=$min_size)?true:false;
      
    }
    
    public function checkFilesize($File,$min_size,$max_size) {
        if($File['size']<=$min_size)
            return 'min';
        if($File['size']>=$max_size)
            return 'max';
            return true;  
    }
    
    public function checkImagefile($File) {
      $allowedExts=['jpg','jpeg','png'];
      $img_arr= getimagesize($File["tmp_name"]);
      if(!empty($img_arr)) {
      $imgext= strtolower(pathinfo($File['name'],PATHINFO_EXTENSION));
      if(!in_array($imgext, $allowedExts))
        return false;
      }
      else 
       return false;
      
      return true;
    }
    
    public function checkDocfile($File) {
      $allowedExts = array("pdf", "doc", "docx");
      $fileext= strtolower(pathinfo($File['name'],PATHINFO_EXTENSION));
      if(!in_array($fileext, $imgext_arr))
        return false;
      
         return true;
    }

    public function createDirectory($directory){
        $dir_arr= explode('/', $directory);
        $dirpath='';
        foreach ($dir_arr as $dir) {
           
            if(trim($dir)!="")
             $dirpath.=$dir.'/';
            if (!file_exists(rtrim($dirpath,'/'))) {
                mkdir($dirpath, 0777, true);
           }
        }
        
    }
    
    public function uploadImage($File,$filepath,$appendfilename) {
   
        $this->createDirectory($filepath);
        $filename= $appendfilename.time().'.'.pathinfo($File['name'],PATHINFO_EXTENSION);
        if(!$this->checkImagefile($File))
            return $this->renderAPIError(Raise::t('security','err_image_extension')); 
        if(!$this->checkFileminsize($File,$this->minimage_size))
            return $this->renderAPIError(Raise::t('security','err_image_size'));
        $targetpath=$filepath.'/'.$filename;
        return (move_uploaded_file($File['tmp_name'], $targetpath))?$filename:'';
        
    }
    
   public function uploadDocument($File,$filepath,$appendfilename) {
   
        $this->createDirectory($filepath);
        $filename= $appendfilename.time().'.'.pathinfo($File['name'],PATHINFO_EXTENSION);
        if(!$this->checkDocfile($File))
            return $this->renderAPIError(Raise::t('security','err_docid_required')); 
        if(!$this->checkFileminsize($File,$this->minimage_size))
            return $this->renderAPIError(Raise::t('security','err_docid_required'));
        $targetpath=$filepath.'/'.$filename;
        return (move_uploaded_file($File['tmp_name'], $targetpath))?$filename:'';
        
    }
    
        
    public function getDate($datetime) {
        return date("yy-m-d H:i:s",$datetime);
    }
    
    public function emptyObject() {
         
        $arr = array();
        $arr = (object) $arr;  
        return $arr;
    }
    
    public function  validateUserid($user_id) {
          if(empty($user_id)) 
            return $this->renderAPIError(Raise::t('common','err_userid_required'));
          
       
//          if(!$this->User_model->checkRecord($user_id)) 
//            return $this->renderAPIError(Raise::t('common','err_user_invalid'));
        
    }
    
    public function saveUserlog($userid,$module,$action,$activity) {
        $arr['module']   = $module;
        $arr['action']   = $action;
        $arr['activity'] = $activity;
        $arr['user_id']  = $userid;
        $this->UserActivityLog->saveUserLog($arr);
    }
    
    
}

