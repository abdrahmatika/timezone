<?php

include "config.php";
echo dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'server.sett.php';
echo dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'smilies'.DIRECTORY_SEPARATOR.'config.php';
function temukan_array_ganda( $arr ) {
	$hide_ganda = array_unique( array_map("strtoupper", $arr) );
	$duplicates = array_diff($arr, $hide_ganda);
	return $duplicates;
}


				$query = "SELECT NIM FROM `t_mhs` WHERE left(NIM,4) = '2013' LIMIT 1";

				$sql = $conn->query($query);

				while ( $r = $sql->fetch_assoc() ) {

					$sql2 = $conn->query("SELECT NIM,KODEMTK,NAMAMTK FROM `t_krs_sementara` WHERE NIM='2013221192' AND NAMAMTK!=''");
					
					if ( $sql2->num_rows > 0 ) {

						while ( $r2 = $sql2->fetch_assoc() ) {

							$data[trim($r2['KODEMTK'])] = trim($r2['NAMAMTK']);

						}
$arr = array_not_unique($data);
print_r($arr);
					}

					
					// print_r($data);

				} ?>

			</table>

		</div>

	</div>

</div>

<table border="1">
<tr>
	<td>no</td>
	<td>2</td>
	<td>3</td>
	<td>3</td>
</tr>

<tr>
	<td rowspan="3">1</td>
	<td>5</td>
	<td>6</td>
	<td>6</td>
</tr>

<tr>
	<td>51</td>
	<td>61</td>
	<td>61</td>
</tr>

<tr>
	<td>51</td>
	<td>61</td>
	<td>61</td>
</tr>

</table>
</body>
</html>
<?php






	

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

