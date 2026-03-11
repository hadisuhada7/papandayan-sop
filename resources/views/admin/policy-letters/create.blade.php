@extends('adminlte::page')

@section('title', 'Papandayan | Add Policy Letter')

@section('plugins.TempusDominusBs4', true)
@section('plugins.Summernote', true)
@section('plugins.Select2', true)
@section('plugins.Toastr', true)
@section('plugins.Dropzone', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Add Policy Letter</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.policy-letters.index') }}">Policy Letters</a></li>
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
                    <h3 class="card-title">Form Policy Letter</h3>
                </div>
                <form method="POST" action="{{ route('admin.policy-letters.store') }}" enctype="multipart/form-data" class="form-horizontal">
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
                                <div class="form-group row">
                                    <label for="Category" class="col-sm-2 col-form-label">Category <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        @php
                                            $selectedCategoryIds = (array) old('category_ids', []);
                                        @endphp
                                        <select class="form-control select2-tags" style="width: 100%;" id="category" name="category_ids[]" multiple="multiple" data-placeholder="" required disabled>
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
                                    <label for="title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" maxlength="255" placeholder="Title" required>
                                        <span class="error invalid-feedback">{{ $errors->first('title') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="documentNumber" class="col-sm-2 col-form-label">Document Number <span class="text-danger">*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="documentNumber" name="document_number" value="{{ old('document_number') }}" maxlength="100" placeholder="Document Number" required>
                                        <span class="error invalid-feedback">{{ $errors->first('document_number') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="effectiveDate" class="col-sm-2 col-form-label">Effective Date <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <div class="input-group date" id="effectiveDate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" name="effective_date" value="{{ old('effective_date') }}" data-target="#effectiveDate" placeholder="dd-MM-yyyy" required/>
                                            <div class="input-group-append" data-target="#effectiveDate" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <span class="error invalid-feedback">{{ $errors->first('effective_date') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="expiredDate" class="col-sm-2 col-form-label">Expired Date <span class="text-danger">*</span></label>
                                    <div class="col-sm-4">
                                        <div class="input-group date" id="expiredDate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" name="expired_date" value="{{ old('expired_date') }}" data-target="#expiredDate" placeholder="dd-MM-yyyy" required/>
                                            <div class="input-group-append" data-target="#expiredDate" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <span class="error invalid-feedback">{{ $errors->first('expired_date') }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="rules" class="col-sm-2 col-form-label">Rules <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="rules" name="rules" maxlength="65535" placeholder="Rules">{{ old('rules') }}</textarea>
                                        <span class="error invalid-feedback">{{ $errors->first('rules') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Documents</legend>
                                    <div class="row">
                                        <div class="col-12">
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
                                                    <tr>
                                                        <td colspan="4" class="text-center">No data available in table</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-document-form" onclick="clearTextBox();">Add Document</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.policy-letters.index') }}" class="btn btn-default" style="margin-right: 5px">Back</a>
                        <button type="reset" class="btn btn-secondary" style="margin-right: 5px">Reset</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>

            <!-- Document Form Modal -->
            <div class="modal fade" id="modal-document-form" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-add-document">Add Document</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearTextBox();">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="document-form" method="" action="" class="form-horizontal">
                                <div class="form-group">
                                    <label for="document_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="document_name" name="document_name" maxlength="255" placeholder="Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="document_attachment">Attachment <span class="text-danger">*</span></label>
                                    <div id="documentDropzone" class="dropzone"></div>
                                    <small class="text-muted">Upload PDF file (Max: 5MB)</small>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" id="btn-modal-cancel" class="btn btn-default" data-dismiss="modal" onclick="clearTextBox();">Cancel</button>
                            <button type="button" id="btn-modal-clear" class="btn btn-secondary" onclick="resetTextBox();">Reset</button>
                            <button type="button" id="btn-modal-save" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
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

        /* Modify Summernote Editor */
        .note-editor.card {
            margin-bottom: 0px !important;
        }

        /* Modify Dropzone */
        .dropzone {
            border: 2px dashed #007bff !important;
            border-radius: 5px !important;
            background: #f8f9fa !important;
            padding: 20px !important;
            min-height: 150px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .dropzone:hover {
            background: #e9ecef !important;
            border-color: #0056b3 !important;
        }

        .dropzone .dz-message {
            font-size: 16px !important;
            color: #6c757d !important;
            margin: 0 !important;
            text-align: center !important;
        }

        .dropzone .dz-preview {
            margin: 10px !important;
        }

        .dropzone .dz-preview .dz-image {
            border-radius: 5px !important;
        }

        .dropzone .dz-preview .dz-details {
            background: rgba(255, 255, 255, 0.9) !important;
        }

        .dropzone .dz-preview .dz-progress {
            opacity: 1 !important;
            z-index: 1000 !important;
            pointer-events: none !important;
            position: absolute !important;
            height: 16px !important;
            left: 50% !important;
            top: 50% !important;
            margin-top: -8px !important;
            width: 80px !important;
            margin-left: -40px !important;
            background: rgba(255, 255, 255, 0.9) !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            transition: opacity 0.4s ease !important;
        }

        .dropzone .dz-preview .dz-progress .dz-upload {
            background: #007bff !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: 0 !important;
            transition: width 0.3s ease-in-out !important;
        }

        .dropzone .dz-preview.dz-success .dz-progress {
            opacity: 0 !important;
            transition: opacity 0.4s ease-in !important;
        }

        .dropzone .dz-preview.dz-error .dz-progress {
            opacity: 0 !important;
        }

        .dropzone .dz-preview .dz-remove {
            font-size: 12px !important;
            color: #dc3545 !important;
        }

        .dropzone .dz-preview .dz-remove:hover {
            color: #c82333 !important;
            text-decoration: underline !important;
        }

        /* Fieldset Styles */
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
            border-radius: 3px;
            padding: 0 14px 14px 14px !important;
            margin: 0px 0 10px 0 !important;
            -webkit-box-shadow: 0px 0px 0px 0px #000;
            box-shadow: 0px 0px 0px 0px #000;
            box-sizing: border-box;
            width: 100%;
            overflow: hidden;
        }

        legend.scheduler-border {
            width: auto;
            padding: 0 10px;
            border-bottom: none;
            font-size: 16px;
            font-family: 'Source Sans Pro', sans-serif;
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
@stop

@section('adminlte_js')
    @include('partials.toastr')
    <script type="text/javascript">
        // Disable auto discover for Dropzone
        Dropzone.autoDiscover = false;
        
        // Document List and Index for managing multiple documents
        let documentList = [];
        let documentIndex = 0;
        let myDropzone;
        
        $(document).ready(function () {
            // Initialize DatePicker
            $('#effectiveDate, #expiredDate').datetimepicker({
                format: 'DD-MM-YYYY'
            });

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

            // Initialize Summernote Editor
            const $rules = $('#rules');
            $rules.summernote();

            // Initialize Dropzone
            myDropzone = new Dropzone("#documentDropzone", {
                url: "/fake-upload",
                autoProcessQueue: false,
                maxFiles: 1,
                maxFilesize: 5,
                acceptedFiles: '.pdf',
                addRemoveLinks: true,
                dictDefaultMessage: '<i class="fa fa-upload fa-3x" style="display: block; margin-bottom: 10px; color: #007bff;"></i><p style="margin: 0; font-size: 16px;">Drag and drop a file here or click to upload</p>',
                dictFileTooBig: 'File size must not exceed 5MB.',
                dictInvalidFileType: 'You can only upload PDF files.',
                dictMaxFilesExceeded: 'You can only upload 1 file.',
                dictRemoveFile: 'Remove file',
                init: function() {
                    this.on("addedfile", function(file) {
                        // Only keep one file
                        if (this.files.length > 1) {
                            this.removeFile(this.files[0]);
                        }
                        
                        // Simulate upload progress for visual feedback
                        let progress = 0;
                        const interval = setInterval(() => {
                            progress += 10;
                            file.previewElement.querySelector('.dz-progress .dz-upload').style.width = progress + '%';
                            
                            if (progress >= 100) {
                                clearInterval(interval);
                                // Mark as complete after animation
                                setTimeout(() => {
                                    file.status = Dropzone.SUCCESS;
                                    this.emit("success", file);
                                    this.emit("complete", file);
                                }, 200);
                            }
                        }, 50);
                    });
                    
                    this.on("error", function(file, errorMessage) {
                        toastr.warning(errorMessage);
                        this.removeFile(file);
                    });
                }
            });

            // Handle company change
            $('#company').on('change', function() {
                const companyId = $(this).val();
                const $categorySelect = $('#category');
                
                // Clear and disable category select
                $categorySelect.empty().prop('disabled', true).trigger('change');
                
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
                                    $categorySelect.append(option);
                                });
                                $categorySelect.prop('disabled', false).trigger('change');
                            } else {
                                // No categories found
                                $categorySelect.prop('disabled', true).trigger('change');
                            }
                        },
                        error: function() {
                            toastr.warning('Failed to load categories');
                        }
                    });
                }
            });

            // Modal Save Button Handler
            $('#btn-modal-save').on('click', function() {
                const name = $('#document_name').val().trim();
                const files = myDropzone.getAcceptedFiles();
                
                // Validate
                if (!name) {
                    toastr.warning('Please enter document name.');
                    $('#document_name').focus();
                    return;
                }
                
                if (files.length === 0) {
                    toastr.warning('Please upload a PDF file.');
                    return;
                }
                
                // Add to document list
                const file = files[0];
                documentList.push({
                    index: documentIndex,
                    name: name,
                    file: file,
                    fileName: file.name
                });
                
                // Update table
                updateDocumentTable();
                
                // Close modal
                $('#modal-document-form').modal('hide');
                clearDocumentForm();
                
                documentIndex++;
            });

            // Function to update document table
            function updateDocumentTable() {
                const tbody = $('#datagrid tbody');
                tbody.empty();
                
                if (documentList.length === 0) {
                    tbody.append('<tr><td colspan="4" class="text-center">No data available in table</td></tr>');
                } else {
                    documentList.forEach(function(doc, idx) {
                        const row = `
                            <tr data-index="${doc.index}">
                                <td>${idx + 1}</td>
                                <td>${doc.name}</td>
                                <td>${doc.fileName}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-document" data-index="${doc.index}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }
            }

            // Modal Delete Button Handler
            $(document).on('click', '.btn-delete-document', function() {
                const index = $(this).data('index');
                documentList = documentList.filter(doc => doc.index !== index);
                updateDocumentTable();
                toastr.success('Document removed from list.');
            });

            // Clear Document Form Function
            window.clearDocumentForm = function() {
                $('#document_name').val('').removeClass('is-invalid');
                $('#document_index').val('');
                if (myDropzone) {
                    myDropzone.removeAllFiles();
                }
            };

            // Clear Form Function (for modal)
            window.clearTextBox = function() {
                clearDocumentForm();
            };

            // Reset Form Function
            window.resetTextBox = function() {
                clearDocumentForm();
            };

            // Enforce rules presence without relying on a hidden required control
            $('form.form-horizontal').on('submit', function (e) {
                const content = $rules.summernote('code');
                const text = $('<div>').html(content).text().trim();
                
                if ($rules.summernote('isEmpty') || text.length === 0) {
                    e.preventDefault();
                    toastr.warning('Rules are required.');
                    $rules.summernote('focus');
                    return false;
                }
                
                // Append documents as FormData
                if (documentList.length > 0) {
                    // Remove any existing document inputs
                    $('input[name="documents[]"]').remove();
                    $('input[name="document_names[]"]').remove();
                    
                    // Create a FormData to handle file uploads
                    const form = this;
                    const formData = new FormData(form);
                    
                    // Append each document
                    documentList.forEach(function(doc, idx) {
                        formData.append('document_names[]', doc.name);
                        formData.append('documents[]', doc.file);
                    });
                    
                    // Submit via AJAX
                    e.preventDefault();
                    
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            window.location.href = '{{ route("admin.policy-letters.index") }}';
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                let errorMessage = '';
                                for (let key in errors) {
                                    errorMessage += errors[key][0] + '<br>';
                                }
                                toastr.error(errorMessage);
                            } else {
                                toastr.error('An error occurred while saving.');
                            }
                        }
                    });
                    
                    return false;
                }
            });
            
            // Handle reset button to clear file input
            $('button[type="reset"]').on('click', function() {
                setTimeout(function() {
                    // Reset Summernote Editor for rules
                    $('#rules').summernote('reset');
                    // Reset Select2 for Company
                    $('#company').val(null).trigger('change');
                    // Reset Select2 for Category
                    $('#category').val(null).prop('disabled', true).trigger('change');

                    // Clear document list
                    documentList = [];
                    documentIndex = 0;
                    updateDocumentTable();

                    bsCustomFileInput.destroy();
                    bsCustomFileInput.init();
                }, 50);
            });
        });
    </script>
@stop