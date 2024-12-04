<?php 

    switch ($muestreo_tipo->mue_sigla) {
		case 'MA':
			$aux_dia_descarte=8;
			break;
		case 'FA':
		case 'FM':
		case 'MM':
			$aux_dia_descarte=15;
			break;
		case 'MP':
			$aux_dia_descarte=1;
			break;			
	}

	
	$aux_fecha_descarte = $muestreo->mue_fecha_muestreo;
	$aux_fecha_descarte = strtotime ( '+'.$aux_dia_descarte.' day' , strtotime ( $aux_fecha_descarte ) ) ;
	$aux_fecha_descarte = date ( 'Y-m-d' , $aux_fecha_descarte );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<style type="text/css">
<!--
.Estilo4 {
color: #000000;
font-size: 20px;
font-family: Verdana, Arial, Helvetica, sans-serif
}
.Estilo5 {
color: #000000;
font-size: 14px;
font-family: Verdana, Arial, Helvetica, sans-serif
}
.Estilo6 {
color: #000000;
font-size: 12px;
font-family: Verdana, Arial, Helvetica, sans-serif
}
.Estilo7 {
color: #000000;
font-size: 10px;
font-family: Verdana, Arial, Helvetica, sans-serif
}


-->
</style>
</head>
<body>
<div id="campo_imprimir">
    <table class="Estilo4" width="800"  border="1" cellspacing="1" cellpadding="0">
    <tr>
        <td align="center"  colspan="8">
            <b>Rotulaci&oacute;n de muestras para an&aacute;lisis</b>
	</td>
    </tr>
    <tr>
        <td colspan="2" width="25%">Codigo</td>
        <td colspan="2" width="25%"><?= $codigo;?></td>
        <td colspan="2" width="25%">Fecha de Ingreso</td>
        <td colspan="2" width="25%"><?=substr($muestreo->mue_fecha_muestreo,0,10);?></td>
    </tr>
    <tr>
        <td colspan="2">Tipo de muestra</td>
        <td colspan="2"><?=$muestreo_tipo->mue_sigla ;?></td>
        <td colspan="2">Hora ingreso</td>
        <td colspan="2"><?=substr($muestreo->mue_fecha_muestreo,10);?></td>
    </tr>
    <tr>
        <td colspan="2">Norma</td>
        <td colspan="2"><?=$norma->nor_nombre;?></td>
        <!-- <td colspan="2"><?=$producto->pro_nombre ;?></td>-->
        <td colspan="2">Fecha descarte</td>
        <td colspan="2"><?=substr($aux_fecha_descarte,0,10);?></td>
    </tr>
    <tr>
        <td colspan="2">Producto</td>
        <!--<td colspan="6"><?=$producto->pro_nombre ;?></td>-->
        <td colspan="6"><?=$producto->pro_nombre ;?> / <?=$muestreo_detalle->mue_identificacion;?></td>
    </tr>
    <tr>
        <td align="center" colspan="8">
            <b>An&aacute;lisis a realizar</b>
	</td>
    </tr>
    <?php
    	$columna=0;
        $fila=0;
        foreach ($ensayos as $ensayo){
            
			$parametro  =   procesar_registro_fetch('parametro', 'id_parametro',$ensayo->id_parametro);
            $seleccion  =   procesar_registro_fetch('ensayo_vs_muestra', 'id_muestra',$muestreo_detalle->id_muestra_detalle, 'id_ensayo', $ensayo->id_ensayo);
            
            $aux_selec = ($seleccion[0]->id_ensayo)?'x':'o';
            if($seleccion[0]->id_ensayo){
				
            if($columna==0){
                echo '<tr>';
            }
            
			echo '<td align="center"> '.substr($parametro[0]->par_nombre, 0,10).'</td><td align="center">'.$aux_selec.'</td>';	
			
            
            
            $columna++;
            if($columna==4){
                echo '</tr>';
                $fila++;
                $columna=0;
            }
            }
        }  
            
        for($fila;$fila<6;$fila++){
            for($i=$columna;$i<=3;$i++){
                if($columna==0){
                echo '<tr>';
                }
                //echo '<td>'.$columna.'_</td><td>'.$fila.'_</td>';
                echo '<td>&nbsp;</td><td>&nbsp;</td>';
                $columna++;
                if($columna==4){
                    echo '</tr>';                                   
                    $columna=0;
                }
            }
            
        }
    
    
    
    ?> -->
    
    <tr>
        
        <td colspan="8">Adicional:<?=$muestreo_detalle->mue_adicional;?></td>
    </tr>
</table>
 </div>
</html>