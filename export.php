<?php

include "config.php";

// error_reporting(0);

$query = dek($_GET['q']);
$tabel_lama = 't_krs_sementara_lama';

$output = '"No","Nim","Nama","Prodi","Angkatan","Semester","SMTR","Tahun","Status","IPS","IPK","SKS","Tot. SKS"';


$output .="\n";

$sql = $conn->query($query);

$no = 1;

while ( $r = $sql->fetch_assoc() ) { 
	

	if ( $_SESSION['filter_tahun'] > 2010 ) {

		$nim = $r['NIM'];
		$smstr = $r['SMSTR'];
		$angkatan = substr($r['NIM'], 0,4);

		$kurangi_sks = 0;
		$kurangi_bobot = 0;

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

		// Database lama
		$mk_arr = array();
		$sql_ = $conn->query("SELECT * FROM $tabel_lama 
										WHERE SMSTER != 'KONVERSI' AND NIM = '$r[NIM]'");
		
		while ( $m = $sql_->fetch_assoc() ) {
			array_push( $mk_arr, 
					array(
						'KODEMTK' => trim($m['kd_mk']),
						'SKS' => trim($m['SKS']),
						'BOBOT' => trim($m['BOBOT']),
						)
					);
		}
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

			// Bandingkan mk yg ada pada database lama
			$key = array_search( trim($r2['KODEMTK']), array_column($mk_arr, 'KODEMTK'));
			$key = "$key";

			$tot_sks_pdb 	= $r2['SKS'] + $tot_sks_pdb;

			// Jika KODEMK tidak ditemukan
			if ( $key == "" ) {

					
				if ( $r2['BOBOT_A'] != NULL ) {

					if ( $r2['BOBOT_A'] > $r2['BOBOT'] ) {
						$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT_A'];
					} else {
						$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];
					}

				} else {

					$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];

				}


		// Bandingkan db lama dg db baru
			} else {

				$bobot_lama = $mk_arr[$key]['BOBOT'];

				$kurangi_sks = $kurangi_sks + $mk_arr[$key]['SKS'];

				if ( $r2['BOBOT_A'] != NULL ) {

					if ( $r2['BOBOT_A'] > $r2['BOBOT'] ) {

						if ( $bobot_lama > $r2['BOBOT_A'] ) {
							$tot_bobot_pdb 	= $tot_bobot_pdb + $bobot_lama;
							$kurangi_bobot = $kurangi_bobot + $bobot_lama;
						} else {
							$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT_A'];
							$kurangi_bobot = $kurangi_bobot + $r2['BOBOT_A'];
						}

					} else {
						if ( $bobot_lama > $r2['BOBOT'] ) {
							$tot_bobot_pdb 	= $tot_bobot_pdb + $bobot_lama;
						} else {
							$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];
						}
					}

				} else {

					$tot_bobot_pdb 	= $tot_bobot_pdb + $r2['BOBOT'];

				}
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

					$bobot_pdb = $dt_didik_baru[$key]['BOBOT'];
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


	// **************
	// matakuliah yang tidak ada pada krs sementara dan database lama

		// Ambil semua data kelas perkuliahan mahasiswa yg bersangkutan
		$sql_all_mk = $conn->query("SELECT A.ID_JDW, A.ID_MK, A.TAHUN, C.tahun AS tahun_mk, C.KODEMTK, C.NAMAMT AS NAMAMTK, A.NIM, A.SMSTR, C.SKS, MAX(A.HURUF) AS HURUF,MAX(A.NILAI) AS NILAI, MAX(C.SKS*A.NILAI) AS BOBOT, MAX(NA.HURUF) AS HURUF_A,MAX(NA.NILAI) AS NILAI_A, MAX(C.SKS*NA.NILAI) AS BOBOT_A 
								FROM t_krs_sementara AS A 
									LEFT JOIN t_jadwal AS B ON A.ID_JDW = B.nomor 
									LEFT JOIN t_matakuliah AS C ON B.ID_MK = C.NOMOR OR A.ID_MK = C.NOMOR 
									LEFT JOIN t_nilai_antara AS NA ON A.KODEMTK = NA.KDMTK AND A.NIM = NA.NIM
										WHERE LENGTH(C.KODEMTK) > 0 AND LENGTH(C.NAMAMT) > 0 AND A.NIM = '$r[NIM]'
											GROUP BY C.KODEMTK ORDER by C.KODEMTK");

		$all_mk 	= array();

		while ( $rl = $sql_all_mk->fetch_assoc() ) {

			array_push( $all_mk, 
					array(
						'KODEMTK' => trim($rl['KODEMTK']),
						'SKS' => trim($rl['SKS']),
						'BOBOT' => trim($rl['BOBOT']),
						'BOBOT_A' => trim($rl['BOBOT_A'])
						)
					);
		}

		$tambah_sks_mk = 0;
		$tambah_bobot_mk = 0;
		$na = $conn->query("SELECT na.NIM, na.KDMTK, na.NILAI, kr.kd_mk, 
							(SELECT SKS FROM t_matakuliah WHERE KODEMTK = na.KDMTK GROUP BY KODEMTK) as sks 
								FROM t_nilai_antara na 
									LEFT join t_krs_sementara_lama kr ON na.KDMTK = kr.kd_mk AND na.NIM = kr.NIM
										 WHERE kr.kd_mk is null and na.NIM = '$r[NIM]'");
		while ( $rn = $na->fetch_assoc() ) {
			$key = array_search( trim($rn['KDMTK']), array_column($all_mk, 'KODEMTK'));
			$key = "$key";
			if ( $key == "" ) {
				$bobot = $rn['NILAI'] * $rn['sks'];
				$tambah_bobot_mk 	= $bobot + $tambah_bobot_mk;
				$tambah_sks_mk 		= $tambah_sks_mk + $rn['sks'];
			}
		}

	// TOTAL
		$tot_bobot_all 	= $tot_bobot_pdb + $tot_bobot_t + $tot_bobot_siakad_lama - $kurangi_bobot + $tambah_bobot_mk;
		$tot_sks_all  	= $tot_sks_pdb + $tot_sks_t + $tot_sks_siakad_lama - $kurangi_sks + $tambah_sks_mk;


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


	$output .= '"' . $no . '","' . trim($r['NIM']) . '","' . $nama . '","'. $jenjang . '","'. trim($angkatan) . '","'. ($r['SMSTER']) . '","'. ($r['SMSTR']) . '","'. ($r['TAHUN']) . '","'. trim($r['KET']) . '","'. $ips . '","'. $ipk . '","'. $jml_sks . '","'. $tot_sks_pdb . '"';

	$output .="\n";

	$no++;
}

// Download the file

$filename = "Laporan-aktivitas-mahasiswa-$_SESSION[filter_tahun]-$_SESSION[filter_semester].csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

echo $output;
exit();

?>