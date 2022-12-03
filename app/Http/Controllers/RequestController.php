<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use Throwable;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = auth()->user()->id;

        if ($request->mode == 'sent') {
            $requests = ModelsRequest::with('receiver')->whereSenderId($userId)->whereStatus('Sent')->orderBy('id', 'desc')->paginate(10);
        } else {
            $requests = ModelsRequest::with('sender')->whereReceiverId($userId)->whereStatus('Sent')->orderBy('id', 'desc')->paginate(10);
        }
        return $requests;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            ModelsRequest::create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => $request->id
            ]);
        } catch (Throwable $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            ModelsRequest::where('id', $id)->update(['status' => $request->status]);
        } catch (Throwable $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            ModelsRequest::destroy($id);
        } catch (Throwable $error) {
            return response()->json([
                'status' => false,
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
