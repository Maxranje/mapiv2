<?php

class Service_Page_Student_Batchcreate extends Zy_Core_Service{

    public function execute () {
        if (!$this->checkAdmin()) {
            throw new Zy_Core_Exception(405, "无权限查看");
        }

        if (empty($this->request['excel'])) {
            throw new Zy_Core_Exception(405, "没有上传文件或无法解析, 请重试");
        }

        $serviceData = new Service_Data_User_Profile();

        $count = 0;
        foreach ($this->request['excel'] as $record) {
            if (empty($record['name'])
                || empty($record['phone'])
                || empty($record['nickname'])) {
                continue;
            }

            if (!is_numeric($record['phone']) 
                || strlen($record['phone']) < 6
                || strlen($record['phone']) > 12) {
                continue;
            }

            $userInfo = $serviceData->getUserInfo($record['name'], $record['phone']);
            if (!empty($userInfo)) {
                continue;
            }

            if (empty($record['school'])) {
                $record['school'] = "";
            }

            if (empty($record['graduate'])) {
                $record['graduate'] = "";
            }

            if (empty($record['sex'])) {
                $record['sex'] = "M";
            }

            if (empty($record['graduate'])) {
                $record['graduate'] = "";
            }

            if (empty($record['birthplace'])) {
                $record['birthplace'] = "";
            }

            if (empty($record['capital'])) {
                $record['capital'] = 0;
            }

            $profile = [
                "type"  => Service_Data_User_Profile::USER_TYPE_STUDENT , 
                "name"  => $record['name'] ,
                "nickname"  => $record['nickname'] , 
                "phone"  => $record['phone']  , 
                "avatar" => "",
                "school"  => $record['school']  , 
                "birthplace" => $record['birthplace'],
                "graduate"  => $record['graduate']  ,
                "sex"  => $record['sex'] , 
                "student_capital" => $record['capital'],
                "teacher_capital" => 0,
                "create_time"  => time() , 
                "update_time"  => time() , 
            ];

            $ret = $serviceData->createUserInfo($profile);
            if ($ret == false) {
                continue;
            }

            $count++;
        }

        return array("count" => $count);
    }
}