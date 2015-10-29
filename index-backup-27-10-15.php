<?php

ob_start();

include "config.php";

$tabel_lama = 't_krs_sementara_lama';

// Filtering

if (!isset( $_SESSION['filter_semester'] ) ) {
	$_SESSION['filter_semester'] = "GENAP";
}

if ( !isset($_SESSION['filter']) ) {
	$_SESSION['filter'] = 1;
}

if ( !isset($_SESSION['filter_tahun']) ) {

	$_SESSION['filter_tahun'] = '2010';

}

$filter_thn = " AND TAHUN='$_SESSION[filter_tahun]'";

// 2011 ke atas
if ( $_SESSION['filter_tahun'] > 2010 ) {


	if ( isset($_SESSION['filter']) ) {

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

		if ( isset( $_SESSION['cari'] ) ) {
			$cari = " AND nim = '$_SESSION[cari]' ";
		} else {
			$cari = "";
		}

		$query = "SELECT NIM,TAHUN,SMSTER,SMSTR,KET FROM t_mhs_status WHERE pack = '0' $filter_thn $filter_prodi $filter_sms $filter_status $filter_angkatan $cari";
		
		$query_jml = "SELECT NIM,TAHUN,SMSTER,SMSTR,KET FROM t_mhs_status WHERE pack = '0'
								$filter_thn $filter_prodi $filter_sms $filter_status $filter_angkatan $cari";

	}

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





// 2010 ke bawah
} else {

	if ( isset($_SESSION['filter']) ) {

		if ( isset($_SESSION['filter_prodi']) ) {

			if ( $_SESSION['filter_prodi'] == "S1-22" ) {
				$filter_prodi = " AND MID(nim,5,2) = '22' ";
			} elseif ( $_SESSION['filter_prodi'] == "S1-21" ) {
				$filter_prodi = " AND MID(nim,5,2) = '21' ";
			} else {
				$filter_prodi = " AND MID(nim,5,2) ='MM'";
			}

		} else { $filter_prodi = ""; }

		if ( isset($_SESSION['filter_semester']) ) {

			$filter_sms = " AND SMSTER='$_SESSION[filter_semester]' ";

		} else { $filter_sms = ""; }

		if ( isset($_SESSION['filter_status']) ) {

			$filter_status = " AND KET='$_SESSION[filter_status]' ";

		} else { $filter_status = ""; }

		if ( isset($_SESSION['filter_angkatan']) ) {

			$filter_angkatan = " AND left(nim,4) = '$_SESSION[filter_angkatan]' ";

		} else { $filter_angkatan = ""; }

		if ( isset( $_SESSION['cari'] ) ) {
			$cari = " AND NIM = '$_SESSION[cari]' ";
		} else {
			$cari = "";
		}

		$query = "SELECT TAHUN, SMSTER, NIM, SMSTR FROM $tabel_lama WHERE SMSTER != '' $filter_thn $filter_prodi $filter_sms $filter_angkatan $cari GROUP BY NIM";

		$query_jml = "SELECT TAHUN, SMSTER, NIM, SMSTR FROM $tabel_lama WHERE SMSTER != '' 
								$filter_thn $filter_prodi $filter_sms $filter_angkatan $cari GROUP BY NIM";

	}

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

			<form action="engine.php?f=filter-aktivitas" id="form-filter" method="post">

				<table border='0'>

					<tr>
						<td width="180">
							<select class="form-control" name="ta">
								<?php for ( $i = 1999; $i<= date('Y'); $i++ ) { ?>
								<option value="<?php echo $i ?>" <?php echo isset($_SESSION['filter_tahun']) && $_SESSION['filter_tahun'] == "$i" ? "selected":"";?>><?php echo $i ?></option>
								<?php } ?>
							</select>
						</td>

						<td>&nbsp;</td>	
						<td width="130">
							<select class="form-control" name="semester">
								<option value="GANJIL" <?php echo isset($_SESSION['filter_semester']) && $_SESSION['filter_semester'] == "GANJIL" ? "selected":"";?>>Ganjil</option>
								<option value="GENAP" <?php echo isset($_SESSION['filter_semester']) && $_SESSION['filter_semester'] == "GENAP" ? "selected":"";?>>Genap</option>
							</select>
						</td>
						
						<td>&nbsp;</td>	
						<td width="180">
							<select class="form-control" name="prodi">
								<option value="">Filter Prodi (All)</option>
								<option value="S1-22" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "S1-22" ? "selected":"";?>>S1-Akuntansi</option>
								<option value="S1-21" <?php echo isset($_SESSION['filter_prodi']) && $_SESSION['filter_prodi'] == "S1-21" ? "selected":"";?>>S1-Manajemen</option>
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
								<?php for ( $i = 1999; $i<= date('Y'); $i++ ) { ?>
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

			<div class="col-md-3">
				<?php echo "<b>Total data: $jml_data</b>"; ?>
			</div>

			<div class="col-md-8 pull-right" style="padding-right:0">
				<div class="pull-right">

					<form class="form-inline" method="post" action="engine.php?f=cari">

					  <div class="form-group">
					    <label class="sr-only">&nbsp;</label>
					    <p class="form-control-static">&nbsp;</p>
					  </div>

					  <div class="form-group">
					    <input type="text" class="form-control" name="cari" placeholder="NIM" value="<?= isset( $_SESSION['cari'] ) ? $_SESSION['cari'] : "" ?>">
					  </div>
					  <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Cari</button>
					</form>

				</div>

			</div>

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
						

						if ( $_SESSION['filter_tahun'] > 2010 ) {

							$nim = $r['NIM'];

							$smstr = $r['SMSTR'];
							$angkatan = substr($r['NIM'], 0,4);

							/**** Hitung IPS **********************************/

							// total bobot & sks semester terakhir
							$sql_bobot = $conn->query("SELECT A.ID_JDW, A.ID_MK, A.TAHUN, C.KODEMTK, C.NAMAMT AS NAMAMTK, A.NIM, A.SMSTR, C.SKS, A.HURUF,A.NILAI, C.SKS*A.NILAI AS BOBOT
														FROM t_krs_sementara AS A
															LEFT JOIN t_jadwal AS B ON A.ID_JDW = B.nomor
															LEFT JOIN t_matakuliah AS C ON B.ID_MK = C.NOMOR OR A.ID_MK = C.NOMOR
																WHERE LENGTH(C.KODEMTK) > 0 AND LENGTH(C.NAMAMT) > 0 
																	AND A.NIM = '$r[NIM]' AND A.SMSTR = '$r[SMSTR]'");

							$jml_sks = 0;
							$tot_bobot = 0;

							while ( $bob = $sql_bobot->fetch_assoc() ) {

								$jml_sks 	= $bob['SKS'] + $jml_sks;
								$tot_bobot = $tot_bobot + $bob['BOBOT'];
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

							$sql2 = $conn->query("SELECT A.ID_JDW, A.ID_MK, A.TAHUN, C.tahun AS tahun_mk, C.KODEMTK, C.NAMAMT AS NAMAMTK, A.NIM, A.SMSTR, C.SKS, MAX(A.HURUF) AS HURUF,MAX(A.NILAI) AS NILAI, MAX(C.SKS*A.NILAI) AS BOBOT, MAX(NA.HURUF) AS HURUF_A,MAX(NA.NILAI) AS NILAI_A, MAX(C.SKS*NA.NILAI) AS BOBOT_A 
													FROM t_krs_sementara AS A 
														LEFT JOIN t_jadwal AS B ON A.ID_JDW = B.nomor 
														LEFT JOIN t_matakuliah AS C ON B.ID_MK = C.NOMOR OR A.ID_MK = C.NOMOR 
														LEFT JOIN t_nilai_antara AS NA ON A.KODEMTK = NA.KDMTK AND A.NIM = NA.NIM
															WHERE LENGTH(C.KODEMTK) > 0 AND LENGTH(C.NAMAMT) > 0 AND A.NIM = '$r[NIM]'  AND C.tahun <= '$_SESSION[filter_tahun]' AND A.SMSTR <= '$smstr'
																GROUP BY C.KODEMTK ORDER by C.KODEMTK");


							$dt_didik_baru 	= array();
							$tot_bobot_pdb 	= 0;
							$tot_sks_pdb 	= 0;

							while ( $r2 = $sql2->fetch_assoc() ) {

								array_push( $dt_didik_baru, 
										array(
											'KODEMTK' => trim($r2['KODEMTK']),
											'SKS' => trim($r2['SKS']),
											'BOBOT' => trim($r2['BOBOT']),
											'BOBOT_A' => trim($r2['BOBOT_A'])
											)
										);

								$tot_sks_pdb 	= $r2['SKS'] + $tot_sks_pdb;
									
								if ( $r2['BOBOT_A'] != NULL ) {

									if ( $r2['BOBOT_A'] > $r2['BOBOT'] ) {
										$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT_A'];
									} else {
										$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];
									}

								} else {

									$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];

								}
							
							}

							// PINDAHAN
							$sql3 = $conn->query("SELECT A.id_mk, B.KODEMTK, B.NAMAMT AS NAMAMTK, A.NIM, A.smstr, B.SKS, A.HURUF,A.NILAI, B.SKS*A.NILAI AS BOBOT 
													FROM t_transfer AS A 
														LEFT JOIN t_matakuliah AS B ON A.id_mk = B.NOMOR 
															WHERE LENGTH(B.KODEMTK) > 0 AND LENGTH(B.NAMAMT) > 0 AND A.NIM='$r[NIM]' 
																ORDER by B.KODEMTK");
							$tot_sks_t = 0;
							$tot_bobot_t = 0;

							if ( $sql3->num_rows > 0 ) {
								while ( $r3 = $sql3->fetch_assoc() ) {

									$key = array_search( trim($r3['KODEMTK']), array_column($dt_didik_baru, 'KODEMTK'));
									$key = "$key";

									// Jika KODEMK ditemukan
									if ( $key != "" ) {

										$bobot_pdb = $dt_didik_baru[$key]['KODEMTK'];
										if ( $r3['BOBOT'] > $bobot_pdb ) {
											// Kurangin total sks yg telah masuk d Pserta didik baru ( $sql2 )
											$tot_sks_pdb = $tot_sks_pdb - $bobot_pdb;

											$tot_sks_t = $r3['SKS'] + $tot_sks_t;
											$tot_bobot_t = $tot_bobot_t + $r3['BOBOT'];
										}

									} else {

										$tot_sks_t = $r3['SKS'] + $tot_sks_t;
										$tot_bobot_t = $tot_bobot_t + $r3['BOBOT'];
									}

								}
							}


						// Dari siakad lama ( tahun < 2011 )

							// Total sks transfer siakad lama(Jika ada)
							$tot_bobot_siakad_lama 	= 0;
							$tot_sks_siakad_lama 	= 0;

							$sql21 = $conn->query("SELECT * FROM $tabel_lama WHERE NIM = '$r[NIM]'");
							

							while ( $rl2 = $sql21->fetch_assoc() ) {

								$tot_sks_siakad_lama 	= $rl2['SKS'] + $tot_sks_siakad_lama;

								$tot_bobot_siakad_lama 	= $tot_bobot_siakad_lama + $rl2['BOBOT'];

							}

						// TOTAL
							$tot_bobot_all 	= $tot_bobot_pdb + $tot_bobot_t + $tot_bobot_siakad_lama;
							$tot_sks_all  	= $tot_sks_pdb + $tot_sks_t + $tot_sks_siakad_lama;

						
							//IPK
							if ( $tot_bobot_all != 0 || $tot_sks_all != 0 ) {
								$ipk = $tot_bobot_all / $tot_sks_all;
								$ipk = round($ipk,2);
							} else {
								$ipk = "-";
							}


// TAHUN < 2011 
						} else {

							$nim = $r['NIM'];

							$angkatan = substr($r['NIM'], 0,4);

							/**** Hitung IPS **********************************/

							// total bobot & sks semester terakhir
							$sql_bobot = $conn->query("SELECT * FROM $tabel_lama 
																	WHERE NIM = '$r[NIM]' AND tahun = '$_SESSION[filter_tahun]' AND SMSTER = '$r[SMSTER]'");

							$jml_sks = 0;
							$tot_bobot = 0;

							while ( $bob = $sql_bobot->fetch_assoc() ) {

								$jml_sks 	= $bob['SKS'] + $jml_sks;
								$tot_bobot = $tot_bobot + $bob['BOBOT'];
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
							$tot_bobot_pdb = 0;
							$tot_sks_pdb = 0;

							$sql2 = $conn->query("SELECT * FROM $tabel_lama 
															WHERE SMSTER != 'KONVERSI' AND NIM = '$r[NIM]'  AND TAHUN <= '$_SESSION[filter_tahun]' AND SMSTR <= '$r[SMSTR]'");

// echo "SELECT * FROM $tabel_lama WHERE SMSTER != 'KONVERSI' AND NIM = '$r[NIM]' AND TAHUN <= '$_SESSION[filter_tahun]' AND SMSTR <= '$r[SMSTR]'";
							while ( $r2 = $sql2->fetch_assoc() ) {

								$tot_sks_pdb 	= $r2['SKS'] + $tot_sks_pdb;

								$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];

							}

							// Total sks transfer (Jika ada)
							$tot_bobot_t = 0;
							$tot_sks_t = 0;
							$sql21 = $conn->query("SELECT * FROM $tabel_lama 
															WHERE SMSTER = 'KONVERSI' AND NIM = '$r[NIM]'");


							while ( $r21 = $sql21->fetch_assoc() ) {

								$tot_sks_t 	= $r21['SKS'] + $tot_sks_t;

								$tot_bobot_t 	= $tot_bobot_t + $r21['BOBOT'];

							}

							// Sks/bobot reguler + sks/bobot transfer
							$tot_bobot_all 	= $tot_bobot_pdb + $tot_bobot_t;
							$tot_sks_all  	= $tot_sks_pdb + $tot_sks_t;

						
						//IPK
							if ( $tot_bobot_all != 0 || $tot_sks_all != 0 ) {
								$ipk = $tot_bobot_all / $tot_sks_all;
								$ipk = round($ipk,2);
							} else {
								$ipk = "-";
							}

						}



						$nama = $DB->get_field('t_mhs','NIM',$nim,'nama');

						$jj = substr($nim,4,2);

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
						<td><?php echo $nim ?></td>
						<td><?php echo $nama ?></td>
						<td><?php echo $jenjang ?></td>
						<td><?php echo $angkatan ?></td>
						<td><?php echo $r['SMSTER'] ?></td>
						<td><?php echo $r['TAHUN'] ?></td>
						<td><?php echo @$r['KET'] ?></td>
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
ob_end_flush(); 
?>