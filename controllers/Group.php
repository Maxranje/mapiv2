<?php
class Controller_Group extends Zy_Core_Controller{

    public $actions = array(
        "areaops"   => "actions/admins/Areaops.php",
        "lists"     => "actions/group/Lists.php",
        "create"    => "actions/group/Create.php",
        "update"    => "actions/group/Update.php",
        "delete"    => "actions/group/Delete.php",
        "onlystudent"  => "actions/group/Onlystudent.php",
        "singleprice"  => "actions/group/Singleprice.php",
        "updateprice"  => "actions/group/Updateprice.php",
    );
}
