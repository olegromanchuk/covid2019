@extends('layouts.app')
@include('custom_header')

@section('content')

    <script>
        $(document).ready(function () {

            var arrOptions4EditorCreateCampaign = [];
            var camps =  @json($campaigns);

            camps.forEach(a => arrOptions4EditorCreateCampaign.push({label: a['name'], value: a['id']})
        )
            ;


            var editor = new $.fn.dataTable.Editor({
                ajax: {
                    create: {
                        type: 'POST',
                        url: '/js/api/v2/contacts/create',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    edit: {
                        type: 'PUT',
                        url: '/js/api/v2/contacts/edit',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    remove: {
                        type: 'POST',
                        url: '/js/api/v2/contacts/remove',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }
                },
                table: '#contacts-table',
                idSrc: 'id',
                dataSrc: function (json) {
                    return json;
                },
                fields: [
                    {label: 'Id', name: 'id', type: "hidden"},
                    {label: 'Name', name: 'name'},
                    {label: 'Number', name: 'phone_primary'},
                    {label: 'Patient', name: 'email'},
                    {label: 'Note', name: 'description'},
                    {
                        label: "Status:",
                        name: "status",
                        type: "select",
                        options: [
                            "Active",
                            "Inactive",
                        ]
                    },
                ]
            });


            var editorCreateCampaign = new $.fn.dataTable.Editor({
                ajax: {
                    edit: {
                        title: "Add to campaign",
                        type: 'POST',
                        url: '/js/api/v2/create-campaign',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (e) {
                            alert('Created records: ' + e['created_records']);
                        },
                        error: function (xhr, error, thrown) {
                            alert('Created records: ' + xhr['responseJSON']['created_records'] + " \nThere are some errors. Please, click \"OK\" to see the errors. ");
                            $('#modalAlert').modal('show')
                            console.log(xhr);
                        },
                    },
                },
                table: '#contacts-table',
                title: 'Add to campaign',
                idSrc: 'id',
                // dataSrc: function (json) {
                //     return json;
                // },
                fields: [
                    {label: 'Id', name: 'id', type: "hidden"},
                    {
                        "label": "Campaign:",
                        "name": "campaign_id",
                        "type": "select",
                        "options": arrOptions4EditorCreateCampaign,

                    },
                    {label: 'Name', name: 'name', type: "hidden"},
                    {label: 'Number', name: 'phone_primary', type: "hidden"},
                    {label: 'Patient', name: 'email', type: "hidden"},
                    {label: 'Note', name: 'description', type: "hidden"},
                    {
                        label: "Status:",
                        name: "status",
                        type: "select",
                        options: [
                            "Active",
                            "Inactive",
                        ],
                        type: "hidden"
                    },
                ]
            });

            // Set title "Add to campaign"
            editorCreateCampaign.on('open', function (e, mode, action) {
                if ( action === 'edit' ) {
                    editorCreateCampaign.title('Add to campaign');
                }
            });

            //custom submit processing. We need it tpo properly format returned error from the server. Each error line is displayed on new line.
            editorCreateCampaign.on('submitUnsuccessful', function( e,  data ){
                var errMsg = "";
                data['error'].forEach(function(val, ind){
                    errMsg += val + "<br>";
                })
                this.error(errMsg);
                this.buttons( {
                    text: 'Close',
                    action: function () {
                        this.close();
                    },
                    className: 'btn btn-primary',
                } )
            } );

            editorCreateCampaign.title("Add to campaign");

            //check if entered amount is not crazy high!!!
            // editorCreateCampaign.on('preSubmit', function (e, o, action) {
            //
            //         var amount = this.field('amount');
            //         if (! amount.val()) {
            //             amount.error('An amount must be given');
            //         }
            //
            //         if (amount.val() >= 99999) {
            //             amount.error('Really???');
            //         }
            //
            //
            //         // If any error was reported, cancel the submission so it can be corrected
            //         if (this.inError()) {
            //             return false;
            //         }
            //
            // });


            var contactTable = $('#contacts-table').DataTable({
                "pageLength": 100,
                "searching": true,
                "dom": "<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'B><'col-sm-12 col-md-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                columnDefs: [
                    {"title": "ID", "data": "id", "width": "5%", "targets": 0},
                    {"title": "Name", "data": "name", "width": "20%", "targets": 1},
                    {"title": "Number", "data": "phone_primary", "width": "10%", "targets": 2},
                    {"title": "Patient", "data": "email", "width": "10%", "targets": 3},
                    {"title": "Note", "data": "description", "width": "30%", "targets": 4},
                    {"title": "Status", "data": "status", "width": "5%", "targets": 5},
                ],
                select: true,
                buttons: [
                    {extend: 'create', editor: editor},
                    {extend: 'edit', editor: editor},
                    {extend: 'remove', editor: editor},
                    {extend: 'copy'},
                    {extend: 'excel'},
                    {extend: 'csv'},
                    {extend: 'print'},
                    {extend: "edit", text: 'Add to campaign', enabled: false, editor: editorCreateCampaign, formButtons: {
                            text: 'Create',
                            action: function () { this.submit(); },
                            className: 'btn btn-primary'
                        }},
                    // {
                    //     text: 'Create Campaign',
                    //     enabled: false,
                    //     action: function (e, dt, node, config) {
                    //         var arrayData = [];
                    //         dt.rows({selected: true}).every(function (rowIdx, tableLoop, rowLoop) {
                    //             arrayData.push(this.data());
                    //         });
                    //         $.ajax({
                    //             url: '/js/api/v2/create-campaign',
                    //             headers: {
                    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //             },
                    //             type: 'post',
                    //             contentType: 'application/json; charset=utf-8',
                    //             // data: dt.data().toArray(),
                    //             // data: JSON.stringify( dt.row( { selected: true } ).data() ),
                    //             data: JSON.stringify(arrayData),
                    //             // data: arrayData,
                    //             dataType: 'json',
                    //             success: function (returnedData) {
                    //                 console.log(returnedData);
                    //             }
                    //         });
                    //     },
                    // }
                ]
            });

            $('.dataTables_filter').addClass('pull-left');

            //enable "Create Campaign" button
            contactTable.on('select deselect', function () {
                var selectedRows = contactTable.rows({selected: true}).count();
                contactTable.button(7).enable(selectedRows > 0)
            });

            //Limit select on 100 records. If more than 143 it will cause problems when passing selected records to the frontend. Only 143 will be sent to backend
            contactTable.on( 'select', function ( e, dt, type, ix ) {
                var selected = dt.rows({selected: true});
                if ( selected.count() > 100 ) {
                    dt.rows(ix).deselect();
                    alert ("You can select only 100 rows per time")
                }
            } );

        });

    </script>


    <div class="container container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                @yield ('header-for-callrecords-change-ivr')
            </div>
            <div class="col-12">
                <br>
                <table class='table table-striped table-condensed' id="contacts-table">
                    <thead>
                    </thead>
                    <tbody>
                    @if (isset($data) && count($data) > 0)
                        @foreach ($data as $row)
                            <tr>
                                @foreach ($row as $key2 => $row2)
                                    <td style="text-align: left">
                                        {{$row2}}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
