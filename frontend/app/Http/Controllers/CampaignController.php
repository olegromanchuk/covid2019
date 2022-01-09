<?php

namespace App\Http\Controllers;

use App\Http\Improcom\Campaign;
use App\Http\Improcom\GoAPI;
use App\Http\Improcom\Utils;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = GoAPI::getCampaigns();
        if ($data instanceof \Exception) {
            $returnError = Utils::oopsCreateMsgBagAndAddError($data, "Can not load contacts: ");
            return redirect()->back()->withErrors($returnError);
        }
        return view('campaigns', ['data' => $data,
            'campaing_started' => false,
        ]);

    }

    public function start(Request $request)
    {
        //array:2 [
        //  "_token" => "uwgCa5yMkKV39n2HaMCpZ4MucfhArsa3lnjgba6H"
        //  "campaign_id" => "volvo"
        //]
        Campaign::startCampaign($request->campaign_id);
        return redirect()->route('callrecords', [$request->campaign_id]);

    }



    public function createCampaignFromContacts(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::createCampaignCallRecords($input);
//                dd($data);

        if (property_exists($data,'error')) {
//            return response()->json([
//                'error' => $data->error,
////                'created_records' => $data->created_records,
//                'status_code' => 500,
//            ], 500);
            return response()->json($data, 500);
        }
        return response()->json($data, 200);
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::createCampaign($input);

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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $input = $request->input();
        $data = GoAPI::deleteCampaigns($input);

        if (property_exists($data,'error')) {
            return response()->json([
                'error' => $data->error,
                'status_code' => 500], 500);
        }
        return response()->json($data, 200);
    }
}
