<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>Pondok Pesantren Modern Darut Tafsir</title>

  {{-- Tailwind CSS --}}
  @vite('resources/css/app.css')
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- FontAwesome --}}
  <script src="https://kit.fontawesome.com/b9470f7400.js" crossorigin="anonymous"></script>

  {{-- AOS Animation Library --}}
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo-darutafsir.png">

  <style>
      body {
          font-family: 'Outfit', sans-serif;
      }
      .glass-nav {
          background: rgba(255, 255, 255, 0.8);
          backdrop-filter: blur(12px);
          -webkit-backdrop-filter: blur(12px);
          border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      }
      .text-gradient {
          background: linear-gradient(to right, #db2777, #be185d);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
      }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 overflow-x-hidden">

  {{-- Navbar --}}
  <nav id="navbar" class="fixed w-full z-50 top-0 left-0 transition-all duration-300 border-b border-transparent">
      <div class="max-w-7xl mx-auto px-6 md:px-12 py-4 flex justify-between items-center">
        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('index') }}">
                <img src="/assets/logo-darutafsir.png" alt="Darut Tafsir Logo" class="h-10 md:h-12 drop-shadow-md">
            </a>
            <h1 id="nav-title" class="text-xl md:text-2xl font-bold tracking-tight text-white transition-colors duration-300">Darut Tafsir</h1>
        </div>

        {{-- Desktop Menu --}}
        <div class="hidden md:flex items-center gap-8 font-medium">
            <a href="#" class="nav-link text-white hover:text-pink-400 transition-colors duration-300">Home</a>
            <a href="#about" class="nav-link text-white hover:text-pink-400 transition-colors duration-300">Tentang</a>
            <a href="#fasilitas" class="nav-link text-white hover:text-pink-400 transition-colors duration-300">Fasilitas</a>
            <a href="#contact" class="nav-link text-white hover:text-pink-400 transition-colors duration-300">Kontak</a>
        </div>

        {{-- CTA Button --}}
        <div class="hidden md:block">
            <a href="/auth/login" class="bg-pink-600 px-6 py-2.5 rounded-full text-white font-semibold shadow-lg shadow-pink-600/30 hover:bg-pink-500 hover:shadow-pink-600/50 transition-all duration-300 transform hover:-translate-y-0.5">
                Login Portal
            </a>
        </div>

        {{-- Mobile Menu Button --}}
        <div class="md:hidden text-white cursor-pointer relative z-50 transition-colors duration-300" id="mobile-menu-btn">
            <i class="fa-solid fa-bars text-2xl"></i>
        </div>
      </div>

      {{-- Mobile Menu Overlay --}}
      <div id="mobile-menu" class="fixed inset-0 bg-slate-900/95 backdrop-blur-xl z-40 transform translate-x-full transition-transform duration-500 flex flex-col items-center justify-center space-y-8 md:hidden">
          <a href="#" class="mobile-link text-3xl font-bold text-slate-300 hover:text-white hover:scale-110 transition-all duration-300 transform translate-y-8 opacity-0">Home</a>
          <a href="#about" class="mobile-link text-3xl font-bold text-slate-300 hover:text-white hover:scale-110 transition-all duration-300 transform translate-y-8 opacity-0" style="transition-delay: 100ms;">Tentang</a>
          <a href="#fasilitas" class="mobile-link text-3xl font-bold text-slate-300 hover:text-white hover:scale-110 transition-all duration-300 transform translate-y-8 opacity-0" style="transition-delay: 200ms;">Fasilitas</a>
          <a href="#contact" class="mobile-link text-3xl font-bold text-slate-300 hover:text-white hover:scale-110 transition-all duration-300 transform translate-y-8 opacity-0" style="transition-delay: 300ms;">Kontak</a>
          <a href="/auth/login" class="mobile-link text-xl font-bold text-white bg-pink-600 px-10 py-4 rounded-full shadow-lg shadow-pink-600/30 hover:bg-pink-500 hover:shadow-pink-600/50 transition-all duration-300 transform translate-y-8 opacity-0" style="transition-delay: 400ms;">Login Portal</a>
      </div>
  </nav>

  {{-- Hero Section --}}
  <section id="home" class="relative h-screen bg-cover bg-center" style="background-image: url('{{ $profile && $profile->banner_image ? asset('storage/' . $profile->banner_image) : asset('assets/banner.jpg') }}');">
     {{-- Overlay --}}
     <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/60 to-slate-50"></div>

     <div class="relative z-10 h-full flex items-center justify-center text-center px-4">
        <div class="max-w-4xl mx-auto mt-16">
            <span class="inline-block py-1 px-3 rounded-full bg-pink-600/20 text-pink-300 border border-pink-500/30 text-sm font-medium mb-6 backdrop-blur-sm" data-aos="fade-down" data-aos-delay="100">
                Pondok Pesantren Modern & Tahfidz
            </span>
            <h2 class="font-bold text-4xl md:text-6xl lg:text-7xl mb-6 text-white leading-tight" data-aos="fade-up" data-aos-delay="200">
                {{ $profile->title ?? 'Membangun Generasi Qur\'ani & Berakhlak' }}
            </h2>
            <p class="text-gray-200 text-lg md:text-xl mb-10 max-w-2xl mx-auto font-light leading-relaxed" data-aos="fade-up" data-aos-delay="300">
                {{ $profile->subtitle ?? 'Lingkungan pendidikan yang menyatukan ilmu agama dan wawasan modern, membekali santri untuk masa depan yang gemilang.' }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-aos="fade-up" data-aos-delay="400">
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSdWsuLWlCtjpn0UfI1gCFHQsllw0NqIxxpFWgsPJ24gAQMkJw/viewform" target="_blank" class="bg-pink-600 text-white px-8 py-4 rounded-full text-lg font-semibold shadow-xl shadow-pink-600/30 hover:bg-pink-500 hover:scale-105 transition-all duration-300">
                    Daftar Sekarang <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                </a>
                <a href="#about" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white/20 transition-all duration-300">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
     </div>

      {{-- Scroll Down Indicator --}}
      <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce text-white/50">
          <i class="fa-solid fa-chevron-down text-2xl"></i>
      </div>
  </section>

  {{-- Tentang Pondok Section --}}
  <section id="about" class="py-24 px-6 md:px-12 bg-slate-50 relative overflow-hidden">
    {{-- Background pattern --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-pink-200/20 rounded-full blur-3xl -z-10 translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-200/20 rounded-full blur-3xl -z-10 -translate-x-1/2 translate-y-1/2"></div>

    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16" data-aos="fade-up">
            <h3 class="text-pink-600 font-bold uppercase tracking-wider text-sm mb-2">Tentang Kami</h3>
            <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6">Mengenal Darut Tafsir</h2>
            <p class="text-lg text-slate-600 max-w-3xl mx-auto leading-relaxed">
                {{ $profile->about ?? 'Pondok Pesantren Modern dan Tahfidz Al-Qur\'an Darut Tafsir hadir sebagai jawaban atas kebutuhan pendidikan Islam yang komprehensif, mencetak kader umat yang intelek dan spiritual.' }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
            <!-- Vision Card -->
            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(219,39,119,0.1)] transition-all duration-300 group border border-slate-100" data-aos="fade-right">
                <div class="w-14 h-14 bg-pink-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-pink-600 transition-colors duration-300">
                     <i class="fa-solid fa-eye text-2xl text-pink-600 group-hover:text-white transition-colors duration-300"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Visi Kami</h3>
                <p class="text-slate-600 leading-relaxed">
                    {{ $profile->vision ?? 'Mewujudkan generasi tahfidz muda yang cinta Al-Qur\'an, berakhlakul karimah, berwawasan modern, dan mampu mengamalkan nilai-nilai Al-Qur\'an dalam kehidupan bermasyarakat.' }}
                </p>
            </div>

            <!-- Mission Card -->
            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(219,39,119,0.1)] transition-all duration-300 group border border-slate-100" data-aos="fade-left">
                <div class="w-14 h-14 bg-pink-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-pink-600 transition-colors duration-300">
                    <i class="fa-solid fa-bullseye text-2xl text-pink-600 group-hover:text-white transition-colors duration-300"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-800">Misi Kami</h3>
                <p class="text-slate-600 leading-relaxed">
                    {{ $profile->mission ?? 'Menyelenggarakan pendidikan tahfidz yang mutqin, menanamkan nilai Al-Qur\'an, membentuk karakter disiplin, serta mengintegrasikan wawasan modern agar santri siap menghadapi tantangan zaman.' }}
                </p>
            </div>
        </div>
    </div>
  </section>

  {{-- Fasilitas Section (New) --}}
  <section id="fasilitas" class="py-24 px-6 md:px-12 bg-white relative">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16" data-aos="fade-up">
            <h3 class="text-pink-600 font-bold uppercase tracking-wider text-sm mb-2">Fasilitas Pondok</h3>
            <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-6">Sarana Penunjang Belajar</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                Kami menyediakan fasilitas lengkap dan nyaman untuk mendukung kegiatan belajar mengajar serta aktivitas harian santri.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            {{-- Facility Item 1 --}}
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 text-pink-600">
                    <i class="fa-solid fa-mosque text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Masjid Jami'</h4>
                <p class="text-slate-500 text-sm">Masjid luas dan nyaman sebagai pusat kegiatan ibadah dan kajian santri.</p>
            </div>

            {{-- Facility Item 2 --}}
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="150">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 text-pink-600">
                    <i class="fa-solid fa-house text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Asrama Berbasis Rumah</h4>
                <p class="text-slate-500 text-sm">Mengusung konsep Pesantren ramah anak dengan pola pengasuhan Usrah (kekeluargaan).</p>
            </div>

             {{-- Facility Item 3 --}}
             <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 text-pink-600">
                    <i class="fa-solid fa-chalkboard-user text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Private Class</h4>
                <p class="text-slate-500 text-sm">Proses pembelajaran dirancang lebih efektif untuk menumbuhkan potensi, minat, dan bakat santri secara optimal.</p>
            </div>

             {{-- Facility Item 4 --}}
             <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="250">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 text-pink-600">
                    <i class="fa-solid fa-book-quran text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Qur'an Generation</h4>
                <p class="text-slate-500 text-sm">Qur’an Generation didukung fasilitas nyaman dan ramah anak untuk menciptakan lingkungan ideal dalam menghafal dan memahami Al-Qur’an.</p>
            </div>

             {{-- Facility Item 5 --}}
             <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 text-pink-600">
                    <i class="fa-solid fa-user-shield text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Character Building</h4>
                <p class="text-slate-500 text-sm">Membentuk santri berakhlak Qurani, Mandiri, dan bertanggung jawab.</p>
            </div>

             {{-- Facility Item 6 --}}
             <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="350">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-4 text-pink-600">
                    <i class="fa-solid fa-futbol text-xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Lapangan Olahraga</h4>
                <p class="text-slate-500 text-sm">Area olahraga futsal, basket, dan badminton untuk kebugaran fisik.</p>
            </div>
        </div>
      </div>
  </section>

  {{-- Kontak Section --}}
  <section id="contact" class="py-24 px-6 md:px-12 bg-slate-50">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-12 text-center" data-aos="fade-up">Hubungi Kami</h2>

        <div class="grid md:grid-cols-3 gap-6 mb-12">
            {{-- Contact Card 1 --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4 text-green-600">
                    <i class="fa-brands fa-whatsapp text-3xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-1">WhatsApp</h3>
                <p class="text-slate-500 text-sm mb-4">{{ $profile->phone ?? '+62 812 1234 4568' }}</p>
                <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $profile->phone ?? '6281212344568') }}" target="_blank" class="text-pink-600 font-semibold hover:text-pink-700 text-sm">Chat Sekarang &rarr;</a>
            </div>

             {{-- Contact Card 2 --}}
             <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4 text-blue-600">
                    <i class="fa-regular fa-envelope text-3xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-1">Email</h3>
                <p class="text-slate-500 text-sm mb-4">{{ $profile->email ?? 'daruttafsir@gmail.com' }}</p>
                <a href="mailto:{{ $profile->email ?? 'daruttafsir@gmail.com' }}" class="text-pink-600 font-semibold hover:text-pink-700 text-sm">Kirim Email &rarr;</a>
            </div>

             {{-- Contact Card 3 --}}
             <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4 text-red-600">
                    <i class="fa-solid fa-location-dot text-3xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-1">Lokasi</h3>
                <p class="text-slate-500 text-sm mb-4">{{ $profile->address ?? 'Kabupaten Lebak, Banten' }}</p>
                <a href="https://www.google.com/maps/search/{{ urlencode($profile->address ?? 'Kabupaten Lebak, Banten') }}" target="_blank" class="text-pink-600 font-semibold hover:text-pink-700 text-sm">Lihat Peta &rarr;</a>
            </div>
        </div>

        <div class="rounded-3xl overflow-hidden shadow-2xl" data-aos="zoom-in">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.2864245057604!2d106.231561!3d-6.3569588999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e42171eff7fba47%3A0x8fb5428daf4af134!2sDarut%20Tafsir%20Homeschooling%20And%20Tahfidz%20Al-Quran!5e0!3m2!1sid!2sid!4v1764941386114!5m2!1sid!2sid"
                height="450" class="w-full" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
      </div>
  </section>

  {{-- Footer --}}
  <footer class="bg-slate-900 text-white pt-16 pb-8 px-6 md:px-12 border-t border-slate-800">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between gap-12 mb-12">
        <div class="md:w-1/3">
            <div class="flex items-center gap-2 mb-6">
                <img src="/assets/logo-darutafsir.png" alt="Logo" class="h-10">
                <span class="text-xl font-bold">Darut Tafsir</span>
            </div>
            <p class="text-slate-400 leading-relaxed text-sm">
                Membangun peradaban dengan Al-Qur'an. Pondok pesantren modern yang mengedepankan adab dan ilmu.
            </p>
        </div>

        <div class="flex gap-12 md:gap-24">
            <div>
                <h4 class="font-bold text-lg mb-6 text-pink-500">Quick Links</h4>
                <ul class="flex flex-col gap-3 text-slate-400 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="#about" class="hover:text-white transition-colors">Tentang</a></li>
                    <li><a href="#fasilitas" class="hover:text-white transition-colors">Fasilitas</a></li>
                    <li><a href="#contact" class="hover:text-white transition-colors">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-6 text-pink-500">Social Media</h4>
                <div class="flex gap-4">
                    <a href="https://instagram.com/darut_tafsir?igshid=xeuv6jzjof35" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-pink-600 transition-all duration-300">
                        <i class="fa-brands fa-instagram text-white"></i>
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=100055205284844" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-all duration-300">
                        <i class="fa-brands fa-facebook-f text-white"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCdRExEmX8Nb2HggOnU6wvvw/featured" target="_blank" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-red-600 transition-all duration-300">
                        <i class="fa-brands fa-youtube text-white"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-800 pt-8 text-center text-slate-500 text-sm">
        &copy; 2025 Darut Tafsir. All rights reserved.
    </div>
  </footer>

  {{-- AOS Script --}}
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100,
    });

    // Navbar Scroll Effect
    const navbar = document.getElementById('navbar');
    const navTitle = document.getElementById('nav-title');
    const navLinks = document.querySelectorAll('.nav-link');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileLinks = document.querySelectorAll('.mobile-link');
    let isMenuOpen = false;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar.classList.add('glass-nav', 'shadow-sm', 'py-3');
            navbar.classList.remove('py-4', 'border-transparent');

            navTitle.classList.remove('text-white');
            navTitle.classList.add('text-slate-800');

            navLinks.forEach(link => {
                link.classList.remove('text-white');
                link.classList.add('text-slate-600');
            });

            // Only change color if menu is CLOSED
            if (!isMenuOpen) {
                mobileMenuBtn.classList.remove('text-white');
                mobileMenuBtn.classList.add('text-slate-800');
            }

        } else {
            navbar.classList.remove('glass-nav', 'shadow-sm', 'py-3');
            navbar.classList.add('py-4', 'border-transparent');

            navTitle.classList.add('text-white');
            navTitle.classList.remove('text-slate-800');

            navLinks.forEach(link => {
                link.classList.add('text-white');
                link.classList.remove('text-slate-600');
            });

             // Only change color if menu is CLOSED
             if (!isMenuOpen) {
                mobileMenuBtn.classList.add('text-white');
                mobileMenuBtn.classList.remove('text-slate-800');
            }
        }
    });

    // Mobile Menu Toggle
    mobileMenuBtn.addEventListener('click', () => {
        isMenuOpen = !isMenuOpen;

        // Toggle Icon
        const icon = mobileMenuBtn.querySelector('i');
        if (isMenuOpen) {
            // Disable scrolling
            document.body.style.overflow = 'hidden';

            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
            icon.style.transform = "rotate(90deg)";
            icon.style.transition = "transform 0.3s ease";

            // Show Menu
            mobileMenu.classList.remove('translate-x-full');

            // Remove glass effect so navbar becomes transparent and reveals dark menu bg
            navbar.classList.remove('glass-nav', 'shadow-sm', 'py-3');
            navbar.classList.add('py-4'); // Maintain vertical spacing

            // Animate Links
            setTimeout(() => {
                mobileLinks.forEach(link => {
                    link.classList.remove('translate-y-8', 'opacity-0');
                });
            }, 100);

            // Force Navbar styling to dark mode compatible (text-white)
            navTitle.classList.remove('text-slate-800');
            navTitle.classList.add('text-white');

            mobileMenuBtn.classList.remove('text-slate-800');
            mobileMenuBtn.classList.add('text-white');

        } else {
            // Enable scrolling
            document.body.style.overflow = '';

            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
            icon.style.transform = "rotate(0deg)";

            // Hide Menu
            mobileMenu.classList.add('translate-x-full');

            // Reset Links
            mobileLinks.forEach(link => {
                link.classList.add('translate-y-8', 'opacity-0');
            });

            // Revert Navbar styling based on scroll
            if (window.scrollY > 20) {
                 navbar.classList.add('glass-nav', 'shadow-sm', 'py-3');
                 navbar.classList.remove('py-4');

                 navTitle.classList.add('text-slate-800');
                 navTitle.classList.remove('text-white');

                 mobileMenuBtn.classList.add('text-slate-800');
                 mobileMenuBtn.classList.remove('text-white');
            } else {
                 navbar.classList.add('py-4');
            }
        }
    });

    // Close menu when link clicked
    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            if(isMenuOpen) mobileMenuBtn.click();
        });
    });

  </script>

</body>
</html>
