@extends('adminlte::page')

@section('title', 'Papandayan | Dashboard')

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
        @foreach($categories as $category)
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
                    <a href="javascript:void(0);" class="small-box-footer" data-category-id="{{ $category->id }}" onclick="showAccordion('{{ $category->id }}'); return false;">Read More <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row" id="standard-operational-row" style="display:none;">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Standard Operationals</h3>

                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <button type="button" class="btn btn-sm btn-default" onclick="showSmallBoxes()"><i class="fas fa-arrow-circle-left"></i> Back</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="accordion">
                        @foreach($categories as $category)
                            <div class="accordion-category" id="accordion-category-{{ $category->id }}" style="display:none;">
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
                                                <a class="d-block w-100" data-toggle="collapse" href="#collapseStandardOperational{{ $standardOperational->id }}">
                                                    {{ $standardOperational->title }}

                                                    <div class="float-right" style="font-size: 16px; font-weight: normal;">
                                                       {{ $standardOperational->document_number }}
                                                       ({{ optional($standardOperational->effective_date)->format('d M Y') ?? '-' }})
                                                    </div>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseStandardOperational{{ $standardOperational->id }}" class="collapse" data-parent="#accordion-category-{{ $category->id }}">
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
                                                            <h6>Standard Operational Procedures</h6>
                                                            <div class="standardRichText">{!! $standardOperational->rules !!}</div>
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
@stop

@section('adminlte_css')
    @if(auth()->check() && auth()->user()->hasRole('viewer'))
        <link rel="stylesheet" href="{{ asset('css/viewer-layout.css') }}">
    @endif
    <style>
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
            gap: 20px;
            margin-bottom: 24px;
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
        }

        .standardRichText ul,
        .standardRichText ol {
            padding-left: 18px;
            margin-bottom: 0;
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
        }
    </style>
@stop

@section('adminlte_js')
    @include('partials.toastr')
    <script type="text/javascript">
        function showAccordion(categoryId) {
            // Hide Category row
            document.getElementById('category-row').style.display = 'none';
            
            // Show Standard Operational row
            document.getElementById('standard-operational-row').style.display = '';
            
            // Hide all Accordion Categories
            document.querySelectorAll('.accordion-category').forEach(function(el) {
                el.style.display = 'none';
            });

            // Show selected Category
            var el = document.getElementById('accordion-category-' + categoryId);
            if (el) el.style.display = 'block';

            // Optionally scroll to Standard Operational row
            document.getElementById('standard-operational-row').scrollIntoView({behavior: 'smooth'});
        }
        
        function showSmallBoxes() {
            // Show Category row
            document.getElementById('category-row').style.display = '';
            
            // Hide Standard Operational row
            document.getElementById('standard-operational-row').style.display = 'none';
            
            // Scroll to top
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function filterStandardOperational(categoryId) {
            var input = document.getElementById('searchStandardOperational' + categoryId);
            var filter = input.value.toLowerCase();
            var cards = document.querySelectorAll('#accordion-category-' + categoryId + ' .card');
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
            // var blurOverlay = document.createElement('div');
            // blurOverlay.id = 'blur-overlay';
            // blurOverlay.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.98); z-index:99999; backdrop-filter:blur(20px);';
            // blurOverlay.innerHTML = '<div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:24px; color:#d33; font-weight:bold; flex-direction:column;"><i class="fas fa-shield-alt" style="font-size:64px; margin-bottom:20px;"></i><div>⚠️ PROTECTED CONTENT</div><div style="font-size:16px; margin-top:10px;">Screenshots Detected</div></div>';
            // document.body.appendChild(blurOverlay);

            // var isBlurred = false;
            
            // Show blur overlay function
            // function showBlurOverlay() {
            //     isBlurred = true;
            //     document.getElementById('blur-overlay').style.display = 'block';
            //     toastr.error('Screenshot attempt detected! Content hidden.');
            // }
            
            // Hide blur overlay function
            // function hideBlurOverlay() {
            //     setTimeout(function() {
            //         isBlurred = false;
            //         document.getElementById('blur-overlay').style.display = 'none';
            //     }, 1000);
            // }

            // Detect Print Screen key
            // document.addEventListener('keyup', function(e) {
            //     if (e.key === 'PrintScreen' || e.keyCode === 44) {
            //         navigator.clipboard.writeText('');
            //         showBlurOverlay();
            //         setTimeout(hideBlurOverlay, 3000);
            //     }
            // });

            // Detect screenshot shortcuts
            // document.addEventListener('keydown', function(e) {
            //     var isScreenshotKey = false;
                
            //     // Print Screen
            //     if (e.key === 'PrintScreen' || e.keyCode === 44) {
            //         isScreenshotKey = true;
            //     }
                
            //     // Windows Snipping Tool (Win + Shift + S)
            //     if ((e.key === 's' || e.key === 'S') && e.shiftKey && e.metaKey) {
            //         isScreenshotKey = true;
            //     }
                
            //     // Mac Screenshot (Cmd + Shift + 3/4/5)
            //     if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4' || e.key === '5')) {
            //         isScreenshotKey = true;
            //     }
                
            //     if (isScreenshotKey) {
            //         e.preventDefault();
            //         showBlurOverlay();
            //         setTimeout(hideBlurOverlay, 3000);
            //         return false;
            //     }
                
            //     // Disable common developer tools shortcuts
            //     if (e.keyCode === 123 || 
            //         (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
            //         (e.ctrlKey && e.keyCode === 85)) {
            //         e.preventDefault();
            //         toastr.warning('Developer tools are disabled!');
            //         return false;
            //     }
            // });

            // Hide content when window loses focus (user switched to snipping tool)
            // var blurTimeout;
            // window.addEventListener('blur', function() {
            //     blurTimeout = setTimeout(function() {
            //         showBlurOverlay();
            //     }, 100);
            // });

            // window.addEventListener('focus', function() {
            //     clearTimeout(blurTimeout);
            //     if (isBlurred) {
            //         hideBlurOverlay();
            //     }
            // });

            // Detect visibility changes (tab switching, minimizing)
            // document.addEventListener('visibilitychange', function() {
            //     if (document.hidden) {
            //         showBlurOverlay();
            //     } else {
            //         hideBlurOverlay();
            //     }
            // });

            // Periodic warning
            // setInterval(function() {
            //     if (!isBlurred && Math.random() > 0.95) {
            //         toastr.warning('This content is protected. Screenshots are monitored.', '', {timeOut: 2000});
            //     }
            // }, 60000);

            // Show initial warning
            // setTimeout(function() {
            //     toastr.info('Screenshot protection is active', '', {timeOut: 3000});
            // }, 1000);
        });
    </script>
@stop