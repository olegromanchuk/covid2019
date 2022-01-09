@extends('layouts.app')

@section('content')

    <script>

        $(document).ready(function () {
            $('#history-table').DataTable({
                "pageLength": 50,
                "searching": true,
                "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "columnDefs": [
                    {"title": "ID",  "width": "2%", "targets": 0},
                    {"title": "Name", "width": "20%", "targets": 1},
                    {"title": "Number",  "width": "15%", "targets": 2},
                    {"title": "Email", "width": "10%", "targets": 3},
                    {"title": "Processed", "width": "2%", "targets": 4},
                    {"title": "Time", "width": "30%", "targets": 5},
                    {"title": "Result", "width": "50%", "targets": 6},
                    {"title": "Note", "width": "30%", "targets": 7},
                    {"title": "Campaign", "width": "10%", "targets": 8},
                ]
            });

            $('.dataTables_filter').addClass('pull-left');

        });

    </script>

    <div class="container container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">
                        <p class="card-text">To modify the message dial 646-340-1045. Pin 2071</p>
                    </div>
                </div>

                <!-- sidebar content -->
                {{--                <div id="sidebar" class="col-md-4">--}}
                    @if ($campaing_started == false)
{{--                        @include('includes.sidebar')--}}
                        {{--                        <a href="/start/1">Start campaign</a>--}}
                        {{--                        <a href="/load/1">Load numbers</a>--}}
                    @endif

                <br>
                <table class='table table-striped table-condensed' id='history-table'>
                    <thead>
                    @foreach ($data[0] as $head => $row)
                        <th>{{$head}}</th>
                    @endforeach
                    </thead>
                    <tbody>
                    @foreach ($data as $row)
                        <tr>
                            @foreach ($row as $key2 => $row2)
                                <td>
                                    <div style="text-align: left">{{$row2}}</div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
