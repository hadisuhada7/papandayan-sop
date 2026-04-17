@extends('adminlte::page')

@section('title', 'Papandayan | Dashboard')

@section('plugins.Datatables', true)
@section('plugins.Toastr', true)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Dashboard</h1>
            @php
                $user = auth()->user();
                $selectedCompany = \App\Models\Company::find(session('selected_company_id'));
            @endphp
            @if($user->hasRole('super_admin'))
                <small class="text-muted">
                    Super Admin - All Companies
                </small>
            @elseif($selectedCompany)
                <small class="text-muted">
                    {{ $selectedCompany->name }} ({{ $selectedCompany->code }})
                </small>
            @endif
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row" id="category-row">

        <!-- Standard Operational Procedures -->
        @foreach($categories->where('standard_operationals_count', '>', 0) as $category)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h5 style="font-weight: bold; margin-top: 10px; margin-bottom: 10px;">{{ $category->name }}</h5>
                        @if(auth()->user()->hasRole('super_admin') && $category->company)
                            <p style="font-size: 12px; margin-bottom: 5px;">{{ $category->company->name }}</p>
                        @endif
                        <p style="font-size: 16px; margin-bottom: 25px;">{{ $category->standard_operationals_count }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="javascript:void(0);" class="small-box-footer" data-category-id="{{ $category->id }}" onclick="showAccordionStandardOperational('{{ $category->id }}'); return false;">Read More <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endforeach

        <!-- Policy Letters -->
        @foreach($categories->where('policy_letters_count', '>', 0) as $category)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h5 style="font-weight: bold; margin-top: 10px; margin-bottom: 10px;">{{ $category->name }}</h5>
                        @if(auth()->user()->hasRole('super_admin') && $category->company)
                            <p style="font-size: 12px; margin-bottom: 5px;">{{ $category->company->name }}</p>
                        @endif
                        <p style="font-size: 16px; margin-bottom: 25px;">{{ $category->policy_letters_count }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="javascript:void(0);" class="small-box-footer" data-category-id="{{ $category->id }}" onclick="showAccordionPolicyLetter('{{ $category->id }}'); return false;">Read More <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endforeach

        <!-- Internal Memos -->
        @foreach($categories->where('internal_memos_count', '>', 0) as $category)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h5 style="font-weight: bold; margin-top: 10px; margin-bottom: 10px;">{{ $category->name }}</h5>
                        @if(auth()->user()->hasRole('super_admin') && $category->company)
                            <p style="font-size: 12px; margin-bottom: 5px;">{{ $category->company->name }}</p>
                        @endif
                        <p style="font-size: 16px; margin-bottom: 25px;">{{ $category->internal_memos_count }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="javascript:void(0);" class="small-box-footer" data-category-id="{{ $category->id }}" onclick="showAccordionInternalMemo('{{ $category->id }}'); return false;">Read More <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endforeach

        <!-- Meeting Memos -->
        @foreach($categories->where('meeting_memos_count', '>', 0) as $category)
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h5 style="font-weight: bold; margin-top: 10px; margin-bottom: 10px;">{{ $category->name }}</h5>
                        @if(auth()->user()->hasRole('super_admin') && $category->company)
                            <p style="font-size: 12px; margin-bottom: 5px;">{{ $category->company->name }}</p>
                        @endif
                        <p style="font-size: 16px; margin-bottom: 25px;">{{ $category->meeting_memos_count }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="javascript:void(0);" class="small-box-footer" data-category-id="{{ $category->id }}" onclick="showAccordionMeetingMemo('{{ $category->id }}'); return false;">Read More <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Standard Operational Procedures -->
    <div class="row" id="standard-operational-row" style="display:none;">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Standard Operational Procedures</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-sm btn-default" onclick="showSmallBoxes()"><i class="fas fa-arrow-circle-left"></i> Back</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="accordion-standard">
                        @foreach($categories as $category)
                            <div class="accordion-category" id="accordion-standard-category-{{ $category->id }}" style="display:none;">
                                <h5>{{ $category->name }}</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="searchStandardOperational{{ $category->id }}" placeholder="Search" onkeyup="filterStandardOperational('{{ $category->id }}')">
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="filterStandardOperational('{{ $category->id }}')">Search</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @forelse($category->standardOperationals as $standardOperational)
                                    <div class="card card-info mb-2">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 accordion-header-link" data-toggle="collapse" href="#collapseStandardOperational{{ $standardOperational->id }}">
                                                    <span class="accordion-header-title">{{ $standardOperational->title }}</span>
                                                    <span class="accordion-header-meta">
                                                       {{ $standardOperational->document_number }}
                                                       ({{ optional($standardOperational->effective_date)->format('d M Y') ?? '-' }})
                                                    </span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseStandardOperational{{ $standardOperational->id }}" class="collapse" data-parent="#accordion-standard-category-{{ $category->id }}">
                                            <div class="card-body">
                                                <div class="standardDetailHeading no-copy">
                                                    <ul class="standardMeta">
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-file-alt"></i>
                                                                <span>Document Number</span>
                                                            </div>
                                                            <strong>{{ $standardOperational->document_number }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-check"></i>
                                                                <span>Effective Date</span>
                                                            </div>
                                                            <strong>{{ optional($standardOperational->effective_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-times"></i>
                                                                <span>Expired Date</span>
                                                            </div>
                                                            <strong>{{ optional($standardOperational->expired_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-folder-open"></i>
                                                                <span>Category</span>
                                                            </div>
                                                            <strong>
                                                                @if($standardOperational->categories->count())
                                                                    @foreach($standardOperational->categories as $cat)
                                                                        <span class="standardCategoryBadge">{{ $cat->name }}</span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-muted">Uncategorized</span>
                                                                @endif
                                                            </strong>
                                                        </li>
                                                    </ul>
                                                    <div class="standardInfoGrid">
                                                        <div class="standardInfoBlock">
                                                            <h6>Standard Operational Procedure</h6>
                                                            <div class="standardRichText">{!! $standardOperational->rules !!}</div>
                                                        </div>
                                                        
                                                        <!-- Table Documents -->
                                                        @if($standardOperational->formDocuments->count() > 0)
                                                            <div class="standardInfoBlock">
                                                                <h6>Form Attachments</h6>
                                                                <div class="table-documents">
                                                                    <table id="datagrid" class="table table-bordered table-hover">
                                                                        <thead class="bg-light">
                                                                            <tr>
                                                                                <th style="width: 30px;">No</th>
                                                                                <th style="width: 300px;">Name</th>
                                                                                <th style="width: 150px;">Type</th>
                                                                                <th scope="col">Attachment</th>
                                                                                <th style="width: 35px;">&nbsp;</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $index = 1; 
                                                                            @endphp
                                                                            @foreach($standardOperational->formDocuments as $document)
                                                                                <tr>
                                                                                    <td scope="row">{{ $index }}</td>
                                                                                    <td>{{ $document->name }}</td>
                                                                                    <td>{{ strtoupper($document->type) }}</td>
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
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policy Letters -->
    <div class="row" id="policy-letter-row" style="display:none;">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Policy Letters</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-sm btn-default" onclick="showSmallBoxes()"><i class="fas fa-arrow-circle-left"></i> Back</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="accordion-policy">
                        @foreach($categories as $category)
                            <div class="accordion-category" id="accordion-policy-category-{{ $category->id }}" style="display:none;">
                                <h5>{{ $category->name }}</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="searchPolicyLetter{{ $category->id }}" placeholder="Search" onkeyup="filterPolicyLetter('{{ $category->id }}')">
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="filterPolicyLetter('{{ $category->id }}')">Search</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @forelse($category->policyLetters as $policyLetter)
                                    <div class="card card-info mb-2">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 accordion-header-link" data-toggle="collapse" href="#collapsePolicyLetter{{ $policyLetter->id }}">
                                                    <span class="accordion-header-title">{{ $policyLetter->title }}</span>
                                                    <span class="accordion-header-meta">
                                                       {{ $policyLetter->document_number }}
                                                       ({{ optional($policyLetter->effective_date)->format('d M Y') ?? '-' }})
                                                    </span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapsePolicyLetter{{ $policyLetter->id }}" class="collapse" data-parent="#accordion-policy-category-{{ $category->id }}">
                                            <div class="card-body">
                                                <div class="standardDetailHeading no-copy">
                                                    <ul class="standardMeta">
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-file-alt"></i>
                                                                <span>Document Number</span>
                                                            </div>
                                                            <strong>{{ $policyLetter->document_number }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-check"></i>
                                                                <span>Effective Date</span>
                                                            </div>
                                                            <strong>{{ optional($policyLetter->effective_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-times"></i>
                                                                <span>Expired Date</span>
                                                            </div>
                                                            <strong>{{ optional($policyLetter->expired_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-folder-open"></i>
                                                                <span>Category</span>
                                                            </div>
                                                            <strong>
                                                                @if($policyLetter->categories->count())
                                                                    @foreach($policyLetter->categories as $cat)
                                                                        <span class="standardCategoryBadge">{{ $cat->name }}</span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-muted">Uncategorized</span>
                                                                @endif
                                                            </strong>
                                                        </li>
                                                    </ul>
                                                    <div class="standardInfoGrid">
                                                        <div class="standardInfoBlock">
                                                            <h6>Policy Letter</h6>
                                                            <div class="standardRichText">{!! $policyLetter->rules !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Internal Memos -->
    <div class="row" id="internal-memo-row" style="display:none;">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Internal Memos</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-sm btn-default" onclick="showSmallBoxes()"><i class="fas fa-arrow-circle-left"></i> Back</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="accordion-memo">
                        @foreach($categories as $category)
                            <div class="accordion-category" id="accordion-memo-category-{{ $category->id }}" style="display:none;">
                                <h5>{{ $category->name }}</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="searchInternalMemo{{ $category->id }}" placeholder="Search" onkeyup="filterInternalMemo('{{ $category->id }}')">
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="filterInternalMemo('{{ $category->id }}')">Search</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @forelse($category->internalMemos as $internalMemo)
                                    <div class="card card-info mb-2">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 accordion-header-link" data-toggle="collapse" href="#collapseInternalMemo{{ $internalMemo->id }}">
                                                    <span class="accordion-header-title">{{ $internalMemo->title }}</span>
                                                    <span class="accordion-header-meta">
                                                       {{ $internalMemo->document_number }}
                                                       ({{ optional($internalMemo->effective_date)->format('d M Y') ?? '-' }})
                                                    </span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseInternalMemo{{ $internalMemo->id }}" class="collapse" data-parent="#accordion-memo-category-{{ $category->id }}">
                                            <div class="card-body">
                                                <div class="standardDetailHeading no-copy">
                                                    <ul class="standardMeta">
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-file-alt"></i>
                                                                <span>Document Number</span>
                                                            </div>
                                                            <strong>{{ $internalMemo->document_number }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-check"></i>
                                                                <span>Effective Date</span>
                                                            </div>
                                                            <strong>{{ optional($internalMemo->effective_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-times"></i>
                                                                <span>Expired Date</span>
                                                            </div>
                                                            <strong>{{ optional($internalMemo->expired_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-folder-open"></i>
                                                                <span>Category</span>
                                                            </div>
                                                            <strong>
                                                                @if($internalMemo->categories->count())
                                                                    @foreach($internalMemo->categories as $cat)
                                                                        <span class="standardCategoryBadge">{{ $cat->name }}</span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-muted">Uncategorized</span>
                                                                @endif
                                                            </strong>
                                                        </li>
                                                    </ul>
                                                    <div class="standardInfoGrid">
                                                        <div class="standardInfoBlock">
                                                            <h6>Internal Memo</h6>
                                                            <div class="standardRichText">{!! $internalMemo->rules !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting Memos -->
    <div class="row" id="meeting-memo-row" style="display:none;">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Meeting Memos</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-sm btn-default" onclick="showSmallBoxes()"><i class="fas fa-arrow-circle-left"></i> Back</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="accordion-meeting-memo">
                        @foreach($categories as $category)
                            <div class="accordion-category" id="accordion-meeting-memo-category-{{ $category->id }}" style="display:none;">
                                <h5>{{ $category->name }}</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="searchMeetingMemo{{ $category->id }}" placeholder="Search" onkeyup="filterMeetingMemo('{{ $category->id }}')">
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" onclick="filterMeetingMemo('{{ $category->id }}')">Search</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @forelse($category->meetingMemos as $meetingMemo)
                                    <div class="card card-info mb-2">
                                        <div class="card-header">
                                            <h4 class="card-title w-100">
                                                <a class="d-block w-100 accordion-header-link" data-toggle="collapse" href="#collapseMeetingMemo{{ $meetingMemo->id }}">
                                                    <span class="accordion-header-title">{{ $meetingMemo->title }}</span>
                                                    <span class="accordion-header-meta">
                                                       {{ $meetingMemo->document_number }}
                                                       ({{ optional($meetingMemo->effective_date)->format('d M Y') ?? '-' }})
                                                    </span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseMeetingMemo{{ $meetingMemo->id }}" class="collapse" data-parent="#accordion-meeting-memo-category-{{ $category->id }}">
                                            <div class="card-body">
                                                <div class="standardDetailHeading no-copy">
                                                    <ul class="standardMeta">
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-file-alt"></i>
                                                                <span>Document Number</span>
                                                            </div>
                                                            <strong>{{ $meetingMemo->document_number ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-check"></i>
                                                                <span>Effective Date</span>
                                                            </div>
                                                            <strong>{{ optional($meetingMemo->effective_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-calendar-times"></i>
                                                                <span>Expired Date</span>
                                                            </div>
                                                            <strong>{{ optional($meetingMemo->expired_date)->format('d F Y') ?? '-' }}</strong>
                                                        </li>
                                                        <li>
                                                            <div class="standardMetaLabelRow">
                                                                <i class="fa fa-folder-open"></i>
                                                                <span>Category</span>
                                                            </div>
                                                            <strong>
                                                                @if($meetingMemo->categories->count())
                                                                    @foreach($meetingMemo->categories as $cat)
                                                                        <span class="standardCategoryBadge">{{ $cat->name }}</span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-muted">Uncategorized</span>
                                                                @endif
                                                            </strong>
                                                        </li>
                                                    </ul>
                                                    <div class="standardInfoGrid">
                                                        <div class="standardInfoBlock">
                                                            <h6>Meeting Memo</h6>
                                                            <div class="standardRichText">{!! $meetingMemo->rules !!}</div>
                                                        </div>

                                                        <!-- Preview Documents -->
                                                        <div class="standardInfoBlock">
                                                            <h6>Preview Documents</h6>
                                                            <div class="preview-documents">
                                                                @if($meetingMemo->documents->count() > 0)
                                                                    <ul class="list-group">
                                                                        @foreach($meetingMemo->documents as $document)
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                {{ $document->name }}
                                                                                @if($document->attachment)
                                                                                    <button type="button" class="btn btn-sm btn-info btn-preview-pdf" 
                                                                                        data-url="{{ Storage::url($document->attachment) }}" 
                                                                                        data-name="{{ $document->name }}" 
                                                                                        title="Preview Document">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <span class="text-muted">No documents available</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewModalLabel">Preview Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div id="pdfToolbar">
                        <div class="pdf-toolbar-group">
                            <button type="button" class="btn btn-sm btn-light" id="pdfPrevPage" title="Previous Page">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="pdf-page-info">
                                <span id="pdfCurrentPage">1</span> / <span id="pdfTotalPages">1</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-light" id="pdfNextPage" title="Next Page">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="pdf-toolbar-group">
                            <button type="button" class="btn btn-sm btn-light" id="pdfZoomOut" title="Zoom Out">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <span class="pdf-zoom-info" id="pdfZoomLevel">100%</span>
                            <button type="button" class="btn btn-sm btn-light" id="pdfZoomIn" title="Zoom In">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light" id="pdfZoomFit" title="Fit to Width">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div id="pdfContainer" style="height: 70vh; overflow: auto;" oncontextmenu="return false;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_css')
    @if(auth()->check() && auth()->user()->hasRole('viewer'))
        <link rel="stylesheet" href="{{ asset('css/viewer-layout.css') }}">
    @endif

    <style type="text/css">
        /* Accordion Header Responsive */
        .accordion-header-link {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
        }
        .accordion-header-title {
            flex: 1;
            min-width: 0;
        }
        .accordion-header-meta {
            font-size: 16px;
            font-weight: normal;
            white-space: nowrap;
            margin-left: 10px;
            text-align: right;
        }
        @media (max-width: 576px) {
            .accordion-header-link {
                flex-direction: column;
                align-items: flex-start;
            }
            .accordion-header-meta {
                margin-left: 0;
                margin-top: 4px;
                font-size: 13px;
                text-align: left;
                white-space: normal;
            }
        }

        /* SOP Detail Styling */
        .standardDetailHeading {
            padding: 0px;
            border-radius: 18px;
        }

        .standardMeta {
            list-style: none;
            padding: 0;
            margin: 0 0 24px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .standardMetaLabelRow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .standardMeta li {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 12px 16px;
            border-radius: 12px;
            background-color: rgba(60, 95, 172, 0.05);
            color: #566089;
            font-size: 14px;
        }

        .standardMeta li i {
            color: #3c5fac;
            font-size: 16px;
        }

        .standardMeta li span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .standardMeta li strong {
            color: #0f1b49;
            font-size: 15px;
            font-weight: 600;
        }

        .standardCategoryBadge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            letter-spacing: 0.05em;
            background-color: rgba(60, 95, 172, 0.15);
            color: #3c5fac;
            font-weight: 600;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .standardInfoGrid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
            margin-bottom: 10px;
        }

        .standardInfoBlock h6 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #3c5fac;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .standardInfoBlock h6 i {
            margin-right: 6px;
        }

        .standardRichText {
            line-height: 1.7;
            overflow-x: auto;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .standardRichText table {
            width: 100%;
            max-width: 100%;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .standardRichText ul,
        .standardRichText ol {
            padding-left: 18px;
            margin-bottom: 0;
        }

        .table-documents {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Prevent Copy or Select */
        .no-copy {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .standardMeta {
                grid-template-columns: 1fr;
            }
            .card-body {
                padding: 10px;
                overflow-x: hidden;
            }
            .standardDetailHeading {
                max-width: 100%;
                overflow-x: auto;
            }
            .standardRichText img {
                max-width: 100%;
                height: auto;
            }
        }

        /* PDF Toolbar */
        #pdfToolbar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 8px 12px;
            background: #f4f4f4;
            border-bottom: 1px solid #ddd;
            flex-wrap: wrap;
        }

        .pdf-toolbar-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pdf-page-info, .pdf-zoom-info {
            font-size: 13px;
            font-weight: 600;
            min-width: 60px;
            text-align: center;
            color: #333;
        }

        #pdfToolbar .btn {
            border: 1px solid #ccc;
            padding: 4px 10px;
        }

        #pdfToolbar .btn:hover {
            background: #e2e2e2;
        }

        /* PDF Preview */
        #pdfContainer {
            background: #525659;
            text-align: center;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        #pdfContainer canvas {
            display: block;
            margin: 10px auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
    </style>
@stop

@section('adminlte_js')
    @include('partials.toastr')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <script type="text/javascript">
        
        // Set PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // PDF Viewer State
        var pdfState = {
            pdf: null,
            currentScale: 1.5,
            totalPages: 0,
            baseScale: 1.5,
            minScale: 0.5,
            maxScale: 4.0,
            scaleStep: 0.25
        };

        function renderAllPages() {
            var container = document.getElementById('pdfContainer');
            container.innerHTML = '';
            if (!pdfState.pdf) return;

            for (var i = 1; i <= pdfState.totalPages; i++) {
                (function(pageNum) {
                    pdfState.pdf.getPage(pageNum).then(function (page) {
                        var viewport = page.getViewport({ scale: pdfState.currentScale });
                        var canvas = document.createElement('canvas');
                        canvas.className = 'pdf-page';
                        canvas.setAttribute('data-page', pageNum);
                        var context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        container.appendChild(canvas);

                        page.render({
                            canvasContext: context,
                            viewport: viewport
                        });
                    });
                })(i);
            }

            updateZoomLabel();
        }

        function updateZoomLabel() {
            var percent = Math.round((pdfState.currentScale / pdfState.baseScale) * 100);
            document.getElementById('pdfZoomLevel').textContent = percent + '%';
        }

        function updateCurrentPageOnScroll() {
            var container = document.getElementById('pdfContainer');
            var canvases = container.querySelectorAll('canvas.pdf-page');
            var scrollTop = container.scrollTop + 50;
            var currentPage = 1;

            canvases.forEach(function(canvas) {
                if (canvas.offsetTop <= scrollTop) {
                    currentPage = parseInt(canvas.getAttribute('data-page'));
                }
            });

            document.getElementById('pdfCurrentPage').textContent = currentPage;
        }

        function scrollToPage(pageNum) {
            var container = document.getElementById('pdfContainer');
            var canvas = container.querySelector('canvas[data-page="' + pageNum + '"]');
            if (canvas) {
                container.scrollTop = canvas.offsetTop - 10;
            }
        }

        // Zoom In
        document.getElementById('pdfZoomIn').addEventListener('click', function() {
            if (pdfState.currentScale < pdfState.maxScale) {
                pdfState.currentScale = Math.min(pdfState.currentScale + pdfState.scaleStep, pdfState.maxScale);
                renderAllPages();
            }
        });

        // Zoom Out
        document.getElementById('pdfZoomOut').addEventListener('click', function() {
            if (pdfState.currentScale > pdfState.minScale) {
                pdfState.currentScale = Math.max(pdfState.currentScale - pdfState.scaleStep, pdfState.minScale);
                renderAllPages();
            }
        });

        // Fit to Width
        document.getElementById('pdfZoomFit').addEventListener('click', function() {
            if (!pdfState.pdf) return;
            pdfState.pdf.getPage(1).then(function(page) {
                var container = document.getElementById('pdfContainer');
                var containerWidth = container.clientWidth - 20;
                var viewport = page.getViewport({ scale: 1.0 });
                pdfState.currentScale = containerWidth / viewport.width;
                renderAllPages();
            });
        });

        // Previous Page
        document.getElementById('pdfPrevPage').addEventListener('click', function() {
            var current = parseInt(document.getElementById('pdfCurrentPage').textContent);
            if (current > 1) {
                scrollToPage(current - 1);
                document.getElementById('pdfCurrentPage').textContent = current - 1;
            }
        });

        // Next Page
        document.getElementById('pdfNextPage').addEventListener('click', function() {
            var current = parseInt(document.getElementById('pdfCurrentPage').textContent);
            if (current < pdfState.totalPages) {
                scrollToPage(current + 1);
                document.getElementById('pdfCurrentPage').textContent = current + 1;
            }
        });

        // Track scroll for page indicator
        document.getElementById('pdfContainer').addEventListener('scroll', updateCurrentPageOnScroll);

        // PDF Preview
        $(document).on('click', '.btn-preview-pdf', function () {
            var pdfUrl = $(this).data('url');
            var docName = $(this).data('name');
            $('#pdfPreviewModalLabel').text('Preview: ' + docName);
            $('#pdfContainer').html('');
            pdfState.currentScale = pdfState.baseScale;

            pdfjsLib.getDocument(pdfUrl).promise.then(function (pdf) {
                pdfState.pdf = pdf;
                pdfState.totalPages = pdf.numPages;
                document.getElementById('pdfCurrentPage').textContent = '1';
                document.getElementById('pdfTotalPages').textContent = pdf.numPages;
                renderAllPages();
            }).catch(function (error) {
                $('#pdfContainer').html('<p class="text-white p-4">Failed to load PDF.</p>');
            });

            $('#pdfPreviewModal').modal('show');
        });

        // Reset state when modal is closed
        $('#pdfPreviewModal').on('hidden.bs.modal', function () {
            pdfState.pdf = null;
            pdfState.totalPages = 0;
            pdfState.currentScale = pdfState.baseScale;
            $('#pdfContainer').html('');
        });

        function showAccordionStandardOperational(categoryId) {
            // Hide Category row
            document.getElementById('category-row').style.display = 'none';
            
            // Show Standard Operational row
            document.getElementById('standard-operational-row').style.display = '';
            
            // Hide all Accordion Categories
            document.querySelectorAll('.accordion-category').forEach(function(el) {
                el.style.display = 'none';
            });

            // Show selected Category
            var el = document.getElementById('accordion-standard-category-' + categoryId);
            if (el) el.style.display = 'block';

            // Optionally scroll to Standard Operational row
            document.getElementById('standard-operational-row').scrollIntoView({behavior: 'smooth'});
        }

        function showAccordionPolicyLetter(categoryId) {
            // Hide Category row
            document.getElementById('category-row').style.display = 'none';
            
            // Show Policy Letter row
            document.getElementById('policy-letter-row').style.display = '';
            
            // Hide all Accordion Categories
            document.querySelectorAll('.accordion-category').forEach(function(el) {
                el.style.display = 'none';
            });

            // Show selected Category
            var el = document.getElementById('accordion-policy-category-' + categoryId);
            if (el) el.style.display = 'block';

            // Optionally scroll to Policy Letter row
            document.getElementById('policy-letter-row').scrollIntoView({behavior: 'smooth'});
        }

        function showAccordionInternalMemo(categoryId) {
            // Hide Category row
            document.getElementById('category-row').style.display = 'none';
            
            // Show Internal Memo row
            document.getElementById('internal-memo-row').style.display = '';
            
            // Hide all Accordion Categories
            document.querySelectorAll('.accordion-category').forEach(function(el) {
                el.style.display = 'none';
            });

            // Show selected Category
            var el = document.getElementById('accordion-memo-category-' + categoryId);
            if (el) el.style.display = 'block';

            // Optionally scroll to Internal Memo row
            document.getElementById('internal-memo-row').scrollIntoView({behavior: 'smooth'});
        }

        function showAccordionMeetingMemo(categoryId) {
            // Hide Category row
            document.getElementById('category-row').style.display = 'none';
            
            // Show Meeting Memo row
            document.getElementById('meeting-memo-row').style.display = '';
            
            // Hide all Accordion Categories
            document.querySelectorAll('.accordion-category').forEach(function(el) {
                el.style.display = 'none';
            });

            // Show selected Category
            var el = document.getElementById('accordion-meeting-memo-category-' + categoryId);
            if (el) el.style.display = 'block';

            // Optionally scroll to Meeting Memo row
            document.getElementById('meeting-memo-row').scrollIntoView({behavior: 'smooth'});
        }
        
        function showSmallBoxes() {
            // Show Category row
            document.getElementById('category-row').style.display = '';
            
            // Hide Standard Operational row
            document.getElementById('standard-operational-row').style.display = 'none';
            
            // Hide Policy Letter row
            document.getElementById('policy-letter-row').style.display = 'none';
            
            // Hide Internal Memo row
            document.getElementById('internal-memo-row').style.display = 'none';
            
            // Hide Meeting Memo row
            document.getElementById('meeting-memo-row').style.display = 'none';
            
            // Scroll to top
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function filterStandardOperational(categoryId) {
            var input = document.getElementById('searchStandardOperational' + categoryId);
            var filter = input.value.toLowerCase();
            var cards = document.querySelectorAll('#accordion-standard-category-' + categoryId + ' .card');
            cards.forEach(function(card) {
                var title = card.querySelector('.card-title a');
                if (title && title.textContent.toLowerCase().indexOf(filter) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterPolicyLetter(categoryId) {
            var input = document.getElementById('searchPolicyLetter' + categoryId);
            var filter = input.value.toLowerCase();
            var cards = document.querySelectorAll('#accordion-policy-category-' + categoryId + ' .card');
            cards.forEach(function(card) {
                var title = card.querySelector('.card-title a');
                if (title && title.textContent.toLowerCase().indexOf(filter) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterInternalMemo(categoryId) {
            var input = document.getElementById('searchInternalMemo' + categoryId);
            var filter = input.value.toLowerCase();
            var cards = document.querySelectorAll('#accordion-memo-category-' + categoryId + ' .card');
            cards.forEach(function(card) {
                var title = card.querySelector('.card-title a');
                if (title && title.textContent.toLowerCase().indexOf(filter) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterMeetingMemo(categoryId) {
            var input = document.getElementById('searchMeetingMemo' + categoryId);
            var filter = input.value.toLowerCase();
            var cards = document.querySelectorAll('#accordion-meeting-memo-category-' + categoryId + ' .card');
            cards.forEach(function(card) {
                var title = card.querySelector('.card-title a');
                if (title && title.textContent.toLowerCase().indexOf(filter) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Prevent copy, cut, and context menu on no-copy elements
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.no-copy').forEach(function(element) {
                element.addEventListener('copy', function(e) {
                    e.preventDefault();
                    toastr.warning('Copying is not allowed!');
                    return false;
                });
                element.addEventListener('cut', function(e) {
                    e.preventDefault();
                    toastr.warning('Cutting is not allowed!');
                    return false;
                });
                element.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    toastr.warning('Right-click is disabled!');
                    return false;
                });
            });
        });

        // Enhanced Screenshot Protection (Multi-layer approach)
        document.addEventListener('DOMContentLoaded', function() {
            // Create blur overlay
            var blurOverlay = document.createElement('div');
            blurOverlay.id = 'blur-overlay';
            blurOverlay.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.98); z-index:99999; backdrop-filter:blur(20px);';
            blurOverlay.innerHTML = '<div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:24px; color:#d33; font-weight:bold; flex-direction:column;"><i class="fas fa-shield-alt" style="font-size:64px; margin-bottom:20px;"></i><div>PROTECTED CONTENT</div><div style="font-size:16px; margin-top:5px;">Screenshots Detected</div></div>';
            document.body.appendChild(blurOverlay);

            var isBlurred = false;
            
            // Show blur overlay function
            function showBlurOverlay() {
                isBlurred = true;
                document.getElementById('blur-overlay').style.display = 'block';
                // toastr.error('Screenshot attempt detected! Content hidden.');
            }
            
            // Hide blur overlay function
            function hideBlurOverlay() {
                setTimeout(function() {
                    isBlurred = false;
                    document.getElementById('blur-overlay').style.display = 'none';
                }, 1000);
            }

            // Detect Print Screen key
            document.addEventListener('keyup', function(e) {
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    navigator.clipboard.writeText('');
                    showBlurOverlay();
                    setTimeout(hideBlurOverlay, 3000);
                }
            });

            // Detect screenshot shortcuts
            document.addEventListener('keydown', function(e) {
                var isScreenshotKey = false;
                
                // Print Screen
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    isScreenshotKey = true;
                }
                
                // Windows Snipping Tool (Win + Shift + S)
                if ((e.key === 's' || e.key === 'S') && e.shiftKey && e.metaKey) {
                    isScreenshotKey = true;
                }
                
                // Mac Screenshot (Cmd + Shift + 3/4/5)
                if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4' || e.key === '5')) {
                    isScreenshotKey = true;
                }
                
                if (isScreenshotKey) {
                    e.preventDefault();
                    showBlurOverlay();
                    setTimeout(hideBlurOverlay, 3000);
                    return false;
                }
                
                // Disable common developer tools shortcuts
                if (e.keyCode === 123 || 
                    (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                    (e.ctrlKey && e.keyCode === 85)) {
                    e.preventDefault();
                    toastr.warning('Developer tools are disabled!');
                    return false;
                }
            });

            // Hide content when window loses focus (user switched to snipping tool)
            var blurTimeout;
            window.addEventListener('blur', function() {
                blurTimeout = setTimeout(function() {
                    showBlurOverlay();
                }, 100);
            });

            window.addEventListener('focus', function() {
                clearTimeout(blurTimeout);
                if (isBlurred) {
                    hideBlurOverlay();
                }
            });

            // Detect visibility changes (tab switching, minimizing)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    showBlurOverlay();
                } else {
                    hideBlurOverlay();
                }
            });

            // Periodic warning
            setInterval(function() {
                if (!isBlurred && Math.random() > 0.95) {
                    toastr.warning('This content is protected. Screenshots are monitored.', '', {timeOut: 2000});
                }
            }, 60000);

            // Show initial warning
            // setTimeout(function() {
            //     toastr.info('Screenshot protection is active', '', {timeOut: 2000});
            // }, 1000);
        });
    </script>
@stop