<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user_login = $_SESSION['id_user'];
$nama_user_login = $_SESSION['nama'];

$cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'foto'");
if(mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($conn, "ALTER TABLE barang ADD COLUMN foto VARCHAR(255) DEFAULT '01.jpg'");
}

if (isset($_GET['done'])) {
    $id = $_GET['done'];
    
    $query_foto = mysqli_query($conn, "SELECT foto FROM barang WHERE id_barang='$id'");
    if($data_foto = mysqli_fetch_assoc($query_foto)) {
        $nama_foto_lama = $data_foto['foto'];
        
        if ($nama_foto_lama != '01.jpg' && file_exists('uploads/' . $nama_foto_lama)) {
            unlink('uploads/' . $nama_foto_lama);
        }
    }
    
    // Foto hilang kalau kasus udah selesai
    mysqli_query($conn, "UPDATE barang SET status='Selesai', foto='01.jpg' WHERE id_barang='$id'");
    
    echo "<script>alert('Kasus ditutup! Foto bukti otomatis dihapus dari sistem demi privasi.'); window.location='index.php';</script>";
}

if (isset($_POST['submit_laporan'])) {
    $nb = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $ds = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kt = $_POST['kategori'];
    $lk = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $st = $_POST['jenis_laporan'];
    $tg = date('Y-m-d');

    $nama_foto = '01.jpg'; 
    if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] == 0) {
        $dir = 'uploads/';
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        
        $ext = pathinfo($_FILES['foto_barang']['name'], PATHINFO_EXTENSION);
        $nama_foto = time() . '_' . uniqid() . '.' . $ext; 
        move_uploaded_file($_FILES['foto_barang']['tmp_name'], $dir . $nama_foto);
    }

    //database (tabel barang)
    mysqli_query($conn, "INSERT INTO barang (id_user, nama_barang, deskripsi, kategori, lokasi, tanggal, status, foto) 
                        VALUES ('$id_user_login', '$nb', '$ds', '$kt', '$lk', '$tg', '$st', '$nama_foto')");
    
    echo "<script>alert('Laporan berhasil disimpan!'); window.location='index.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UnsikaFinder Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media screen and (max-width: 768px) {
            html, body {
                height: auto !important;
                overflow-y: auto !important;
            }
            .app-container {
                display: block !important; 
                height: auto !important;
            }
            .sidebar {
                width: 100% !important;
                height: auto !important;
                position: static !important; 
                min-height: unset !important;
                padding-bottom: 20px !important;
                border-bottom: 2px solid #eee;
                box-shadow: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 15px !important;
                position: static !important; 
                height: auto !important;
            }
            .card-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
            }
            .category-list, .tabs {
                display: flex !important;
                overflow-x: auto !important;
                flex-wrap: nowrap !important;
                padding-bottom: 10px !important;
            }
            .cat-btn, .tab-link {
                flex: 0 0 auto !important;
            }

            .sidebar-footer {
                margin-top: 10px !important;
            }
            .logout-btn {
                display: block !important;
                padding: 10px 15px !important;
                margin: 0 15px 10px 15px !important; 
                border-radius: 10px !important;
                color: #d32f2f !important; 
                background-color: #ffebee !important; 
                text-decoration: none !important;
                font-weight: bold !important;
            }
        }
    </style>
