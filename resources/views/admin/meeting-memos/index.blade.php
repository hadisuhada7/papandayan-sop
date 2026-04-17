@extends('adminlte::page')

@section('title', 'Papandayan | Meeting Memos')

@section('plugins.Datatables', true)
@section('plugins.Toastr', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Meeting Memos</h1> 
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Meeting Memos</li>
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
                                <th style="width: 150px;">Title</th>
                                <th style="width: 200px;">Document Number</th>
                                <th scope="col">Effective Date</th>
                                <th scope="col">Expired Date</th>
                                <th style="width: 100px;">Category</th>
                                <th style="width: 105px;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $index = 1; 
                            @endphp
                            @foreach($meetingMemos as $meetingMemo)
                                <tr>
                                    <td scope="row">{{ $index }}</td>
                                    <td>{{ $meetingMemo->title }}</td>
                                    <td>{{ $meetingMemo->document_number }}</td>
                                    <td>{{ $meetingMemo->effective_date->format('d F Y') }}</td>
                                    <td>{{ $meetingMemo->expired_date ? $meetingMemo->expired_date->format('d F Y') : '-' }}</td>
                                    <td>
                                        @if($meetingMemo->categories->count())
                                            @foreach($meetingMemo->categories as $category)
                                                <span class="badge bg-info">{{ $category->name }}</span>
                                            @endforeach
                                        @else
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.meeting-memos.show', $meetingMemo) }}" class="btn btn-sm btn-info item-detail"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.meeting-memos.edit', $meetingMemo) }}" class="btn btn-sm btn-primary item-edit"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger item-remove" data-id="{{ $meetingMemo->id }}"><i class="fas fa-trash-alt"></i></a>
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

            <!-- Delete Modal -->
            <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Confirmation</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <i class="fa fa-question-circle" style="color: #f39c12; font-size: 48px; margin-bottom: 10px; display: block;"></i>
                                <h4 style="font-size: 16px; margin-bottom: 5px;">Are you sure you want to delete this?</h4>
                                <p class="text-muted">This action cannot be undone.</p>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" id="btn-modal-cancel" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" id="btn-modal-delete" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Form for Delete Action -->
            <form id="delete-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
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

            var selectedRow;

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
                    { targets: 6, orderable: false }
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
                    
                    $('<a href="{{ route('admin.meeting-memos.create') }}" class="btn btn-sm btn-primary" style="margin-left: 10px;">Add New</a>')
                        .appendTo($('#datagrid_filter'));
                    
                    $('#datagrid_length label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });

            // Modal Delete Button Handler
            $(document).on("click", ".item-remove", function(){
                selectedRow = $(this).data("id");
                $("#modal-delete").modal("show");
            });

            // Remove Button Handler
            $("#btn-modal-delete").on("click", function(){
                if (selectedRow) {
                    var deleteUrl = "{{ route('admin.meeting-memos.index') }}/" + selectedRow;
                    $("#delete-form").attr("action", deleteUrl);
                    $("#delete-form").submit();
                }
            });
        });
    </script>
@stop