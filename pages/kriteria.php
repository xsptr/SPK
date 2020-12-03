<?php

$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
    $sql = $conn->query("SELECT * FROM kriteria WHERE kd_kriteria='$_GET[key]'");
    $rows = $sql->fetch_assoc();
}
$bot = $rows["bobot"]*100;
$bobot = $_POST["bobot"]/100;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validasi = false; $err = false;
    if ($update) {
        $sql = "UPDATE kriteria SET kd_beasiswa=1, nama='$_POST[nama]', sifat='$_POST[sifat]', bobot='$bobot' WHERE kd_kriteria='$_GET[key]'";
    } else {
        $sql = "INSERT INTO kriteria VALUES (NULL, '1', '$_POST[nama]', '$_POST[sifat]', '$bobot')";
        $validasi = true;
    }

    if ($validasi) {
        $q = $conn->query("SELECT kd_kriteria FROM kriteria WHERE kd_beasiswa=1 AND nama LIKE '%$_POST[nama]%'");
        if ($q->num_rows) {
            echo alert("warning", "Kriteria sudah ada!", "?pages=kriteria");
            $err = true;
        }
    }

    if (!$err AND $conn->query($sql)) {
        echo alert("success", "Berhasil!", "?pages=kriteria");
    } else {
        echo alert("error", "Gagal!", "?pages=kriteria");
    }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
    $conn->query("DELETE FROM kriteria WHERE kd_kriteria='$_GET[key]'");
    echo alert("success", "Berhasil!", "?pages=kriteria");
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
                        <!-- /.form-group -->
                        <div class="form-group">
                        <label for="nama">Nama Kriteria</label>
                        <input type="text" name="nama" class="form-control" <?= (!$update) ?: 'value="'.$rows["nama"].'"' ?>>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="bobot">Bobot</label>
                            <div class="input-group">
                                <input type="text" name="bobot" class="form-control" <?= (!$update) ?: 'value="'.$bot.'"' ?>>
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                </div>
                            </div>
                            <!-- /.input group -->
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="sifat">Sifat</label>
                            <select name="sifat" class="form-control">
                            <option value="max" <?= (!$update) ?: (($rows["sifat"] != "max") ?: ' selected="on"') ?>>Max</option>
                            </select>
                        </div>
                        <!-- /.form-group -->
                        <div class="col-md-4">
                        <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
                        <?php if ($update): ?>
                            <a href="?pages=kriteria" class="btn btn-info btn-block">Cancel</a>
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
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                <th>Bobot</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php if ($query = $conn->query("SELECT a.nama AS kriteria, b.nama AS beasiswa, a.kd_kriteria, a.bobot FROM kriteria a JOIN beasiswa b USING (kd_beasiswa)")): ?>
                                <?php while($row = $query->fetch_assoc()): ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td><?=$row['kriteria']?></td>
                                    <td><?=$row['bobot']*100?>%</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="?pages=kriteria&action=update&key=<?=$row['kd_kriteria']?>#form" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="?pages=kriteria&action=delete&key=<?=$row['kd_kriteria']?>" class="btn btn-danger btn-sm">Hapus</a>
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
        <!-- /.col-md-8 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.col -->