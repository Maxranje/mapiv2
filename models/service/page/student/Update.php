<?php

class Service_Page_Student_Update extends Zy_Core_Service{

    public function execute () {
        if (!$this->checkAdmin()) {
            throw new Zy_Core_Exception(405, "无权限查看");
        }

        $uid        = empty($this->request['uid']) ? 0 : intval($this->request['uid']);
        $name       = empty($this->request['name']) ? "" : trim($this->request['name']);
        $phone      = empty($this->request['phone']) ? "" : trim($this->request['phone']);
        $nickname   = empty($this->request['nickname']) ? "" : trim($this->request['nickname']);
        $school     = empty($this->request['school']) ? "" : trim($this->request['school']);
        $graduate   = empty($this->request['graduate']) ? "" : trim($this->request['graduate']);
        $birthplace = empty($this->request['birthplace']) ? "" : trim($this->request['birthplace']);
        $sex        = empty($this->request['sex']) ? "M" : trim($this->request['sex']);
        $remark     = empty($this->request['capital_remark']) ? "" : trim($this->request['capital_remark']);
        $capital    = empty($this->request['capital']) ? 0 : $this->request['capital'];

        if ($uid <= 0) {
            throw new Zy_Core_Exception(405, "请求参数错误, 请检查");
        }

        if (empty($name) || empty($phone) || empty($nickname)) {
            throw new Zy_Core_Exception(405, "管理员名或手机号等提交数据有空值, 请检查");
        }

        if (!is_numeric($phone) || strlen($phone) < 6 || strlen($phone) > 12) {
            throw new Zy_Core_Exception(405, "手机号参数错误, 6-12位数字, 请检查");
        }
        
        $serviceData = new Service_Data_User_Profile();
        $userInfo = $serviceData->getUserInfoByUid($uid);
        if (empty($userInfo)) {
            throw new Zy_Core_Exception(405, "无法查到相关用户");
        }

        $profile = [
            "type"          => Service_Data_User_Profile::USER_TYPE_STUDENT , 
            "name"          => $name,
            "nickname"      => $nickname, 
            "phone"         => $phone, 
            "birthplace"    => $birthplace, 
            "avatar"        => "",
            "school"        => $school, 
            "graduate"      => $graduate,
            "sex"           => $sex, 
            "update_time"   => time() , 
            "capital_remark" => $remark,
        ];

        $needStudentCapital = false;
        if (is_numeric($capital) && $capital != 0) {
            $needStudentCapital = true;
            $userInfo['student_capital'] += intval($capital * 100);
            $profile['student_capital'] = $userInfo['student_capital'];
            $profile['capital'] = intval($capital * 100);
        }

        $ret = $serviceData->editUserInfo($uid, $profile, $needStudentCapital);
        if ($ret === false) {
            throw new Zy_Core_Exception(405, "更新失败, 请重试");
        }
        return array();
    }
}