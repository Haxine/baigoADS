<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if (!defined('IN_BAIGO')) {
    exit('Access Denied');
}


class CONTROL_INSTALL_REQUEST_UPGRADE {

    function __construct() { //构造函数
        $this->general_install   = new GENERAL_INSTALL();
        $this->obj_tpl      = $this->general_install->obj_tpl;

        $this->mdl_opt = new MODEL_OPT();

        $this->obj_file          = new CLASS_FILE();
        $this->obj_file->dir_mk(BG_PATH_CACHE . 'ssin');

        $this->upgrade_init();
    }

    function ctrl_dbconfig() {
        $this->check_db();

        $_arr_dbconfigSubmit = $this->mdl_opt->input_dbconfig();

        if ($_arr_dbconfigSubmit['rcode'] != 'ok') {
            $this->obj_tpl->tplDisplay('result', $_arr_dbconfigSubmit);
        }

        $_arr_return = $this->mdl_opt->mdl_dbconfig();

        $this->obj_tpl->tplDisplay('result', $_arr_return);
    }


    function ctrl_submit() {
        $this->check_db();

        $_num_countSrc = 0;

        foreach ($this->obj_tpl->opt[$this->act]['list'] as $_key=>$_value) {
            if ($_value['min'] > 0) {
                $_num_countSrc++;
            }
        }

        $_arr_const = $this->mdl_opt->input_const($this->act);

        $_num_countInput = count(array_filter($_arr_const));

        if ($_num_countInput < $_num_countSrc) {
            $_arr_tplData = array(
                'rcode' => 'x030204',
            );
            $this->obj_tpl->tplDisplay('result', $_arr_tplData);
        }

        $_arr_return = $this->mdl_opt->mdl_const($this->act);

        if ($_arr_return['rcode'] != 'y060101') {
            $this->obj_tpl->tplDisplay('result', $_arr_return);
        }

        $_arr_tplData = array(
            'rcode' => 'y030404',
        );
        $this->obj_tpl->tplDisplay('result', $_arr_tplData);
    }


    function ctrl_admin() {
        $this->check_db();

        $_mdl_admin  = new MODEL_ADMIN();

        $_arr_adminInput = $_mdl_admin->input_submit();

        if ($_arr_adminInput['rcode'] != 'ok') {
            $this->obj_tpl->tplDisplay('result', $_arr_adminInput);
        }

        $_arr_adminInputThis = $this->input_admin();

        if ($_arr_adminInputThis['rcode'] != 'ok') {
            $this->obj_tpl->tplDisplay('result', $_arr_adminInputThis);
        }

        $_obj_sso = new CLASS_SSO();

        $_arr_adminSubmit = array(
            'user_name' => $_arr_adminInput['admin_name'],
            'user_pass' => $this->adminInput['admin_pass'],
            'user_mail' => $_arr_adminInput['admin_mail'],
            'user_nick' => $_arr_adminInput['admin_nick'],
        );

        $_arr_ssoReg = $_obj_sso->sso_user_reg($_arr_adminSubmit);
        if ($_arr_ssoReg['rcode'] != 'y010101') {
            $this->obj_tpl->tplDisplay('result', $_arr_ssoReg);
        }

        $_mdl_admin->mdl_submit($_arr_ssoReg['user_id']);

        $_arr_tplData = array(
            'rcode' => 'y030409',
        );
        $this->obj_tpl->tplDisplay('result', $_arr_tplData);
    }


    function ctrl_auth() {
        $this->check_db();

        $_mdl_admin  = new MODEL_ADMIN(); //设置管理组模型

        $_arr_adminInput = $_mdl_admin->input_submit();
        if ($_arr_adminInput['rcode'] != 'ok') {
            $this->obj_tpl->tplDisplay('result', $_arr_adminInput);
        }

        $_obj_sso = new CLASS_SSO();

        $_arr_ssoGet = $_obj_sso->sso_user_read($_arr_adminInput['admin_name'], 'user_name');
        if ($_arr_ssoGet['rcode'] != 'y010102') {
            if ($_arr_ssoGet['rcode'] == 'x010102') {
                $_arr_tplData = array(
                    'rcode' => 'x020205',
                );
                $this->obj_tpl->tplDisplay('result', $_arr_tplData);
            } else {
                $this->obj_tpl->tplDisplay('result', $_arr_ssoGet);
            }
        } else {
            //检验用户是否存在
            $_arr_adminRow = $_mdl_admin->mdl_read($_arr_ssoGet['user_id']);
            if ($_arr_adminRow['rcode'] == 'y020102') {
                $_arr_tplData = array(
                    'rcode' => 'x020218',
                );
                $this->obj_tpl->tplDisplay('result', $_arr_tplData);
            }
        }

        $_mdl_admin->mdl_submit($_arr_ssoGet['user_id']);

        $_arr_tplData = array(
            'rcode' => 'y030409',
        );
        $this->obj_tpl->tplDisplay('result', $_arr_tplData);
    }


