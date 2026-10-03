<?php
session_start();

// amanin biar ga gampang di akses/celah XSS (kode skripting)
function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

// helper, nampilin pesan error (klo ada)
function pesanError($errors, $kolom)
{
    if (isset($errors[$kolom])) 
        {
            echo '<p class="pesan-error" id="err-' . e($kolom) . '">* ' . e($errors[$kolom]) . '</p>';
        }
}

//daftar matkul 
$daftarMatkul = [
    'Pengembangan Aplikasi Website',
    'Pemrograman Berorientasi Objek',
    'Basis Data',
    'Pemrograman Mobile',
    'Algoritma dan Pemrograman',
];

$data = ['nama' => '', 'whatsapp' => '', 'email' => '', 'matkul' => '', 'motivasi' => '', ];
$errors = [];
$info = '';
$halaman = 'form';
$baruMendaftar = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        //ambil smua input, buang spasi dan pastiin string bentuknya
        foreach ($data as $kolom => $nilai)
            {
                $mentah = $_POST[$kolom] ?? '';
                $data[$kolom] = is_string($mentah) ? trim($mentah) : '';
            }
        
            $aksi = $_POST['aksi'] ?? 'daftar';

            if ($aksi === 'lihat')
                {
                    // bagian tombol lihat data yg daftar
                    if (isset($_SESSION['pendaftar']))
                        {
                            $halaman = 'kartu';
                        }
                        else
                            {
                              $info = 'Belum ada data pendaftar.';  
                            }
                }
                elseif ($aksi === 'kembali')
                    {
                        //bagian tombol balik ke form
                         $halaman = 'form';
                    }
                    else 
                        {
                            // bagian tombol "daftar sekarang", validasi sisi servernya

                            // bagian nama, gabisa kosong (huruf & spasi only)
                            if ($data['nama'] === '')
                                {
                                    $errors['nama'] = 'Nama lengkap tidak boleh kosong';
                                }
                                elseif (!preg_match('/^[\p{L}\s]+$/u', $data['nama']))
                                    {
                                        $errors['nama'] = 'Nama hanya boleh berupa huruf';
                                    }
                            
                            // bagian whatsapp, awalin 0 atau 62
                            if ($data['whatsapp'] === '')
                                {
                                    $errors['whatsapp'] = 'Nomor WhatsApp tidak boleh kosong';
                                }   
                                elseif (!preg_match('/^(0|62)/', $data['whatsapp']))
                                    {
                                        $errors['whatsapp'] = 'Nomor WhatsApp harus diawali 0 atau 62';
                                    }
                
                            //bagian email, formatnya hrs kayak email pada umumnya
                            if ($data['email'] === '')
                                {
                                   $errors['email'] = 'Email institusi tidak boleh kosong'; 
                                }
                                elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
                                    {
                                        $errors['email'] = 'Format email institusi tidak valid';
                                    }
                            
                            //bagian matkul, wajib pilih yg ada d daftar
                            if (!in_array($data['matkul'], $daftarMatkul, true))
                                {
                                    $errors['matkul'] = 'Pilihan mata kuliah praktikum wajib dipilih';
                                }

                            //bagian motivasi, harus diisi
                            if ($data['motivasi'] === '')
                                {
                                    $errors['motivasi'] = 'Motivasi mendaftar tidak boleh kosong';
                                }

                            //klo smua lolos, simpen ke session trs tampilin card regist
                            if (empty($errors))
                                {
                                    $_SESSION['pendaftar'] = $data + [
                                        'no_registrasi' => date('md') . '-' . random_int(1000000, 9999999),
                                    ];
                                    $halaman = 'kartu';
                                    $baruMendaftar = true;
                                }
                        }

                
    }
?>

<!-- BAGIAN HTML NYA -->
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $halaman === 'kartu' ? 'Kartu Registrasi' : 'Pendaftaran Asisten Praktikum' ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="hiasanhalamanasprak1.css">
    </head>

    <body>
        <main class="card">
            <svg class="logo" width="44" height="32" viewBox="0 0 44 32" aria-hidden="true">
                <path d="M14 2 L2 16 L14 30 L20 24 L12 16 L20 8 Z" fill="#2563eb"/>
                <path d="M30 2 L42 16 L30 30 L24 24 L32 16 L24 8 Z" fill="#dc2626"/>
            </svg>

