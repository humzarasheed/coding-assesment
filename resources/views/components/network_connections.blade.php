<div class="row justify-content-center mt-5">
    <div class="col-12">
        <div class="card shadow  text-white bg-dark">
            <div class="card-header">Coding Challenge - Network connections</div>
            <div class="card-body">
                <div class="btn-group w-100 mb-3" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off"
                        onclick="getSuggestions();" checked>
                    <label class="btn btn-outline-primary" for="btnradio1" id="get_suggestions_btn">Suggestions
                        ({{ $suggestionsCount }})</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off"
                        onclick="getRequests('sent')">
                    <label class="btn btn-outline-primary" for="btnradio2" id="get_sent_requests_btn">Sent Requests
                        ({{ $sentRequestsCount }})</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off"
                        onclick="getRequests('received')">
                    <label class="btn btn-outline-primary" for="btnradio3" id="get_received_requests_btn">Received
                        Requests({{ $receivedRequestsCount }})</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off"
                        onclick="getConnections()">
                    <label class="btn btn-outline-primary" for="btnradio4" id="get_connections_btn">Connections
                        ({{ $connectionsCount }})</label>
                    <input type="hidden" id="loggedInUser" value="{{Auth::id()}}">
                </div>
                <hr>
                <div id="content">
                    <div id="suggestion">
                        <x-suggestion />
                    </div>
                    <div id="sent_request" style="display: none">
                        <x-request :mode="'sent'" />
                    </div>

                    <div id="received_request" style="display: none">
                        <x-request :mode="'received'" />
                    </div>

                    <div id="connection" style="display: none">
                        <x-connection />
                    </div>
                </div>
            </div>
        </div>
        <div id="loading" class="{{-- d-none --}}">
            <div class="px-2">
                @for ($i = 0; $i < 10; $i++)
                    <x-skeleton />
                @endfor
            </div>
        </div>
    </div>
</div>
