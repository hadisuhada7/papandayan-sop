@extends('adminlte::page')

@section('title', 'Papandayan | Companies')

@section('plugins.Datatables', true)
@section('plugins.Toastr', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Companies</h1> 
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Companies</li>
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
                                <th style="width: 150px;">Code</th>
                                <th style="width: 300px;">Name</th>
                                <th scope="col">Description</th>
                                <th style="width: 65px;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $index = 1; 
                            @endphp
                            @foreach($companies as $company)
                                <tr>
                                    <td scope="row">{{ $index }}</td>
                                    <td>{{ $company->code }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->description }}</td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary item-edit" data-id="{{ $company->id }}"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-danger item-remove" data-id="{{ $company->id }}"><i class="fas fa-trash-alt"></i></a>
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

            <!-- Company Form Modal -->
            <div class="modal fade" id="modal-company-form" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-add-company">Add Company</h4>
                            <h4 class="modal-title" id="modal-edit-company" style="display:none;">Edit Company</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearTextBox();">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="company-form" method="POST" action="{{ route('admin.companies.store') }}" class="form-horizontal">
                                @csrf
                                
                                <input type="hidden" id="company_id" name="company_id" />
                                
                                <div class="form-group row">
                                    <label for="code" class="col-sm-3 col-form-label">Code <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" maxlength="75" placeholder="Code" required>
                                        <span class="error invalid-feedback">{{ $errors->first('code') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="name" class="col-sm-3 col-form-label">Name <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="75" placeholder="Name" required>
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="description" class="col-sm-3 col-form-label">Description</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="description" name="description" maxlength="255" placeholder="Description">{{ old('description') }}</textarea>
                                        <span class="error invalid-feedback">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" id="btn-modal-cancel" class="btn btn-default" data-dismiss="modal" onclick="clearTextBox();">Cancel</button>
                            <button type="button" id="btn-modal-clear" class="btn btn-secondary" onclick="resetTextBox();">Reset</button>
                            <button type="button" id="btn-modal-update" class="btn btn-primary" style="display: none;">Update</button>
                            <button type="button" id="btn-modal-save" class="btn btn-primary">Save</button>
                        </div>
                    </div>
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
                    { targets: 4, orderable: false }
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
                    
                    $('<button type="button" class="btn btn-sm btn-primary" style="margin-left: 10px;" data-toggle="modal" data-target="#modal-company-form" onclick="clearTextBox();">Add New</button>')
                        .appendTo($('#datagrid_filter'));
                    
                    $('#datagrid_length label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });

            // Add Company Data Function
            function addCompany() {
                $.ajax({
                    url: "{{ route('admin.companies.store') }}",
                    method: "POST",
                    dataType: 'json',
                    data: {
                        code: $('#code').val(),
                        name: $('#name').val(),
                        description: $('#description').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modal-company-form').modal('hide');
                            window.location.href = "{{ route('admin.companies.index') }}";
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.code) {
                                $('#code').addClass('is-invalid');
                                $('#code').next('.invalid-feedback').text(errors.code[0]);
                            }
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('#name').next('.invalid-feedback').text(errors.name[0]);
                            }
                            if (errors.description) {
                                $('#description').addClass('is-invalid');
                                $('#description').next('.invalid-feedback').text(errors.description[0]);
                            }
                        } else {
                            toastr.error('An error occurred while processing your request.');
                        }
                    }
                });
            }

            // Update Company Data Function
            function updateCompany(id) {
                $.ajax({
                    url: "{{ route('admin.companies.index') }}/" + id,
                    method: "PUT",
                    dataType: 'json',
                    data: {
                        code: $('#code').val(),
                        name: $('#name').val(),
                        description: $('#description').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#modal-company-form').modal('hide');
                            window.location.href = "{{ route('admin.companies.index') }}";
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            if (errors.code) {
                                $('#code').addClass('is-invalid');
                                $('#code').next('.invalid-feedback').text(errors.code[0]);
                            }
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('#name').next('.invalid-feedback').text(errors.name[0]);
                            }
                            if (errors.description) {
                                $('#description').addClass('is-invalid');
                                $('#description').next('.invalid-feedback').text(errors.description[0]);
                            }
                        } else {
                            toastr.error('An error occurred while processing your request.');
                        }
                    }
                });
            }

            // Modal Save Button Handler
            $('#btn-modal-save').on('click', function() {
                addCompany();
            });

            // Modal Update Button Handler
            $('#btn-modal-update').on('click', function() {
                updateCompany(
                    $('#company_id').val()
                );
            });

            // Modal Edit Button Handler
            $(document).on("click", ".item-edit", function(){
                var id = $(this).data("id");
                
                $.ajax({
                    url: "{{ route('admin.companies.index') }}/" + id,
                    method: "GET",
                    dataType: 'json',
                    success: function(response) {
                        $('#company_id').val(response.id);
                        $('#code').val(response.code).removeClass('is-invalid');
                        $('#name').val(response.name).removeClass('is-invalid');
                        $('#description').val(response.description).removeClass('is-invalid');
                        $('#modal-company-form').modal('show');
                        $('#modal-add-company').hide();
                        $('#modal-edit-company').show();
                        $('#btn-modal-save').hide();
                        $('#btn-modal-update').show();
                    },
                    error: function() {
                        toastr.error('Failed to load company data.');
                    }
                });
            });

            // Modal Delete Button Handler
            $(document).on("click", ".item-remove", function(){
                selectedRow = $(this).data("id");
                $("#modal-delete").modal("show");
            });

            // Remove Button Handler
            $("#btn-modal-delete").on("click", function(){
                if (selectedRow) {
                    var deleteUrl = "{{ route('admin.companies.index') }}/" + selectedRow;
                    $("#delete-form").attr("action", deleteUrl);
                    $("#delete-form").submit();
                }
            });

            // Clear Form Function
            window.clearTextBox = function() {
                $('#company_id').val('');
                $('#code').val('').removeClass('is-invalid');
                $('#code').next('.invalid-feedback').text('');
                $('#name').val('').removeClass('is-invalid');
                $('#name').next('.invalid-feedback').text('');
                $('#description').val('').removeClass('is-invalid');
                $('#description').next('.invalid-feedback').text('');
                $('#modal-add-company').show();
                $('#modal-edit-company').hide();
                $('#btn-modal-save').show();
                $('#btn-modal-update').hide();
            };

            // Reset Form Function
            window.resetTextBox = function() {
                $('#code').val('').removeClass('is-invalid');
                $('#code').next('.invalid-feedback').text('');
                $('#name').val('').removeClass('is-invalid');
                $('#name').next('.invalid-feedback').text('');
                $('#description').val('').removeClass('is-invalid');
                $('#description').next('.invalid-feedback').text('');
            };
        });
    </script>
@stop