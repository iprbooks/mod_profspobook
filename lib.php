<?php

defined('MOODLE_INTERNAL') || die();

function profspobook_supports($feature)
{
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

function profspobook_add_instance($moduleinstance, $mform = null)
{
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('profspobook', $moduleinstance);

    return $id;
}

function profspobook_update_instance($moduleinstance, $mform = null)
{
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('profspobook', $moduleinstance);
}

function profspobook_delete_instance($id)
{
    global $DB;

    $exists = $DB->get_record('profspobook', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('profspobook', array('id' => $id));

    return true;
}
