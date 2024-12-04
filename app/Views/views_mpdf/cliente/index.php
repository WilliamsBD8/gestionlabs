<?php

require_once('../vendor/autoload.php');

require_once('plantillas/reporte/index.php');

$css = file_get_contents('plantillas/reporte/styles.css');

require_once('funciones.php');
require_once('f_certificados.php');

$certificados = $_POST['certificado'];

if (isset($certificados)) {
	$archivo = 'certificados_amc.rar';
	$zip = new \ZipArchive();

	foreach($certificados as $certificado){
		$mpdf = new \Mpdf\Mpdf([]);
		$certificado_nro = $certificado; //183342;
		$frm_plantilla= 1;
		// $frm_plantilla= $_POST['plantilla'] ;
		$frm_form_valo = 1; //tipo de formateo de la plantilla

		$frm_mensaje_resultado = 0; // cero para cuando venga de creacion de construccion de documento
		$frm_mensaje_observacion = 1;
		$frm_mensaje_firma = 5;
		$frm_id_procedencia = 1; // 0 PRELIMINAR - 1 REPORTE DE ENSAYO


		$plantilla = getPlantilla($certificado_nro,$frm_plantilla, $frm_form_valo,$frm_mensaje_resultado,$frm_mensaje_observacion, $frm_mensaje_firma, $frm_id_procedencia);

		$mpdf->showImageErrors = true;

		$mpdf->writeHtml($css , \Mpdf\HTMLParserMode::HEADER_CSS);
		$mpdf->writeHtml($plantilla , \Mpdf\HTMLParserMode::HTML_BODY);

		$name = 'Certificado_'.$certificado.'.pdf';

		if(count($certificados) == 1){
			$mpdf->Output($name, 'D');
			exit;
		}

		$mpdf->Output($name,'F');

		$zip->open($archivo, \ZIPARCHIVE::CREATE);
		$zip->addFile($name, $name);
		$zip->close();
		unlink($name);
	}
	header("Content-type: application/octet-stream");
    header("Content-disposition: attachment; filename=$archivo");
    readfile($archivo);
    unlink($archivo);
}else{
	$refer = $_SERVER['HTTP_REFERER'];
	header("Location: $refer");
}

