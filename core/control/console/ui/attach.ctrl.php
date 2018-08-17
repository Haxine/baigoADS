<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if (!defined('IN_BAIGO')) {
    exit('Access Denied');
}


/*-------------用户类-------------*/
class CONTROL_CONSOLE_UI_ATTACH {

    private $is_super = false;
    public $allowExtRows    = array();
    public $allowMimeRows   = array();

    function __construct() { //构造函数
        $this->general_console    = new GENERAL_CONSOLE();
        $this->general_console->chk_install();

        $this->adminLogged  = $this->general_console->ssin_begin();
        $this->general_console->is_admin($this->adminLogged);

        $this->obj_tpl        = $this->general_console->obj_tpl;

        $this->mdl_attach      = new MODEL_ATTACH(); //设置上传信息对象
        $this->mdl_admin      = new MODEL_ADMIN();
        $this->setUpload();
        $this->tplData = array(
            'adminLogged'    => $this->adminLogged,
            'uploadSize'     => BG_UPLOAD_SIZE * $this->sizeUnit,
            'allowExtRows'   => $this->allowExtRows,
            'allowMimeRows'  => $this->allowMimeRows,
        );

        if ($this->adminLogged['admin_type'] == 'super') {
            $this->is_super = true;
        }
    }


    /**
     * ctrl_form function.
     *
     * @access public
     */
    function ctrl_form() {
        if (!isset($this->adminLogged['admin_allow']['attach']['upload']) && !$this->is_super) {
            $this->tplData['rcode'] = 'x070302';
            $this->obj_tpl->tplDisplay('error', $this->tplData);
        }

        $_arr_yearRows    = $this->mdl_attach->mdl_year(100);
        $_arr_extRows     = $this->mdl_attach->mdl_ext();

        $_arr_tpl = array(
            'yearRows'   => $_arr_yearRows,
            'extRows'    => $_arr_extRows,
        );

        $_arr_tplData = array_merge($this->tplData, $_arr_tpl);

        $this->obj_tpl->tplDisplay('attach_form', $_arr_tplData);
    }


    /**
     * ctrl_list function.
     *
     * @access public
     */
    function ctrl_list() {
        if (!isset($this->adminLogged['admin_allow']['attach']['browse']) && !$this->is_super) {
            $this->tplData['rcode'] = 'x070301';
            $this->obj_tpl->tplDisplay('error', $this->tplData);
        }

        $_arr_search = array(
            'box'        => fn_getSafe(fn_get('box'), 'txt', 'normal'),
            'key'        => fn_getSafe(fn_get('key'), 'txt', ''),
            'year'       => fn_getSafe(fn_get('year'), 'txt', ''),
            'month'      => fn_getSafe(fn_get('month'), 'txt', ''),
            'ext'        => fn_getSafe(fn_get('ext'), 'txt', ''),
            'admin_id'   => fn_getSafe(fn_get('admin_id'), 'int', 0),
        ); //搜索设置

        $_num_attachCount  = $this->mdl_attach->mdl_count($_arr_search);
        $_arr_page        = fn_page($_num_attachCount);
        $_str_query       = http_build_query($_arr_search);
        $_arr_yearRows    = $this->mdl_attach->mdl_year(100);
        $_arr_extRows     = $this->mdl_attach->mdl_ext();
        $_arr_attachRows   = $this->mdl_attach->mdl_list(BG_DEFAULT_PERPAGE, $_arr_page['except'], $_arr_search);

        $_arr_searchAll = array(
            'box'        => 'normal',
        ); //搜索设置
        $_arr_searchRecycle = array(
            'box'        => 'recycle',
        ); //搜索设置
        $_arr_attachCount['all']     = $this->mdl_attach->mdl_count($_arr_searchAll);
        $_arr_attachCount['recycle'] = $this->mdl_attach->mdl_count($_arr_searchRecycle);

        $_arr_tpl = array(
            'query'      => $_str_query,
            'pageRow'    => $_arr_page,
            'search'     => $_arr_search,
            'attachCount' => $_arr_attachCount,
            'attachRows'  => $_arr_attachRows, //上传信息
            'yearRows'   => $_arr_yearRows, //目录列表
            'extRows'    => $_arr_extRows, //扩展名列表
        );

        $_arr_tplData = array_merge($this->tplData, $_arr_tpl);

        $this->obj_tpl->tplDisplay('attach_list', $_arr_tplData);
    }


    /**
     * setUpload function.
     *
     * @access private
     * @return void
     */
    private function setUpload() {
        $_arr_allowMimeRows = array();
        $_arr_allowExtRows  = array();

        foreach ($this->mdl_attach->mimeRows as $_key=>$_value) {
            $_arr_allowExtRows[]    = strtolower($_key);
            $_arr_allowMimeRows     = array_merge($_arr_allowMimeRows, $_value);
        }

        $this->allowExtRows     = array_filter(array_unique($_arr_allowExtRows));
        $this->allowMimeRows    = array_filter(array_unique($_arr_allowMimeRows));

        switch (BG_UPLOAD_UNIT) { //初始化单位
            case 'B':
                $this->sizeUnit = 1;
            break;

            case 'KB':
                $this->sizeUnit = 1024;
            break;

            case 'MB':
                $this->sizeUnit = 1024 * 1024;
            break;

            case 'GB':
                $this->sizeUnit = 1024 * 1024 * 1024;
            break;
        }
    }
}
