@extends('layouts.app')
@include('custom_header')

@section('content')

    <script>

        $(document).ready(function () {

            var editor = new $.fn.dataTable.Editor({
                ajax: {
                    create: {
                        type: 'POST',
                        url: '/js/api/v2/campaigns/create',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    remove: {
                        type: 'POST',
                        url: '/js/api/v2/campaigns/remove',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }
                },
                table: '#campaigns-table',
                idSrc: 'id',
                dataSrc: function (json) {
                    return json;
                },
                fields: [
                    {label: 'Id', name: 'id', type: "hidden"},
                    {label: 'Name', name: 'name'},
                    {label: 'Note', name: 'description'},
                ]
            });


            $('#campaigns-table').DataTable({
                "pageLength": 50,
                "searching": true,
                "dom": "<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'B><'col-sm-12 col-md-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "columnDefs": [
                    {"title": "ID", "data": "id", "width": "2%", "targets": 0},
                    {"title": "Name", "data": "name", "width": "20%", "targets": 1},
                    {"title": "Note", "data": "description", "width": "30%", "targets": 2},
                ],
                select: {
                    style: 'single'
                },
                buttons: [
                    {extend: 'create', editor: editor},
                    // {extend: 'remove', editor: editor},
                    {extend: 'csv'},
                    {extend: 'print'},
                ],
            });

            $('.dataTables_filter').addClass('pull-left');

        });

    </script>

    <div class="container container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                @yield ('header-for-callrecords-change-ivr')
            </div>
            <div class="col-12">
                <br>
                <table class='table table-striped table-condensed' id='campaigns-table'>
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
