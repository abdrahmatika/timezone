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

<?php

include "config.php";

?>

<div class="container">

<div class="row">

<div class="col-md-offset-3 col-md-6">

<table class="table table-bordered">

<?php

// Looping TA
for ( $i = 2014; $i< date('Y'); $i++ ) { 

	// Looping Jenis semester (Ganjil/Genap)
	for( $ii = 1; $ii<=2; $ii++ ){

		$smster = $ii == 1 ? "GANJIL":"GENAP"; ?>

		<tr><th colspan="7"><?= $i."-".$ii ?></th></tr>
		<tr><td>Angkatan</td><td>AKTIF</td><td>NON-AKTIF</td><td>LULUS</td><td>DO</td><td>MUTASI</td><td>UNKNOWN</td><td>Total</td></tr>

<?php

		$tot_aktif		= 0;
		$tot_non_aktif	= 0;
		$tot_lulus		= 0;
		$tot_do			= 0;
		$tot_mutasi		= 0;

		$sub_total 		= 0;
		$tot_unknown 	= 0;

		// Looping angkatan
		for ( $a = 2011; $a<= date('Y'); $a++ ){

			$tot_mhs = $conn->query("SELECT COUNT(*) as jml FROM t_mhs WHERE LEFT(NIM,4)='$a'");

			// Looping status
			for ( $s = 1; $s<=5; $s++ ) {

				switch ( $s ) {
					case 1: $status = "AKTIF"; break;
					case 2: $status = "NON-AKTIF"; break;
					case 3: $status = "LULUS"; break;
					case 4: $status = "DO"; break;
					case 5: $status = "MUTASI"; break;
				}

				$query = "SELECT COUNT(*)as jml FROM `t_mhs_status` 
							WHERE LEFT(NIM,4)='$a' 
								AND TAHUN='$i' 
								AND SMSTER='$smster' 
								AND KET='$status'";
				$sql[$s] = $conn->query($query);
			}

			$aktif 		= $sql[1]->fetch_assoc();
			$non_aktif 	= $sql[2]->fetch_assoc();
			$lulus 		= $sql[3]->fetch_assoc();
			$do 		= $sql[4]->fetch_assoc();
			$mutasi 	= $sql[5]->fetch_assoc();

			$total 		= $tot_mhs->fetch_assoc(); 

			$tot_aktif		= $tot_aktif + $aktif['jml'];
			$tot_non_aktif	= $tot_non_aktif + $non_aktif['jml'];
			$tot_lulus		= $tot_lulus + $lulus['jml'];
			$tot_do			= $tot_do + $do['jml'];
			$tot_mutasi		= $tot_mutasi + $mutasi['jml'];

			$sub_total_status 	= $aktif['jml'] + $non_aktif['jml'] + $lulus['jml'] + $do['jml'] + $mutasi['jml'];
			
			$sub_total		= $sub_total + $total['jml'];

			$unknown = $total['jml'] - $sub_total_status;

			$tot_unknown = $tot_unknown + $unknown;
			?>

			<tr>
				<td><?= $a ?></td>
				<td><?= $aktif['jml'] ?></td>
				<td><?= $non_aktif['jml'] ?></td>
				<td><?= $lulus['jml'] ?></td>
				<td><?= $do['jml'] ?></td>
				<td><?= $mutasi['jml'] ?></td>
				<td><?= $unknown ?></td>
				<td><?= $total['jml'] ?></td>
			</tr>

		<?php } ?>

			<tr>
				<th>TOTAL</th>
				<th><?= $tot_aktif ?></th>
				<th><?= $tot_non_aktif ?></th>
				<th><?= $tot_lulus ?></th>
				<th><?= $tot_do ?></th>
				<th><?= $tot_mutasi ?></th>
				<th><?= $tot_unknown ?></th>
				<th><?= $sub_total ?></th>
			</tr>
	
	<?php } ?>

<?php } ?>

</table>
</div>
</div>
</div>
</body>
</html>