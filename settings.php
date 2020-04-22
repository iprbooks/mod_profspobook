<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('profspobook/org_id', get_string('org_id', 'mod_profspobook'), "", null, PARAM_INT));
    $settings->add(new admin_setting_configtext('profspobook/org_token', get_string('org_token', 'mod_profspobook'), "", null));
    $settings->add(new admin_setting_configtext('profspobook/user_email', get_string('user_email', 'mod_profspobook'), "", null));
    $settings->add(new admin_setting_configtext('profspobook/user_pass', get_string('user_pass', 'mod_profspobook'), "", null));
}
