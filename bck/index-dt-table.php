<?php

ob_start();

include "config.php";

error_reporting(0);

$start = Mulai_tes();
// Filtering


if (!isset( $_SESSION['filter_semester'] ) ) {
	$_SESSION['filter_semester'] = "GENAP";
}

if ( isset($_SESSION['filter']) ) {

	if ( isset($_SESSION['filter_tahun']) ) {

		$filter_thn = " AND ms.TAHUN='$_SESSION[filter_tahun]'";

	} else { $filter_thn = ""; }

	if ( isset($_SESSION['filter_prodi']) ) {

		if ( $_SESSION['filter_prodi'] == "S1-22" ) {
			$filter_prodi = " AND MID(m.NIM,5,2) = '22' ";
		} elseif ( $_SESSION['filter_prodi'] == "S1-21" ) {
			$filter_prodi = " AND MID(m.NIM,5,2) = '21' ";
		} else {
			$filter_prodi = " AND MID(m.NIM,5,2) ='MM'";
		}

	} else { $filter_prodi = ""; }

	if ( isset($_SESSION['filter_semester']) ) {

		$filter_sms = " AND ms.SMSTER='$_SESSION[filter_semester]' ";

	} else { $filter_sms = ""; }

	if ( isset($_SESSION['filter_status']) ) {

		$filter_status = " AND ms.KET='$_SESSION[filter_status]' ";

	} else { $filter_status = ""; }

	if ( isset($_SESSION['filter_angkatan']) ) {

		$filter_angkatan = " AND left(m.NIM,4) = '$_SESSION[filter_angkatan]' ";

	} else { $filter_angkatan = " AND left(m.NIM,4) > '2008' "; }


	$query = "SELECT m.NIM,m.NAMA,m.NAMA_PRODI,ms.SMSTER,ms.SMSTR,ms.KET,ms.TAHUN
						FROM t_mhs_status ms
						JOIN t_mhs m ON ms.NIM=m.NIM 
							WHERE m.NIM!='' $filter_thn $filter_prodi $filter_sms $filter_status $filter_angkatan 
								GROUP BY m.NIM ORDER BY m.NIM ASC";

}

/* Query :
	ambil data dri tbl mhs dan ambil data pelengkap dari tbl mhs_status (JOIN) dengan FILTER:
		- ms.SMSTER
		- ms.TAHUN
		- ms.KET
		- Jenjang studi (S1/S2)
		- Angkatan
*/

$no = 1;

$sql = $conn->query( $query ); ?>

<!DOCTYPE html>
<html lang='id'>

<head>
	<title>Laporan Aktivitas Mahasiswa</title>

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/table_jui.css" rel="stylesheet">
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>

<script>

$(document).ready(function() {
    $('#data-table').dataTable( {
        	"oLanguage": {
            "sSearch": "Cari Data: "
        },
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort": true
    });
});

</script>
</head>

