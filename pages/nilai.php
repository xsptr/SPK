<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
    $sql = $conn->query("SELECT * FROM nilai JOIN penilaian USING(kd_kriteria) WHERE kd_nilai='$_GET[key]'");
    $row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_POST["save"])) {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE nilai SET kd_kriteria='$_POST[kd_kriteria]', nis='$_POST[nis]', nilai='$_POST[nilai]' WHERE kd_nilai='$_GET[key]'";
	} else {
		$query = "INSERT INTO nilai VALUES ";
		foreach ($_POST["nilai"] as $kd_kriteria => $nilai) {
			$query .= "(NULL, '$_POST[kd_beasiswa]', '$kd_kriteria', '$_POST[nis]', '$nilai'),";
		}
		$sql = rtrim($query, ',');
		$validasi = true;
	}

	if ($validasi) {
		foreach ($_POST["nilai"] as $kd_kriteria => $nilai) {
			$q = $conn->query("SELECT kd_nilai FROM nilai WHERE kd_beasiswa=$_POST[kd_beasiswa] AND kd_kriteria=$kd_kriteria AND nis=$_POST[nis] AND nilai LIKE '%$nilai%'");
			if ($q->num_rows) {
				echo alert("warning", "Nilai untuk ".$_POST["nis"]." sudah ada!", "?pages=nilai");
				$err = true;
			}
		}
	}

  if (!$err AND $conn->query($sql)) {
		echo alert("success", "Berhasil!", "?pages=nilai");
	} else {
		echo alert("error", "Gagal!", "?pages=nilai");
	}
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
    $conn->query("DELETE FROM nilai WHERE kd_nilai='$_GET[key]'");
      echo alert("success", "Berhasil!", "?pages=nilai");
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
                            <label for="nis">Siswa</label>
                            <?php if ($_POST): ?>
                                <input type="text" name="nis" value="<?=$_POST["nis"]?>" class="form-control" readonly="on">
                            <?php else: ?>
                                <select name="nis" class="form-control">
                                    <option>---</option>
                                    <?php $sql = $conn->query("SELECT * FROM siswa");while ($data = $sql->fetch_assoc()): ?>
                                        <option value="<?=$data["nis"]?>" <?= (!$update) ? "" : (($row["nis"] != $data["nis"]) ? "" : 'selected="selected"') ?>><?=$data["nama"]?></option> | <?=$data["nama"]?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label for="kd_beasiswa">Beasiswa</label>
                                <?php if ($_POST): ?>
                                    <?php $q = $conn->query("SELECT nama FROM beasiswa WHERE kd_beasiswa=$_POST[kd_beasiswa]"); ?>
                                    <input type="text" value="<?=$q->fetch_assoc()["nama"]?>" class="form-control" readonly="on">
                                    <input type="hidden" name="kd_beasiswa" value="<?=$_POST["kd_beasiswa"]?>">
                                <?php else: ?>
                                <select name="kd_beasiswa" id="beasiswa" class="form-control">
                                    <?php $sql = $conn->query("SELECT * FROM beasiswa");while ($data = $sql->fetch_assoc()): ?>
                                        <option value="<?=$data["kd_beasiswa"]?>"<?= (!$update) ? "" : (($row["kd_beasiswa"] != $data["kd_beasiswa"]) ? "" : 'selected="selected"') ?>><?=$data["nama"]?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <!-- /.form-group -->
                            <?php if ($_POST): ?>
                            <?php $q = $conn->query("SELECT * FROM kriteria WHERE kd_beasiswa=$_POST[kd_beasiswa]"); while ($r = $q->fetch_assoc()): ?>
                        <div class="form-group">
                            <label for="nilai"><?=ucfirst($r["nama"])?></label>
                            <select class="form-control" name="nilai[<?=$r["kd_kriteria"]?>]" id="nilai">
                                    <option>---</option>
                                    <?php $sql = $conn->query("SELECT * FROM penilaian WHERE kd_kriteria=$r[kd_kriteria]"); while ($data = $sql->fetch_assoc()): ?>
                                        <option value="<?=$data["bobot"]?>" class="<?=$data["kd_kriteria"]?>"<?= (!$update) ? "" : (($row["kd_penilaian"] != $data["kd_penilaian"]) ? "" : ' selected="selected"') ?>><?=$data["keterangan"]?></option>
                                    <?php endwhile; ?>
                                </select>              
                        </div>
                        <!-- /.form-group -->
                        <?php endwhile; ?>
                            <input type="hidden" name="save" value="true">
                    <?php endif; ?>
                    <button type="submit" id="simpan" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block"><?=($_POST) ? "Simpan" : "Tampilkan"?></button>
                        <?php if ($update): ?>
                            <a href="?pages=nilai" class="btn btn-info btn-block">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-5 -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Data Pendukung</h2>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kriteria</th>
                                <th>Nilai</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php if ($query = $conn->query("SELECT a.kd_nilai, b.nama AS nama_kriteria, d.nis, d.nama AS nama_siswa, a.nilai FROM nilai a JOIN kriteria b ON a.kd_kriteria=b.kd_kriteria JOIN beasiswa c ON a.kd_beasiswa=c.kd_beasiswa JOIN siswa d ON d.nis=a.nis ORDER BY nama_siswa ASC")): ?>
                                <?php while($row = $query->fetch_assoc()): ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td><?=$row['nis']?></td>
                                    <td><?=$row['nama_siswa']?></td>
                                    <td><?=$row['nama_kriteria']?></td>
                                    <td><?=$row['nilai']?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="?pages=nilai&action=update&key=<?=$row['kd_nilai']?>" class="btn btn-warning">Edit</a>
                                            <a href="?pages=nilai&action=delete&key=<?=$row['kd_nilai']?>" class="btn btn-danger ">Hapus</a>
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
        <!-- /.col-md-7 -->
    </div>
</div>

<script type="text/javascript">
    $("#kriteria").chained("#beasiswa");
    $("#nilai").chained("$kriteria");
</script>