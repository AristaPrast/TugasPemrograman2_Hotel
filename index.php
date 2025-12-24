<<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
  <title>Luviana Hotel - Sistem Informasi Hotel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <style>
    .admin-login {
      background: #007bff;
      color: #fff;
      padding: 8px 15px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    .admin-login:hover {
      background: #0056b3;
    }
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .nav-right {
      display: flex;
      align-items: center;
      gap: 15px;
    }
  </style>
</head>

<body>
  <!-- NAVIGASI -->
  <header class="header" id="navigation-menu">
    <div class="container">
      <nav>
        <a href="#" class="logo">
          <img src="image/logo.png" alt="Logo Hotel" style="height:50px;">
        </a>
        <ul class="nav-menu">
          <li><a href="#home" class="nav-link">Beranda</a></li>
          <li><a href="#about" class="nav-link">Tentang Kami</a></li>
          <li><a href="#rooms" class="nav-link">Kamar & Harga</a></li>
          <li><a href="#restaurant" class="nav-link">Restoran</a></li>
          <li><a href="#gallery" class="nav-link">Galeri</a></li>
          <li><a href="#contact" class="nav-link">Kontak Kami</a></li>
        </ul>
        <div class="nav-right">
          <a href="login.php" class="admin-login"><i class="fas fa-user-shield"></i> Login</a>
          <div class="hambuger">
            <span class="bar"></span><span class="bar"></span><span class="bar"></span>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <script>
    const hambuger = document.querySelector('.hambuger');
    const navMenu = document.querySelector('.nav-menu');
    hambuger.addEventListener("click", () => {
      hambuger.classList.toggle("active");
      navMenu.classList.toggle("active");
    });
  </script>

  <!-- BERANDA -->
  <section class="home" id="home">
    <div class="head_container">
      <div class="box">
        <div class="text">
          <h1>Selamat Datang di Hotel Luviana</h1>
          <p>Nikmati kemewahan dan ketenangan di tepi pantai.Dapatkan pengalaman menginap eksklusif dengan pemandangan laut yang memukau, pelayanan berkelas, serta suasana tropis yang menenangkan.
</p>
          <button onclick="window.location.href='#rooms'">Lihat Kamar</button>
        </div>
      </div>
      <div class="image">
        <img src="image/home1.jpg" class="slide" alt="Foto Hotel">
      </div>
      <div class="image_item">
        <img src="image/home1.jpg" alt="" class="slide active" onclick="img('image/home1.jpg')">
        <img src="image/home2.jpg" alt="" class="slide" onclick="img('image/home2.jpg')">
        <img src="image/home3.jpg" alt="" class="slide" onclick="img('image/home3.jpg')">
        <img src="image/home4.jpg" alt="" class="slide" onclick="img('image/home4.jpg')">
      </div>
    </div>
  </section>

  <script>
    function img(src) { document.querySelector('.slide').src = src; }
  </script>

  <!-- TENTANG KAMI -->
  <section class="about top" id="about">
    <div class="container flex">
      <div class="left">
        <div class="img">
          <img src="image/a1.jpg" alt="" class="image1">
          <img src="image/a2.jpg" alt="" class="image2">
        </div>
      </div>
      <div class="right">
        <div class="heading">
          <h5>KENYAMANAN ADALAH PRIORITAS KAMI</h5>
          <h2>Tentang Hotel Luviana</h2>
          <p>Hotel Luviana adalah hotel bintang 5 yang menghadirkan kemewahan dan kenyamanan di tepi pantai. Dikelilingi pemandangan laut yang menakjubkan dan suasana tropis yang tenang, Hotel Luviana menjadi pilihan sempurna untuk liburan romantis, perjalanan keluarga, maupun kunjungan bisnis. Dengan fasilitas premium, layanan ramah, dan lokasi strategis dekat pusat wisata, kami menghadirkan pengalaman menginap yang elegan dan tak terlupakan.</p>
          <p>Kami berkomitmen untuk memberikan pengalaman terbaik dengan pelayanan profesional dan suasana yang ramah bagi setiap tamu yang datang.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- KAMAR -->
  <section class="room top" id="rooms">
    <div class="container">
      <div class="heading_top flex1">
        <div class="heading">
          <h5>KENYAMANAN KELAS TINGGI</h5>
          <h2>Daftar Kamar & Harga</h2>
        </div>
      </div>
      <div class="content grid">
        <div class="box">
          <div class="img"><img src="image/r1.jpg" alt=""></div>
          <div class="text">
            <h3>Kamar Standar</h3>
            <p>Rp 500.000 / malam</p>
          </div>
        </div>
        <div class="box">
          <div class="img"><img src="image/r2.jpg" alt=""></div>
          <div class="text">
            <h3>Kamar Deluxe</h3>
            <p>Rp 750.000 / malam</p>
          </div>
        </div>
        <div class="box">
          <div class="img"><img src="image/r3.jpg" alt=""></div>
          <div class="text">
            <h3>Kamar Suite</h3>
            <p>Rp 1.200.000 / malam</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- RESTORAN -->
  <section class="restaurant top" id="restaurant">
    <div class="container flex">
      <div class="left">
        <img src="image/re.jpg" alt="Restoran Hotel">
      </div>
      <div class="right">
        <div class="text">
          <h2>Restoran & Kuliner</h2>
          <p>Di Luviana Ocean View Restaurant, para tamu dapat menikmati hidangan internasional dan lokal yang disajikan oleh chef profesional dengan bahan-bahan segar pilihan. Setiap menu kami dikurasi dengan cermat untuk menghadirkan rasa autentik dan pengalaman gastronomi yang tak terlupakan mulai dari seafood segar hasil tangkapan harian, hidangan khas nusantara yang kaya rempah, hingga sajian modern bergaya Mediterania.</p>

<p>Untuk suasana yang lebih santai, Sunset Lounge & Bar menjadi tempat sempurna menikmati minuman tropis sambil menyaksikan keindahan matahari terbenam di cakrawala laut. Di malam hari, suasana semakin hidup dengan alunan musik lembut dan pencahayaan romantis yang menciptakan pengalaman tak tertandingi.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- GALERI -->
  <section class="gallary mtop" id="gallery">
    <div class="container">
      <div class="heading_top flex1">
        <div class="heading">
          <h5>GALERI HOTEL</h5>
          <h2>Foto dan Aktivitas Hotel</h2>
        </div>
      </div>
      <div class="owl-carousel owl-theme">
        <div class="item"><img src="image/g1.jpg" alt=""></div>
        <div class="item"><img src="image/g2.jpg" alt=""></div>
        <div class="item"><img src="image/g3.jpg" alt=""></div>
        <div class="item"><img src="image/g4.jpg" alt=""></div>
      </div>
    </div>
  </section>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <script>
    $('.owl-carousel').owlCarousel({
      loop: true,
      margin: 10,
      nav: true,
      dots: false,
      navText: ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"],
      responsive: { 0: { items: 1 }, 768: { items: 2 }, 1000: { items: 4 } }
    });
  </script>

  <!-- KONTAK -->
  <section class="map top" id="contact">
    <div class="container">
      <h2>Hubungi Kami</h2>
      <p>Alamat: Jl. Mawar No. 123, Jakarta Pusat<br>Email: info@luvianahotel.com | Telepon: (021) 555-1234</p>
    </div>
    <iframe src="https://www.google.com/maps/embed?..." width="100%" height="400" style="border:0;" allowfullscreen=""></iframe>
  </section>

  <footer>
    <div class="container grid top">
      <div class="box">
        <img src="image/logo.png" alt="Logo Hotel">
        <p>Hotel Luviana - Kenyamanan dan kemewahan dalam satu tempat.</p>
      </div>
      <div class="box">
        <h3>Tautan Cepat</h3>
        <ul>
          <li><a href="#about">Tentang Kami</a></li>
          <li><a href="#rooms">Kamar & Harga</a></li>
          <li><a href="#restaurant">Restoran</a></li>
          <li><a href="#contact">Kontak</a></li>
        </ul>
      </div>
      <div class="box">
        <h3>Hubungi Kami</h3>
        <ul>
          <li><i class="fas fa-map-marker-alt"></i> Jl. Mawar No. 123, Jakarta</li>
          <li><i class="fas fa-phone"></i> (021) 555-1234</li>
          <li><i class="fas fa-envelope"></i> info@luvianahotel.com</li>
        </ul>
      </div>
    </div>
  </footer>
</body>
</html>
