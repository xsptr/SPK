<?php

$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
    $sql = $conn->query("SELECT * FROM siswa WHERE nis='$_GET[key]'");
    $rows = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validasi = false; $err = false;
    if ($update) {
        $sql = "UPDATE siswa SET nis='$_POST[nis]', nama='$_POST[nama]', alamat='$_POST[alamat]', jenis_kelamin='$_POST[jenis_kelamin]', kelas='$_POST[kelas]' WHERE nis='$_GET[key]'";
    } else {
        $sql = "INSERT INTO siswa VALUES ('$_POST[nis]', '$_POST[nama]', '$_POST[alamat]', '$_POST[jenis_kelamin]', '$_POST[kelas]')";
        $validasi = true;
    }

    if ($validasi) {
        $q = $conn->query("SELECT nis FROM siswa WHERE nis=$_POST[nis]");
        if ($q->num_rows) {
            echo alert("warning", $_POST["nis"]." sudah terdaftar!" , "?pages=siswa");
            $err = true;
        }
    }

    if (!$err AND $conn->query($sql)) {
        echo alert("success", "Berhasil!", "?pages=siswa");
    } else {
        echo alert("error", "Gagal!", "?pages=siswa");
    }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
    $conn->query("DELETE FROM siswa WHERE nis='$_GET[key]'");
    echo alert("success", "Berhasil!", "?pages=siswa");
}

?>

<div class="col-12">
    <div class="row">
        <div class="col-md-4">
             <div id="form" class="card card-<?= ($update) ? "warning" : "info" ?>">
                <div class="card-header">
                    <div class="card-title"><?= ($update) ? "Edit Data" : "Tambah Data" ?></div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
                        <div class="form-group">
                            <label for="nis">NIS</label>
                            <input type="text" name="nis" class="form-control" <?= (!$update) ?: 'value="'.$rows["nis"].'"' ?>>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" <?= (!$update) ?: 'value="'.$rows["nama"].'"' ?>>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" class="form-control" <?= (!$update) ?: 'value="'.$rows["alamat"].'"' ?>>
                        </div>                
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select name="jenis_kelamin" id="" class="form-control">
                                    <option>----</option>
                                    <option value="Laki-Laki" <?= (!$update) ?: (($rows["jenis_kelamin"] != "Laki-laki") ?: 'selected="on"') ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= (!$update) ?: (($rows["jenis_kelamin"] != "Perempuan") ?: 'selected="on"') ?>>Perempuan</option>
                                </select>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <select name="kelas" id="" class="form-control">
                                <option>----</option>
                                <?php $sql=$conn->query("SELECT kelas FROM kelas") ?>
                                <?php while ($data = $sql->fetch_assoc()): ?>
                                <option value="<?=$data["kelas"]?>" <?= (!$update) ?: (($rows["kelas"] != $data["kelas"]) ?: 'selected="on"') ?>><?=$data["kelas"]?></option>
                            <?php endwhile; ?>
                            </select>
                        </div>
                        <!-- /.form-group -->
                        <div class="col-md-4">
                        <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
                        <?php if ($update): ?>
                            <a href="?pages=siswa" class="btn btn-info btn-block">Cancel</a>
                        <?php endif; ?>
                        </div>
                        <!-- /.col-md-3 -->
                    </form>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-4 -->

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Siswa</h2>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Alamat</th>
                                <th>Jenis Kelamin</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php if ($query = $conn->query("SELECT * FROM siswa")): ?>
                                <?php while($row = $query->fetch_assoc()): ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td><?=$row['nis']?></td>
                                    <td><?=$row['nama']?></td>
                                    <td><?=$row['kelas']?></td>
                                    <td><?=$row['alamat']?></td>
                                    <td><?=$row['jenis_kelamin']?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="?pages=siswa&action=update&key=<?=$row['nis']?>#form" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="?pages=siswa&action=delete&key=<?=$row['nis']?>" class="btn btn-danger btn-sm">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>

    

    
    </div>
    <!-- /.row -->
</div>
<!-- /.col -->