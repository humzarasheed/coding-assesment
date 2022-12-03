var skeletonId = "skeleton";
var contentId = "content";
var skipCounter = 0;
var takeAmount = 10;

function getRequests(mode) {
    $.ajax({
        url: "connection/request/",
        method: "GET",
        data: { mode: mode },
        success: function (response) {
            $("#loading").hide();
            $("#suggestion").hide();
            $("#sent_request").hide();
            $("#received_request").hide();
            $("#connection").hide();
            $(`#${mode}_request`).show();

            var data = updateRequests(mode, response);

            if (response.next_page_url !== null) {
                data += `<div class="d-flex justify-content-center mt-2 py-3" id="load_more_btn_requests">
                    <button class="btn btn-primary" data-url="${response.next_page_url}&mode=${mode}" onclick="getMoreRequests('${mode}')" id="load_more_requests">Load more</button>
                </div>`;
            }
            $(`#${mode}_request`).empty().append(data);
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

function getMoreRequests(mode) {
    var link = $("#load_more_requests").data("url");
    console.log(link);
    $("#loading").show();

    $.get(link, function (response) {
        console.log(response);
        $("#loading").hide();
        var data = updateRequests(mode, response);
        $(`#${mode}_requests`).append(data);
        if (response.next_page_url === null) {
            $("#load_more_btn_requests").addClass("d-none");
        } else {
            $("#load_more_requests").data("url", response.next_page_url+'&mode='+mode);
        }
    });
}

function updateRequests(mode, response) {
    var data = `<div id="${mode}_requests">`;
    jQuery.each(response.data, function (key, val) {
        data += `<div class="my-2 shadow text-white bg-dark p-1" id="request${val.id}">`;
        if(mode == 'sent') {
        data += `<div class="d-flex justify-content-between">
            <table class="ms-1">
                    <td class="align-middle">${val.receiver.name}</td>
                    <td class="align-middle"> - </td>
                    <td class="align-middle">${val.receiver.email}</td>
                    <td class="align-middle">
            </table>
            <div>
            <button id="cancel_request_btn_" class="btn btn-danger me-1" onclick="deleteRequest('${val.id}')">Withdraw
                Request</button>`;
        }
            else {
            data += `<div class="d-flex justify-content-between">
            <table class="ms-1">
                    <td class="align-middle">${val.sender.name}</td>
                    <td class="align-middle"> - </td>
                    <td class="align-middle">${val.sender.email}</td>
                    <td class="align-middle">
            </table>
            <div>
                    <button id="accept_request_btn_" class="btn btn-primary me-1" onclick="acceptRequest('${val.id}')">Accept</button>`;
            }
            data +=`
            </div>
        </div>
    </div>`;
    });
    data += "</div>";
    return data;
}

function getConnections() {
    $.ajax({
        url: "connection/",
        method: "GET",
        success: function (response) {
            $("#loading").hide();
            $("#suggestion").hide();
            $("#sent_request").hide();
            $("#received_request").hide();
            $("#connection").show();

            var data = updateConnections(response);

            if (response.next_page_url !== null) {
                data += `<div class="d-flex justify-content-center mt-2 py-3" id="load_more_btn_connections">
                    <button class="btn btn-primary" data-url="${response.next_page_url}" onclick="getMoreConnections()" id="load_more_connections">Load more</button>
                </div>`;
            }

            $(`#connection`).empty().append(data);
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

function getMoreConnections() {
    var link = $("#load_more_connections").data("url");
    $("#loading").show();

    $.get(link, function (response) {
        $("#loading").hide();
        var data = updateConnections(response);
        $(`#connections`).append(data);
        if (response.next_page_url === null) {
            $("#load_more_btn_connections").addClass("d-none");
        } else {
            $("#load_more_connections").data("url", response.next_page_url);
        }
    });
}


function updateConnections(response) {
    var userId = $('#loggedInUser').val();
    var data = `<div id="connections">`;
    jQuery.each(response.data, function (key, val) {
        data += `<div class="my-2 shadow text-white bg-dark p-1" id="connection${val.id}">
        <div class="d-flex justify-content-between">
          <table class="ms-1">`;
          if(val.sender.id == userId) {

            data += `<td class="align-middle">${val.receiver.name}</td>
            <td class="align-middle"> - </td>
            <td class="align-middle">${val.receiver.email}</td>
            <td class="align-middle">`;
          }
          else {
            data += `<td class="align-middle">${val.sender.name}</td>
            <td class="align-middle"> - </td>
            <td class="align-middle">${val.sender.email}</td>
            <td class="align-middle">`;
          }
          data +=`</table>
          <div>
            <button style="width: 220px" id="get_connections_in_common_" class="btn btn-primary" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapse_${val.id}" aria-expanded="false" aria-controls="collapseExample">
              Connections in common (${val.commonConnectionsCount})
            </button>
            <button id="create_request_btn_" class="btn btn-danger me-1" onclick="removeConnection('${val.id}')">Remove Connection</button>
          </div>

        </div>
        <div class="collapse" id="collapse_${val.id}">
          <div id="content_${val.id}" class="p-2">`;
          jQuery.each(val.commonConnections.data, function (key, val) {
            data += `<div class="p-2 shadow rounded mt-2  text-white bg-dark">${val.name} - ${val.email}</div>
            </div>`;
          });
          data += `<div class="d-flex align-items-center  mb-2  text-white bg-dark p-1 shadow d-none" style="height: 45px" id="common_loading_${val.id}">
          <strong class="ms-1 text-primary">Loading...</strong>
          <div class="spinner-border ms-auto text-primary me-4" role="status" aria-hidden="true"></div>
        </div>`;
          if (val.commonConnections.next_page_url !== null) {
            data += `<div class="d-flex justify-content-center mt-2 py-3" id="load_more_btn_common_connections${val.id}">
                <button class="btn btn-primary" data-page="${parseInt(response.current_page) + 1}" onclick="getMoreConnectionsInCommon('${val.id}')" id="load_more_common_connections${val.id}">Load more</button>
            </div>`;
          }
        data +=`</div>
      </div>
      `;
    });
    data += "</div>";
    return data;
}

function getMoreConnectionsInCommon(id) {
    var page = $(`#load_more_common_connections${id}`).data("page");
    $(`#common_loading_${id}`).removeClass('d-none');

    $.get(`/connection/connectionsInCommon?connection_id=${id}&page=${page}`, function (response) {
        $(`#common_loading_${id}`).addClass('d-none');
        var data = '';
        jQuery.each(response.data, function (key, val) {
            data += `<div class="p-2 shadow rounded mt-2  text-white bg-dark">${val.name} - ${val.email}</div>
            </div>`;
          });
        $(`#content_${id}`).append(data);
        if (response.next_page_url === null) {
            $(`#load_more_btn_common_connections${id}`).addClass("d-none");
        } else {
            $(`#load_more_common_connections${id}`).data("page", parseInt(response.current_page) + 1 );
        }
    });
}

function getSuggestions() {
    $.ajax({
        url: "/suggestions",
        method: "GET",
        success: function (response) {
            $("#loading").hide();
            $("#suggestion").show();
            $("#sent_request").hide();
            $("#received_request").hide();
            $("#connection").hide();

            var data = updateSuggestions(response);

            if (response.next_page_url !== null) {
                data += `<div class="d-flex justify-content-center mt-2 py-3" id="load_more_btn_suggestions">
                    <button class="btn btn-primary" data-url="${response.next_page_url}" onclick="getMoreSuggestions()" id="load_more_suggestions">Load more</button>
                </div>`;
            }
            $(`#suggestion`).empty().append(data);
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

function getMoreSuggestions() {
    var link = $("#load_more_suggestions").data("url");
    $("#loading").show();

    $.get(link, function (response) {
        $("#loading").hide();
        var data = updateSuggestions(response);
        $(`#suggestions`).append(data);
        if (response.next_page_url === null) {
            $("#load_more_btn_suggestions").addClass("d-none");
        } else {
            $("#load_more_suggestions").data("url", response.next_page_url);
        }
    });
}

function updateSuggestions(response) {
    var data = `<div id="suggestions">`;
    jQuery.each(response.data, function (key, val) {
        data += `<div class="my-2 shadow  text-white bg-dark p-1" id="suggestion${val.id}">
                    <div class="d-flex justify-content-between">
                    <table class="ms-1">
                        <td class="align-middle">${val.name}</td>
                        <td class="align-middle"> - </td>
                        <td class="align-middle">${val.email}</td>
                        <td class="align-middle">
                    </table>
                    <div>
                        <button id="create_request_btn_" class="btn btn-primary me-1" onclick="sendRequest('${val.id}')">Connect</button>
                    </div>
                    </div>
                </div>`;
    });
    data += "</div>";
    return data;
}

function sendRequest(receiverId) {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: "/connection/request",
        method: "POST",
        data: {id: receiverId},
        success: function (response) {
            $('#suggestion'+receiverId).remove();
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

function deleteRequest(requestId) {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: `/connection/request/${requestId}`,
        method: "DELETE",
        success: function (response) {
            $('#request'+requestId).remove();
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

function acceptRequest(requestId) {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: `/connection/request/${requestId}`,
        method: "PUT",
        data: {status: 'Accepted'},
        success: function (response) {
            $('#request'+requestId).remove();
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

function removeConnection(requestId) {
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: `/connection/request/${requestId}`,
        method: "PUT",
        data: {status: 'Removed'},
        success: function (response) {
            $('#connection'+requestId).remove();
        },
        error: function (data) {
            var responseText = JSON.parse(data.responseText);
            console.log(responseText);
        },
    });
}

$(function () {
    getSuggestions();
});
