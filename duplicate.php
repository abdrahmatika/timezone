<?php

include "config.php";

function array_not_unique($raw_array) {
    $dupes = array();
    natcasesort($raw_array);
    reset($raw_array);

    $old_key   = NULL;
    $old_value = NULL;
    foreach ($raw_array as $key => $value) {
        if ($value === NULL) { continue; }
        if (strcasecmp($old_value, $value) === 0) {
            $dupes[$old_key] = $old_value;
            $dupes[$key]     = $value;
        }
        $old_value = $value;
        $old_key   = $key;
    }
    return $dupes;
}

if ( !isset( $_SESSION['jenis_duplicate']) ) {

	$_SESSION['jenis_duplicate'] = "NAMAMTK";
} else {
	$jenis = $_SESSION['jenis_duplicate'];
}

?>

<!DOCTYPE html>
<html lang='id'>

<head>
	<title>Data Mahasiswa</title>

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</head>

<body>

<div class="container">

	<div class="row">

		<div class="col-md-12">

			<h3><?= $_SESSION['jenis_duplicate'] == "NAMAMTK" ? "Data Matakuliah":"Data Kode Matakuliah ";?> Ganda Pada KRS <?= $_SESSION['filter_tahun'] ?></h3>

			<hr />

			<div class="col-md-2">Jenis Data Ganda</div>
			<div class="col-md-4">
				<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
					<option value="engine.php?f=jenis-duplikat&val=NAMAMTK&query=<?php echo $_GET['query'] ?>&smstr=<?php echo $_GET['smstr'] ?>" <?php echo isset($_SESSION['jenis_duplicate']) && $_SESSION['jenis_duplicate'] == "NAMAMTK" ? "selected":"";?>>Nama Matakuliah</option>
					<option value="engine.php?f=jenis-duplikat&val=KODEMTK&query=<?php echo $_GET['query'] ?>&smstr=<?php echo $_GET['smstr'] ?>" <?php echo isset($_SESSION['jenis_duplicate']) && $_SESSION['jenis_duplicate'] == "KODEMTK" ? "selected":"";?>>Kode Matakuliah</option>
				</select>
			</div>
			<br>
			<br>

			<?php if ( $_SESSION['jenis_duplicate'] == "NAMAMTK" ) { ?>

				<table class="table table-hover table-bordered">
					<tr>
						<th>NIM</th>
						<th>Kode Matakuliah</th>
						<th>Nama Matakuliah</th>
						<th>SKS</th>
						<th>Nilai</th>
						<th>Tahun</th>
						<th>Semester</th>
					</tr>

					<?php

					// $query = "SELECT NIM FROM `t_mhs` WHERE left(NIM,4) = '2013'";

					$query = $_GET['query'];

					$sql = $conn->query($query);

					$no = 1;

					while ( $r = $sql->fetch_assoc() ) {

						$sql2 = $conn->query("SELECT NIM,KODEMTK,NAMAMTK FROM `t_krs_sementara` WHERE NIM='$r[NIM]' AND NAMAMTK!=''");
						
						if ( $sql2->num_rows > 0 ) {

							$arr_data = array();

							while ( $r2 = $sql2->fetch_assoc() ) {

								$arr_data[trim($r2['KODEMTK'])] = trim($r2['NAMAMTK']);

							}

							$arr_data = array_not_unique($arr_data);
							// print_r($arr_data);
							
							$dpt = count( $arr_data );

							$i = 1;
							foreach ( $arr_data as $key=>$val ) {
								
								$query3 = "SELECT TAHUN,SMSTR,SKS,NIM,KODEMTK,NAMAMTK,HURUF FROM t_krs_sementara WHERE KODEMTK='$key' AND NIM='$r[NIM]' AND NAMAMTK!=''";
								$sql3 = $conn->query($query3);

								if ( $sql3->num_rows > 0 ) {

									$r3 = $sql3->fetch_assoc(); 

									if ( $i == 1 ) { ?>

										<tr>
											<td rowspan="<?= $dpt ?>"><?php echo $r3['NIM'] ?></td>
											<td><?php echo $r3['KODEMTK'] ?></td>
											<td><?php echo $r3['NAMAMTK'] ?></td>
											<td><?php echo $r3['SKS'] ?></td>
											<td><?php echo $r3['HURUF'] ?></td>
											<td><?php echo $r3['TAHUN'] ?></td>
											<td><?php echo $r3['SMSTR'] ?></td>
										</tr>

									<?php } else { ?>

										<tr>
											<td><?php echo $r3['KODEMTK'] ?></td>
											<td><?php echo $r3['NAMAMTK'] ?></td>
											<td><?php echo $r3['SKS'] ?></td>
											<td><?php echo $r3['HURUF'] ?></td>
											<td><?php echo $r3['TAHUN'] ?></td>
											<td><?php echo $r3['SMSTR'] ?></td>
										</tr>

									<?php } 

								}

								$i++;

							}

						}

						$no++;

					} ?>

				</table>

			<?php } else { ?>

				<table class="table table-hover table-bordered">
					<tr>
						<th>NIM</th>
						<th>Kode Matakuliah</th>
						<th>Nama Matakuliah</th>
						<th>SKS</th>
						<th>Nilai</th>
						<th>Tahun</th>
						<th>Semester</th>
					</tr>

					<?php

					// $query = "SELECT NIM FROM `t_mhs` WHERE left(NIM,4) = '2013'";

					$query = $_GET['query'];

					$sql = $conn->query($query);

					$no = 1;

					while ( $r = $sql->fetch_assoc() ) {

						$sql2 = $conn->query("SELECT NIM,KODEMTK,NAMAMTK FROM `t_krs_sementara` WHERE NIM='$r[NIM]' AND NAMAMTK!=''");
						
						if ( $sql2->num_rows > 0 ) {

							$arr_data = array();

							while ( $r2 = $sql2->fetch_assoc() ) {

								$arr_data[trim($r2['KODEMTK'])] = trim($r2['KODEMTK']);

							}

							$arr_data = array_not_unique($arr_data);
							$dpt = count($arr_data);

							$i = 1;
							foreach ( $arr_data as $key=>$val ) {
								
								$query3 = "SELECT TAHUN,SMSTR,SKS,NIM,KODEMTK,NAMAMTK,HURUF FROM t_krs_sementara WHERE KODEMTK='$key' AND NIM='$r[NIM]' AND NAMAMTK!=''";
								$sql3 = $conn->query($query3);

								if ( $sql3->num_rows > 0 ) {

									$r3 = $sql3->fetch_assoc(); 

									if ( $i == 1 ) { ?>

										<tr>
											<td rowspan="<?= $dpt ?>"><?php echo $r3['NIM'] ?></td>
											<td><?php echo $r3['KODEMTK'] ?></td>
											<td><?php echo $r3['NAMAMTK'] ?></td>
											<td><?php echo $r3['SKS'] ?></td>
											<td><?php echo $r3['HURUF'] ?></td>
											<td><?php echo $r3['TAHUN'] ?></td>
											<td><?php echo $r3['SMSTR'] ?></td>
										</tr>

									<?php } else { ?>

										<tr>
											<td><?php echo $r3['KODEMTK'] ?></td>
											<td><?php echo $r3['NAMAMTK'] ?></td>
											<td><?php echo $r3['SKS'] ?></td>
											<td><?php echo $r3['HURUF'] ?></td>
											<td><?php echo $r3['TAHUN'] ?></td>
											<td><?php echo $r3['SMSTR'] ?></td>
										</tr>

									<?php } 

								}

								$i++;
							}

						}

						$no++;

					} ?>

				</table>

			<?php } ?>



		</div>

	</div>

</div>

</body>
</html>