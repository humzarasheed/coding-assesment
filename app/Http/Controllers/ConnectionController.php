<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class ConnectionController extends Controller
{
    public function index()
    {
        // get list of connections and corresponding common connections count
        $userId = auth()->user()->id;

        $connections = ModelsRequest::where(function ($q) use ($userId) {
            $q->where('receiver_id', $userId)
                ->orWhere('sender_id', $userId);
        })->with(['sender', 'receiver'])->where('status', 'Accepted')->Paginate(10);
        $connectedUsers = ModelsRequest::where(function ($q) use ($userId) {
            $q->where('receiver_id', $userId)
                ->orWhere('sender_id', $userId);
        })->with(['sender', 'receiver'])->where('status', 'Accepted')->get();
        $connectedUserIds = [];
        foreach ($connectedUsers as $connectedUser) {
            if ($connectedUser->receiver_id != $userId) {
                array_push($connectedUserIds, $connectedUser->receiver_id);
            }
            if ($connectedUser->sender_id != $userId) {
                array_push($connectedUserIds, $connectedUser->sender_id);
            }
        }
        // code to get common connections count
        foreach ($connections as $connection) {
            $connected_ids = [];
            $userConnections = [];
            if ($connection->receiver_id != $userId) {
                $userConnections = ModelsRequest::where('sender_id', '!=', $userId)->where('receiver_id', '!=', $userId)->where(function ($q) use ($connection) {
                    $q->where('receiver_id', $connection->receiver_id)
                        ->orWhere('sender_id', $connection->receiver_id);
                })->where('status', 'Accepted')->get();
            }
            if ($connection->sender_id != $userId) {
                $userConnections = ModelsRequest::where('sender_id', '!=', $userId)->where('receiver_id', '!=', $userId)->where(function ($q) use ($connection) {
                    $q->where('receiver_id', $connection->sender_id)
                        ->orWhere('sender_id', $connection->sender_id);
                })->where('status', 'Accepted')->get();
            }
            foreach ($userConnections as $userConnection) {
                if ($userConnection->receiver_id != $userId && ($userConnection->receiver_id != $connection->receiver_id && $userConnection->receiver_id != $connection->sender_id)) {
                    array_push($connected_ids, $userConnection->receiver_id);
                }
                if ($userConnection->sender_id != $userId && ($userConnection->sender_id != $connection->sender_id && $userConnection->sender_id != $connection->receiver_id)) {
                    array_push($connected_ids, $userConnection->sender_id);
                }
            }

            $commonConnectionIds = array_intersect($connectedUserIds, $connected_ids);
            $connection->commonConnectionsCount = User::whereIn('id', $commonConnectionIds)->where('id', '!=', $userId)->count();
            $connection->commonConnections = User::whereIn('id', $commonConnectionIds)->where('id', '!=', $userId)->Paginate(1);
        }
        return response()->json($connections);
    }

    public function getMoreConnectionsInCommon(Request $request)
    {
        $userId = auth()->user()->id;
        $connected_ids = [];
        $connection = ModelsRequest::find($request->connection_id);
        $userConnections = [];
        if ($connection->receiver_id != $userId) {
            $userConnections = ModelsRequest::where('sender_id', '!=', $userId)->where('receiver_id', '!=', $userId)->where(function ($q) use ($connection) {
                $q->where('receiver_id', $connection->receiver_id)
                    ->orWhere('sender_id', $connection->receiver_id);
            })->where('status', 'Accepted')->with(['sender', 'receiver'])->get();
        }
        if ($connection->sender_id != $userId) {
            $userConnections = ModelsRequest::where('sender_id', '!=', $userId)->where('receiver_id', '!=', $userId)->where(function ($q) use ($connection) {
                $q->where('receiver_id', $connection->sender_id)
                    ->orWhere('sender_id', $connection->sender_id);
            })->where('status', 'Accepted')->with(['sender', 'receiver'])->get();
        }
        foreach ($userConnections as $userConnection) {
            if ($userConnection->receiver_id != $userId && ($userConnection->receiver_id != $connection->receiver_id && $userConnection->receiver_id != $connection->sender_id)) {
                array_push($connected_ids, $userConnection->receiver_id);
            }
            if ($userConnection->sender_id != $userId && ($userConnection->sender_id != $connection->sender_id && $userConnection->sender_id != $connection->receiver_id)) {
                array_push($connected_ids, $userConnection->sender_id);
            }
        }
        return User::whereIn('id', $connected_ids)->where('id', '!=', $userId)->Paginate(1);
    }
}
