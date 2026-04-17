@extends('adminlte::page')

@section('title', 'Papandayan | Meeting Memo Detail')

@section('plugins.Datatables', true)
@section('plugins.Toastr', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Meeting Memo Detail</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.meeting-memos.index') }}">Meeting Memos</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Meeting Memo Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Title:</strong>
                            <p>{{ $meetingMemo->title }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Document Number:</strong>
                            <p>{{ $meetingMemo->document_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Company:</strong>
                            <p>{{ $meetingMemo->company->name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Categories:</strong>
                            <p>
                                @if($meetingMemo->categories->count())
                                    @foreach($meetingMemo->categories as $category)
                                        <span class="badge bg-info">{{ $category->name }}</span>
                                    @endforeach
                                @else
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <strong>Effective Date:</strong>
                            <p>{{ $meetingMemo->effective_date->format('d F Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Expired Date:</strong>
                            <p>{{ $meetingMemo->expired_date ? $meetingMemo->expired_date->format('d F Y') : '-' }}</p>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-12">
                            <strong>Rules:</strong>
                            <div class="border rounded p-3 bg-light">
                                {!! ($meetingMemo->rules) !!}
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Documents List</h3>
                </div>
                <div class="card-body">
                    <table id="datagrid" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th style="width: 300px;">Name</th>
                                <th scope="col">Attachment</th>
                                <th style="width: 35px;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = 1;
                            @endphp
                            @foreach($meetingMemo->documents as $document)
                                <tr>
                                    <td scope="row">{{ $index }}</td>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ basename($document->attachment) }}</td>
                                    <td class="text-center">
                                        @if($document->attachment)
                                            <a href="{{ Storage::url($document->attachment) }}" target="_blank" class="btn btn-sm btn-success" title="Download Document">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
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

@section('css')
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

@section('js')
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

                columnDefs: [
                    { targets: 3, orderable: false }
                ],
                
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