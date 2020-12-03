<?php

$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
    $sql = $conn->query("SELECT * FROM penilaian WHERE kd_penilaian='$_GET[key]'");
    $rows = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validasi = false; $err = false;
    if ($update) {
        $sql = "UPDATE penilaian SET kd_kriteria='$_POST[kd_kriteria]', keterangan='$_POST[keterangan]', bobot='$_POST[bobot]' WHERE kd_penilaian='$_GET[key]'";
    } else {
        $sql = "INSERT INTO penilaian VALUES (NULL, '$_POST[kd_beasiswa]', '$_POST[kd_kriteria]', '$_POST[keterangan]', '$_POST[bobot]')";
        $validasi = true;
    }

    if ($validasi) {
        $q = $conn->query("SELECT kd_penilaian FROM penilaian WHERE kd_beasiswa=$_POST[kd_beasiswa] AND kd_kriteria=$_POST[kd_kriteria] AND keterangan LIKE '%$_POST[keterangan]%' AND bobot=$_POST[bobot]");
        if ($q->num_rows) {
            echo alert("warning", "Penilaian sudah ada!" , "?pages=alternatif");
            $err = true;
        }
    }

    if (!$err AND $conn->query($sql)) {
        echo alert("success", "Berhasil!", "?pages=alternatif");
    } else {
        echo alert("error", "Gagal!", "?pages=alternatif");
    }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
    $conn->query("DELETE FROM penilaian WHERE kd_penilaian='$_GET[key]'");
    echo alert("success", "Berhasil!", "?pages=alternatif");
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
                            <label for="kd_beasiswa">Beasiswa</label>
                            <select name="kd_beasiswa" class="form-control" id="beasiswa" >
                                <?php $sql = $conn->query("SELECT * FROM beasiswa") ?>
                                <?php while ($data = $sql->fetch_assoc()): ?>
                                    <option value="<?=$data["kd_beasiswa"]?>" <?= (!$update) ?: (($rows["kd_beasiswa"] != $data["kd_beasiswa"]) ?: 'selected="selected"') ?>><?=$data["nama"]?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kd_kriteria">Kriteria</label>
                            <select name="kd_kriteria" class="form-control" id="kriteria">
                                <option>----</option>
                                <?php $sql = $conn->query("SELECT * FROM kriteria") ?>
                                <?php while ($data = $sql->fetch_assoc()): ?>
                                    <option value="<?=$data["kd_kriteria"]?>" class="<?=$data["kd_beasiswa"]?>" <?= (!$update) ?: (($rows["kd_kritera"] != $data["kd_kriteria"]) ?: 'selected="selected"') ?>><?=$data["nama"]?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" <?= (!$update) ?: 'value="'.$rows["keterangan"].'"' ?>>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="bobot">Bobot</label>
                            <input type="text" name="bobot" class="form-control" <?= (!$update) ?: 'value="'.$rows["bobot"].'"' ?>>
                        </div>
                        <!-- /.form-group -->
                        <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
                        <?php if ($update): ?>
                            <a href="?pages=alternatif" class="btn btn-info btn-block">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-4 -->
    

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kriteria Tambahan</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kriteria</th>
                                <th>Keterangan</th>
                                <th>Bobot</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php if ($query = $conn->query("SELECT a.kd_penilaian, c.nama AS nama_beasiswa, b.nama AS nama_kriteria, a.keterangan, a.bobot FROM penilaian a JOIN kriteria b ON a.kd_kriteria=b.kd_kriteria JOIN beasiswa c ON a.kd_beasiswa=c.kd_beasiswa")): ?>
                                <?php while($row = $query->fetch_assoc()): ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td><?=$row['nama_kriteria']?></td>
                                    <td><?=$row['keterangan']?></td>
                                    <td><?=$row['bobot']?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="?pages=alternatif&action=update&key=<?=$row['kd_penilaian']?>#form" class="btn btn-warning">Edit</a>
                                            <a href="?pages=alternatif&action=delete&key=<?=$row['kd_penilaian']?>" class="btn btn-danger">Hapus</a>
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

<script type="text/javascript">
    $("#kriteria").chained("#beasiswa");
</script>