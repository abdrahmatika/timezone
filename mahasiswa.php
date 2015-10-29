<?php

include "config.php";

$URL = basename($_SERVER['REQUEST_URI']);

// ANGKATAN

if ( isset( $_SESSION['angkatan_mhs']) ) {

	 $count_angkatan = count($_SESSION['angkatan_mhs']);

	if ( $count_angkatan != 0 ) {

			$angkatan = " AND ( LEFT(NIM,4) ='";
			$a = 0;
			foreach ( $_SESSION['angkatan_mhs'] as $val ) {

				$angkatan .= $val."'";
				if ( $a < ($count_angkatan-1) ) {
					$angkatan .= " OR LEFT(NIM,4) ='";
				}

				$a++;
			}

			$angkatan .= ") ";

	} else {

		unset( $_SESSION['angkatan_mhs'] );
		$angkatan = "";
	}

} else { $angkatan = ""; }


// PRODI

if ( isset( $_SESSION['prodi_mhs']) ) {

	 $count_prodi = count($_SESSION['prodi_mhs']);

	if ( $count_prodi != 0 ) {

			$prodi = " AND (MID(NIM,5,2)='";
			$a = 0;
			foreach ( $_SESSION['prodi_mhs'] as $val ) {
				if ( $val == 1 ) {
					$prodi .= "22' ";
				} elseif ( $val == 2 ) {
					$prodi .= "21' ";
				} else {
					$prodi .= "MM' ";
				}

				if ( $a < ($count_prodi-1) ) {
					$prodi .= " OR MID(NIM,5,2)='";
				}

				$a++;
			}

			$prodi .= ") ";

	} else {

		unset( $_SESSION['prodi_mhs'] );
		$prodi = "";
	}

} else { $prodi = ""; }


// Tahun AKADEMIK
if ( isset( $_SESSION['ta_mhs']) ) {

	$val = explode("-",$_SESSION['ta_mhs']);
	$thn = $val[0];


	if ( $val[1] == 1 ) {
		$sms = "GANJIL";
	} else {
		$sms = "GENAP";
	}

	$semester = " AND TAHUN='$thn' AND SMSTER='$sms'";
} else {
	$semester = "";
}


// STATUS
if ( isset( $_SESSION['status_mhs']) ) {
	$status = " AND KET='$_SESSION[status_mhs]'";
} else {
	$status = "";
}

$pg = new Paging_google();
$batas = 20;

$posisi = $pg->cari_posisi($batas);

