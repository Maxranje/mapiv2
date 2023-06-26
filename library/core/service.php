<?php

class Zy_Core_Service {
    
    public $request ;
    public $adption ; 
    public function __construct($request = array(), $adption = array()) {
        $this->request = $request;
        $this->adption = $adption;
    }

    // 检测是否是超管,  管理员或老师
    public function checkAdmin () {
        if (empty($this->adption['type']) 
            || !in_array($this->adption['type'], Service_Data_User_Profile::ADMIN_GRANT)) {
            return false;
        }
        return true;
    }

    public function checkSuper () {
        if (empty($this->adption['type']) 
            || $this->adption['type'] != Service_Data_User_Profile::USER_TYPE_SUPER) {
            return false;
        }
        return true;
    }

    public function checkTeacher () {
        if (empty($this->adption['type']) 
            || $this->adption['type'] != Service_Data_User_Profile::USER_TYPE_TEACHER) {
            return false;
        }
        return true;
    }

    public function checkStudent () {
        if (empty($this->adption['type']) 
            || $this->adption['type'] != Service_Data_User_Profile::USER_TYPE_STUDENT) {
            return false;
        }
        return true;
    }

    public function getUserRolePage () {
        return empty($this->adption['pages']) ? array() : $this->adption['pages'];
    }
}