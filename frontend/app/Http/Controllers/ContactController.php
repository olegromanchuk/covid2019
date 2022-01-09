<?php

namespace App\Http\Controllers;

use App\Http\Improcom\GoAPI;
use App\Http\Improcom\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ContactController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = GoAPI::getContacts();
        if ($data instanceof \Exception) {
            $returnError = Utils::oopsCreateMsgBagAndAddError($data, "Can not load contacts: ");
            return redirect()->back()->withErrors($returnError);
        }

        //get campaigns for "Create Campaign" button select
        $campaigns = GoAPI::getCampaigns();
        return view('contacts', ['data' => $data,
            'campaing_started' => false,
            'campaigns' => $campaigns
        ]);

    }

    public function loadContacts(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::uploadContacts($input);
        if ($data instanceof \Exception) {
            $returnError = Utils::oopsCreateMsgBagAndAddError($data, "Can not upload contacts: ");
            return redirect()->back()->withErrors($returnError);
        }
        if (isset($data) && count($data) > 0) {
            $returnError = new MessageBag();
            foreach ($data as $errorRecordIndex => $errorRecordValue) {
                $returnError->add('justkey', $errorRecordValue->error);
            }
            return redirect()->back()->withErrors($returnError);
        }

        return redirect()->route('contacts');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::createContact($input);

        if (property_exists($data,'error')) {
            return response()->json([
                'error' => $data->error,
                'status_code' => 500], 500);
        }
        return response()->json($data, 200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::updateContacts($input);
//        dd($data);

        if ($data instanceof \Exception) {
            return response()->json([
                'error' => $data->getMessage(),
                'status_code' => $data->getCode()], $data->getCode());
        }

        if (property_exists($data,'error')) {
            return response()->json([
                'error' => $data->error,
                'status_code' => 500], 500);
        }
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::deleteContacts($input);

        if (property_exists($data,'error')) {
            return response()->json([
                'error' => $data->error,
                'status_code' => 500], 500);
        }
        return response()->json($data, 200);
    }
}
