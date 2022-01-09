@section('header-for-callrecords-change-ivr')
    <div class="card">
        <div class="card-body">
            <p class="card-text">To modify the message dial {{ env('PHONE_NUMBER_IVR_UPDATE') }}. Pin 2071</p>
        </div>
    </div>

    {{--    <!-- sidebar content -->--}}
    {{--    --}}{{--                <div id="sidebar" class="col-md-4">--}}
    {{--    @if ($campaing_started == false)--}}
    {{--        --}}{{--                        @include('includes.sidebar')--}}
    {{--        --}}{{--                        <a href="/start/1">Start campaign</a>--}}
    {{--        --}}{{--                        <a href="/load/1">Load numbers</a>--}}
    {{--    @endif--}}
@endsection

@section('header-for-callrecords-menu')
                @if (isset($campaing_started_info['campaign_number']))
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text float-right"> Campaign {{$campaing_started_info['campaign_number']}} in progress.
                                Started by {{$campaing_started_info['started_by']}}</p>
                        </div>
                    </div>
                @else
                    <div class="float-right">
                    <a data-toggle="modal" data-target="#modalStartCampaign" href="#"> Start
                        campaign </a>
                    </div>
                @endif
@endsection

{{--@section('header-for-callrecords-menu')--}}

{{--    <div class="navbar navbar-default" id="navbarCallRecords">--}}
{{--        <!-- Left Side Of Navbar -->--}}
{{--        <ul class="navbar-nav mr-auto">--}}

{{--            <li class="nav-item">--}}
{{--                @if (isset($campaing_started_info['campaign_number']))--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <p class="card-text"> Campaign {{$campaing_started_info['campaign_number']}} in progress.--}}
{{--                                Started by {{$campaing_started_info['started_by']}}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @else--}}
{{--                    <a class="nav-link" data-toggle="modal" data-target="#modalStartCampaign" href="#"> Start--}}
{{--                        campaign </a>--}}
{{--                @endif--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--    </div>--}}

{{--@endsection--}}

