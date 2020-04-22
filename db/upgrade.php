<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_profspobook_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    return true;
}
