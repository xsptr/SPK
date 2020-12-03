<div class="col-12">

    <?php
    // Query Perhitungan
    if (isset($_GET["beasiswa"])) {
        $sqlkriteria = "";
        $namakriteria = [];
        $querykriteria = $conn->query("SELECT kd_kriteria, nama FROM kriteria WHERE kd_beasiswa=$_GET[beasiswa]");
        while ($kr = $querykriteria->fetch_assoc()) {
            $sqlkriteria .= "SUM(
                IF(
                    c.kd_kriteria=".$kr["kd_kriteria"].", nilai.nilai/c.normalization, 0
                )
            ) AS ".strtolower(str_replace(" ", "_", $kr["nama"])). ",";
            $namakriteria[] = strtolower(str_replace(" ", "_", $kr["nama"]));
        }
        // Rangking
        $sql = "SELECT
                    (SELECT nama FROM siswa WHERE nis=swa.nis) AS nama,
                    (SELECT nis FROM siswa WHERE nis=swa.nis) AS nis,
                    (SELECT kelas FROM siswa WHERE nis=swa.nis) AS kelas,
                    SUM(
                        nilai.nilai / c.normalization * c.bobot
                    ) AS rangking
                FROM
                    nilai
                    JOIN siswa swa USING(nis)
                    JOIN (
                        -- Normalisasi
                        SELECT
                            nilai.kd_kriteria AS kd_kriteria,
                            (
                                SELECT bobot FROM kriteria WHERE kd_kriteria=nilai.kd_kriteria AND kd_beasiswa=beasiswa.kd_beasiswa
                            ) AS bobot,
                            (
                                SELECT MAX(nilai) FROM nilai
                            ) AS normalization
                        FROM nilai
                        JOIN kriteria USING(kd_kriteria)
                        JOIN beasiswa ON kriteria.kd_beasiswa=beasiswa.kd_beasiswa
                        WHERE beasiswa.kd_beasiswa=$_GET[beasiswa]
                        GROUP BY nilai.kd_kriteria
                    ) c USING(kd_kriteria)
                WHERE kd_beasiswa=$_GET[beasiswa]
                GROUP BY nilai.nis
                ORDER BY rangking DESC"; ?>
    <div class="card">
        <div class="card-body">
            <table id="example1" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Nilai Akhir</th>
                </tr>
                </thead>
                <tbody>
                    <?php $query = $conn->query($sql); while ($row = $query->fetch_assoc()): ?>
                    <?php
                    $rangking = number_format((float) $row["rangking"], 3, '.', '');
                    ?>
                    <tr>
                        <td><?=$row["nis"]?></td>
                        <td><?=$row["nama"]?></td>
                        <td><?=$row["kelas"]?></td>
                        <?php for($i=0; $i<count($namakriteria); $i++): ?>
                        <?php endfor ?>
                        <td><?=$rangking?></td>
                    </tr>
                        <?php endwhile;?>
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
                    <?php } else { ?>
                    <h1>Beasiswa belum dipilih...</h1>
                    <?php } ?>
</div>