<?php

class Service_Page_Order_Change_Lists extends Zy_Core_Service{

    public function execute () {
        if (!$this->checkAdmin()) {
            throw new Zy_Core_Exception(405, "无权限查看");
        }

        $pn             = empty($this->request['page']) ? 1 : intval($this->request['page']);
        $rn             = empty($this->request['perPage']) ? 20 : intval($this->request['perPage']);
        $orderId        = empty($this->request['order_id']) ? 0 : intval($this->request['order_id']);
        $studentUid     = empty($this->request['student_uid']) ? 0 : intval($this->request['student_uid']);
        $dataRange      = empty($this->request['date_range']) ? "" : $this->request['date_range'];
        $isExport       = empty($this->request['is_export']) ? false : true;
        $pn             = ($pn-1) * $rn;
        list($sts, $ets) = empty($dataRange) ? array(0,0) : explode(",", $dataRange);

        $conds = array();

        if ($orderId > 0) {
            $conds['order_id'] = $orderId;
        }

        if ($studentUid > 0) {
            $conds['student_uid'] = $studentUid;
        }

        if ($sts > 0) {
            $conds[] = "update_time >= ".$sts;
        }
        if ($ets > 0) {
            $conds[] = "update_time <= ".($ets + 1);
        }

        $arrAppends = array(
            'order by id desc',
        );
        if (!$isExport) {
            $arrAppends[] = "limit {$pn} , {$rn}";
        }
        
        $serviceData = new Service_Data_Orderchange();
        $lists = $serviceData->getListByConds($conds, array(), NULL, $arrAppends);
        if (empty($lists)) {
            return array();
        }
        
        $lists = $this->formatDefault($lists);

        if ($isExport) {
            $data = $this->formatExcel($lists);
            Zy_Helper_Utils::exportExcelSimple("order_change", $data['title'], $data['lists']);
        }

        $total = $serviceData->getTotalByConds($conds);
        return array(
            'rows' => $lists,
            'total' => $total,
        );
    }

    // 默认格式化
    private function formatDefault ($lists) {

        $studentUids = Zy_Helper_Utils::arrayInt($lists, 'student_uid');
        $operator  = Zy_Helper_Utils::arrayInt($lists, 'operator');
        
        $uids = array_unique(array_merge($studentUids, $operator));

        $serviceData = new Service_Data_Profile();
        $userInfos = $serviceData->getListByConds(array(sprintf("uid in (%s)", implode(",", $uids))));
        $userInfos = array_column($userInfos, null, 'uid');

        $result = array();
        foreach ($lists as $v) {
            if (empty($userInfos[$v['student_uid']]['nickname'])) {
                continue;
            }
            if (empty($userInfos[$v['operator']]['nickname'])) {
                continue;
            }
            
            $ext = empty($v['ext']) ? array() : json_decode($v['ext'], true);
            $v['student_name']      = $userInfos[$v['student_uid']]['nickname'];
            $v['operator']          = $userInfos[$v['operator']]['nickname'];
            $v['balance']           = sprintf("%.2f", intval($v['balance']) / 100);
            $v['duration']          = sprintf("%.2f", $v['duration']);
            $v['update_time']       = date("Y年m月d日",$v['update_time']);
            $v['create_time']       = date("Y年m月d日",$v['create_time']);
            $v['isfree']            = empty($ext['isfree']) ? 0 : 1;
            $v['remark']            = empty($ext['remark']) ? "" : $ext['remark'];

            unset($v['ext']);

            $result[] = $v;
        }
        return $result;
    }

    private function formatExcel($lists) {
        $result = array(
            'title' => array( 'UID', '学员名', '订单ID', "订单信息", "类型", "免费课", '结转金额(元)', '结转课时(小时)', "备注", '操作员',  '更新时间'),
            'lists' => array(),
        );
        
        foreach ($lists as $item) {
            $typeInfo = $item['type'] == Service_Data_Orderchange::CHANGE_CREATE ? "创建订单" : 
                ($item['type'] == Service_Data_Orderchange::CHANGE_DELETE ? "删除订单" : "结转到账户");
            $ext = json_encode($item['ext'], true);
            $remark = empty($ext['remark']) ? "" : $ext['remark'];
            $tmp = array(
                $item['student_uid'],
                $item['student_name'],
                $item['order_id'],
                $item['order_info'],
                $typeInfo,
                $item['isfree'] ? "是" : "否",
                $item['balance'],
                $item['duration'],
                $remark,
                $item['operator'],
                $item['update_time'],
            );
            $result['lists'][] = $tmp;
        }
        return $result;

    }
}