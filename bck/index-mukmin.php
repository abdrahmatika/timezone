<?php

ob_start();

include "config.php";

// $start = Mulai_tes();
// Filtering


if (!isset( $_SESSION['filter_semester'] ) ) {
	$_SESSION['filter_semester'] = "GENAP";
}

if ( !isset($_SESSION['filter']) ) {
	$_SESSION['filter'] = 1;
}

if ( isset($_SESSION['filter']) ) {

	if ( isset($_SESSION['filter_tahun']) ) {

		$filter_thn = " AND TAHUN='$_SESSION[filter_tahun]'";

	} else { $filter_thn = ""; }

	if ( isset($_SESSION['filter_prodi']) ) {

		if ( $_SESSION['filter_prodi'] == "S1-22" ) {
			$filter_prodi = " AND MID(NIM,5,2) = '22' ";
		} elseif ( $_SESSION['filter_prodi'] == "S1-21" ) {
			$filter_prodi = " AND MID(NIM,5,2) = '21' ";
		} else {
			$filter_prodi = " AND MID(NIM,5,2) ='MM'";
		}

	} else { $filter_prodi = ""; }

	if ( isset($_SESSION['filter_semester']) ) {

		$filter_sms = " AND SMSTER='$_SESSION[filter_semester]' ";

	} else { $filter_sms = ""; }

	if ( isset($_SESSION['filter_status']) ) {

		$filter_status = " AND KET='$_SESSION[filter_status]' ";

	} else { $filter_status = ""; }

	if ( isset($_SESSION['filter_angkatan']) ) {

		$filter_angkatan = " AND left(NIM,4) = '$_SESSION[filter_angkatan]' ";

	} else { $filter_angkatan = " AND left(NIM,4) > '2008' "; }

	if ( isset($_SESSION['cari']) ) {

		$filter_cari = " AND NIM LIKE '%$_SESSION[cari]%' ";

	} else {
		$filter_cari = "";
	}
	// $query = "SELECT NIM FROM t_mhs WHERE left(NIM,4) > '2008'";

	$query = "SELECT NIM,TAHUN,SMSTER,SMSTR,KET FROM t_mhs_status WHERE left(NIM,4) > '2008' $filter_thn $filter_prodi $filter_sms $filter_status $filter_angkatan";
	
	$query_jml = "SELECT NIM,TAHUN,SMSTER,SMSTR,KET FROM t_mhs_status WHERE left(NIM,4) > '2008'
							$filter_thn $filter_prodi $filter_sms $filter_status $filter_angkatan";
}
// echo $query;
/* Query :
	ambil data dri tbl mhs dan ambil data pelengkap dari tbl mhs_status (JOIN) dengan FILTER:
		- ms.SMSTER
		- ms.TAHUN
		- ms.KET
		- Jenjang studi (S1/S2)
		- Angkatan
*/
$sql_jml = $conn->query($query_jml);
$jml_data = $sql_jml->num_rows;

$pg = new Paging_google();
$batas = 10;

$posisi = $pg->cari_posisi($batas);

$no = 1+$posisi;

// Sorting
if ( !isset( $_SESSION['sort'] ) ) {
	$_SESSION['sort'] = "ASC";
}

if ( $_SESSION['sort'] == "ASC" ) {
	$sort = " ORDER BY NIM ASC ";
} else {
	$sort = " ORDER BY NIM DESC ";
}

$sql = $conn->query( $query . $sort . " LIMIT $posisi,$batas" ); ?>

<!DOCTYPE html>
<html lang='id'>

<head>
	<title>Laporan Aktivitas Mahasiswa</title>

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<script src="js/jquery-1.10.2.min.js"></script>

</head>