<body>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">

			<center><h4>Laporan Aktifitas Mahasiswa <b>STIE NOBEL Indonesia Makassar</b></h4></center>
			<br />
			<br />

			
			<table border='0'>

				<tr>
					<td width="120">
						<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
							<option value="engine.php?f=tahun&val=">Filter tahun (All)</option>
							<?php for ( $i = 2010; $i<= date('Y'); $i++ ) { ?>
							<option value="engine.php?f=tahun&val=<?php echo $i ?>" <?php echo isset($_SESSION['filter_tahun']) && $_SESSION['filter_tahun'] == "$i" ? "selected":"";?>><?php echo $i ?></option>
							<?php } ?>
						</select>
					</td>

					<td>&nbsp;</td>	
					<td width="180">
						<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
							<option value="engine.php?f=semester&val=GENAP" <?php echo isset($_SESSION['filter_semester']) && $_SESSION['filter_semester'] == "GENAP" ? "selected":"";?>>Genap</option>
							<option value="engine.php?f=semester&val=GANJIL" <?php echo isset($_SESSION['filter_semester']) && $_SESSION['filter_semester'] == "GANJIL" ? "selected":"";?>>Ganjil</option>
						</select>
					</td>
					
					<td>&nbsp;</td>	
					<td width="180">
						<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
							<option value="engine.php?f=prodi&val=">Filter Prodi (All)</option>
							<option value="engine.php?f=prodi&val=S1-21" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "S1-21" ? "selected":"";?>>S1-Akuntansi</option>
							<option value="engine.php?f=prodi&val=S1-22" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "S1-22" ? "selected":"";?>>S1-Manajemen</option>
							<option value="engine.php?f=prodi&val=MM" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "MM" ? "selected":"";?>>S2-Manajemen</option>
						</select>
					</td>

					<td>&nbsp;</td>	
					<td width="180">
						<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
							<option value="engine.php?f=status&val=">Filter Status (All)</option>
							<option value="engine.php?f=status&val=AKTIF" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "AKTIF" ? "selected":"";?>>AKTIF</option>
							<option value="engine.php?f=status&val=NON-AKTIF" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "NON-AKTIF" ? "selected":"";?>>NON-AKTIF</option>
							<option value="engine.php?f=status&val=LULUS" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "LULUS" ? "selected":"";?>>LULUS</option>
							<option value="engine.php?f=status&val=CUTI" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "CUTI" ? "selected":"";?>>CUTI</option>
							<option value="engine.php?f=status&val=DO" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "DO" ? "selected":"";?>>DO</option>
						</select>
					</td>
					<td>&nbsp;</td>	
					<td width="180">
						<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
							<option value="engine.php?f=angkatan&val=">Filter Angkatan (All)</option>
							<?php for ( $i = 2009; $i<= date('Y'); $i++ ) { ?>
							<option value="engine.php?f=angkatan&val=<?php echo $i ?>" <?php echo isset($_SESSION['filter_angkatan']) && $_SESSION['filter_angkatan'] == "$i" ? "selected":"";?>><?php echo $i ?></option>
							<?php } ?>
						</select>

					</td>
					<td>&nbsp; &nbsp;</td>	
					<td width="100"><a href="engine.php?f=reset" class="btn btn-default"><i class="fa fa-times"></i> Reset Filter</a></td>
					<td>&nbsp; &nbsp;</td>	
					<td width="50"><a href="export.php?q=<?php echo enk($query) ?>" target="_blank" class="btn btn-primary"><i class="fa fa-download"></i> Export</a></td>
					<td>&nbsp; &nbsp;</td>	
					<td width="150"><a href="mahasiswa.php" class="btn btn-default"><i class="fa fa-pencil"></i> Ubah Status Mahasiswa</a>
			</table>

			<hr />

			<table class="table table-hover table-bordered" id="data-table">

				<thead>

				<tr>
					<th>No</th>
					<th>Nim</th>
					<th>Nama</th>
					<th>Prodi</th>
					<th>Angkatan</th>
					<th>Semester1</th>
					<th>Tahun</th>
					<th>Status</th>
					<th>IPS</th>
					<th>IPK</th>
					<th>SKS</th>
					<th>Total SKS</th>
					<th>Semester2</th>
				</tr>

				</thead>
				
				<tbody>

				<?php if ( $sql->num_rows > 0 ) { 
				
					while ( $r = $sql->fetch_assoc() ) { 

						$angkatan = substr($r['NIM'], 0,4);

				/**** Hitung IPS **********************************/

					// total bobot & sks semester terakhir
						$sql_bobot = $conn->query("SELECT id_mk, KODEMTK,NILAI FROM t_krs_sementara WHERE NIM='$r[NIM]' AND SMSTR='$r[SMSTR]'");

						$jml_sks = 0;
						$tot_bobot = 0;

						while ( $bob = $sql_bobot->fetch_assoc() ) {

								if ( !empty($bob['id_mk']) ) {
									$sql_mk = $conn->query("SELECT SKS FROM t_matakuliah WHERE nomor='$bob[id_mk]'");
								} else {

									$sql_mk = $conn->query("SELECT SKS FROM t_matakuliah WHERE KODEMTK = '$bob[KODEMTK]'");
									if ( !$sql_mk ) {
										echo $conn->error;
									}
								}

								$sks_mk = $sql_mk->fetch_assoc();
								$jml_sks = $sks_mk['SKS'] + $jml_sks;
								$bobot = $sks_mk['SKS'] * $bob['NILAI'];
								$tot_bobot = $tot_bobot + $bobot;
						}

						// ips
						if ( $sql_bobot->num_rows > 0 ) {
							$ips = $tot_bobot / $jml_sks;
							$ips = round ($ips,2);
						} else {
							$ips = "-";
							$jml_nilai = "-";
						}


				/**** Hitung IPK **********************************/
						
					// total sks

						// DIDIK BARU
						$sql2 = $conn->query("SELECT SMSTRAK,TAHUN,id_mk,KODEMTK,NILAI FROM t_krs_sementara WHERE NIM='$r[NIM]' AND HURUF!=''");
						
						$tot_bobot_pdb = 0;
						$tot_sks_pdb = 0;

						while ( $r2 = $sql2->fetch_assoc() ) {
							
							if ( trim($r2['SMSTRAK']) == "GANJIL" ) {
							
								$tahun_akademik = " AND THNAK = '".trim($r2['TAHUN'])."1'";
							
							} elseif ( trim($r2['SMSTRAK']) == "GENAP" ) {
							
								$tahun_akademik = " AND THNAK = '".trim($r2['TAHUN'])."2'";
							
							} else {

								$tahun_akademik = "";
							}

							if ( !empty($r2['id_mk']) ) {

								$sql_mk = $conn->query("SELECT SKS FROM t_matakuliah WHERE nomor='$r2[id_mk]' $tahun_akademik");
							
							} else {

								$sql_mk = $conn->query("SELECT SKS FROM t_matakuliah WHERE KODEMTK = '$r2[KODEMTK]'");
							}

							$r_sks = $sql_mk->fetch_assoc();

							$tot_sks_pdb 	= $r_sks['SKS'] + $tot_sks_pdb;
							$bobot_pdb 		= $r_sks['SKS'] * $r2['NILAI'];
							$tot_bobot_pdb 	= $tot_bobot_pdb + $bobot_pdb;
						
						}

						// PINDAHAN
						$sql3 = $conn->query("SELECT id_mk,sks,NILAI,sks_t,NILAI_t FROM t_transfer WHERE nim='$r[NIM]'");
						$tot_sks_t = 0;
						$tot_bobot_t = 0;

						while ( $r3 = $sql3->fetch_assoc() ) {

							if ( $r3['sks'] != 0 ) {
								$tot_sks_t = $r3['sks'] + $tot_sks_t;
								$bobot_t = $r3['sks'] * $r3['NILAI'];
								$tot_bobot_t = $tot_bobot_t + $bobot_t;
							} else {
								$sql_mk2 = $conn->query("SELECT SKS FROM t_matakuliah WHERE nomor='$r3[id_mk]' $tahun_akademik");
								$mk2 = $sql_mk2->fetch_assoc();
								$tot_sks_t = $mk2['SKS'] + $tot_sks_t;
								$bobot_t = $mk2['SKS'] * $r3['NILAI_t'];
								$tot_bobot_t = $tot_bobot_t + $bobot_t;
							}
						}


						$tot_bobot_all 	= $tot_bobot_pdb + $tot_bobot_t;
						$tot_sks_all  	= $tot_sks_pdb + $tot_sks_t;
						//IPK
						$ipk = $tot_bobot_all / $tot_sks_all;
						$ipk = round($ipk,2);


					?>

					<tr>
						<td><?php echo $no ?></td>
						<td><?php echo $r['NIM'] ?></td>
						<td><?php echo $r['NAMA'] ?></td>
						<td><?php echo ucwords($r['NAMA_PRODI']) ?></td>
						<td><?php echo $angkatan ?></td>
						<td><?php echo $r['SMSTER'] ?></td>
						<td><?php echo $r['TAHUN'] ?></td>
						<td><?php echo $r['KET'] ?></td>
						<td><?php echo $ips ?></td>
						<td><?php echo $ipk ?></td>
						<td><?php echo $jml_sks ?></td>
						<td><?php echo $tot_sks_all ?></td>
						<td><?php echo $r['SMSTR'] ?></td>
					</tr>


					<?php $no++; } ?>

				<?php } ?>

				</tbody>

			</table>
	
		</div>

	</div>
</div>

</body>

</html>

<?php 
$conn->close;
// Selesai_tes($start);
ob_end_flush(); 
?>