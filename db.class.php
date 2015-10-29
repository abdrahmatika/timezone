<?php

class DB_Class {
	
	protected $CON;
	
	public function __construct( $conn ) {
		$this->CON = $conn;
	}
	
	public function get_all( $nm_tabel, $pre = '' ) {
		$sql = $this->CON->query("SELECT * FROM " . $nm_tabel . " $pre");
		return $sql;
	}
	
	public function get_id( $nm_tabel, $Primary_tabel = '', $id, $pre = '' ) {
		$sql = $this->CON->query("SELECT * FROM " . $nm_tabel . " where " . $Primary_tabel . "='$id' $pre");
		
		if ( $sql->num_rows > 0 ) {
			return $sql->fetch_assoc();
		} else {
			return "Tidak Menemukan Data";
		}
	}
	
	public function get_field( $nm_tabel, $Primary_tabel, $id, $field ) {
		$sql = $this->CON->query("SELECT $field FROM " . $nm_tabel . " where " . $Primary_tabel . "='$id'");
		
		if ( $sql->num_rows > 0 ) {
			$r = $sql->fetch_assoc();
			return $r[$field];
		} else {
			return false;
		}
	}	
	
	public function get_count( $nm_tabel, $pre = '' ){
		$sql = $this->CON->query("SELECT COUNT(*) AS jml FROM " . $nm_tabel . " $pre");
		$r = $sql->fetch_assoc();
		return $r['jml'];
	}

	/* 
	 * CUD Method
	*/
	public function insert( $nm_tabel, $data ){

		$sql = "INSERT INTO " . $nm_tabel . "(";

		$no = 1;
		foreach( $data as $key=>$val ){
			if ( $no != count($data) ) {
				$sql .= $key . ",";
			} else {
				$sql .= $key . ")";
			}
		$no++;
		}

		$sql .= " VALUES(";

		$no = 1;
		foreach( $data as $key=>$val ){
			if ( $no != count($data) ) {
				$sql .= "'" . $val . "',";
			} else {
				$sql .= "'" . $val . "')";
			}
		$no++;
		}

		$query = $this->CON->query( $sql );

		return ( $query ) ? true : "Gagal.!!! " . $this->CON->error;
	}
	public function update( $nm_tabel, $Primary_tabel, $data, $id ){

		$sql = "UPDATE " . $nm_tabel . " SET ";

		$no = 1;
		foreach( $data as $key=>$val ){
			if ( $no != count($data) ) {
				$sql .= $key . "='" . $val . "',";
			} else {
				$sql .= $key. "='" . $val . "'";
			}
		$no++;
		}

		$sql .= " WHERE " . $Primary_tabel . "='$id'";

		$query = $this->CON->query( $sql );

		return ( $query ) ? true : "Gagal.!!! " . $this->CON->error;
	}
	public function delete( $nm_tabel, $Primary_tabel, $id ){
		
		$sql = $this->CON->query("DELETE FROM {$nm_tabel} WHERE {$Primary_tabel} ='$id'");
		return ( $sql ) ? true : "Gagal.!!! " . $this->CON->error;
	}
}