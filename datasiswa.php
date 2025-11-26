<?php include "connect.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Absensi Kelas</title>
    <link rel="stylesheet" href="css/style.css">

<style>
.content-wrapper { padding: 20px; }
.title { font-size: 24px; font-weight: bold; margin-bottom: 15px; }

.btn-primary {
    background: #005bbb; padding: 10px 18px; color: white;
    border-radius: 6px; border: none; cursor: pointer;
}
.btn-secondary {
    background: #636e72; padding: 6px 12px; color: white;
    border-radius: 6px; border: none; cursor: pointer;
}
.btn-danger {
    background: #d63031; padding: 6px 12px; color: white;
    border-radius: 6px; border: none; cursor: pointer;
}

table {
    width: 100%; background: white; border-collapse: collapse;
    margin-top: 20px; border-radius: 10px; overflow: hidden;
}
th { background: #005bbb; color: white; padding: 12px; }
td { padding: 10px; border-bottom: 1px solid #ddd; }

#popupBg {
    display: none; position: fixed; z-index: 999; background: rgba(0,0,0,0.4);
    width: 100%; height: 100%; top: 0; left: 0;
}

.form-popup {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: white; width: 380px; padding: 25px;
    border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.2);
}

.form-popup input {
    width: 100%; padding: 12px; margin-bottom: 12px;
    border: 1px solid #ccc; border-radius: 6px;
}
</style>
</head>

<body>

<div class="dashboard-container">

<header class="dashboard-header">
    <div class="header-left">
        <img src="assets/absensi.png" alt="Logo Absensi" class="logo">
        <h1>Absensi Kelas</h1>
    </div>
    <div class="header-right">
        <span id="greeting" class="greeting"></span>
        <button id="logoutBtn" class="btn-secondary">Keluar</button>
    </div>
</header>

<nav class="dashboard-nav">
    <ul>
        <li><a href="dashboard.html" class="nav-link">Dashboard</a></li>
        <li><a href="absen.html" class="nav-link">Absensi Harian</a></li>
        <li><a href="rekap.html" class="nav-link">Rekap Absen</a></li>
        <li><a href="datasiswa.php" class="nav-link active">Data Siswa</a></li>
        <li><a href="datakelas.php" class="nav-link">Data Kelas</a></li>
        <li class="dropdown">
            <a href="#" class="nav-link">Laporan</a>
            <div class="dropdown-content">
                <a href="laporan_harian.html">Laporan Harian</a>
                <a href="laporan_bulanan.html">Laporan Bulanan</a>
            </div>
        </li>
        <li><a href="histori.html" class="nav-link">Histori Absensi</a></li>
    </ul>
</nav>

<main class="content-wrapper">

    <div class="title">Data Siswa</div>

    <!-- PILIH KELAS -->
    <select id="pilihKelas" onchange="loadSiswa()" 
        style="padding:8px; border-radius:6px; margin-bottom:15px;">
        <option value="">-- Pilih Kelas --</option>
        <option value="XI PPLG 1">XI PPLG 1</option>
        <option value="XI PPLG 2">XI PPLG 2</option>
        <option value="XI PPLG 3">XI PPLG 3</option>
    </select>

    <button class="btn-primary" onclick="openForm()">+ Tambah Siswa</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Siswa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tbodySiswa"></tbody>
    </table>

</main>

</div>

<!-- POPUP -->
<div id="popupBg">
    <div class="form-popup">
        <h3 style="margin-bottom:15px;">Tambah Siswa Baru</h3>
        <input type="text" id="namaBaru" placeholder="Nama siswa...">
        <button class="btn-primary" onclick="tambahSiswa()">Simpan</button>
        <button class="btn-secondary" onclick="closeForm()">Batal</button>
    </div>
</div>


<script>
function loadSiswa() {
    let kelas = document.getElementById("pilihKelas").value;

    fetch("datasiswa.php?getData=1&kelas=" + kelas)
    .then(res => res.json())
    .then(data => {
        let tbody = document.getElementById("tbodySiswa");
        tbody.innerHTML = "";

        data.forEach(s => {
            tbody.innerHTML += `
                <tr>
                    <td>${s.id}</td>
                    <td>${s.Nama}</td>
                    <td>
                        <button class="btn-danger" onclick="hapusSiswa(${s.id})">Hapus</button>
                    </td>
                </tr>
            `;
        });
    });
}

function tambahSiswa() {
    let nama = document.getElementById("namaBaru").value;
    let kelas = document.getElementById("pilihKelas").value;

    fetch("tambah-siswa.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `nama=${nama}&kelas=${kelas}`
    })
    .then(res => res.text())
    .then(res => {
        if (res === "success") {
            closeForm();
            loadSiswa();
        } else {
            alert("Gagal menambah siswa");
        }
    });
}

function hapusSiswa(id) {
    if (!confirm("Hapus siswa ini?")) return;

    fetch("hapus-siswa.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${id}`
    })
    .then(res => res.text())
    .then(res => {
        if (res === "success") loadSiswa();
        else alert("Gagal menghapus");
    });
}

function openForm() { document.getElementById("popupBg").style.display = "block"; }
function closeForm() { document.getElementById("popupBg").style.display = "none"; }

loadSiswa();
</script>

</body>
</html>

<?php
// =======================================
//      BAGIAN BACKEND (JSON RESPONSE)
// =======================================
if (isset($_GET['getData'])) {

    $kelas = isset($_GET['kelas']) ? $_GET['kelas'] : "";

    if ($kelas != "") {
        $sql = "SELECT id, Nama FROM siswa WHERE Kelas='$kelas' ORDER BY Nama ASC";
    } else {
        $sql = "SELECT id, Nama FROM siswa ORDER BY Nama ASC";
    }

    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}
?>
