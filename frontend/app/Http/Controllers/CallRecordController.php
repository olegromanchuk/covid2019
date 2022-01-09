<?php

namespace App\Http\Controllers;

use App\Http\Covid2019\Campaign;
use App\Http\Covid2019\Utils;
use App\Http\Covid2019\GoAPI;
use Illuminate\Http\Request;

class CallRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //get campaigns for "Start Campaign" form
        $campaigns = GoAPI::getCampaigns();
        $campaign_select4startCampaign = Utils::generateSelectOptions($campaigns);

        #check if campaign started
        $startedCampaignData = [];
        if (file_exists("runcampaign")) {
            $startedCampaignRawData = file_get_contents("runcampaign");
            $arrStartedCampaignData = explode(",", $startedCampaignRawData);
            $startedCampaignData['campaign_number'] = $arrStartedCampaignData[0];
            $startedCampaignData['started_by'] = $arrStartedCampaignData[1];
        }

        return view('callrecords',
            [
                'campaing_started_info' => $startedCampaignData,
                'campaing_info_select' => $campaign_select4startCampaign,
            ]
        );
    }


    //TODO - remove it
    public function loadnumbers(Request $request, $campaign_id = 1)
    {
        $input = $request->input();
        $data = str_getcsv($input['numbers'], "\n");
        $rows = [];
        foreach ($data as &$row) {
            $row = str_getcsv($row, ",");
            $rows[] = $row;
        }

        $classCampaign = new Campaign();
        $data = $classCampaign->setAsteriskPredictiveDialerRecords($rows);

        return redirect()->route('home');
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
        //
    }

    /**
     * Just extract campaign ID from here
     */
    public function show(Request $request)
    {
        //array:2 [▼
        //  "_token" => "uwgCa5yMkKV39n2HaMCpZ4MucfhArsa3lnjgba6H"
        //  "campaign_id" => "5"
        //]
        $input = $request->input();
        return redirect()->route('callrecords', [$input['campaign_id']]);

    }

    public function showCampaignCallRecords($campaign_id)
    {

        //TODO - rewrite on golang API
        $classCampaign = new Campaign();
        $data = $classCampaign->getAsteriskPredictiveDialerRecords($campaign_id);

        //get campaigns for "Start Campaign" form
        $campaigns = GoAPI::getCampaigns();
        $campaign_select4startCampaign = Utils::generateSelectOptions($campaigns);

        #check if campaign started
        $startedCampaignData = [];
        if (file_exists("runcampaign")) {
            $startedCampaignRawData = file_get_contents("runcampaign");
            $arrStartedCampaignData = explode(",", $startedCampaignRawData);
            $startedCampaignData['campaign_number'] = $arrStartedCampaignData[0];
            $startedCampaignData['started_by'] = $arrStartedCampaignData[1];
        }


        return view('callrecords',
            [
                'data' => $data,
                'campaing_started_info' => $startedCampaignData,
                'campaing_info_select' => $campaign_select4startCampaign,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
