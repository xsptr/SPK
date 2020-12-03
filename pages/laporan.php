<div class="col-12">
        <div class="card-body">
            <table id="example1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php if ($query = $conn->query("SELECT b.nama AS beasiswa, a.nis, a.nilai, a.tahun, c.nama FROM hasil a JOIN beasiswa b USING(kd_beasiswa) JOIN siswa c ON a.nis=c.nis")): ?>
                        <?php while($row = $query->fetch_assoc()): ?>
                        <tr>
                            <td><?=$no++?></td>
                            <td><?=$row["nis"]?></td>
                            <td><?=$row["nama"]?></td>
                            <td><?=number_format((float) $row["nilai"], 2, '.', '')?></td>
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
<!-- /.col-md-12 -->