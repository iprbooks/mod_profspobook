<?php

use Profspo\Sdk\Client;
use Profspo\Sdk\collections\BooksCollection;
use Profspo\Sdk\Managers\IntegrationManager;
use Profspo\Sdk\Models\Book;

define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/profspobook/vendor/autoload.php');

require_login();

$page = optional_param('page', 0, PARAM_INT);
$title = optional_param('title', "", PARAM_TEXT);
$id = optional_param('profspobook_id', 0, PARAM_TEXT);


$orgId = get_config('profspobook', 'org_id');
$orgToken = get_config('profspobook', 'org_token');
$usrEmail = get_config('profspobook', 'user_email');
$usrPass = get_config('profspobook', 'user_pass');

$content = "";
$details = "";
try {
    $client = new Client($orgId, $orgToken, $usrEmail, $usrPass);
} catch (Exception $e) {
    die();
}

$integrationManager = new IntegrationManager($client);
//$autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT);
$autoLoginUrl = '';

$bc = new BooksCollection($client);

if ($id > 0) {
    $book = $bc->getById($id);
    $details .= getDetails($book, $autoLoginUrl);
}

$booksCollection = new BooksCollection($client);
$booksCollection->setFilter(BooksCollection::TITLE, $title);
$booksCollection->setOffset($booksCollection->getLimit() * $page);
$booksCollection->get();

$message = $booksCollection->getMessage();

foreach ($booksCollection as $book) {
    $content .= getTemplate($book, $autoLoginUrl);
}

$content .= pagination($booksCollection->getTotal(), $page + 1);

if (mb_strlen($content) < 200) {
    $content = '<div style="font-size: 150%; text-align: center;">' . $message . '</div>' . $content;
}

echo json_encode(['page' => $page, 'html' => $content, 'details' => $details]);

function getTemplate(Book $book, $autoLoginUrl)
{
    return "<div class=\"profspobook-item\" data-id=\"" . $book->getId() . "\">
                    <div class=\"row\" style='padding: 10px'>
                        <div id=\"profspobook-item-image-" . $book->getId() . "\" class=\"col-sm-3 pub-image\">
                            <img src=\"" . $book->getCover() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                            <a id=\"profspobook-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                        </div>
                        <div class=\"col-sm-8\">
                            <div id=\"profspobook-item-title-" . $book->getId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                            <div id=\"profspobook-item-title_additional-" . $book->getId() . "\" hidden><strong>Альтернативное
                                название:</strong> " . $book->getTitleAdditional() . " </div>
                            <div id=\"profspobook-item-pubhouse-" . $book->getId() . "\"><strong>Издательство:</strong> " . $book->getPublishers() . " </div>
                            <div id=\"profspobook-item-authors-" . $book->getId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                            <div id=\"profspobook-item-pubyear-" . $book->getId() . "\"><strong>Год издания:</strong> " . $book->getYear() . " </div>
                            <div id=\"profspobook-item-description-" . $book->getId() . "\" hidden><strong>Описание:</strong> " . $book->getDescription() . " </div>
                            <div id=\"profspobook-item-isbn-" . $book->getId() . "\" hidden><strong>Ключевые слова:</strong> " . $book->getIsbn() . " </div>
                            <div id=\"profspobook-item-pubtype-" . $book->getId() . "\" hidden><strong>Тип издания:</strong> " . $book->getType() . " </div>
                            <br>
                            <a  class=\"btn btn-secondary profspobook-select\" data-id=\"" . $book->getId() . "\">Выбрать</a>
                        </div>
                    </div>
                </div>";
}

function getDetails(Book $book, $autoLoginUrl)
{
    return "<div class=\"row\">
                <div id=\"profspobook-item-detail-image\" class=\"col-sm-5 pub-image\">
                            <img src=\"" . $book->getCover() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                            <a id=\"profspobook-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                        </div>
                <div class=\"col-sm-7\">
                    <br>
                    <div id=\"profspobook-item-detail-title\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                    <div id=\"profspobook-item-detail-title_additional\"></div>
                    <div id=\"profspobook-item-detail-pubhouse\"><strong>Издательство:</strong> " . $book->getPublishers() . " </div>
                    <div id=\"profspobook-item-detail-authors\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                    <div id=\"profspobook-item-detail-pubtype\"><strong>Тип издания:</strong> " . $book->getType() . " </div>
                    <div id=\"profspobook-item-detail-pubyear\"><strong>Год издания:</strong> " . $book->getYear() . " </div>
                    <br>
                    <a id=\"profspobook-item-detail-read\" style=\"display: none\" class=\"btn btn-secondary\" target=\"_blank\">Читать</a>
                </div>
            </div>
            <br>
            <div id=\"profspobook-details-fields\">
                <div id=\"profspobook-item-detail-description\"><strong>Описание:</strong> " . $book->getDescription() . " </div>
                <br>
                <div id=\"profspobook-item-detail-isbn\"><strong>Ключевые слова:</strong> " . $book->getIsbn() . " </div>
            </div>";
}

function pagination($count, $page)
{
    $output = '';
    $output .= "<nav aria-label=\"Страница\" class=\"pagination pagination-centered justify-content-center\"><ul class=\"mt-1 pagination \">";
    $pages = ceil($count / 10);


    if ($pages > 1) {

        if ($page > 1) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . ($page - 2) . "\" class=\"page-link profspobook-page\" ><span>«</span></a></li>";
        }
        if (($page - 3) > 0) {
            $output .= "<li class=\"page-item \"><a data-page=\"0\" class=\"page-link profspobook-page\">1</a></li>";
        }
        if (($page - 3) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link profspobook-page\">...</span></li>";
        }


        for ($i = ($page - 2); $i <= ($page + 2); $i++) {
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($i - 1) . "\" class=\"page-link profspobook-page\" >" . $i . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($i - 1) . "\" class=\"page-link profspobook-page\">" . $i . "</a></li>";
        }


        if (($pages - ($page + 2)) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link profspobook-page\">...</span></li>";
        }
        if (($pages - ($page + 2)) > 0) {
            if ($page == $pages)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link profspobook-page\" >" . $pages . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link profspobook-page\">" . $pages . "</a></li>";
        }
        if ($page < $pages) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . $page . "\" class=\"page-link profspobook-page\"><span>»</span></a></li>";
        }

    }

    $output .= "</ul></nav>";
    return $output;
}

die();