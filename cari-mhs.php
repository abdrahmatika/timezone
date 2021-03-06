<?php

include "config.php";

$val = @trim($_POST['val']);

$query = "SELECT NIM,TAHUN,SMSTER,SMSTR,KET FROM t_mhs_status WHERE left(NIM,4) > '2008' AND NIM LIKE '%$val%' GROUP BY NIM ORDER BY NIM ASC,SMSTR DESC LIMIT 10"; ?>
	
	<table class="table table-hover table-bordered">
		<tr>
			<th width="15">No</th>
			<th>NIM</th>
			<th>Tahun</th>
			<th>Jenis Semester</th>
			<th>Semester</th>
			<th width="150">Status</th>
		</tr>

			<?php

			$sql = $conn->query($query);

			if ( $sql->num_rows > 0 ) {

				$no = 1;
				while ( $r = $sql->fetch_assoc() ) { ?>

					<tr style="cursor:pointer" 
						data-target="#edit-data" 
						data-toggle="modal"
						data-nim="<?php echo $r['NIM'] ?>"
						data-semester="<?php echo $r['SMSTER'] ?>"
						data-tahun="<?php echo $r['TAHUN'] ?>"
						data-status="<?php echo $r['KET'] ?>">

						<td><?php echo $no ?></td>
						<td><?php echo $r['NIM'] ?></td>
						<td><?php echo $r['TAHUN'] ?></td>
						<td><?php echo $r['SMSTER'] ?></td>
						<td><?php echo $r['SMSTR'] ?></td>
						<td><?php echo $r['KET'] ?></td>

					</tr>

				<?php $no++; } ?> 
					
					<script>$(".submit").show();</script>

			<?php } else { ?>

				<tr><td colspan="6">Tidak menemukan data</td></tr>
				<script>$(".submit").hide();</script>

			<?php } ?>
			
		</form>

	</table>
<div id="edit-data" data-backdrop="" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">

			<form action="engine.php?f=update-mhs" method="post" class="form-horizontal">

				<input type="hidden" name="URL" value="<?php echo basename($_SERVER['REQUEST_URI']) ?>">
				<input type="hidden" name="nim" id="nim" value="">
				<input type="hidden" name="tahun" id="tahun" value="">
				<input type="hidden" name="semester" id="semester" value="">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="title">Sunting Status Mahasiswa</h4>
				</div>

				<div class="modal-body">

			      <div class="form-group">
				    <label class="col-sm-2 control-label">NIM</label>
				    <div class="col-sm-10">
				      <input type="text" class="form-control" id="nim2" value="" disabled>
				    </div>
				  </div>

			      <div class="form-group">
				    <label class="col-sm-2 control-label">Tahun</label>
				    <div class="col-sm-10">
				      <input type="text" class="form-control" id="tahun2" value="" disabled>
				    </div>
				  </div>

				  <div class="form-group">
				    <label class="col-sm-2 control-label">Semester</label>
				    <div class="col-sm-10">
				      <input type="text" class="form-control" id="semester2" value="" disabled>
				    </div>
				  </div>

				  <div class="form-group">

				  	<label class="col-sm-2 control-label">Status</label>
				  	<div class="col-sm-10">
						<select name="status" id="status" class="form-control">
															
							<option value="AKTIF">AKTIF</option>
							<option value="NON-AKTIF">NON-AKTIF</option>
							<option value="LULUS">LULUS</option>
							<option value="DO">DO</option>
							<option value="CUTI">CUTI</option>

						</select>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-info">Simpan</button>
					<button type="button" data-dismiss="modal" class="btn btn-default">Batal</button>
				</div>

			</form>

		</div>
	</div>
</div>
<script>
	$(function(){

		$('#edit-data').on('show.bs.modal', function (event) {
			var div = $(event.relatedTarget) // Tombol dimana modal di tampilkan

			var nim 	= div.data('nim');
			var nama 	= div.data('nama');
			var tahun 	= div.data('tahun');
			var status 	= div.data('status');
			var semester = div.data('semester');

			var modal 	= $(this)

			// Isi nilai pada field
			modal.find('#nim').attr("value",nim);
			modal.find('#nim2').attr("value",nim);
			modal.find('#nama').attr("value",nama);
			modal.find('#nama2').attr("value",nama);
			modal.find('#tahun').attr("value",tahun);
			modal.find('#tahun2').attr("value",tahun);
			modal.find('#semester').attr("value",semester);
			modal.find('#semester2').attr("value",semester);
			modal.find('#status').attr("value",status);

			// Membuat combobox terpilih berdasarkan jenis kelamin yg tersimpan saat pengeditan
			modal.find('#status option').each(function(){

				  if ($(this).val() == status.trim() ) {
				    $(this).attr("selected","selected");
				  }
			});

		});

	});

</script>