// $query = "SELECT * FROM t_mhs_status WHERE left(NIM,4) > '2008' $angkatan $semester $status ORDER BY NIM ASC,SMSTR ASC LIMIT 20";
$query = "SELECT NIM,TAHUN,SMSTER,SMSTR,KET FROM t_mhs_status WHERE left(NIM,4) > '2008' $angkatan $prodi $semester $status ORDER BY NIM ASC,SMSTR DESC LIMIT $posisi,$batas";
$query_jml = "SELECT NIM FROM t_mhs_status WHERE left(NIM,4) > '2008' $angkatan $prodi $semester $status";
$sql_jml = $conn->query($query_jml);
$jml_data = $sql_jml->num_rows;
ECHO $query;

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

			<h3>Data Mahasiswa <button class="submit btn btn-info btn-sm pull-right">Simpan</button></h3>

			<hr style="margin-top:1px">

			<?php if ( isset( $_SESSION['sukses']) ) { ?>

				<div class="alert alert-success" style="padding:5px">Berhasil menyimpan data</div>

			<?php unset($_SESSION['sukses']); } ?>
			<a href="index.php" class="btn btn-default btn-sm pull-left"><i class="fa fa-users"></i> Aktivitas Mahasiswa</a>

			<div class="col-md-4 pull-right" style="padding-right:0;margin-bottom:5px">

				<input class="form-control" type="text" name="cari" placeholder="Cari Mahasiswa" onkeyup="cari_mhs(this.value);">

			</div>

			<div class="clearfix"></div>
			<hr>
		</div>
			
	</div>

	<div class="row">

		<div class="col-md-4">

			<h4><i class='fa fa-filter'></i> Filter <a href="engine.php?f=reset-filter-mhs&uri=<?= $URL ?>" class="btn btn-default btn-xs pull-right"><i class="fa fa-times"></i> Reset Filter</a></h4><hr>

			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			
			<!-- ANGKATAN -->
			  <div class="panel panel-default">
			    <div class="panel-heading" role="tab" id="headingOne">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
			          Angkatan 
			        </a>
					
					<?php if ( isset( $_SESSION['angkatan_mhs']) ) { ?>
			        	<a href="engine.php?f=reset-filter-angkatan" class="pull-right" title="Reset Filter"><i class="fa fa-filter"></i></a>
			      	<?php } ?>

			      </h4>
			    </div>

			    <div id="collapseOne" class="panel-collapse collapse <?php echo isset($_SESSION['angkatan_mhs']) ? "in":"";?>" role="tabpanel" aria-labelledby="headingOne">
			      <div class="panel-body">
			      		<ul>
			      			<?php for ( $i = 2009; $i <= date('Y'); $i++ ) { ?>
			      				<li>

			      					<?php if ( isset($_SESSION['angkatan_mhs']) && in_array($i,$_SESSION['angkatan_mhs']) ) { ?>
			      						
			      						<a href="engine.php?f=filter-angkatan-mhs&val=<?= $i ?>&uri=<?= $URL ?>"><?php echo $i ?></a>
			      						<i class='fa fa-check'></i>

			      					<?php } else { ?>

			      						<a href="engine.php?f=filter-angkatan-mhs&val=<?= $i ?>&uri=<?= $URL ?>"><?php echo $i ?></a>

			      					<?php } ?>

			      				</li>
			      			<?php } ?>
			      		</ul>
			      </div>
			    </div>
			  </div>

			  <!-- PRODI -->
			  <div class="panel panel-default">
			    <div class="panel-heading" role="tab" id="headingTWO">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTWO" aria-expanded="true" aria-controls="collapseTWO">
			          Prodi 
			        </a>

			        <?php if ( isset( $_SESSION['prodi_mhs']) ) { ?>
			        	<a href="engine.php?f=reset-filter-prodi" class="pull-right" title="Reset Filter"><i class="fa fa-filter"></i></a>
			      	<?php } ?>

			      </h4>
			    </div>

			    <div id="collapseTWO" class="panel-collapse collapse <?php echo isset($_SESSION['prodi_mhs']) ? "in":"";?>" role="tabpanel" aria-labelledby="headingTWO">
			      <div class="panel-body">
			      		<ul>
			      			<?php for ( $i = 1; $i <= 3; $i++ ) { ?>
			      				<li>

			      					<?php if ( isset($_SESSION['prodi_mhs']) && in_array($i,$_SESSION['prodi_mhs']) ) { ?>
			      						
			      						<a href="engine.php?f=filter-prodi-mhs&val=<?= $i ?>&uri=<?= $URL ?>">
				      						<?php 
				      							if ( $i == 1 ) {
				      								echo "S1-Manajemen";
				      							} elseif ( $i == 2 ) {
				      								echo "S1-Akuntansi";
				      							} else {
				      								echo "S2-Manajemen";
				      							}
				      						?>
			      						</a>
			      						<i class='fa fa-check'></i>

			      					<?php } else { ?>

			      						<a href="engine.php?f=filter-prodi-mhs&val=<?= $i ?>&uri=<?= $URL ?>">
			      							<?php 
				      							if ( $i == 1 ) {
				      								echo "S1-Manajemen";
				      							} elseif ( $i == 2 ) {
				      								echo "S1-Akuntansi";
				      							} else {
				      								echo "S2-Manajemen";
				      							}
				      						?>
			      						</a>

			      					<?php } ?>

			      				</li>
			      			<?php } ?>
			      		</ul>
			      </div>
			    </div>
			  </div>

			  <!-- TA -->
			  <div class="panel panel-default">
			    <div class="panel-heading" role="tab" id="heading2">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
			          Tahun Akademik 
			        </a>

			        <?php if ( isset( $_SESSION['ta_mhs']) ) { ?>
			        	<a href="engine.php?f=reset-filter-ta" class="pull-right" title="Reset Filter"><i class="fa fa-filter"></i></a>
			      	<?php } ?>

			      </h4>
			    </div>
			    <div id="collapse2" class="panel-collapse collapse <?php echo isset($_SESSION['ta_mhs']) ? "in":"";?>" role="tabpanel" aria-labelledby="heading2">
			      <div class="panel-body">
			      	<ul>
			      		<?php for ( $i = date('Y')-4; $i <= date('Y'); $i++ ) { ?>
			      			<?php for ( $ii = 1; $ii <= 2; $ii++ ) { ?>
			      			<li>
			      				<a href="engine.php?f=filter-ta-mhs&val=<?= $i."-".$ii ?>&uri=<?= $URL ?>"><?php echo $i."-".$ii ?>
			      					
			      					<?php if ( isset( $_SESSION['ta_mhs']) && $i."-".$ii == $_SESSION['ta_mhs']) { ?>
			      						<i class="fa fa-check"></i>
			      					<?php } ?>

			      				</a>
			      			</li>
			      			<?php } ?>
			      		<?php } ?>

			      	</ul>
			      </div>
			    </div>
			  </div>


			  <!-- STATUS -->
			  <div class="panel panel-default">
			    <div class="panel-heading" role="tab" id="heading3">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
			          Status
			        </a>

			        <?php if ( isset( $_SESSION['status_mhs']) ) { ?>
			        	<a href="engine.php?f=reset-filter-status" class="pull-right" title="Reset Filter"><i class="fa fa-filter"></i></a>
			      	<?php } ?>

			      </h4>
			    </div>
			    <div id="collapse3" class="panel-collapse collapse <?php echo isset($_SESSION['status_mhs']) ? "in":"";?>" role="tabpanel" aria-labelledby="heading3">
			      <div class="panel-body">
			      	<ul>
			      		<li><a href="engine.php?f=filter-status-mhs&val=AKTIF&uri=<?= $URL ?>">AKTIF</a> <?php echo isset( $_SESSION['status_mhs'] ) && $_SESSION['status_mhs'] == "AKTIF" ? "<i class='fa fa-check'></i>":"";?></li>
			      		<li><a href="engine.php?f=filter-status-mhs&val=NON-AKTIF&uri=<?= $URL ?>">NON-AKTIF</a> <?php echo isset( $_SESSION['status_mhs'] ) && $_SESSION['status_mhs'] == "NON-AKTIF" ? "<i class='fa fa-check'></i>":"";?></li>
			      		<li><a href="engine.php?f=filter-status-mhs&val=LULUS&uri=<?= $URL ?>">LULUS</a> <?php echo isset( $_SESSION['status_mhs'] ) && $_SESSION['status_mhs'] == "LULUS" ? "<i class='fa fa-check'></i>":"";?></li>
			      		<li><a href="engine.php?f=filter-status-mhs&val=CUTI&uri=<?= $URL ?>">CUTI</a> <?php echo isset( $_SESSION['status_mhs'] ) && $_SESSION['status_mhs'] == "CUTI" ? "<i class='fa fa-check'></i>":"";?></li>
			      		<li><a href="engine.php?f=filter-status-mhs&val=DO&uri=<?= $URL ?>">DO</a> <?php echo isset( $_SESSION['status_mhs'] ) && $_SESSION['status_mhs'] == "DO" ? "<i class='fa fa-check'></i>":"";?></li>
			      	</ul>
			      </div>
			    </div>
			  </div>

			</div>

		</div>

		<div class="col-md-8">
			
			<div id="cari-mhs"></div>

			<div id="show-mhs">

				<b>Total data: <?= $jml_data ?></b>
				<table class="table table-hover table-bordered">
					<tr>
						<th width="15">No</th>
						<th>NIM</th>
						<th>Tahun</th>
						<th>Jenis Semester</th>
						<th>Semester</th>
						<th width="150">Status</th>
					</tr>
					
					<form method="post" action="engine.php?f=update-mhs" id="form-edit">
					
						<input type="hidden" name="URL" value="<?php echo basename($_SERVER['REQUEST_URI']); ?>">

						<?php
						
						$sql = $conn->query($query);

						if ( $sql->num_rows > 0 ) {

							$no = 1+$posisi;
							while ( $r = $sql->fetch_assoc() ) { ?>

								<input type="hidden" name="nim[]" value="<?php echo $r['NIM'] ?>">
								<input type="hidden" name="tahun[]" value="<?php echo $r['TAHUN'] ?>">
								<input type="hidden" name="semester[]" value="<?php echo $r['SMSTER'] ?>">
								<tr>
									<td><?php echo $no ?></td>
									<td><?php echo $r['NIM'] ?></td>
									<td><?php echo $r['TAHUN'] ?></td>
									<td><?php echo $r['SMSTER'] ?></td>
									<td><?php echo $r['SMSTR'] ?></td>
									<td>

										<select name="status[]" class="form-control">

											<option value="AKTIF" <?php echo trim($r['KET']) == "AKTIF" ? "selected":"";?>>AKTIF</option>
											<option value="NON-AKTIF" <?php echo trim($r['KET']) == "NON-AKTIF" ? "selected":"";?>>NON-AKTIF</option>
											<option value="LULUS" <?php echo trim($r['KET']) == "LULUS" ? "selected":"";?>>LULUS</option>
											<option value="DO" <?php echo trim($r['KET']) == "DO" ? "selected":"";?>>DO</option>
											<option value="CUTI" <?php echo trim($r['KET']) == "CUTI" ? "selected":"";?>>CUTI</option>
										
										</select>

									</td>

								</tr>

							<?php $no++; } ?> 

						<?php } else { ?>

							<tr><td colspan="6">Tidak menemukan data</td></tr>
							<script>$(".submit").hide();</script>

						<?php } ?>
						
					</form>

				</table>

				<?php

					$jml_hal = $pg->jumlah_halaman($jml_data,$batas);
					$navigasi = $pg->nav_halaman($_GET['hal'],$jml_hal,"http://localhost/nobel-report/mahasiswa.php");

					if ( $jml_data > $jml_hal ) {

						 echo "<ul class='pagination'>$navigasi</ul>";

					}

				?>
			</div>

			<!-- <button class="submit btn btn-info btn-sm pull-right">Simpan</button> -->

		</div>

	</div>

</div>

<br />
<br />

<script>
	$(function(){

		$(".submit").click(function(){
			$("#form-edit").submit();
		});

	});

	function cari_mhs(val){

		if ( val != "" ) {
			$("#show-mhs").hide();
			$.post('cari-mhs.php',{val:val},function(data){
				$("#cari-mhs").html(data);
			});

		} else {

			$("#show-mhs").show();
			$("#cari-mhs").html('');
			$(".submit").show();

		}
	}
</script>



</body>
</html>