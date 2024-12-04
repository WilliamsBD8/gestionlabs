<?php 

    switch ($muestra->mue_sigla) {
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

	
	$aux_fecha_descarte = $muestra->mue_fecha_muestreo;
	$aux_fecha_descarte = strtotime ( '+'.$aux_dia_descarte.' day' , strtotime ( $aux_fecha_descarte ) ) ;
	$aux_fecha_descarte = date ( 'Y-m-d' , $aux_fecha_descarte );

  $parametros = [];
  $aux_param = [];
  $aux_number = 0;
  foreach ($muestra->parametros as $key => $parametro){
    if($key%5 == 0){
      $aux_param = [];
      $aux_number++;
    }
    array_push($aux_param, $parametro);
    $parametros[$aux_number] = $aux_param;
  }
?>
<body style="background: rgb(51, 51, 51); display: flex; justify-content: center;">
	<div style="width: 800px;background: white;">
    <table class="analisis">
      <thead>
        <tr>
          <th colspan="5"><strong>Producto:</strong>  <?=$muestra->mue_identificacion;?></th> <!-- <tr><?=$muestra->pro_nombre?> / </tr> -->
        </tr>
        <!-- <tr>
          <th colspan="5">Rotulación de muestras para análisis</th>
        </tr> -->
      </thead>
      <tbody>
        <tr>
          <td><b>Codigo:</b></td>
          <td><?= $codigo ?></td>
          <td><b>Fecha de ingreso</b></td>
          <td><?=substr($muestra->mue_fecha_muestreo,0,10);?></td>
          <td rowspan="4" class="parametros">
            <?php foreach ($parametros as $key => $parametro):?>
              <div class="div">
                <ul>
                  <?php foreach ($parametro as $key => $value): ?>
                    <li><span><?= $value->parametro ?></span></li>
                  <?php endforeach ?>
                </ul>
              </div>
            <?php endforeach?>
          </td>
        </tr>
        <tr>
          <td><b>Dilución</b></td>
          <td colspan="3"><?=$muestra->mue_dilucion;?></td>
          <!--<td><b>Tipo</b></td>-->
        <!--<td colspan="1"><?=$muestra->duplicado == 0 ? 'Primera muestra':'Duplicado '.$muestra->duplicado;?></td>-->
        </tr>
        <?php if(!empty($muestra->fechasUtiles)): ?>
          <tr>
              <td><b>Fechas vidas &uacute;tiles</b></td>
              <td colspan="3">
                  <div class="div">
                    <ul>
                      <?php foreach ($muestra->fechasUtiles as $key => $fecha): ?>
                        <li><span><?= "[{$fecha->dia}] - $fecha->fecha" ?></span></li>
                      <?php endforeach ?>
                    </ul>
                  </div>
              </td>
          </tr>
        <?php endif ?>
      </tbody>
    </table>
    <!-- <table>
      <thead>
        <tr class="parametro">
          <th colspan="3">Observaciones</th>
          <th>Tipo de analisis</th>
          <th>Producto</th>
          <th>Identificacion</th>
          <th>Cantidad</th>
          <th>Empaque</th>
          <th>Unidad medida</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="3"><?= $muestra->mue_adicional ?></td>
          <td><?= $muestra->mue_nombre ?> | <?= $muestra->mue_sigla ?></td>
          <td><?= $muestra->pro_nombre ?></td>
          <td><?= $muestra->mue_identificacion ?></td>
          <td><?= $muestra->mue_cantidad ?></td>
          <td> Empaque </td>
          <td><?= $muestra->mue_unidad_medida ?></td>
        </tr>
        <tr>
          <td colspan="3"><strong>Parametro:</strong></td>
          <td colspan="3"><strong>Descripcion:</strong></td>
          <td colspan="3"><strong>Tecnica:</strong></td>
        </tr>
        <?php foreach ($muestra->parametros as $key => $parametro): ?>
          <tr>
            <td colspan="3"><?= $parametro->parametro ?></td>
            <td colspan="3"><?= $parametro->parametro_descripcion ?></td>
            <td colspan="3"><?= $parametro->tecnica ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table> -->
  </div>
</body>