@extends('layouts.newsApp')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-xl p-8">

        {{-- Back button --}}
        <div class="mb-6 no-print">
            <a href="{{ route('resourcefile.show') }}" 
               class="inline-flex items-center text-gray-600 hover:text-red-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Good News
            </a>
        </div>

        {{-- Header Section --}}
        <div class="text-center mb-8">
            {{-- Tanggal --}}
            <p class="text-sm text-gray-500 mb-4 print-date">
                {{ $resourcefile->created_at ? $resourcefile->created_at->translatedFormat('l, d F Y') : '-' }}
            </p>
            
            {{-- Judul --}}
            <h1 class="text-1xl font-bold text-gray-800 leading-tight print-title">
                {{ $resourcefile->title }}
            </h1>
        </div>

        {{-- Content Section --}}
        @if($resourcefile->content)
        <div class="mb-10 print-content">
            <div class="prose prose-lg max-w-none text-justify leading-relaxed text-gray-700">
                {!! $resourcefile->content !!}
            </div>
        </div>
        @endif

        {{-- Refleksi Diri Section --}}
        @if($resourcefile->refleksi_diri)
        <div class="mb-8 print-section">
            <h2 class="text-lg font-semibold text-blue-600 mb-4 uppercase tracking-wide print-section-title">
                Refleksi Diri:
            </h2>
            <div class="prose max-w-none text-justify leading-relaxed text-gray-700 space-y-4 print-section-content">
                {!! $resourcefile->refleksi_diri !!}
            </div>
        </div>
        @endif

        {{-- Pengakuan Iman Section --}}
        @if($resourcefile->pengakuan_iman)
        <div class="mb-8 print-section">
            <h2 class="text-lg font-semibold text-blue-600 mb-4 uppercase tracking-wide print-section-title">
                Pengakuan Iman:
            </h2>
            <div class="prose max-w-none text-center p-6 rounded-lg print-confession">
                <div class="font-medium text-blue-800 leading-relaxed print-confession-text">
                    {!! $resourcefile->pengakuan_iman !!}
                </div>
            </div>
        </div>
        @endif

        {{-- Bacaan Alkitab Section --}}
        @if($resourcefile->bacaan_alkitab)
        <div class="mb-8 print-section">
            <h2 class="text-lg font-semibold text-blue-600 mb-4 uppercase tracking-wide print-section-title">
                Bacaan Alkitab:
            </h2>
            <div class="prose max-w-none text-justify leading-relaxed text-gray-700 print-section-content">
                {!! $resourcefile->bacaan_alkitab !!}
            </div>
        </div>
        @endif

        {{-- Footer Navigation --}}
        <div class="mt-12 pt-8 border-t border-gray-200 no-print">
            <div class="flex justify-between items-center">
                {{-- Previous/Next navigation jika diperlukan --}}
                <div class="flex space-x-4">
                    @if(isset($archives) && $archives->count() > 0)
                        <a href="{{ route('resourcefile.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Archive
                        </a>
                    @endif
                </div>

                {{-- Share buttons jika diperlukan --}}
                {{-- <div class="flex space-x-2">
                    <button onclick="sharePage()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                        </svg>
                        Share
                    </button>
                    
                    <button onclick="downloadPDF()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </button>
                </div> --}}
            </div>
        </div>

        {{-- Meta Information --}}
        {{-- <div class="mt-8 pt-6 border-t border-gray-100 no-print">
            <div class="flex justify-between items-center text-sm text-gray-500">
                <div>
                    @if($resourcefile->creator)
                        Created by: {{ $resourcefile->creator->name }}
                    @endif
                </div>
                <div>
                    @if($resourcefile->updated_at && $resourcefile->updated_at != $resourcefile->created_at)
                        Last updated: {{ $resourcefile->updated_at->translatedFormat('d F Y') }}
                    @endif
                </div>
            </div>
        </div> --}}
    </div>
</div>

{{-- JavaScript untuk functionality --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function sharePage() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $resourcefile->title }}',
                text: 'Good News - {{ $resourcefile->title }}',
                url: window.location.href
            });
        } else {
            // Fallback - copy URL to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            });
        }
    }

    function downloadPDF() {
        // Hide elements that shouldn't be in PDF
        const elementsToHide = document.querySelectorAll('.no-print');
        elementsToHide.forEach(el => el.style.display = 'none');
        
        // Get the main content container
        const element = document.querySelector('.bg-white.shadow-lg.rounded-xl');
        
        const opt = {
            margin: [10, 10, 10, 10],
            filename: '{{ $resourcefile->title ? Str::slug($resourcefile->title) : "devotional" }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                letterRendering: true,
                backgroundColor: '#ffffff'
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait' 
            }
        };

        // Generate PDF
        html2pdf().set(opt).from(element).save().then(() => {
            // Show hidden elements again
            elementsToHide.forEach(el => el.style.display = '');
        });
    }


</script>

{{-- Print-specific styles --}}
<style>
    @media print {
        /* Hide only specific unwanted elements */
        .no-print {
            display: none !important;
        }
        
        /* Body setup */
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: black !important;
            background: white !important;
            margin: 0;
            padding: 0;
        }
        
        /* Container */
        .max-w-4xl {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .bg-white {
            background: white !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 20pt !important;
        }
        
        .shadow-lg,
        .rounded-xl {
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        
        /* Date styling */
        .text-sm.text-gray-500 {
            font-size: 11pt;
            color: black !important;
            text-align: left;
            margin-bottom: 10pt;
        }
        
        /* Title */
        h1 {
            font-size: 16pt;
            font-weight: bold;
            color: black !important;
            text-align: center;
            margin: 10pt 0 15pt 0;
        }
        
        /* Content */
        .prose {
            font-size: 11pt;
            line-height: 1.5;
            color: black !important;
            max-width: none !important;
        }
        
        .prose p {
            margin-bottom: 8pt;
            text-align: justify;
            color: black !important;
        }
        
        /* Section headers */
        h2 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: black !important;
            margin: 15pt 0 8pt 0;
            border-bottom: 1pt solid black;
            padding-bottom: 2pt;
        }
        
        /* Remove colored text */
        .text-blue-600,
        .text-blue-800,
        .text-gray-700,
        .text-gray-500,
        .text-gray-800 {
            color: black !important;
        }
        
        /* Section spacing */
        .mb-10,
        .mb-8 {
            margin-bottom: 15pt;
        }
        
        .pl-6 {
            padding-left: 0 !important;
        }
        
        /* Confession box styling */
        .text-center.p-6.rounded-lg {
            border: 1pt solid black !important;
            background: #f5f5f5 !important;
            padding: 10pt !important;
            margin: 10pt 0 !important;
            border-radius: 0 !important;
            text-align: center !important;
        }
        
        .font-medium {
            font-weight: bold !important;
            color: black !important;
        }
        
        /* Remove rounded corners */
        .rounded-lg {
            border-radius: 0 !important;
        }
        
        /* Typography */
        strong, b {
            font-weight: bold !important;
            color: black !important;
        }
        
        em, i {
            font-style: italic !important;
            color: black !important;
        }
    }
    
    /* Enhanced prose styling for screen */
    .prose {
        line-height: 1.75;
    }
    
    .prose p {
        margin-bottom: 1.25rem;
    }
    
    .prose h1,
    .prose h2,
    .prose h3 {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    
    .prose blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6b7280;
    }
</style>
@endsection