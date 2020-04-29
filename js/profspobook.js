$(document).ready(function () {
    send_request(0, $("#id_profspobook_id").val());

    $('.profspobook-form-control').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("profspobook-search").click();
        }
    });
});

$("#profspobook-search").click(function () {
    send_request();
});

function send_request(page = 0, profspobook_id = 0) {
    var filter = $(".profspobook-filter"),
        title = $("#filter-title").val();

    $.ajax({
        url: M.cfg.wwwroot + "/mod/profspobook/ajax.php?action=getlist&page=" + page + "&title=" + title + "&profspobook_id=" + profspobook_id
    }).done(function (data) {
        clear_details();

        // set data
        $("#profspobook-items-list").scrollTop(0);
        $("#profspobook-items-list").html(data.html);

        // set details click listener
        $(".profspobook-select").click(function () {
            $(".profspobook-item").removeClass("profspobook-item-selected");
            set_details($(this).data("id"));
            $(this).parent().parent().parent().addClass("profspobook-item-selected");
        });

        if (profspobook_id > 0) {
            $('#profspobook-items-details').html(data.details);
        }

        // pagination
        $(".profspobook-page").click(function () {
            send_request($(this).data('page'));
        });
    });
}

function set_details(id) {
    var title = $("#profspobook-item-title-" + id).html();

    $("#id_profspobook_id").val(id);
    $("#id_name").val(title.substring(title.lastIndexOf(">") + 1));
    $("#profspobook-item-detail-image").html($("#profspobook-item-image-" + id).html());
    $("#profspobook-item-detail-title").html(title);
    $("#profspobook-item-detail-pubhouse").html($("#profspobook-item-pubhouse-" + id).html());
    $("#profspobook-item-detail-authors").html($("#profspobook-item-authors-" + id).html());
    $("#profspobook-item-detail-pubyear").html($("#profspobook-item-pubyear-" + id).html());
    $("#profspobook-item-detail-description").html($("#profspobook-item-description-" + id).html());
    $("#profspobook-item-detail-isbn").html($("#profspobook-item-isbn-" + id).html());
    $("#profspobook-item-detail-pubtype").html($("#profspobook-item-pubtype-" + id).html());

    // var rb = $("#profspobook-item-detail-read");
    // rb.attr("href", $("#profspobook-item-url-" + id).attr("href"));
    // if ($("#profspobook-item-url-" + id).attr("href")) {
    //     rb.show();
    // }
}

function clear_details() {
    $("#profspobook-item-detail-image").html('');
    $("#profspobook-item-detail-title").html('');
    $("#profspobook-item-detail-pubhouse").html('');
    $("#profspobook-item-detail-authors").html('');
    $("#profspobook-item-detail-pubyear").html('');
    $("#profspobook-item-detail-description").html('');
    $("#profspobook-item-detail-isbn").html('');
    $("#profspobook-item-detail-pubtype").html('');
}