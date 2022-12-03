<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // get counts of all suggestions, requests, connections
        $userId = auth()->user()->id;

        $addedUsers = ModelsRequest::whereSenderId($userId)->pluck('receiver_id')->toArray();
        $suggestionsCount = User::where('id', '!=', $userId)->whereNotIn('id', $addedUsers)->count();

        $sentRequestsCount = ModelsRequest::whereSenderId($userId)->whereStatus('Sent')->count();
        $receivedRequestsCount = ModelsRequest::whereReceiverId($userId)->whereStatus('Sent')->count();

        $connectionsCount = ModelsRequest::where(function ($query) use ($userId) {
            $query->where('sender_id', '=', $userId)
                ->orWhere('receiver_id', '=', $userId);
        })->where('status', 'Accepted')->count();

        return view('home', compact('suggestionsCount', 'sentRequestsCount', 'receivedRequestsCount', 'connectionsCount'));
    }

    public function suggestions()
    {
        // show suggestions to logged in user
        $userId = auth()->user()->id;

        $addedUsers = ModelsRequest::whereSenderId($userId)->pluck('receiver_id')->toArray();
        $suggestedUsers = User::where('id', '!=', $userId)->whereNotIn('id', $addedUsers)->paginate(10);

        return $suggestedUsers;
    }
}
