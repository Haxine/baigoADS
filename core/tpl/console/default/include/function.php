<?php function opt_form_process($arr_formList, $lang_opt, $tplRows = array(), $timezoneRows = array(), $timezoneLang = array(), $timezoneType = '', $lang_mod = array(), $rcode = array()) {
    $_str_json = 'var opts_validator_form = {';

    $_count = 1;

    foreach ($arr_formList as $_key=>$_value) {
        //form
        if (defined($_key)) {
            $_this_value = constant($_key);
        } else {
            $_this_value = $_value['default'];
        } ?>
        <div class="form-group">
            <label>
                <?php if (isset($lang_opt[$_key]['label'])) {
                    $_label = $lang_opt[$_key]['label'];
                } else {
                    $_label = $_key;
                }

                echo $_label;

                if ($_value['min'] > 0) { ?> <span class="text-danger">*</span><?php } ?>
            </label>

            <?php switch ($_value['type']) {
                case 'select': ?>
                    <select name="opt[<?php echo $GLOBALS['route']['bg_act']; ?>][<?php echo $_key; ?>]" id="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" data-validate="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" class="form-control">
                        <?php foreach ($_value['option'] as $_key_opt=>$_value_opt) { ?>
                            <option<?php if ($_this_value == $_key_opt) { ?> selected<?php } ?> value="<?php echo $_key_opt; ?>">
                                <?php if (isset($lang_opt[$_key]['option'][$_key_opt])) {
                                    echo $lang_opt[$_key]['option'][$_key_opt];
                                } else {
                                    echo $_value_opt;
                                } ?>
                            </option>
                        <?php } ?>
                    </select>
                <?php break;

                case 'radio':
                    foreach ($_value['option'] as $_key_opt=>$_value_opt) { ?>
                        <div class="form-check">
                            <label for="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>_<?php echo $_key_opt; ?>" class="form-check-label">
                                <input type="radio"<?php if ($_this_value == $_key_opt) { ?> checked<?php } ?> value="<?php echo $_key_opt; ?>" data-validate="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" name="opt[<?php echo $GLOBALS['route']['bg_act']; ?>][<?php echo $_key; ?>]" id="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>_<?php echo $_key_opt; ?>" class="form-check-input">
                                <?php if (isset($lang_opt[$_key]['option'][$_key_opt]['value'])) {
                                    echo $lang_opt[$_key]['option'][$_key_opt]['value'];
                                } else {
                                    echo $_value_opt['value'];
                                } ?>
                            </label>
                        </div>
                        <?php if (isset($lang_opt[$_key]['option'][$_key_opt]['note']) && !fn_isEmpty($lang_opt[$_key]['option'][$_key_opt]['note'])) { ?>
                            <small class="form-text"><?php echo $lang_opt[$_key]['option'][$_key_opt]['note']; ?></small>
                        <?php }
                    }
                break;

                case 'textarea': ?>
                    <textarea name="opt[<?php echo $GLOBALS['route']['bg_act']; ?>][<?php echo $_key; ?>]" id="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" data-validate="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" class="form-control bg-textarea-md"><?php echo $_this_value; ?></textarea>
                <?php break;

                default: ?>
                    <input type="text" value="<?php echo $_this_value; ?>" name="opt[<?php echo $GLOBALS['route']['bg_act']; ?>][<?php echo $_key; ?>]" id="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" data-validate="opt_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>" class="form-control">
                <?php break;
            } ?>

            <small class="form-text" id="msg_<?php echo $GLOBALS['route']['bg_act']; ?>_<?php echo $_key; ?>"></small>

            <?php if (isset($lang_opt[$_key]['note']) && !fn_isEmpty($lang_opt[$_key]['note'])) { ?>
                <small class="form-text"><?php echo $lang_opt[$_key]['note']; ?></small>
            <?php } ?>
        </div>

        <?php //json
        if ($_value['type'] == 'str' || $_value['type'] == 'textarea') {
            $str_msg_min = 'too_short';
            $str_msg_max = 'too_long';
        } else {
            $str_msg_min = 'too_few';
            $str_msg_max = 'too_many';
        }
        $_str_json .= 'opt_' . $GLOBALS['route']['bg_act'] . '_' . $_key . ': {
            len: { min: ' . $_value['min'] . ', max: 900 },
            validate: { selector: "[data-validate=\'opt_' . $GLOBALS['route']['bg_act'] . '_' . $_key . '\']", type: "' . $_value['type'] . '",';
            if (isset($_value['format'])) {
                $_str_json .= ' format: "' . $_value['format'] . '",';
            }
            $_str_json .= ' },
            msg: { ' . $str_msg_min . ': "' . $rcode['x060201'] . $_label . '", ' . $str_msg_max . ': "' . $_label . $rcode['x060202'] . '", format_err: "' . $_label . $rcode['x060203'] . '" }
        }';
        if ($_count < count($arr_formList)) {
            $_str_json .= ',';
        }

        $_count++;
    }

    $_str_json .= '};';

    if ($GLOBALS['route']['bg_act'] == 'base') { ?>
        <div class="form-group">
            <label><?php echo $lang_mod['label']['tpl']; ?> <span class="text-danger">*</span></label>
            <select name="opt[base][BG_SITE_TPL]" id="opt_base_BG_SITE_TPL" data-validate class="form-control">
                <?php foreach ($tplRows as $_key=>$_value) {
                    if ($_value['type'] == 'dir') { ?>
                        <option<?php if (BG_SITE_TPL == $_value['name']) { ?> selected<?php } ?> value="<?php echo $_value['name']; ?>"><?php echo $_value['name']; ?></option>
                    <?php }
               } ?>
            </select>
            <small class="form-text" id="msg_base_BG_SITE_TPL"></small>
        </div>

        <div class="form-group">
            <label><?php echo $lang_mod['label']['timezone']; ?> <span class="text-danger">*</span></label>
            <div class="form-row">
                <div class="col">
                    <select name="timezone_type" id="timezone_type" class="form-control">
                        <?php foreach ($timezoneRows as $_key=>$_value) { ?>
                            <option<?php if ($timezoneType == $_key) { ?> selected<?php } ?> value="<?php echo $_key; ?>">
                                <?php if (isset($timezoneLang[$_key]['title'])) {
                                    echo $timezoneLang[$_key]['title'];
                                } else {
                                    echo $_value['title'];
                                } ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col">
                    <select name="opt[base][BG_SITE_TIMEZONE]" id="opt_base_BG_SITE_TIMEZONE" data-validate class="form-control">
                        <?php foreach ($timezoneRows[$timezoneType]['sub'] as $_key=>$_value) { ?>
                            <option<?php if (BG_SITE_TIMEZONE == $_key) { ?> selected<?php } ?> value="<?php echo $_key; ?>">
                                <?php if (isset($timezoneLang[$timezoneType]['sub'][$_key])) {
                                    echo $timezoneLang[$timezoneType]['sub'][$_key];
                                } else {
                                    echo $_value;
                                } ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <small class="form-text" id="msg_base_BG_SITE_TIMEZONE"></small>
        </div>

        <?php $_str_json .= 'opts_validator_form.opt_base_BG_SITE_TPL = {
            len: { min: 1, max: 0 },
            validate: { type: "select" },
            msg: { too_few: "' . $rcode['x060201'] . $lang_mod['label']['tpl'] . '" }
        };
        opts_validator_form.opt_base_BG_SITE_TIMEZONE = {
            len: { min: 1, max: 0 },
            validate: { type: "select" },
            msg: { too_few: "' . $rcode['x060201'] . $lang_mod['label']['timezone'] . '" }
        };';
    }

    return array(
        'json' => $_str_json,
    );
}


function admin_status_process($str_status, $status_admin) {
    $_str_css = '';

    switch ($str_status) {
        case 'enable':
            $_str_css = 'success';
        break;

        default:
            $_str_css = 'secondary';
        break;
    }

    ?><span class="badge badge-<?php echo $_str_css; ?>"><?php echo $status_admin[$str_status]; ?></span><?php
}


function advert_status_process($arr_advertRow, $status_advert) {
    $_str_css       = '';
    $_str_status    = '';

    if (($arr_advertRow['advert_put_type'] == "date" && $arr_advertRow['advert_put_opt'] < time()) || ($arr_advertRow['advert_put_type'] == "show" && $arr_advertRow['advert_put_opt'] < $arr_advertRow['advert_count_show']) || ($arr_advertRow['advert_put_type'] == "hit" && $arr_advertRow['advert_put_opt'] < $arr_advertRow['advert_count_hit'])) {
        $_str_css = "warning";
        $_str_status = $status_advert['expired'];
    } else {
        if ($arr_advertRow['advert_status'] == 'enable') {
            $_str_css = 'success';
            $_str_status = $status_advert[$arr_advertRow['advert_status']];
        } else {
            $_str_css = 'secondary';
            $_str_status = $status_advert[$arr_advertRow['advert_status']];
        }
    }

    ?><span class="badge badge-<?php echo $_str_css; ?>"><?php echo $_str_status; ?></span><?php
}


function posi_status_process($str_status, $status_posi) {
    $_str_css = '';

    switch ($str_status) {
        case 'enable':
            $_str_css = 'success';
        break;

        default:
            $_str_css = 'secondary';
        break;
    }

    ?><span class="badge badge-<?php echo $_str_css; ?>"><?php echo $status_posi[$str_status]; ?></span><?php
}

function link_status_process($str_status, $status_link) {
    $_str_css = '';

    switch ($str_status) {
        case 'enable':
            $_str_css = 'success';
        break;

        default:
            $_str_css = 'secondary';
        break;
    }

    ?><span class="badge badge-<?php echo $_str_css; ?>"><?php echo $status_link[$str_status]; ?></span><?php
}

function plugin_status_process($str_status, $status_plugin) {
    $_str_css = '';

    switch ($str_status) {
        case 'not':
            $_str_css = 'warning';
        break;

        case 'enable':
            $_str_css = 'success';
        break;

        default:
            $_str_css = 'secondary';
        break;
    }

    if (isset($status_plugin[$str_status])) {
        ?><span class="badge badge-<?php echo $_str_css; ?>"><?php echo $status_plugin[$str_status]; ?></span><?php
    }
}

function pm_status_process($arr_pmRow, $status_pm, $lang, $search_type) {
    $_bold_begin    = '';
    $_bold_end      = '';
    $_str_text      = '';
    $_css_text      = '';
    $_css_label     = '';

    if ($search_type == 'in') {
        if ($arr_pmRow['pm_status'] == 'wait') {
            $_bold_begin    = '<strong>';
            $_bold_end      = '</strong>';
            $_css_text      = 'warning';
            $_css_label     = 'warning';
        } else {
            $_css_label     = 'success';
        }

        $_str_text = $status_pm[$arr_pmRow['pm_status']];
    } else {
        switch ($arr_pmRow['pm_send_status']) {
            case 'wait':
                $_bold_begin    = '<strong>';
                $_bold_end      = '</strong>';
                $_css_text      = 'warning';
                $_css_label     = 'warning';
                $_str_text      = $status_pm[$arr_pmRow['pm_send_status']];
            break;

            case 'revoke':
                $_str_text      = $lang['label']['revoke'];
                $_css_label     = 'secondary';
            break;

            default:
                $_css_label     = 'success';
                $_str_text      = $status_pm[$arr_pmRow['pm_send_status']];
            break;
        }
    }

    return array(
        'bold_begin'    => $_bold_begin,
        'bold_end'      => $_bold_end,
        'str_text'      => $_str_text,
        'css_text'      => $_css_text,
        'css_label'     => $_css_label,
    );
}


function allow_list($arr_consoleMod, $lang_consoleMod = array(), $arr_opt, $lang_opt = array(), $lang_label = array(), $lang_common_page = array(), $arr_allow = array(), $admin_type = '', $is_edit = true) { ?>
    <dl>
        <?php if ($is_edit) { ?>
            <dd>
                <div class="form-check">
                    <label for="chk_all" class="form-check-label">
                        <input type="checkbox" name="chk_all" id="chk_all" data-parent="first" class="form-check-input">
                        <?php echo $lang_label['all']; ?>
                    </label>
                </div>
            </dd>
        <?php }

        foreach ($arr_consoleMod as $_key_m=>$_value_m) { ?>
            <dt>
                <?php if (isset($lang_consoleMod[$_key_m]['main']['title'])) {
                    echo $lang_consoleMod[$_key_m]['main']['title'];
                } else {
                    echo $_value_m['main']['title'];
                } ?>
            </dt>
            <dd>
                <?php if ($is_edit) { ?>
                    <div class="form-check form-check-inline">
                        <label for="allow_<?php echo $_key_m; ?>" class="form-check-label">
                            <input type="checkbox" id="allow_<?php echo $_key_m; ?>" data-parent="chk_all" class="form-check-input">
                            <?php echo $lang_label['all']; ?>
                        </label>
                    </div>
                <?php }

                foreach ($_value_m['allow'] as $_key_s=>$_value_s) {
                    if ($is_edit) { ?>
                        <div class="form-check form-check-inline">
                            <label for="allow_<?php echo $_key_m; ?>_<?php echo $_key_s; ?>" class="form-check-label">
                                <input type="checkbox" name="group_allow[<?php echo $_key_m; ?>][<?php echo $_key_s; ?>]" value="1" id="allow_<?php echo $_key_m; ?>_<?php echo $_key_s; ?>" <?php if (isset($arr_allow[$_key_m][$_key_s]) || $admin_type == 'super') { ?>checked<?php } ?> data-parent="allow_<?php echo $_key_m; ?>" class="form-check-input">
                                <?php if (isset($lang_consoleMod[$_key_m]['allow'][$_key_s])) {
                                    echo $lang_consoleMod[$_key_m]['allow'][$_key_s];
                                } else {
                                    echo $_value_s;
                                } ?>
                            </label>
                        </div>
                    <?php } else { ?>
                        <span>
                            <span class="oi oi-<?php if (isset($arr_allow[$_key_m][$_key_s]) || $admin_type == 'super') { ?>circle-check text-success<?php } else { ?>circle-x text-danger<?php } ?>"></span>
                            <?php if (isset($lang_consoleMod[$_key_m]['allow'][$_key_s])) {
                                echo $lang_consoleMod[$_key_m]['allow'][$_key_s];
                            } else {
                                echo $_value_s;
                            } ?>
                        </span>
                    <?php }
                } ?>
            </dd>
        <?php } ?>

        <dt><?php echo $lang_common_page['opt']; ?></dt>
        <dd>
            <?php if ($is_edit) { ?>
                <div class="form-check form-check-inline">
                    <label for="allow_opt" class="form-check-label">
                        <input type="checkbox" id="allow_opt" data-parent="chk_all" class="form-check-input">
                        <?php echo $lang_label['all']; ?>
                    </label>
                </div>
            <?php }

            foreach ($arr_opt as $_key_s=>$_value_s) {
                if ($is_edit) { ?>
                    <div class="form-check form-check-inline">
                        <label for="allow_opt_<?php echo $_key_s; ?>" class="form-check-label">
                            <input type="checkbox" name="group_allow[opt][<?php echo $_key_s; ?>]" value="1" id="allow_opt_<?php echo $_key_s; ?>" data-parent="allow_opt" <?php if (isset($arr_allow['opt'][$_key_s]) || $admin_type == 'super') { ?>checked<?php } ?> class="form-check-input">
                            <?php if (isset($lang_opt[$_key_s]['title'])) {
                                echo $lang_opt[$_key_s]['title'];
                            } else {
                                echo $_value_s['title'];
                            } ?>
                        </label>
                    </div>
                <?php } else { ?>
                    <span>
                        <span class="oi oi-<?php if (isset($arr_allow['opt'][$_key_s]) || $admin_type == 'super') { ?>circle-check text-success<?php } else { ?>circle-x text-danger<?php } ?>"></span>
                        <?php if (isset($lang_opt[$_key_s]['title'])) {
                            echo $lang_opt[$_key_s]['title'];
                        } else {
                            echo $_value_s['title'];
                        } ?>
                    </span>
                <?php }
            }

            if ($is_edit) { ?>
                <div class="form-check form-check-inline">
                    <label for="allow_opt_dbconfig" class="form-check-label">
                        <input type="checkbox" name="group_allow[opt][dbconfig]" value="1" id="allow_opt_dbconfig" data-parent="allow_opt" <?php if (isset($arr_allow['opt']['dbconfig']) || $admin_type == 'super') { ?>checked<?php } ?> class="form-check-input">
                        <?php echo $lang_common_page['dbconfig']; ?>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <label for="allow_opt_chkver" class="form-check-label">
                        <input type="checkbox" name="group_allow[opt][chkver]" value="1" id="allow_opt_chkver" data-parent="allow_opt" <?php if (isset($arr_allow['opt']['chkver']) || $admin_type == 'super') { ?>checked<?php } ?> class="form-check-input">
                        <?php echo $lang_common_page['chkver']; ?>
                    </label>
                </div>
            <?php } else { ?>
                <span>
                    <span class="oi oi-<?php if (isset($arr_allow['opt']['dbconfig']) || $admin_type == 'super') { ?>circle-check text-success<?php } else { ?>circle-x text-danger<?php } ?>"></span>
                    <?php echo $lang_common_page['dbconfig']; ?>
                </span>
                <span>
                    <span class="oi oi-<?php if (isset($arr_allow['opt']['chkver']) || $admin_type == 'super') { ?>circle-check text-success<?php } else { ?>circle-x text-danger<?php } ?>"></span>
                    <?php echo $lang_common_page['chkver']; ?>
                </span>
            <?php } ?>
        </dd>
    </dl>
<?php }