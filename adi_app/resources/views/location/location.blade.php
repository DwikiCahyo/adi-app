@extends('layouts.newsApp')

@section('content')
<div class="max-w-sm sm:max-w-md md:max-w-2xl lg:max-w-4xl mx-auto bg-white rounded-xl shadow overflow-hidden">
    <!-- Google Maps Embed -->
    <div class="h-48 sm:h-64 md:h-80 lg:h-[500px]">
        <a href="https://maps.app.goo.gl/EvB4gjtciyXAXid8A?g_st=aw" 
        target="_blank" 
        class="block w-full h-full">
       <iframe
         src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.713126914276!2d106.81976689999999!3d-6.169155000000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5e18affdc4b%3A0xe28e684deb124176!2sGBDI%20Kasih%20Karunia%20(MyCDC7K)!5e0!3m2!1sid!2sid!4v1756018029690!5m2!1sid!2sid"
         class="w-full h-full border-0 pointer-events-none"
         allowfullscreen
         loading="lazy"
         referrerpolicy="no-referrer-when-downgrade">
       </iframe>
     </a>
    </div>
  
    <!-- Location Info (Clickable) -->
    <a href="https://maps.app.goo.gl/EvB4gjtciyXAXid8A?g_st=aw"
       target="_blank" 
       class="block p-4 flex items-center justify-between hover:bg-gray-50 transition">
      <div>
        <h3 class="font-bold text-lg text-gray-800">GBDI Kasih Karunia (MyCDC7K)</h3>
        <p class="text-sm text-gray-600 leading-snug">
            Komplek perkantoran Majapahit permai, Jl. Majapahit No.22 blok C, Petojo Sel., <br>
            Kecamatan Gambir – Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10160
        </p>
      </div>
      <div class="text-gray-400 group-hover:text-gray-600">
        <!-- Arrow Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke-width="2" 
             stroke="currentColor" 
             class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </div>
    </a>
</div>
@endsection
