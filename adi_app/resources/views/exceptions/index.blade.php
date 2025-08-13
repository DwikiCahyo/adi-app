@extends('layouts.exception')

@section('content')
   <div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="error-page text-center">
                
                <!-- Error Icon & Code -->
                <div class="error-header mb-4">
                    <div class="error-icon mb-3">
                        @switch($statusCode ?? 500)
                            @case(400)
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                                @break
                            @case(401)
                                <i class="fas fa-lock text-danger" style="font-size: 4rem;"></i>
                                @break
                            @case(403)
                                <i class="fas fa-ban text-danger" style="font-size: 4rem;"></i>
                                @break
                            @case(404)
                                <i class="fas fa-search text-primary" style="font-size: 4rem;"></i>
                                @break
                            @case(405)
                                <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                                @break
                            @case(422)
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                                @break
                            @case(419)
                                <i class="fas fa-shield-alt text-warning" style="font-size: 4rem;"></i>
                                @break
                            @case(429)
                                <i class="fas fa-hourglass-half text-info" style="font-size: 4rem;"></i>
                                @break
                            @case(500)
                                <i class="fas fa-server text-danger" style="font-size: 4rem;"></i>
                                @break
                            @case(503)
                                <i class="fas fa-tools text-warning" style="font-size: 4rem;"></i>
                                @break
                            @default
                                <i class="fas fa-exclamation-circle text-secondary" style="font-size: 4rem;"></i>
                        @endswitch
                    </div>
                    
                    <h1 class="error-code display-1 fw-bold">
                        {{ $statusCode ?? '500' }}
                    </h1>
                </div>
                
                <!-- Error Message -->
                <div class="error-content mb-5">
                    <h2 class="error-title h3 mb-3">
                        {{ $title ?? 'Terjadi kesalahan' }}
                    </h2>
                    <p class="error-message lead text-muted mb-4">
                        {{ $message ?? 'Terjadi kesalah dalam memuat halaman' }}
                    </p>
                </div>
            </div>   
        </div>
    </div>
</div>               
@endsection