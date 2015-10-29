<?php
session_start();
// error_reporting(0);

$config = array (
	'db_host'	 	=> 'localhost',
	'db_username' 	=> 'root',
	'db_password' 	=> '',
	'db_name'		=> 'nobel_2015'
);

$conn = new mysqli($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);

if ($conn->connect_error){
	echo "Gagal terkoneksi ke database : (".$mysqli->connect_error.")".$mysqli->connect_error;
}

include "db.class.php";

$DB = new DB_Class( $conn );


/*************
 Functions
*************/

function enk($string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'AbdRahmatIkaCode';
    $secret_iv = 'AbdRahmatIkaCode';

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);

    return $output;
}
function dek($string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'AbdRahmatIkaCode';
    $secret_iv = 'AbdRahmatIkaCode';

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

    return $output;
}

class Paging_google {

    // Fungsi untuk mencek halaman dan posisi data
    function cari_posisi($batas){
    if(empty($_GET['hal'])){
        $posisi=0;
        $_GET['hal']=1;
    } else{
        $posisi = ($_GET['hal']-1) * $batas;
    }
    return $posisi;
    }

    // Fungsi untuk menghitung total halaman
    function jumlah_halaman($jmldata, $batas){
        $jmlhalaman = ceil($jmldata/$batas);
        return $jmlhalaman;
    }

    // Fungsi untuk link halaman 1,2,3 
    function nav_halaman($halaman_aktif, $jmlhalaman, $link ){

    if ( $halaman_aktif > 1 ) {
        $first = "<li><a href='$link?hal=1'>Pertama</a></li>";
    } else {
        $first = "<li class='disabled'><a href=''>Pertama</a></li>";
    }
    $prev = "";
    /*  previous */
    if ( $halaman_aktif > 1 ) {
        $a = $halaman_aktif - 1;
        $prev = "<li><a href='$link?hal=$a'>&laquo;</a></li> ";
    } else {
        $prev = "<li class='disabled'><a>&laquo;</a><li> ";
    }

    $angka = ($halaman_aktif > 3 ? "<li><a><b>...</b></a></li> ":" ");
    for ( $i = $halaman_aktif-2; $i<$halaman_aktif; $i++){
      if ($i < 1)
        continue;
        $angka .= "<li><a href='$link?hal=$i'>$i</a></li> ";
    }

    $angka .= "<li class='active'><a>$halaman_aktif</a></li> ";
    for ( $i = $halaman_aktif + 1;$i<$halaman_aktif+3;$i++ ) {
        if ( $i > $jmlhalaman )
            break;
            $angka .= "<li><a href='$link?hal=$i'>$i</a></li> ";
    }

    $angka .= ($halaman_aktif+2<$jmlhalaman ? "<li><a><b>...</b></a></li> <li><a href='$link?hal=$jmlhalaman'>$jmlhalaman</a></li> ":"");
    
    if ($halaman_aktif < $jmlhalaman){
        $a2 = $halaman_aktif + 1;
        $next = "<li><a href='$link?hal=$a2'>&raquo; </a></li>";
    } else {
        $next = "<li class='disabled'><a>&raquo;</a></li>";
    }

    if ( $halaman_aktif < $jmlhalaman ) {
        $last = "<li><a href='$link?hal=$jmlhalaman'>Terakhir</a></li>";
    } else {
        $last = "<li class='disabled'><a href=''>Terakhir</a></li>";
    }

    return $first.$prev.$angka.$next.$last;
    }
}

function Mulai_tes(){
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $start = $time;
    return $start;
}

function Selesai_tes ( $start ){
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    echo 'Halaman dimuat dalam '.$total_time.' detik.';
}