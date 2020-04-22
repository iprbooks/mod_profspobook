<?php

use Profspo\Sdk\Client;
use Profspo\Sdk\collections\BooksCollection;
use Profspo\Sdk\Managers\IntegrationManager;
use Profspo\Sdk\Models\User;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/mod/profspobook/vendor/autoload.php');

global $CFG;

$id = optional_param('id', 0, PARAM_INT);
$i = optional_param('i', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('profspobook', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('profspobook', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($i) {
    $moduleinstance = $DB->get_record('profspobook', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('profspobook', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_profspobook'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);


$PAGE->set_url('/mod/profspobook/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$orgId = get_config('profspobook', 'org_id');
$orgToken = get_config('profspobook', 'org_token');
$usrEmail = get_config('profspobook', 'user_email');
$usrPass = get_config('profspobook', 'user_pass');

$client = new Client($orgId, $orgToken, $usrEmail, $usrPass);

$style = file_get_contents($CFG->dirroot . "/mod/profspobook/style/profspobook.css");

$integrationManager = new IntegrationManager($client);

$bookCollection = new BooksCollection($client);
$book = $bookCollection->getById($moduleinstance->profspobook_id);
$autoLoginUrl = $integrationManager->generateLoginOrRegisterUrl($USER->email, $USER->email, User::STUDENT, 'books/' . $book->getId());

$template = "<style>" . $style . "</style>
            <div class=\"profspobook-item\" data-id=\"" . $book->getId() . "\">
                <div class=\"row\" style='padding: 10px'>
                    <div id=\"profspobook-item-image-" . $book->getId() . "\" class=\"col-sm-2 pub-image\">
                        <img src=\"" . $book->getCover() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                        <a id=\"profspobook-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                    </div>
                    <div class=\"col-sm-8\">
                        <div id=\"profspobook-item-title-" . $book->getId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                        <div id=\"profspobook-item-title_additional-" . $book->getId() . "\" ><strong>Альтернативное
                            название:</strong> " . $book->getTitleAdditional() . " </div>
                        <div id=\"profspobook-item-pubhouse-" . $book->getId() . "\"><strong>Издательство:</strong> " . $book->getPublishers() . " </div>
                        <div id=\"profspobook-item-authors-" . $book->getId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                        <div id=\"profspobook-item-pubyear-" . $book->getId() . "\"><strong>Год издания:</strong> " . $book->getYear() . " </div>
                        <div id=\"profspobook-item-description-" . $book->getId() . "\" ><strong>Описание:</strong> " . $book->getDescription() . " </div>
                        <div id=\"profspobook-item-isbn-" . $book->getId() . "\" ><strong>ISBN:</strong> " . $book->getIsbn() . " </div>
                        <div id=\"profspobook-item-pubtype-" . $book->getId() . "\" ><strong>Тип издания:</strong> " . $book->getType() . " </div>
                        <br>
                        <a id=\"profspobook-item-detail-read\" class=\"btn btn-secondary\" target=\"_blank\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\">Читать</a>
                    </div>
                </div>
            </div>";

echo $OUTPUT->header();

echo $template;

echo $OUTPUT->footer();