<body>

    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="unsika.png" alt="Logo" class="logo-img" onerror="this.style.display='none'">
                <h3>UnsikaFinder</h3>
            </div>

            <div style="padding: 10px 20px; color: #555; font-size: 14px; text-align: center; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                👋 Halo, <strong><?php echo htmlspecialchars($nama_user_login); ?></strong>
            </div>

            <nav class="sidebar-nav">
                <a href="#" class="nav-link active" onclick="showSection('barang-section', this)">
                    <i class="fas fa-th-large"></i> Semua Barang
                </a>
                <a href="#" class="nav-link" onclick="showSection('form-section', this)">
                    <i class="fas fa-file-signature"></i> Form Pelapor
                </a>
                <a href="#" class="nav-link" onclick="showSection('riwayat-section', this)">
                    <i class="fas fa-history"></i> Riwayat Saya
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn" onclick="return confirm('Yakin ingin keluar?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <main class="main-content">
            
            <section id="barang-section" class="content-section">
                <div class="dashboard-content">
                    <h2>Semua Barang</h2>

                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Cari nama barang..." onkeyup="handleSearch()">
                        <button onclick="handleSearch()" class="btn-primary">Cari</button>
                    </div>

                    <div class="category-list">
                        <button class="cat-btn active" onclick="filterKategori('Semua', this)">Semua</button>
                        <button class="cat-btn" onclick="filterKategori('Dokumen', this)">Dokumen</button>
                        <button class="cat-btn" onclick="filterKategori('Elektronik', this)">Elektronik</button>
                        <button class="cat-btn" onclick="filterKategori('Lainnya', this)">Lainnya</button>
                    </div>

                    <div class="tabs">
                        <button class="tab-link active" onclick="filterBarang('semua', this)">Semua</button>
                        <button class="tab-link" onclick="filterBarang('hilang', this)">Hilang</button>
                        <button class="tab-link" onclick="filterBarang('temuan', this)">Temuan</button>
                        <button class="tab-link" onclick="filterBarang('selesai', this)">Selesai</button>
                    </div>

                    <div class="card-container">
                        <div class="card-grid" id="itemGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                            
                            <?php
                            $res = mysqli_query($conn, "SELECT b.*, u.nama, u.no_wa FROM barang b JOIN users u ON b.id_user = u.id_user ORDER BY b.id_barang DESC");
                            
                            while ($r = mysqli_fetch_assoc($res)) {
                                $st = strtolower(trim($r['status']));
                                
                                if(strpos($st, 'selesai') !== false) {
                                    $tipe = 'selesai'; $tag = 'tag-selesai'; $teks = 'SELESAI';
                                } elseif (strpos($st, 'hilang') !== false) {
                                    $tipe = 'hilang'; $tag = 'tag-hilang'; $teks = 'HILANG';
                                } else {
                                    $tipe = 'temuan'; $tag = 'tag-temuan'; $teks = 'TEMUAN';
                                }

                                $wa = trim($r['no_wa']);
                                if(substr($wa, 0, 1) === '0') $wa = '62' . substr($wa, 1);
                                $pesanWA = ($tipe == 'hilang') ? "Halo, saya menemukan barang Anda: " : "Halo, saya pemilik barang: ";
                                
                                // Cek apakah ada foto yang diupload (dan file fisiknya benar-benar ada)
                                $ada_foto = (!empty($r['foto']) && $r['foto'] != '01.jpg' && file_exists('uploads/' . $r['foto']));
                            ?>

                            <div class="item-card" data-status="<?php echo $tipe; ?>" data-kategori="<?php echo strtolower($r['kategori']); ?>" style="display: flex; flex-direction: column; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(138,21,56,0.1);">
                                
                                <?php if($ada_foto) { ?>
                                    <img src="uploads/<?php echo $r['foto']; ?>" alt="Barang" style="width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid #eee;">
                                <?php } ?>

                                <div class="card-content" style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                                    <div>
                                        <span class="<?php echo $tag; ?>" style="font-weight:bold; font-size:12px; padding:6px 10px; border-radius:4px; <?php echo ($tipe=='hilang')?'background:#ffebee; color:#d32f2f;':(($tipe=='temuan')?'background:#e3f2fd; color:#1976d2;':'background:#e8f5e9; color:#388e3c;'); ?>"><?php echo $teks; ?></span>
                                        
                                        <h3 class="judul-barang" style="margin-top:15px; font-size: 18px; margin-bottom: 5px;"><?php echo htmlspecialchars($r['nama_barang']); ?></h3>
                                        <p class="category" style="color:gray; font-size:12px; margin-top:0;">Kategori: <?php echo htmlspecialchars($r['kategori']); ?></p>
                                        <p class="description" style="font-size: 14px; line-height: 1.5; color: #444; margin-bottom: 10px;"><?php echo htmlspecialchars($r['deskripsi']); ?></p>
                                    </div>
                                    
                                    <div style="margin-top: auto;">
                                        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 15px 0;">
                                        <ul class="item-details" style="list-style:none; padding:0; font-size:13px; color: #555; line-height: 1.8;">
                                            <li>📍 <strong>Lokasi:</strong> <?php echo htmlspecialchars($r['lokasi']); ?></li>
                                            <li>📅 <strong>Tanggal:</strong> <?php echo $r['tanggal']; ?></li>
                                            <li>👤 <strong>Pelapor:</strong> <?php echo htmlspecialchars($r['nama']); ?></li>
                                        </ul>

                                        <div class="card-actions" style="display:flex; gap:10px; margin-top:15px;">
                                            <?php if($tipe != 'selesai') { ?>
                                                <a href="https://wa.me/<?php echo $wa; ?>?text=<?php echo urlencode($pesanWA.$r['nama_barang']); ?>" target="_blank" style="text-decoration:none; text-align:center; flex:1; background:#25D366; color:white; padding:10px; border-radius:6px; font-size: 14px; font-weight: bold; transition: 0.2s;">Chat WA</a>
                                                <?php if($r['id_user'] == $id_user_login) { ?>
                                                    <a href="index.php?done=<?php echo $r['id_barang']; ?>" onclick="return confirm('Tandai sudah selesai? Foto bukti akan dihapus permanen.')" style="text-decoration:none; text-align:center; flex:1; background:#f8f9fa; color:#333; border: 1px solid #ddd; padding:10px; border-radius:6px; font-size: 14px; font-weight: bold; transition: 0.2s;">Selesai</a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <div style="flex:1; text-align:center; background:#e8f5e9; color:#388e3c; padding:10px; border-radius:6px; font-weight:bold; font-size: 14px;">Barang sudah ditemukan</div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </section>

            <section id="form-section" class="content-section" style="display: none;">
                <h2>Lapor Barang Temuan / Hilang</h2>
                <div class="form-container">
                    <form id="laporForm" method="POST" action="" enctype="multipart/form-data">
                        
                        <div class="input-group">
                            <label>Foto Barang Bukti (Opsional)</label>
                            <div class="upload-area" style="border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 8px; background: #fafafa;">
                                <input type="file" id="foto_barang" name="foto_barang" accept="image/*" onchange="previewImage(event)" style="width: 100%; cursor: pointer;">
                                <img id="gambar-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 250px; margin-top: 15px; display: none; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            </div>
                        </div>

                        <div class="input-group" style="margin-top: 15px;">
                            <label for="jenis_laporan">Jenis Laporan</label>
                            <select id="jenis_laporan" name="jenis_laporan" required>
                                <option value="hilang">Kehilangan Barang</option>
                                <option value="temuan">Menemukan Barang</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" id="nama_barang" name="nama_barang" placeholder="Masukkan nama barang" required>
                        </div>

                        <div class="input-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsikan barang.." required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label for="kategori">Kategori</label>
                                <select id="kategori" name="kategori">
                                    <option value="Dokumen">Dokumen</option>
                                    <option value="Elektronik">Elektronik</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="lokasi">Lokasi</label>
                                <input type="text" id="lokasi" name="lokasi" placeholder="Contoh: Gedung A" required>
                            </div>
                        </div>
                        
                        <div class="button-group" style="margin-top: 20px;">
                            <button type="submit" name="submit_laporan" class="btn-primary" style="padding: 12px; font-weight: bold; border-radius: 6px;">Simpan Laporan</button>
                            <button type="reset" class="btn-secondary" style="padding: 12px; font-weight: bold; border-radius: 6px;" onclick="document.getElementById('gambar-preview').style.display='none';">Batal</button>
                        </div>
                    </form>
                </div>
            </section>

            <section id="riwayat-section" class="content-section" style="display: none;">
                <h2>Riwayat Laporan Saya</h2>
                <table class="history-table" style="width:100%; text-align:left; border-collapse:collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding:15px; border-bottom: 2px solid #dee2e6;">Barang</th>
                            <th style="padding:15px; border-bottom: 2px solid #dee2e6;">Tanggal</th>
                            <th style="padding:15px; border-bottom: 2px solid #dee2e6;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $riwayat = mysqli_query($conn, "SELECT nama_barang, tanggal, status FROM barang WHERE id_user='$id_user_login' ORDER BY id_barang DESC");
                        
                        if(mysqli_num_rows($riwayat) == 0) {
                            echo "<tr><td colspan='3' style='text-align:center; padding:20px; color:gray;'>Belum ada laporan yang Anda buat.</td></tr>";
                        } else {
                            while($row = mysqli_fetch_assoc($riwayat)) {
                                $st = strtolower($row['status']);
                                $badge = (strpos($st, 'selesai') !== false) ? 'Selesai' : ((strpos($st, 'hilang') !== false) ? 'Hilang' : 'Temuan');
                        ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:15px;"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td style="padding:15px;"><?php echo $row['tanggal']; ?></td>
                            <td style="padding:15px;"><span class="badge" style="font-weight:bold;"><?php echo $badge; ?></span></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

    <script>
        function showSection(sectionId, element) {
            document.querySelectorAll('.content-section').forEach(sec => sec.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
            
            document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => link.classList.remove('active'));
            if(element) element.classList.add('active');
        }

        function filterBarang(status, btn) {
            document.querySelectorAll('.tab-link').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            document.querySelectorAll('.item-card').forEach(card => {
                let cardStatus = card.getAttribute('data-status');
                if (status === 'semua' || cardStatus === status) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterKategori(kat, btn) {
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            let katLower = kat.toLowerCase();
            document.querySelectorAll('.item-card').forEach(card => {
                let cardKat = card.getAttribute('data-kategori');
                if (katLower === 'semua' || cardKat === katLower) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function handleSearch() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.item-card').forEach(card => {
                let title = card.querySelector('.judul-barang').innerText.toLowerCase();
                if (title.includes(input)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        //Preview Gambar saat user upload foto (di form)
        function previewImage(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('gambar-preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>