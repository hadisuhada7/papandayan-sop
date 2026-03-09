@extends('adminlte::page')

@section('title', 'Papandayan | Add User Profile')

@section('plugins.Select2', true)
@section('plugins.Toastr', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Add User Profile</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.user-details.index') }}">User Profiles</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Form User Profile</h3>
                </div>
                <form method="POST" action="{{ route('admin.user-details.store') }}" enctype="multipart/form-data" class="form-horizontal">
                    @csrf
                    <div class="card-body">

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-danger" role="alert">
                                        {{ $error }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="75" placeholder="Name" required>
                                        <span class="error invalid-feedback">{{ $errors->first('name') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="categoryUser" class="col-sm-2 col-form-label">Category User <span class="text-danger">*</span></label>
                                    <div class="col-sm-1">
                                        <div class="custom-control custom-radio" style="padding-top: 0.5rem;">
                                            <input class="custom-control-input" type="radio" id="categoryUserAdmin" name="category_user" value="admin" {{ old('category_user') == 'admin' ? 'checked' : '' }} required>
                                            <label for="categoryUserAdmin" class="custom-control-label">Admin</label>
                                        </div>
                                        <span class="error invalid-feedback">{{ $errors->first('category_user') }}</span>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="custom-control custom-radio" style="padding-top: 0.5rem;">
                                            <input class="custom-control-input" type="radio" id="categoryUserViewer" name="category_user" value="viewer" {{ old('category_user') == 'viewer' ? 'checked' : '' }} required>
                                            <label for="categoryUserViewer" class="custom-control-label">Viewer</label>
                                        </div>
                                        <span class="error invalid-feedback">{{ $errors->first('category_user') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="company" class="col-sm-2 col-form-label">Company <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <select class="form-control select2bs4" style="width: 100%;" id="company" name="company_id" required>
                                            <option value="">-- Select Company --</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error invalid-feedback">{{ $errors->first('company_id') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row" id="access-row">
                                    <label for="access" class="col-sm-2 col-form-label">Access <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        @php
                                            $selectedCategoryIds = (array) old('category_ids', []);
                                        @endphp
                                        <select class="form-control select2-tags" style="width: 100%;" id="access" name="category_ids[]" multiple="multiple" data-placeholder="" required disabled>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds, true) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Please select Company first.</small>
                                        <span class="error invalid-feedback">{{ $errors->first('category_ids') }}</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" maxlength="50" placeholder="Email" required>
                                        <span class="error invalid-feedback">{{ $errors->first('email') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" minlength="6" maxlength="25" placeholder="Password" required>
                                        <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.user-details.index') }}" class="btn btn-default" style="margin-right: 5px">Back</a>
                        <button type="reset" class="btn btn-secondary" style="margin-right: 5px">Reset</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('adminlte_css')
    <style type="text/css">
        /* Modify Select2 */
        .select2-container--bootstrap4 .select2-selection--single:focus,
        .select2-container--bootstrap4.select2-container--focus .select2-selection--single {
            box-shadow: none !important;
        }

        .select2-container--bootstrap4 .select2-selection--multiple:focus,
        .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
            box-shadow: none !important;
        }
    </style>
@stop

@section('adminlte_js')
    @include('partials.toastr')
    <script type="text/javascript">
        $(document).ready(function () {
            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

            // Initialize Select2 for multiple selection
            $('.select2-tags').select2({
                theme: 'bootstrap4',
                placeholder: '',
                allowClear: true
            });

            // Handle category user change (Admin/Viewer)
            function handleCategoryUserChange() {
                const categoryUser = $('input[name="category_user"]:checked').val();
                const $accessRow = $('#access-row');
                const $accessSelect = $('#access');
                
                if (categoryUser === 'admin') {
                    // Hide access row for admin
                    $accessRow.hide();
                    $accessSelect.prop('required', false);
                    
                    // Auto-select all categories for the selected company
                    const companyId = $('#company').val();
                    if (companyId) {
                        $accessSelect.find('option').prop('selected', true).trigger('change');
                    }
                } else if (categoryUser === 'viewer') {
                    // Show access row for viewer
                    $accessRow.show();
                    $accessSelect.prop('required', true);
                }
            }

            // Bind category user radio buttons
            $('input[name="category_user"]').on('change', handleCategoryUserChange);
            
            // Initial check on page load
            handleCategoryUserChange();

            // Handle company change
            $('#company').on('change', function() {
                const companyId = $(this).val();
                const $accessSelect = $('#access');
                const categoryUser = $('input[name="category_user"]:checked').val();
                
                // Clear and disable access select
                $accessSelect.empty().prop('disabled', true).trigger('change');
                
                if (companyId) {
                    // Fetch categories by company
                    $.ajax({
                        url: '{{ route("admin.categories.by-company") }}',
                        type: 'GET',
                        data: { company_id: companyId },
                        success: function(categories) {
                            if (categories.length > 0) {
                                // Populate categories
                                categories.forEach(function(category) {
                                    const option = new Option(category.name, category.id, false, false);
                                    $accessSelect.append(option);
                                });
                                $accessSelect.prop('disabled', false);
                                
                                // If admin, auto-select all categories
                                if (categoryUser === 'admin') {
                                    $accessSelect.find('option').prop('selected', true);
                                }
                                
                                $accessSelect.trigger('change');
                            } else {
                                // No categories found
                                $accessSelect.prop('disabled', true).trigger('change');
                            }
                        },
                        error: function() {
                            alert('Failed to load categories');
                        }
                    });
                }
            });

            // Handle reset button to clear file input
            $('button[type="reset"]').on('click', function() {
                setTimeout(function() {
                    // Reset Select2 for Access
                    $('#company').val(null).trigger('change');
                    $('#access').val(null).prop('disabled', true).trigger('change');
                }, 50);
            });
        });
    </script>
@stop