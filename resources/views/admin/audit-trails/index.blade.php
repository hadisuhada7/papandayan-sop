@extends('adminlte::page')

@section('title', 'Papandayan | Audit Trails')

@section('plugins.Datatables', true)
@section('plugins.Toastr', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Audit Trails</h1> 
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Audit Trails</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Data Tables</h3>
                </div>
                <div class="card-body">
                    <table id="datagrid" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th style="width: 200px;">User</th>
                                <th style="width: 200px;">Date & Time</th>
                                <th scope="col">Action</th>
                                <th style="width: 300px;">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $index = 1; 
                            @endphp
                            @foreach($auditTrails as $auditTrail)
                                <tr>
                                    <td scope="row">{{ $index }}</td>
                                    <td>
                                        @if($auditTrail->user)
                                            {{ $auditTrail->user->name }}
                                            @if($auditTrail->user->trashed())
                                                <span class="badge badge-secondary">Deleted</span>
                                            @endif
                                        @else
                                        @endif
                                    </td>
                                    <td>{{ $auditTrail->datetime_stamp->format('d F Y H:i:s') }}</td>
                                    <td>{{ $auditTrail->action }}</td>
                                    <td>{{ $auditTrail->description }}</td>
                                </tr>
                                @php 
                                    $index++; 
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_css')
    <style type="text/css">
        /* Modify DataGrid Filter */
        #datagrid_filter input {
            margin-left: 0 !important;
            width: 180px;
            border-radius: 3px;
        }

        /* Modify DataGrid Length */
        #datagrid_length {
            float: left !important;
        }
    </style>
@stop

@section('adminlte_js')
    @include('partials.toastr')
    <script type="text/javascript">
        $(document).ready(function () {

            // Initialize DataTable
            $("#datagrid").DataTable({
                paging: true,
                ordering: true,
                searching: true,
                responsive: true, 
                lengthChange: true, 
                autoWidth: false,
                language: {
                    emptyTable: "No data available in table",
                    zeroRecords: "No matching records found"
                },

                initComplete: function(settings, json) {
                    $('#datagrid_filter label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();

                    $('#datagrid_filter input')
                        .attr('placeholder', 'Search')
                        .attr('id', 'datagrid_search')
                        .attr('name', 'datagrid_search')
                        .addClass('form-control input-sm');
                    
                    $('#datagrid_length label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });
        });
    </script>
@stop