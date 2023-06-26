<?php

class Actions_Home extends Zy_Core_Actions {

    // 执行入口
    public function execute() {
        if (!$this->isLogin() ) {
            $this->redirectLogin();
        }

        $serivce = new Service_Page_Base_Page ($this->_request, $this->_userInfo);
        $data = $serivce->execute();

        $result = array(
            'lists' => $data,
            'user' => $this->_userInfo,
        );

        $this->_output['data'] = $result;
        $this->displayTemplate("calendar");
    }

}