<?php if ($halaman === 'form'): ?>
    <h1>Pendaftaran Asisten Praktikum</h1>
    <p class="subjudul">Laboratorium Enterprise Application Development</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-gagal" role="alert">
            <strong>Pendaftaran gagal!</strong> Harap perbaiki data yang salah
        </div>
    <?php endif; ?>

    <?php if ($info !== ''): ?>
        <div class="alert alert-info" role="status"><?= e($info) ?></div>
    <?php endif; ?>

    <form method="post">
       <div class="field">
            <label for="nama">Nama Lengkap <span class="wajib">*</span></label>
            <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso"
                value="<?= e($data['nama']) ?>"
                class="<?= isset($errors['nama']) ? 'invalid' : '' ?>"
                <?= isset($errors['nama']) ? 'aria-invalid="true" aria-describedby="err-nama"' : '' ?>>

            <?php pesanError($errors, 'nama'); ?>
        </div>

        <div class="field">
            <label for="whatsapp">Nomor WhatsApp <span class="wajib">*</span></label>
            <input type="text" id="whatsapp" name="whatsapp" placeholder="Contoh: 081234567890"
                value="<?= e($data['whatsapp']) ?>"
                class="<?= isset($errors['whatsapp']) ? 'invalid' : '' ?>"
                <?= isset($errors['whatsapp']) ? 'aria-invalid="true" aria-describedby="err-whatsapp"' : '' ?>>
            
             <?php pesanError($errors, 'whatsapp'); ?>
        </div>
        
        <div class="field">
            <label for="email">Email Institusi <span class="wajib">*</span></label>
            <input type="email" id="email" name="email" placeholder="Contoh: budi@university.ac.id"

                value="<?= e($data['email']) ?>"
                class="<?= isset($errors['email']) ? 'invalid' : '' ?>"
                <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="err-email"' : '' ?>>
             <?php pesanError($errors, 'email'); ?>
        </div>

        <div class="field">
            <label for="matkul">Pilihan Mata Kuliah Praktikum <span class="wajib">*</span></label>
            <select id="matkul" name="matkul"
                class="<?= isset($errors['matkul']) ? 'invalid' : '' ?>"
                <?= isset($errors['matkul']) ? 'aria-invalid="true" aria-describedby="err-matkul"' : '' ?>>

                <option value="">--- Pilih Mata Kuliah ---</option>
                <?php foreach ($daftarMatkul as $matkul): ?>
                    <option value="<?= e($matkul) ?>" <?= $data['matkul'] === $matkul ? 'selected' : '' ?>>
                        <?= e($matkul) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php pesanError($errors, 'matkul'); ?>
        </div>

        <div class="field">
            <label for="motivasi">Motivasi Mendaftar <span class="wajib">*</span></label>
            <textarea id="motivasi" name="motivasi" placeholder="Tuliskan alasan kamu ingin menjadi asisten praktikum..."
                    class="<?= isset($errors['motivasi']) ? 'invalid' : '' ?>"
                    <?= isset($errors['motivasi']) ? 'aria-invalid="true" aria-describedby="err-motivasi"' : '' ?>><?= e($data['motivasi']) ?></textarea>
            <?php pesanError($errors, 'motivasi'); ?>
        </div>

        <button type="submit" name="aksi" value="daftar" class="btn btn-utama">Daftar Sekarang</button>
        <button type="submit" name="aksi" value="lihat" class="btn btn-garis" formnovalidate>Lihat Data Pendaftar</button>
    </form>

<?php else: ?>
    <?php $p = $_SESSION['pendaftar']; ?>
    <h1>Kartu Registrasi</h1>
    <p class="subjudul">Calon Asisten Praktikum Laboratorium</p>

    <?php if ($baruMendaftar): ?>
        <div class="alert alert-sukses" role="status">
            <strong>Berhasil!</strong> Data pendaftaran telah diterima.
        </div>
    <?php endif; ?>

    <dl class="kartu-data">
        <div class="baris"><dt>Nama Lengkap</dt><dd><?= e($p['nama']) ?></dd></div>
        <div class="baris"><dt>No. WhatsApp</dt><dd><?= e($p['whatsapp']) ?></dd></div>
        <div class="baris"><dt>Email Institusi</dt><dd><?= e($p['email']) ?></dd></div>
        <div class="baris"><dt>Mata Kuliah</dt><dd><?= e($p['matkul']) ?></dd></div>
        <div class="baris"><dt>Motivasi</dt><dd><?= nl2br(e($p['motivasi'])) ?></dd></div>
    </dl>

    <div class="badge-wrap"><span class="badge">Pendaftaran Berhasil</span></div>

    <form method="post">
        <button type="submit" name="aksi" value="kembali" class="btn btn-utama">Kembali ke Form</button>
    </form>

    <p class="catatan">Nomor Registrasi: <?= e($p['no_registrasi']) ?> &bull; Dicetak otomatis oleh sistem</p>
<?php endif; ?>
    </main>
    </body>
</html>