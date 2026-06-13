1. Daftar Anggota Kelompok
* **Rizki Isma Ramadhani** - `2410631170047`
* **Sri Bintang Pamungkas** - `2410631170110`
* **Fitroh Aulia Muzzaki** - `2410631170020`

2. Deskripsi dan Tujuan Website
* **Deskripsi:** UnsikaFinder adalah sistem informasi berbasis web yang dirancang khusus sebagai wadah *lost and found* (pelaporan barang hilang dan temuan) untuk civitas akademika di lingkungan Universitas Singaperbangsa Karawang.
* **Tujuan:** 
  1. Mempermudah dan mempercepat mahasiswa dalam melacak atau mencari barang mereka yang hilang secara efisien.
  2. Menekan angka kehilangan tanpa kejelasan melalui sistem pelaporan yang jelas dan terdokumentasi.
  3. Membangun pusat informasi terpadu (satu pintu) agar info barang hilang tidak lagi berserakan di berbagai grup chat.

Fitur-Fitur Utama Website
* 🔑 **Autentikasi Pengguna (Login & Register):** Pengguna wajib memiliki akun terdaftar untuk membuat laporan demi keamanan data.
* 🔎 **Dashboard & Filter Status Barang:** Menampilkan daftar barang hilang/temuan yang bisa difilter secara real-time berdasarkan kategori (Dokumen, Elektronik, Lainnya).
* 📋 **Form Laporan Barang:** Fitur bagi user untuk mengunggah detail nama barang, deskripsi spesifik, lokasi, serta foto bukti fisik barang.
* 🕒 **Riwayat Laporan Saya:** Halaman khusus bagi user untuk memantau status laporan yang pernah mereka buat (Temuan / Hilang / Selesai).
* 💬 **Tombol Direct WhatsApp:** Integrasi langsung yang mengubah nomor HP pelapor menjadi link chat WhatsApp (`wa.me`) dengan pesan otomatis, memudahkan pemilik dan penemu untuk langsung berkoordinasi secara instan.

4. Struktur Project Beserta Penjelasan Folder/File Penting
Berikut adalah struktur direktori utama pada sistem UnsikaFinder:

unsikafinder/
│
├── uploads/          # Folder lokal tempat menampung seluruh file gambar/foto barang yang di-upload user
├── koneksi.php       # File krusial berisi skrip php untuk menyambungkan aplikasi ke database MySQL
├── index.php         # Halaman utama aplikasi (menampilkan list barang, riwayat, dan logika filter)
├── login.php         # Halaman autentikasi masuk untuk mengecek session user
├── logout.php        # File pemutus session untuk mengeluarkan akun user dengan aman
├── login.css         # Berkas stylesheet khusus untuk mengatur tampilan visual halaman login & daftar
├── unsika.png        # Aset gambar logo Universitas Singaperbangsa Karawang yang digunakan pada UI
└── README.md         # File dokumentasi Markdown ini

 5. Cara Menjalankan Aplikasi

    Persiapan Server Lokal: Pastikan aplikasi XAMPP sudah terinstal di komputer Anda. Aktifkan modul Apache dan MySQL melalui XAMPP Control Panel.

    Pindahkan File Proyek: Ekstrak atau letakkan folder unsikafinder ke dalam direktori server lokal Anda (biasanya di C:\xampp\htdocs\).

    Import Database:

        Buka browser dan akses halaman http://localhost/phpMyAdmin/.

        Buat database baru bernama unsika_lost_found.

        Pilih menu Import, lalu pilih berkas database .sql proyek ini dan klik Go/Import.

    Konfigurasi Koneksi: Buka file koneksi.php menggunakan teks editor Anda, lalu pastikan parameter host, user (root), password, dan nama database sudah sesuai dengan pengaturan MySQL lokal.

    Akses Aplikasi: Buka browser dan jalankan aplikasi dengan mengetikkan alamat URL: http://localhost/unsikafinder/.

    6. Link Video Presentasi Project

Silakan akses tautan di bawah ini untuk melihat video demo dan presentasi lengkap mengenai sistem UnsikaFinder:

👉 [Link Video Presentasi Proyek Kelompok 4](https://drive.google.com/drive/folders/1QoXfbXqXMS8WB9cnqa5MmvUdfciB22w5?usp=sharing)
