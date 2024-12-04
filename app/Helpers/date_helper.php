<?php
	function date_fecha($date){
		$meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		$dias = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
		$date = strtotime($date);
		$date = $dias[date('w')].' '.date('d', $date).' de '.$meses[(date("m", $date)-1)].' del '.date("Y", $date);
		return $date;
	}

	function date_certificados(){
		$date = '2021-10-15 00:00:00';
		return $date;
	}
?>