<body>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">

			<center><h4>Laporan Aktifitas Mahasiswa <b>STIE NOBEL Indonesia Makassar</b></h4></center>
			<br />
			<br />


			<!-- <form acion="engine.php?p=cari" method="post">
				<input type="text" class="form-control col-md-4" name="cari" value="<?php echo isset( $_SESSION['car'] ) ? $_SESSION['cari'] : "";?>" placeholder="Cari Nim...">
			</form> -->
			
			<form action="engine.php?f=filter-aktivitas" id="form-filter" method="post">

				<table border='0'>

					<tr>
						<td width="180">
							<select class="form-control" name="ta">
								<option value="">Filter tahun (All)</option>
								<?php for ( $i = 2010; $i<= date('Y'); $i++ ) { ?>
								<option value="<?php echo $i ?>" <?php echo isset($_SESSION['filter_tahun']) && $_SESSION['filter_tahun'] == "$i" ? "selected":"";?>><?php echo $i ?></option>
								<?php } ?>
							</select>
						</td>

						<td>&nbsp;</td>	
						<td width="130">
							<select class="form-control" name="semester">
								<option value="">Semester (ALL)</option>
								<option value="GENAP" <?php echo isset($_SESSION['filter_semester']) && $_SESSION['filter_semester'] == "GENAP" ? "selected":"";?>>Genap</option>
								<option value="GANJIL" <?php echo isset($_SESSION['filter_semester']) && $_SESSION['filter_semester'] == "GANJIL" ? "selected":"";?>>Ganjil</option>
							</select>
						</td>
						
						<td>&nbsp;</td>	
						<td width="180">
							<select class="form-control" name="prodi">
								<option value="">Filter Prodi (All)</option>
								<option value="S1-21" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "S1-21" ? "selected":"";?>>S1-Akuntansi</option>
								<option value="S1-22" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "S1-22" ? "selected":"";?>>S1-Manajemen</option>
								<option value="MM" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "MM" ? "selected":"";?>>S2-Manajemen</option>
							</select>
						</td>

						<td>&nbsp;</td>	
						<td width="180">
							<select class="form-control" name="status">
								<option value="">Status (ALL)</option>
								<option value="AKTIF" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "AKTIF" ? "selected":"";?>>AKTIF</option>
								<option value="NON-AKTIF" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "NON-AKTIF" ? "selected":"";?>>NON-AKTIF</option>
								<option value="LULUS" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "LULUS" ? "selected":"";?>>LULUS</option>
								<option value="CUTI" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "CUTI" ? "selected":"";?>>CUTI</option>
								<option value="DO" <?php echo isset($_SESSION['filter_status']) && $_SESSION['filter_status'] == "DO" ? "selected":"";?>>DO</option>
							</select>
						</td>
						<td>&nbsp;</td>	
						<td width="180">
							<select class="form-control" name="angkatan">
								<option value="">Filter Angkatan (All)</option>
								<?php for ( $i = 2009; $i<= date('Y'); $i++ ) { ?>
								<option value="<?php echo $i ?>" <?php echo isset($_SESSION['filter_angkatan']) && $_SESSION['filter_angkatan'] == "$i" ? "selected":"";?>><?php echo $i ?></option>
								<?php } ?>
							</select>

						</td>
						<td>&nbsp; &nbsp;</td>	
						<td width="50"><button type="submit" class="btn btn-default"><i class="fa fa-filter"></i> Filter</button></td>
						<td>&nbsp; &nbsp;</td>	
						<td width="50"><a href="export.php?q=<?php echo enk($query) ?>" target="_blank" class="btn btn-primary"><i class="fa fa-download"></i> Export</a></td>
						<td>&nbsp; &nbsp;</td>	
						<td width="150"><a href="mahasiswa.php" class="btn btn-default export"><i class="fa fa-pencil"></i> Ubah Status Mahasiswa</a>
				</table>

			</form>

			<hr />

			<?php echo "<b>Total data: $jml_data</b>"; ?>

			<table class="table table-hover table-bordered">

				<thead>

				<tr>
					<th>No</th>
					<th style="cursor:pointer" onclick="location.href='engine.php?f=sort&val=<?php echo $_SESSION['sort'] == "ASC" ? "DESC":"ASC"; ?>'">NIM <i class="pull-right fa fa-sort-numeric-<?php echo strtolower($_SESSION['sort']) ?>"></i></th>
					<th>Nama</th>
					<th>Prodi</th>
					<th>Angkatan</th>
					<th>Semester</th>
					<th>Tahun</th>
					<th>Status</th>
					<th>IPS</th>
					<th>IPK</th>
					<th>SKS</th>
					<th>Total SKS</th>
					<th>SMSTR</th>
				</tr>

				</thead>
				
				<tbody>

				<?php if ( $sql->num_rows > 0 ) { 
				
					while ( $r = $sql->fetch_assoc() ) {

						$angkatan = substr($r['NIM'], 0,4);

						/**** Hitung IPS **********************************/

						// total bobot & sks semester terakhir
						$query2 = "SELECT a.tahun,a.SMSTER,a.nim,b.nama,a.smstr,a.ket,c.kodemtk,c.id_jdw,c.id_mk,c.huruf,c.nilai, d.sks,a.smster,a.tahun,a.TAHUN
													from T_MHS_STATUS as a 
														left join t_mhs as b on a.nim = b.nim 
														left join T_KRS_SEMENTARA as c on a.nim = c.nim and a.tahun = c.tahun 
														left join T_MATAKULIAH as d on a.tahun = d.tahun and a.smster = d.smstrak and c.kodemtk = d.KODEMTK 
															where a.SMSTER = '$r[SMSTER]' and c.SMSTR = '$r[SMSTR]' AND b.NIM = '$r[NIM]' GROUP BY d.NAMAMT";
						$sql2 = $conn->query($query2);
						echo $query2;
						
						// $sql_bobot = $conn->query("SELECT id_mk, KODEMTK,NILAI FROM t_krs_sementara WHERE NIM='$r[NIM]' AND SMSTR='$r[SMSTR]'");

						$jml_sks = 0;
						$tot_bobot = 0;

						while ( $r2 = $sql2->fetch_assoc() ) {

								$jml_sks = $r2['sks'] + $jml_sks;
								$bobot = $r2['sks'] * $r2['nilai'];
								$tot_bobot = $tot_bobot + $bobot;

						}

						// ips
						if ( $tot_bobot != 0 && $jml_sks != 0 ) {
							$ips = $tot_bobot / $jml_sks;
							$ips = round ($ips,2);
						} else {
							$ips = "0";
							$jml_nilai = "0";
						}


						/**** Hitung IPK **********************************/
							
						// Cek data pada tabel nilai antara
						$sql_nil_antara = $conn->query("SELECT * FROM t_nilai_antara WHERE NIM='$r[NIM]'");

						if ( $sql_nil_antara->num_rows > 0 ) {

							while ( $nil_antara = $sql_nil_antara->fetch_assoc() ) {
								$nilai_antara[] = strtolower(trim($nil_antara['KDMTK']));
							}

						}

						// total sks

						// DIDIK BARU
						$sql2 = $conn->query("SELECT SKS,SMSTRAK,TAHUN,id_mk,KODEMTK,NAMAMTK,NILAI FROM t_krs_sementara WHERE NIM='$r[NIM]' AND HURUF!=''");
						
						$tot_bobot_pdb = 0;
						$tot_sks_pdb = 0;

						while ( $r2 = $sql2->fetch_assoc() ) {
							

							// Menentukan jenis TA untuk pengambilan pada tabel matakuliah
							if ( trim($r2['SMSTRAK']) == "GANJIL" ) {
							
								$tahun_akademik = " AND THNAK = '".trim($r2['TAHUN'])."1'";
							
							} elseif ( trim($r2['SMSTRAK']) == "GENAP" ) {
							
								$tahun_akademik = " AND THNAK = '".trim($r2['TAHUN'])."2'";
							
							} else {

								$tahun_akademik = "";
							}


							// Ambil data matakuliah berdasarkan id_mk pada t_krs_sementara
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
// echo $tot_sks_pdb;

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
						// echo $tot_sks_pdb."-".$tot_sks_t;
						$tot_bobot_all 	= $tot_bobot_pdb + $tot_bobot_t;
						$tot_sks_all  	= $tot_sks_pdb + $tot_sks_t;

						//IPK
						if ( $tot_bobot_all != 0 || $tot_sks_all != 0 ) {
							$ipk = $tot_bobot_all / $tot_sks_all;
							$ipk = round($ipk,2);
						} else {
							$ipk = "-";
						}

						$nama = $DB->get_field('t_mhs','NIM',$r['NIM'],'nama');

						$jj = substr($r['NIM'],4,2);

						if ( $jj == "22" ) {
							$jenjang = "S1-Akuntansi";
						} elseif ( $jj == "21" ) {
							$jenjang = "S1-Manajemen";
						} elseif ( $jj == "MM" ) {
							$jenjang = "S2-Manajemen";
						} else {
							$jenjang = "-";
						}

					?>

					<tr>
						<td><?php echo $no ?></td>
						<td><?php echo $r['NIM'] ?></td>
						<td><?php echo $nama ?></td>
						<td><?php echo $jenjang ?></td>
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

				<?php } else { ?>

					<tr><td colspan="13"><center>Tidak menemukan data</center></td></tr>

				<?php } ?>

				</tbody>

			</table>
				
			<?php

				$jml_hal = $pg->jumlah_halaman($jml_data,$batas);
				$navigasi = $pg->nav_halaman($_GET['hal'],$jml_hal,"http://localhost/nobel-report");

				if ( $jml_data > $jml_hal ) {

					 echo "<ul class='pagination'>$navigasi</ul>";

				}

			?>

		</div>

	</div>
</div>

</body>

</html>

<?php 
$conn->close();
// Selesai_tes($start);
ob_end_flush(); 
?>