<?php

ob_start();

include "config.php";

$f 		= @$_GET['f'];
$val 	= @$_GET['val'];

switch ( $f ) {

	case "filter-aktivitas":

		if ( isset($_POST['ta']) && !empty($_POST['ta']) ) {
			$_SESSION['filter_tahun'] = $_POST['ta'];
		} else {
			unset($_SESSION['filter_tahun']);
		}

		if ( isset($_POST['prodi']) && !empty($_POST['prodi']) ) {
			$_SESSION['filter_prodi'] = $_POST['prodi'];
		} else {
			unset($_SESSION['filter_prodi']);
		}

		if ( isset($_POST['angkatan']) && !empty($_POST['angkatan']) ) {
			$_SESSION['filter_angkatan'] = $_POST['angkatan'];
		} else {
			unset($_SESSION['filter_angkatan']);
		}

		if ( isset($_POST['status']) && !empty($_POST['status']) ) {
			$_SESSION['filter_status'] = $_POST['status'];
		} else {
			unset($_SESSION['filter_status']);
		}

		if ( isset($_POST['semester']) && !empty($_POST['semester']) ) {
			$_SESSION['filter_semester'] = $_POST['semester'];
		} else {
			unset($_SESSION['filter_semester']);
		}

		header("location:index.php");

	break;

	case "tahun":

		if ( !empty( $val ) ) {

			$_SESSION['filter'] = 1;

			$_SESSION['filter_tahun'] = $val;
			header("location:index.php");

		} else { 

			unset( $_SESSION['filter_tahun']);
			header("location:index.php");

		}

	break;

	case "angkatan":

		if ( !empty( $val ) ) {

			$_SESSION['filter'] = 2; 

			$_SESSION['filter_angkatan'] = $val;
			header("location:index.php");

		} else { 

			unset( $_SESSION['filter_angkatan']);
			header("location:index.php");

		}

	break;

	case "prodi":

		if ( !empty( $val ) ) {

			$_SESSION['filter'] = 2; 

			$_SESSION['filter_prodi'] = $val;
			header("location:index.php");

		} else { 

			unset( $_SESSION['filter_prodi']);
			header("location:index.php");

		}

	break;

	case "semester":

		if ( !empty( $val ) ) {

		$_SESSION['filter'] = 4; 

			$_SESSION['filter_semester'] = $val;
			header("location:index.php");

		} else { 

			unset( $_SESSION['filter_semester']);
			header("location:index.php");

		}

	break;

	case "status":

		if ( !empty( $val ) ) {

		$_SESSION['filter'] = 5; 

			$_SESSION['filter_status'] = $val;
			header("location:index.php");

		} else { 

			unset( $_SESSION['filter_status']);
			header("location:index.php");

		}

	break;

	case "reset":

		session_destroy();
		header("location:index.php");

	break;

	case "update-mhs":

		$nim		 = $_POST['nim'];
		$semester	 = $_POST['semester'];
		$tahun		 = $_POST['tahun'];
		$status	 	 = $_POST['status'];
		$URL	 	 = $_POST['URL'];

		if ( !empty($nim) ) {

			if ( is_array($nim) ) {

				for ( $i = 0; $i < count($nim); $i++ ) {

					$conn->query("UPDATE t_mhs_status SET KET='$status[$i]' WHERE NIM='$nim[$i]' AND SMSTER='$semester[$i]' AND TAHUN='$tahun[$i]'");
				
				}
				
				$_SESSION['sukses'] = 1;
				header("location:$URL");

			} else {

				$conn->query("UPDATE t_mhs_status SET KET='$status' WHERE NIM='$nim' AND SMSTER='$semester' AND TAHUN='$tahun'");
				$_SESSION['sukses'] = 1;
				header("location:mahasiswa.php");

			}

		} else {

			echo "NIM Kosong";

		}

	break;


	// FILTER MAHASISWA.PHP

	case "filter-angkatan-mhs":

		if ( isset($_SESSION['angkatan_mhs']) ) {

			if ( ( $key = array_search($val, $_SESSION['angkatan_mhs']) ) !== false) {

			    unset($_SESSION['angkatan_mhs'][$key]);

			} else {
				array_push($_SESSION['angkatan_mhs'], $val);
			}

		} else {
			$_SESSION['angkatan_mhs'] = array($val);
		}

		header("location:mahasiswa.php");

	break;

	case "filter-prodi-mhs":

		if ( isset($_SESSION['prodi_mhs']) ) {

			if ( ( $key = array_search($val, $_SESSION['prodi_mhs']) ) !== false) {

			    unset($_SESSION['prodi_mhs'][$key]);

			} else {
				array_push($_SESSION['prodi_mhs'], $val);
			}

		} else {
			$_SESSION['prodi_mhs'] = array($val);
		}

		header("location:mahasiswa.php");

	break;

	case "filter-ta-mhs":

		if ( isset( $_SESSION['ta_mhs']) && $_SESSION['ta_mhs'] == $val ) {

			unset($_SESSION['ta_mhs']);

		} else {

			$_SESSION['ta_mhs'] = $val;

		}

		header("location:mahasiswa.php");

	break;

	case "filter-status-mhs":

		if ( isset( $_SESSION['status_mhs']) && $_SESSION['status_mhs'] == $val ) {

			unset($_SESSION['status_mhs']);

		} else {

			$_SESSION['status_mhs'] = $val;

		}

		header("location:mahasiswa.php");

	break;

	case "reset-filter-mhs":

		unset($_SESSION['angkatan_mhs']);
		unset($_SESSION['ta_mhs']);
		unset($_SESSION['status_mhs']);
		unset($_SESSION['prodi_mhs']);

		header("location:mahasiswa.php");
	break;

	case "sort":

		$_SESSION['sort'] = $val;
		header("location:index.php");

	break;

	case "jenis-duplikat":

		$_SESSION['jenis_duplicate'] = $val;
		header("location:duplicate.php?query=$_GET[query]&smstr=$_GET[smstr]");

	break;

	case "cari":

		if ( !empty($_POST['cari']) ) {
			$_SESSION['cari'] = trim($_POST['cari']);
		} else {
			if ( isset( $_SESSION['cari'] ) ) {
				unset( $_SESSION['cari'] );
			}
		}

		header("location:index.php");

	break;
}