    function ctrl_over() {
        $this->check_db();

        $_arr_return = $this->mdl_opt->mdl_over();

        if ($_arr_return['rcode'] != 'y060101') {
            $this->obj_tpl->tplDisplay('result', $_arr_return);
        }

        $_arr_tplData = array(
            'rcode' => 'y030412',
        );
        $this->obj_tpl->tplDisplay('result', $_arr_tplData);
    }


    function ctrl_chkname() {
        $this->check_db();

        $_mdl_admin   = new MODEL_ADMIN(); //设置管理组模型
        $_obj_sso     = new CLASS_SSO();

        $_str_adminName   = fn_getSafe(fn_get('admin_name'), 'txt', '');
        $_arr_ssoGet      = $_obj_sso->sso_user_read($_str_adminName, 'user_name');

        if ($_arr_ssoGet['rcode'] == 'y010102') {
            $_arr_adminRow = $_mdl_admin->mdl_read($_arr_ssoGet['user_id']);
            if ($_arr_adminRow['rcode'] == 'y020102') {
                $_arr_tplData = array(
                    'rcode' => 'x020218',
                );
                $this->obj_tpl->tplDisplay('result', $_arr_tplData);
            } else {
                $_arr_tplData = array(
                    'rcode' => 'x020204',
                );
                $this->obj_tpl->tplDisplay('result', $_arr_tplData);
            }
        }

        $arr_re = array(
            'msg' => 'ok'
        );

        $this->obj_tpl->tplDisplay('result', $arr_re);
    }


    function ctrl_chkauth() {
        $this->check_db();

        $_mdl_admin   = new MODEL_ADMIN(); //设置管理组模型
        $_obj_sso     = new CLASS_SSO();

        $_str_adminName   = fn_getSafe(fn_get('admin_name'), 'txt', '');
        $_arr_ssoGet      = $_obj_sso->sso_user_read($_str_adminName, 'user_name');

        if ($_arr_ssoGet['rcode'] == 'y010102') {
            //检验用户是否存在
            $_arr_adminRow = $_mdl_admin->mdl_read($_arr_ssoGet['user_id']);
            if ($_arr_adminRow['rcode'] == 'y020102') {
                $_arr_tplData = array(
                    'rcode' => 'x020218',
                );
                $this->obj_tpl->tplDisplay('result', $_arr_tplData);
            }
        } else {
            if ($_arr_ssoGet['rcode'] == 'x010102') {
                $_arr_tplData = array(
                    'rcode' => 'x020205',
                );
                $this->obj_tpl->tplDisplay('result', $_arr_tplData);
            } else {
                $this->obj_tpl->tplDisplay('result', $_arr_ssoGet);
            }
        }

        $arr_re = array(
            'msg' => 'ok'
        );

        $this->obj_tpl->tplDisplay('result', $arr_re);
    }


    private function check_db() {
        if (!defined('BG_DB_HOST') || fn_isEmpty(BG_DB_HOST) || !defined('BG_DB_NAME') || fn_isEmpty(BG_DB_NAME) || !defined('BG_DB_PASS') || fn_isEmpty(BG_DB_PASS) || !defined('BG_DB_CHARSET') || fn_isEmpty(BG_DB_CHARSET)) {
            $_arr_tplData = array(
                'rcode' => 'x030412',
            );
            $this->obj_tpl->tplDisplay('result', $_arr_tplData);
        }
    }


    private function upgrade_init() {
        $_str_rcode = '';

        if (file_exists(BG_PATH_CONFIG . 'installed.php')) { //如果新文件存在
            fn_include(BG_PATH_CONFIG . 'installed.php');  //载入
        } else if (file_exists(BG_PATH_CONFIG . 'is_install.php')) { //如果旧文件存在
            $this->obj_file->file_copy(BG_PATH_CONFIG . 'is_install.php', BG_PATH_CONFIG . 'installed.php'); //拷贝
            fn_include(BG_PATH_CONFIG . 'installed.php');  //载入
        } else {
            $_str_rcode = 'x030415';
        }

        if (defined('BG_INSTALL_PUB') && PRD_ADS_PUB <= BG_INSTALL_PUB) {
            $_str_rcode = 'x030403';
        }

        $_arr_phplibRow      = get_loaded_extensions();
        $_num_errCount    = 0;

        foreach ($this->obj_tpl->phplib as $_key=>$_value) {
            if (!in_array($_key, $_arr_phplibRow)) {
                $_num_errCount++;
            }
        }

        if ($_num_errCount > 0) {
            $_arr_tplData = array(
                'rcode' => 'x030414',
            );
            $this->obj_tpl->tplDisplay('result', $_arr_tplData);
        }

        $this->act = fn_getSafe($GLOBALS['route']['bg_act'], 'txt', 'phplib');
    }
}