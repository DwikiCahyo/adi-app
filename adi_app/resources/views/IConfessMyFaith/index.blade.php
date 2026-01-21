@extends('layouts.newsApp')

@section('content')
<div class="bg-gradient-to-b from-blue-50 to-white min-h-screen px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Back Button --}}
    <div class="max-w-4xl mx-auto mb-6">
        <a href="{{ route('resource.index') }}" class="inline-flex items-center gap-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- Carousel Header Section --}}
    <div class="max-w-4xl mx-auto mb-8">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            {{-- Carousel Container --}}
            <div class="relative">
                {{-- Slides --}}
                <div id="carousel" class="relative overflow-hidden rounded-t-2xl">
                    {{-- Slide 1 - Cover Pengakuan Iman --}}
                    <div class="carousel-slide active">
                        <img 
                            src="{{ asset('Images/MyfaithBook1.png') }}" 
                            alt="Pengakuan Iman - Cover 1" 
                            class="w-full h-auto object-cover"
                        >
                    </div>

                    {{-- Slide 2 - I Confess My Faith --}}
                    <div class="carousel-slide">
                        <img 
                            src="{{ asset('Images/MyfaithBook2.png') }}" 
                            alt="I Confess My Faith - Cover 2" 
                            class="w-full h-auto object-cover"
                        >
                    </div>
                </div>

                {{-- Navigation Arrows --}}
                <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 bg-white text-gray-800 rounded-full p-2 sm:p-3 shadow-lg transition-all z-10 hover:scale-110">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 bg-white text-gray-800 rounded-full p-2 sm:p-3 shadow-lg transition-all z-10 hover:scale-110">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                {{-- Indicators --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    <button onclick="goToSlide(0)" class="indicator active w-3 h-3 rounded-full bg-white shadow-md transition-all"></button>
                    <button onclick="goToSlide(1)" class="indicator w-3 h-3 rounded-full bg-white/50 shadow-md transition-all"></button>
                </div>
            </div>
            
            <div class="px-6 py-6 sm:px-8 sm:py-8">
                <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 sm:p-8">
                    <p class="text-gray-800 text-base sm:text-lg leading-relaxed text-center italic mb-4">
                        Sebab Ezra telah bertekad untuk meneliti Taurat TUHAN dan melakukannya 
                        serta mengajar ketetapan dan peraturan di antara orang Israel.
                    </p>
                    <p class="text-blue-700 font-bold text-lg sm:text-xl text-center">Ezra 7:10</p>
                </div>
                <p class="text-sm text-gray-600 italic text-center mt-6">Penulis: Dr. Hendra Zefanya M.Th.</p>
            </div>
        </div>
    </div>

    {{-- Content Sections with Dropdowns --}}
    <div class="max-w-4xl mx-auto space-y-4">
        
        {{-- Pendahuluan --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <button type="button" onclick="toggleSection('intro')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Pendahuluan</h2>
                <svg id="icon-intro" class="w-6 h-6 text-gray-600 transform transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="content-intro" class="px-6 py-4 border-t border-gray-100">
                <div class="prose max-w-none text-gray-700">
                    <p class="mb-4 text-justify drop-cap">Pengakuan Iman adalah bentuk peneguhan bagi umat Kristen yang menyatakan identitas diri mereka, apa yang mereka percaya dan siapa yang mereka percaya. Jadi pengakuan iman merupakan sebuah tindakan iman yang sangat penting yang harus dilakukan oleh setiap orang percaya.</p>
                    
                    <p class="mb-4 text-justify">Ada banyak yang dapat menjadi sumber pengakuan iman seseorang. Pengakuan imannya bisa berasal dari apa kata orang-orang yang menjadi otoritas terhadapnya. Misalnya apa kata orang tua, apa kata pemimpin, apa kata guru atau dosen dan lain-lain. Bisa juga pengakuan imannya berasal dari apa kata dunia pada umumnya.</p>
                    
                    <p class="mb-4 text-justify">Disadari atau tidak, pengakuan imannya akan sangat menentukan identitas apa yang dia percayai dan juga dapat menentukan kehidupannya di masa yang akan datang</p>
                    
                    <p class="mb-4 text-justify">Oleh karena itu sangat penting bagi kita mempunyai sumber yang benar untuk pengakuan iman kita yang akan kita akui terus menerus. Sebagai orang percaya, Anda harus mendasari setiap pengakuan iman kita bersumber dari firman Tuhan, apa yang dikatakan oleh Tuhan.</p>
                    
                    <p class="mb-4 text-justify">Melalui buku <span class="font-bold">"Pengakuan Iman"</span> ini, penulis berdoa kepada Tuhan agar setiap pembaca mendapatkan roh Hikmat dan Wahyu agar dapat mengenal Tuhan Yesus dengan benar. Dan melalui buku ini setiap pembaca diubahkan, dipulihkan, diperbaharui dan diberkati oleh Yesus, Tuhan dan Juru selamat satu-satunya.</p>
                    
                    <p class="mb-4 text-justify">Anda dapat sambil membaca dan memperkatakan <span class="font-bold">"Pengakuan Iman Setiap Hari"</span> yang ada di Bab VII. Selamat membaca dan mengalami kasih karunia-Nya yang Tuhan sudah berikan kepada kita semua.</p>
                    
                    <p class="mt-8 mb-2">Tuhan Yesus memberkati</p>
                    
                    <p class="font-semibold">Hendra Zefanya<br>Hamba-Nya</p>
                </div>
            </div>
        </div>

        {{-- Bab 1 --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <button type="button" onclick="toggleSection('bab1')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Bab 1: Apa Itu Pengakuan Iman?</h2>
                <svg id="icon-bab1" class="w-6 h-6 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="content-bab1" class="hidden px-6 py-4 border-t border-gray-100">
                <div class="prose max-w-none text-gray-700">
                    <p class="mb-4 text-justify drop-cap">Kata "pengakuan" dalam kamus bahasa Indonesia berarti proses, cara, perbuatan mengaku atau mengakui. Sedangkan dalam bahasa Alkitab dalam Perjanjian Baru kata "pengakuan" adalah <span class="italic">"homologia"</span>. Kata <span class="italic">"homologia"</span> berasal dari kata <span class="italic">"homolegeo"</span> yang mempunyai arti mengatakan hal yang sama, setuju dengan, bersyukur, memuji dan merayakan. Karena itu sangatlah penting bagi kita mempunyai sumber yang benar untuk pengakuan iman kita yang akan kita akui terus menerus.</p>
                    
                    <p class="mb-4 text-justify">Dan arti kata <span class="font-bold">"iman"</span> dalam kamus besar bahasa Indonesia adalah kepercayaan kepada Allah dan kitab-Nya. Sedangkan dalam bahasa Alkitab dalam Perjanjian Baru, yaitu <span class="italic">"pistis"</span> yang mempunyai arti kepercayaan kepada Allah atau Kristus.</p>
                    
                    <p class="mb-4 text-justify">Jadi kita dapat simpulkan secara sederhana arti dari <span class="font-bold">"Pengakuan Iman"</span> adalah mengakui Tuhan dan perkataan-Nya sebagai kepercayaan kita kepada-Nya! Pengakuan iman juga berarti setuju dengan perkataan-Nya (firman-Nya), mengatakan perkataan-Nya dan bersyukur karena perkataan-Nya menjadi milik kita.</p>
                </div>
            </div>
        </div>

        {{-- Bab 2 --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <button type="button" onclick="toggleSection('bab2')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Bab 2: Pentingnya Pengakuan Iman</h2>
                <svg id="icon-bab2" class="w-6 h-6 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="content-bab2" class="hidden px-6 py-4 border-t border-gray-100">
                <div class="prose max-w-none text-gray-700">
                    <p class="mb-4 text-justify drop-cap">Dalam surat Yakobus 3:1-12, kita akan menemukan peranan lidah yang sangat penting. Dengan lidah kita dapat memuji Tuhan, tetapi dengan lidah kita juga dapat mengutuk manusia yang diciptakan oleh Tuhan. Dengan lidah dapat keluar berkat, tetapi dengan lidah dapat juga keluar kutuk. Artinya dengan lidah atau mulut kita, dapat keluar perkataan-perkataan yang benar atau salah, yang baik atau tidak baik dan yang membangun atau meruntuhkan.</p>
                    
                    <p class="mb-4 text-justify">Dengan pengakuan iman, kita sebenarnya sedang mengendalikan lidah atau mulut kita hanya untuk memperkatakan perkataan-perkataan yang benar.</p>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 my-6">
                        <p class="text-gray-800 text-base leading-relaxed italic mb-2">
                            Hidup dan mati dikuasai lidah, siapa suka menggemakan nya, akan memakan buahnya <span class="font-bold not-italic">(Amsal 18:21)</span>.
                        </p>
                    </div>
                    
                    <p class="mb-4 text-justify">Penulis kitab Amsal ini juga menegaskan betapa pentingnya memakai lidah atau mulut kita dengan benar. Karena apa yang keluar dari lidah atau mulut yaitu perkataan-perkataan kita akan kita makan. Artinya perkataan-perkataan yang keluar dari mulut kita akan menentukan seperti apa kehidupan kita.</p>
                    
                    <p class="mb-4 text-justify">Dalam Alkitab ada banyak ayat-ayat firman Tuhan yang mengatakan bahwa lidah atau perkataan-perkataan kita sangatlah penting. Yesus juga mengatakan hal yang sama mengenai pentingnya perkataan-perkataan kita.</p>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 my-6">
                        <p class="text-gray-800 text-base leading-relaxed italic mb-2">
                            "Karena menurut ucapanmu engkau akan dibenarkan, dan menurut ucapanmu pula engkau akan dihukum" <span class="font-bold not-italic">(Matius 12:37)</span>.
                        </p>
                    </div>
                    
                    <p class="mb-4 text-justify">Jadi perkataan-perkataan kita sangatlah penting. Oleh karena itu sangatlah penting untuk kita memperkatakan <span class="font-bold">"Pengakuan Iman"</span>, yaitu memperkatakan dengan percaya perkataan- perkataan Tuhan, firman-Nya setiap hari.</p>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 my-8">
                        <div class="flex justify-center mb-4">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <p class="text-gray-800 text-base leading-relaxed italic text-center mb-4">
                            Janganlah engkau lupa memperkatakan kitab Taurat ini, tetapi renungkanlah itu siang dan malam, supaya engkau bertindak hati-hati sesuai dengan segala yang tertulis di dalamnya, sebab dengan demikian perjalananmu akan berhasil dan engkau akan beruntung.
                        </p>
                        <p class="text-blue-700 font-bold text-lg text-center">Yosua 1:8</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bab 3 --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <button type="button" onclick="toggleSection('bab3')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Bab 3: Kuasa Perkataan Firman Tuhan</h2>
                <svg id="icon-bab3" class="w-6 h-6 text-gray-600 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div id="content-bab3" class="hidden px-6 py-4 border-t border-gray-100">
                <div class="prose max-w-none text-gray-700">
                    <p class="mb-4 text-justify">Perkataan atau firman Tuhan itu kekal untuk selama-lamanya.</p>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 my-6">
                        <p class="text-gray-800 text-base leading-relaxed italic mb-2">
                            Langit dan bumi akan berlalu, tetapi perkataan-Ku tidak akan berlalu. <span class="font-bold not-italic">(Matius 24:35)</span>
                        </p>
                    </div>
                    
                    <p class="mb-4 text-justify">Semua yang ada di langit dan di bumi suatu saat akan lenyap, tidak ada lagi. Tetapi perkataan Yesus tidak akan lenyap, ada untuk selama-lamanya!</p>
                    
                    <h3 class="text-base font-bold text-gray-800 mt-6 mb-3">• PERKATAAN-NYA MELEBIHI SEGALA SESUATU</h3>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 my-6">
                        <p class="text-gray-800 text-base leading-relaxed italic mb-3">
                            sebab Kaubuat nama-Mu dan janji-Mu melebihi segala sesuatu <span class="font-bold not-italic">(Mazmur 138:2c)</span>
                        </p>
                        <p class="text-gray-800 text-base leading-relaxed italic mb-0">
                            for you have made your word greater than all your names <span class="font-bold not-italic">(Psalm 138:2c)</span>
                        </p>
                    </div>
                    
                    <p class="mb-4 text-justify">Tuhan Allah telah membuat perkataan-Nya melebihi segala sesuatu, bahkan melebihi nama-Nya.</p>
                    
                    <h3 class="text-base font-bold text-gray-800 mt-6 mb-3">• PERKATAAN YESUS IALAH PERKATAAN YANG MEMBERI KEHIDUPAN KEKAL, KEHIDUPAN YANG BERKELIMPAHAN.</h3>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 my-6">
                        <p class="text-gray-800 text-base leading-relaxed italic mb-0">
                            Perkataan-perkataan yang Kukatakan kepadamu adalah roh dan hidup. <span class="font-bold not-italic">(Yohanes 6:63b)</span>
                        </p>
                    </div>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6 my-6">
                        <p class="text-gray-800 text-base leading-relaxed italic mb-0">
                            Aku datang, supaya mereka mempunyai hidup, dan mempunyainya dalam segala kelimpahan. <span class="font-bold not-italic">(Yohanes 10:10b)</span>
                        </p>
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 my-8">
                        <div class="flex justify-center mb-4">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <p class="text-gray-800 text-base leading-relaxed italic text-center mb-4">
                            Apabila aku bertemu dengan perkataan-perkataan-Mu, maka aku menikmatinya; firman-Mu itu menjadi kegirangan bagiku, dan menjadi kesukaan hatiku, sebab nama-Mu telah diserukan atasku, ya TUHAN, Allah semesta alam.
                        </p>
                        <p class="text-blue-700 font-bold text-lg text-center">Yeremia 15:16</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

<style>
/* Drop Cap Styling - More Reliable Method */
.drop-cap::first-letter {
    float: left;
    font-size: 4.5rem;
    line-height: 3.8rem;
    padding-right: 8px;
    padding-top: 4px;
    font-weight: 700;
    color: #1a202c;
    font-family: Georgia, serif;
}

/* Carousel Styles */
.carousel-slide {
    display: none;
    animation: fadeIn 0.5s ease-in-out;
}

.carousel-slide.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.indicator {
    cursor: pointer;
    transition: all 0.3s ease;
}

.indicator.active {
    background-color: white;
    transform: scale(1.2);
}

.indicator:not(.active) {
    background-color: rgba(255, 255, 255, 0.5);
}

.indicator:hover {
    transform: scale(1.1);
}
</style>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const indicators = document.querySelectorAll('.indicator');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        indicators[i].classList.remove('active');
    });
    
    slides[index].classList.add('active');
    indicators[index].classList.add('active');
    currentSlide = index;
}

function nextSlide() {
    let next = (currentSlide + 1) % slides.length;
    showSlide(next);
}

function prevSlide() {
    let prev = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(prev);
}

function goToSlide(index) {
    showSlide(index);
}

// Toggle section function with accordion behavior
function toggleSection(sectionId) {
    const content = document.getElementById('content-' + sectionId);
    const icon = document.getElementById('icon-' + sectionId);
    
    // Get all sections
    const allSections = ['intro', 'bab1', 'bab2', 'bab3'];
    
    // Close all other sections
    allSections.forEach(id => {
        if (id !== sectionId) {
            const otherContent = document.getElementById('content-' + id);
            const otherIcon = document.getElementById('icon-' + id);
            if (otherContent && !otherContent.classList.contains('hidden')) {
                otherContent.classList.add('hidden');
                otherIcon.style.transform = 'rotate(0deg)';
            }
        }
    });
    
    // Toggle current section
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

@endsection