<?php
session_start();

// Nama  : Nadrah Atha Fannani
// NIM   : 102042500052
// Kelas : 25-01

$daftar_matkul = [
    "Algoritma dan Pemrograman",
    "Analisis dan Perancangan Sistem Informasi",
    "Arsitektur Enterprise",
    "Data Warehouse dan Business Intelligence",
    "Komputasi Awan",
    "Pemodelan Proses Bisnis",
    "Pengantar Sistem Informasi",
    "Pengembangan Aplikasi Bergerak",
    "Pengembangan Aplikasi Website",
    "Pengembangan UI Lanjut",
    "Proyek Perangkat Lunak",
    "Sistem Enterprise",
    "Sistem Informasi Akuntansi",
    "Sistem Operasi"
];

$nama = "";
$whatsapp = "";
$email = "";
$matkul = "";
$motivasi = "";

$namaErr = "";
$waErr = "";
$emailErr = "";
$matkulErr = "";
$motivasiErr = "";

$mode = "form";

if (isset($_GET['page']) && $_GET['page'] === 'id_card') {
    if (!empty($_SESSION['data_pendaftar'])) {
        $nama = $_SESSION['data_pendaftar']['nama'];
        $whatsapp = $_SESSION['data_pendaftar']['whatsapp'];
        $email = $_SESSION['data_pendaftar']['email'];
        $matkul = $_SESSION['data_pendaftar']['matkul'];
        $motivasi = $_SESSION['data_pendaftar']['motivasi'];
        $mode = "id_card";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nama lengkap
    $nama = trim($_POST["nama_lengkap"] ?? "");
    if ($nama === "") {
        $namaErr = "Nama lengkap tidak boleh kosong";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $nama)) {
        $namaErr = "Nama hanya boleh berupa huruf";
    }

    // Nomor WhatsApp
    $whatsapp = trim($_POST["no_whatsapp"] ?? "");
    if ($whatsapp === "") {
        $waErr = "Nomor WhatsApp tidak boleh kosong";
    } elseif (substr($whatsapp, 0, 1) !== "0" && substr($whatsapp, 0, 2) !== "62") {
        $waErr = "Nomor WhatsApp harus diawali 0 atau 62";
    }

    // Email institusi
    $email = trim($_POST["email_institusi"] ?? "");
    if ($email === "") {
        $emailErr = "Email institusi tidak boleh kosong";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Format email tidak valid";
    }

    // Mata kuliah
    $matkul = $_POST["pilihan_matkul"] ?? "";
    if ($matkul === "") {
        $matkulErr = "Pilihan mata kuliah praktikum wajib dipilih";
    }

    // Motivasi
    $motivasi = trim($_POST["motivasi"] ?? "");
    if ($motivasi === "") {
        $motivasiErr = "Motivasi mendaftar tidak boleh kosong";
    }

    // Simpan data jika semua valid
    if (empty($namaErr) && empty($waErr) && empty($emailErr) && empty($matkulErr) && empty($motivasiErr)) {
        $_SESSION['data_pendaftar'] = [
            'nama' => $nama,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'matkul' => $matkul,
            'motivasi' => $motivasi
        ];
        $mode = "id_card";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Calon Asisten Praktikum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="hiasanhalamanasprak1.css">
</head>

<body>

    <?php if ($mode === "id_card") { ?>

        <!-- ==================== MODE ID CARD ==================== -->
        <div class="id-card">
            <img src="logo.png" alt="Logo" class="logo">
            <div class="id-card-header">
                <h2>Kartu Registrasi</h2>
                <p>Calon Asisten Praktikum Laboratorium</p>
            </div>

            <div class="alert alert-success">
                <strong>Berhasil!</strong> Data pendaftaran telah diterima.
            </div>

            <div class="id-card-body">
                <div class="id-card-row">
                    <span class="id-card-label">Nama Lengkap</span>
                    <span class="id-card-value"><?php echo htmlspecialchars($nama); ?></span>
                </div>

                <div class="id-card-row">
                    <span class="id-card-label">No. WhatsApp</span>
                    <span class="id-card-value"><?php echo htmlspecialchars($whatsapp); ?></span>
                </div>

                <hr class="id-card-divider">

                <div class="id-card-row">
                    <span class="id-card-label">Email Institusi</span>
                    <span class="id-card-value"><?php echo htmlspecialchars($email); ?></span>
                </div>

                <div class="id-card-row">
                    <span class="id-card-label">Mata Kuliah</span>
                    <span class="id-card-value"><?php echo htmlspecialchars($matkul); ?></span>
                </div>

                <hr class="id-card-divider">

                <div class="id-card-row">
                    <span class="id-card-label">Motivasi</span>
                    <span class="id-card-value"><?php echo nl2br(htmlspecialchars($motivasi)); ?></span>
                </div>

                <div style="text-align: center; margin-top: 18px;">
                    <span class="id-card-badge">Pendaftaran Berhasil</span>
                </div>
            </div>

            <a href="?page=form" class="btn-kembali">Kembali ke Form</a>

            <div class="id-card-footer">
                Nomor Registrasi: REG-<?php echo strtoupper(substr(md5(time()), 0, 8)); ?> &bull; Dicetak otomatis oleh
                sistem
            </div>
        </div>

    <?php } else { ?>

        <!-- ==================== MODE FORM ==================== -->
        <div class="container">
            <img src="logo.png" alt="Logo" class="logo">
            <h2>Pendaftaran Asisten Praktikum</h2>
            <p class="subtitle">Laboratorium Enterprise Application Development</p>

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && (!empty($namaErr) || !empty($waErr) || !empty($emailErr) || !empty($matkulErr) || !empty($motivasiErr))) { ?>
                <div class="alert alert-danger">
                    <strong>Pendaftaran gagal!</strong> Harap perbaiki data yang salah.
                </div>
            <?php } ?>

            <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama_lengkap" placeholder="Contoh: Budi Santoso"
                        value="<?php echo htmlspecialchars($nama); ?>">
                    <span class="error"><?php echo $namaErr ? "* $namaErr" : ""; ?></span>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp <span class="required">*</span></label>
                    <input type="number" name="no_whatsapp" placeholder="Contoh: 081234567890"
                        value="<?php echo htmlspecialchars($whatsapp); ?>">
                    <span class="error"><?php echo $waErr ? "* $waErr" : ""; ?></span>
                </div>

                <div class="form-group">
                    <label>Email Institusi <span class="required">*</span></label>
                    <input type="email" name="email_institusi" placeholder="Contoh: budi@university.ac.id"
                        value="<?php echo htmlspecialchars($email); ?>">
                    <span class="error"><?php echo $emailErr ? "* $emailErr" : ""; ?></span>
                </div>

                <div class="form-group">
                    <label>Pilihan Mata Kuliah Praktikum <span class="required">*</span></label>
                    <select name="pilihan_matkul">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php foreach ($daftar_matkul as $mk) { ?>
                            <option value="<?php echo $mk; ?>" <?php echo ($matkul == $mk) ? 'selected' : ''; ?>>
                                <?php echo $mk; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span class="error"><?php echo $matkulErr ? "* $matkulErr" : ""; ?></span>
                </div>

                <div class="form-group">
                    <label>Motivasi Mendaftar <span class="required">*</span></label>
                    <textarea name="motivasi"
                        placeholder="Tuliskan alasan kamu ingin menjadi asisten praktikum..."><?php echo htmlspecialchars($motivasi); ?></textarea>
                    <span class="error"><?php echo $motivasiErr ? "* $motivasiErr" : ""; ?></span>
                </div>

                <div class="button-container">
                    <button type="submit">Daftar Sekarang</button>
                    <?php if (!empty($_SESSION['data_pendaftar'])) { ?>
                        <a href="?page=id_card" class="btn-lihat-data">Lihat Data Pendaftar</a>
                    <?php } ?>
                </div>
            </form>
        </div>

    <?php } ?>

</body>

</html>