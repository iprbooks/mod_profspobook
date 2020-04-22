<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_profspobook_mod_form extends moodleform_mod
{

    public function definition()
    {
        global $CFG;
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $js = file_get_contents($CFG->dirroot . "/mod/profspobook/js/profspobook.js");
        $style = file_get_contents($CFG->dirroot . "/mod/profspobook/style/profspobook.css");
        $main = file_get_contents($CFG->dirroot . "/mod/profspobook/templates/main.html");

        $mform->addElement('html', "<style>" . $style . "</style>");
        $mform->addElement('html', $main);
        $mform->addElement('html', "<script src=\"https://code.jquery.com/jquery-1.9.1.min.js\"></script>");
        $mform->addElement('html', "<script type=\"text/javascript\"> " . $js . " </script>");

        $mform->addElement('text', 'profspobook_id', 'profspobook_id');
        $mform->setType('profspobook_id', PARAM_TEXT);
        $mform->addRule('profspobook_id', null, 'required', null, 'client');

        $mform->addElement('text', 'name', 'name');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');


        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

}
