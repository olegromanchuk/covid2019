@extends('layouts.app')
@include('custom_header')

@section('content')

    <script>

        $(document).ready(function () {


            var editor = new $.fn.dataTable.Editor({
                // ajax: {
                //     create: {
                //         type: 'POST',
                //         url: '/js/api/v2/contacts/create',
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         }
                //     },
                //     edit: {
                //         type: 'PUT',
                //         url: '/js/api/v2/contacts/edit',
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         }
                //     },
                //     remove: {
                //         type: 'POST',
                //         url: '/js/api/v2/contacts/remove',
                //         headers: {
                //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //         }
                //     }
                // },
                table: '#history-table',
                idSrc: 'id',
                dataSrc: function (json) {
                    return json;
                },
                fields: [
                    {label: 'Id', name: 'id', type: "hidden"},
                    {label: 'Name', name: 'main_contact'},
                    {label: 'Number', name: 'main_contact_phone'},
                    {label: 'Patient', name: 'email_address'},
                    {label: 'Note', name: 'description'},
                    {label: 'Processed', name: 'processed'},
                ]
            });


            editor.on('preSubmit', function (e, data, action) {
                if (action === 'remove') {

                    $.each(data.data, function (key, values) {
                        consle.log(data.data['processed']);
                    });
                    // var processed = this.field( 'processed' );
                    //
                    // // Only validate user input values - different values indicate that
                    // // the end user has not entered a value
                    // if ( ! firstName.isMultiValue() ) {
                    //     if ( ! firstName.val() ) {
                    //         firstName.error( 'A first name must be given' );
                    //     }
                    //
                    //     if ( firstName.val().length >= 20 ) {
                    //         firstName.error( 'The first name length must be less that 20 characters' );
                    //     }
                    // }
                    //
                    // // ... additional validation rules
                    //
                    // // If any error was reported, cancel the submission so it can be corrected
                    // if ( this.inError() ) {
                    //     return false;
                    // }

                }
            });


            $('#history-table').DataTable({
                "pageLength": 100,
                "searching": true,
                select: true,
                // "searchPane": true,
                "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'P>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "columnDefs": [
                    {"title": "ID", "data": "id", "width": "2%", "targets": 0},
                    {"title": "Name", "data": "main_contact", "width": "20%", "targets": 1},
                    {"title": "Number", "data": "main_contact_phone", "width": "15%", "targets": 2},
                    {"title": "Patient", "data": "email_address", "width": "10%", "targets": 3},
                    {"title": "Processed", "data": "processed", "width": "2%", "targets": 4},
                    {"title": "Time", "data": "processed_datetime", "width": "30%", "targets": 5},
                    {"title": "Result", "data": "result", "width": "50%", "targets": 6},
                    {"title": "Note", "data": "description", "width": "30%", "targets": 7},
                    {"title": "Campaign", "data": "campaign_name", "width": "10%", "targets": 8},
                ],
                "buttons": [
                    // {extend: 'remove', editor: editor},
                    {extend: 'copy'},
                    {extend: 'excel'},
                    {extend: 'csv'},
                    {extend: 'print'},
                    // {extend: 'searchPanes'},
                ],
                "searchPanes": {
                    columns: [8]
                }
            });

            $('.dataTables_filter').addClass('pull-left');

        });

    </script>



    <div class="container container-fluid">
        <div class="row">
            <div class="col-6">
                @yield ('header-for-callrecords-change-ivr')
            </div>
            <div class="col-6">
                @yield ('header-for-callrecords-menu')
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12">
                <form action="/callrecords" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="campaign_id">To display call records select a campaign</label>
                        <select name="campaign_id" class="form-control" id="campaign_id">
                            {!! $campaing_info_select !!}
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Go</button>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($data))
            <br>
            <table class='table table-striped table-condensed' id='history-table'>
                <thead>
                </thead>
                <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $key2 => $row2)
                            <td style="text-align: left">
                                {{$row2}}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

    </div>

    <div class="modal fade" id="modalStartCampaign" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <form action="/start-campaign" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Start dialing campaign</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="campaign_id">Select campaign</label>
                            <select name="campaign_id" class="form-control" id="campaign_id">
                                {!! $campaing_info_select !!}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Start</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection
