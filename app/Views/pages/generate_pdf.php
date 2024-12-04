<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head> -->

<body style="background: rgb(51, 51, 51); display: flex; justify-content: center;">
    <div style="width: 800px;background: white; padding: 20px 40px;">
        
    
    <table class="table" style="width: 100%;">
    <tr>
      <th style="width: 25%">
        <img src="assets/img/logo_1.png" width="150">
      </th>
      <th>
        <div id="amc-header">
                        <br>
                        <strong>Reporte de Ensayo</strong><br>
                </div>
      </th>
      <th>
        <div id="amc-header">
                        <br>
                        <strong>AMC - EI -FT - 01 <br> Versi&oacute;n 03</strong><br>
                </div>
      </th>
          
    </tr>
    </table>
    
    
    <table width="100%">
            <tr>
        <td colspan="2">
          <div id="amc-header">     <br>                   
                        <strong>REPORTE DE ENSAYO No. AMC - <?= $value ?></strong>
          </div><br>
        </td>       
      <tr>
      <tr>
        <td class="vertical-align-top" style="width: 50%;">
                    <table>
                        <tr>
                            <td><b>CLIENTE:</b></td>
                            <td>1033796211</td>
                        </tr>
                        <tr>
                            <td><b>DIRIGIDO A:</b></td>
                            <td>Wilson Andres Bachiller Ortiz</td>
                        </tr>
            <tr>
                            <td><b>DIRECCIÓN:</b></td>
                            <td> Av. 68 Nº 49 A – 47 Bogotá</td>
                        </tr>
            <tr>
              <td><b>TELÉFAX:</b></td>
              <td>300 304 77 77</td>                        
                        </tr>
            <tr>
                            <td><b>MAIL:</b></td>
                            <td> amc@amc-laboratorios.com</td>
                        </tr>
                        
                    </table>
                </td>
        <td class="vertical-align-top" style="width: 50%;">
                    <table>
                        <tr>
                            <td><b>PUNTO DE TOMA DE MUESTRA:</b></td>
                            <td>Bodega 34</td>
                        </tr>
                        <tr>
                            <td><b>RESPONSABLE DE TOMA DE MUESTRA:</b></td>
                            <td>Wilson Andres Bachiller Ortiz</td>
                        </tr> 
            <tr>
              <td><b>FECHA TOMA DE MUESTRA/HORA:</b></td>
              <td> 2021-05-26 08:05</td>
            </tr> 
                        <tr>
                            <td><b>FECHA DE RECEPCIÓN</b></td>
                            <td>2021-05-26 08:05</td>
                        </tr>
                        <tr>
                            <td><b>FECHA ANÁLISIS:</b></td>
                            <td> 2021-05-25</td>
                        </tr>
                        <tr>
                            <td><b>FECHA DE INFORME:</b></td>
                            <td> 2021-05-26</td>
                        </tr>
                        <tr>
                            <td><b>MÉTODO DE TOMA DE MUESTRA:</b></td>
                            <td>Micro Alimentos</td>
                        </tr>                       
                    </table>
                </td>
      </tr>
    </table>
    
    <br>
    <!-- IDENTIFICACIÓN DE LA MUESTRA -->
    <table class="table" style="width: 100%;">
            <thead>
        <tr>
          <th colspan="5">
            <div id="amc-header2">                        
                        <strong>IDENTIFICACIÓN DE LA MUESTRA</strong><br>
            </div>
          </th>
        </tr>
                <tr>
                    <th class="text-center">No. Muestra</th>
                    <th class="text-center">Identificación</th>
                    <th class="text-center">Cod. AMC</th>
                    <th class="text-center">Tipo de muestra</th>
          <th class="text-center">Estado / Área / Función</th>
                </tr>
            </thead>
            <tbody>
        <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">Cucharon /  Antes</td>
                    <td class="text-center"> 21-05-0000</td>
                    <td class="text-center">Frotis de Superfcie Limpia</td>
          <td class="text-center">Cocina fría</td>
                </tr>
        <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">Cucharon /  Despues</td>
                    <td class="text-center"> 21-05-0000</td>
                    <td class="text-center">Frotis de Superfcie Limpia</td>
          <td class="text-center">Cocina fría</td>
                </tr>
      </tbody>
    </table>
    
    <!-- Tabla de resultados -->
    
    <table class="table" style="width: 100%;">
        <thead>
            <tr>
              <th colspan="7">
                <div id="amc-header2">                        
                            <strong>TABLA DE RESULTADOS</strong><br>
                </div>
              </th>
            </tr>
            <tr>
                <th class="text-center" rowspan="2">ENSAYO/ MÉTODO</th>
                <th class="text-center" colspan="2">RESULTADOS</th>         
                <th class="text-center" rowspan="2">UNIDADES</th>
                <th class="text-center" style="font-size: 12px;" rowspan="2">µ</th>
                <th class="text-center" rowspan="2">REGLA</th>
                <th class="text-center" >ESPECIFICACIÓN</th>
            </tr>
            <tr>
                <th class="text-center">Cucharon / Antes</th>
                <th class="text-center">Cucharon / Despues</th>
                <th class="text-center">INVIMA Cafe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                    <td class="text-center">Coliformes Totales (Ausencia/Presencia / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center">4.020</td>
          <td class="text-center">4.020</td>
                    <td class="text-center"> 210gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">Regla 1</td>
          <td class="text-center">20.000 - 50.000</td>
                </tr>
        <tr>
                    <td class="text-center">Coliformes Fecales (Ausencia/Presencia / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center">3.6</td>
          <td class="text-center"><3</td>
                    <td class="text-center"> 20gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">Regla 2</td>
          <td class="text-center">29</td>
                </tr>
        <tr>
                    <td class="text-center">Mohos (M) y Levaduras (L) (UFC / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center"><3</td>
          <td class="text-center"><3</td>
                    <td class="text-center"> 20gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">No aplica</td>
          <td class="text-center">29</td>
                </tr>
        <tr>
                    <td class="text-center">Coliformes Totales (Ausencia/Presencia / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center">4.020</td>
          <td class="text-center">4.020</td>
                    <td class="text-center"> 210gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">Regla 1</td>
          <td class="text-center">20.000 - 50.000</td>
                </tr>
        <tr>
                    <td class="text-center">Coliformes Fecales (Ausencia/Presencia / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center">3.6</td>
          <td class="text-center"><3</td>
                    <td class="text-center"> 20gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">Regla 2</td>
          <td class="text-center">29</td>
                </tr>
        <tr>
                    <td class="text-center">Mohos (M) y Levaduras (L) (UFC / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center"><3</td>
          <td class="text-center"><3</td>
                    <td class="text-center"> 20gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">No aplica</td>
          <td class="text-center">29</td>
                </tr>
        <tr>
                    <td class="text-center">Coliformes Totales (Ausencia/Presencia / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center">4.020</td>
          <td class="text-center">4.020</td>
                    <td class="text-center"> 210gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">Regla 1</td>
          <td class="text-center">20.000 - 50.000</td>
                </tr>
        <tr>
                    <td class="text-center">Coliformes Fecales (Ausencia/Presencia / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center">3.6</td>
          <td class="text-center"><3</td>
                    <td class="text-center"> 20gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">Regla 2</td>
          <td class="text-center">29</td>
                </tr>
        <tr>
                    <td class="text-center">Mohos (M) y Levaduras (L) (UFC / und ó 100 cm) NTC  5230:2017</td>
                    <td class="text-center"><3</td>
          <td class="text-center"><3</td>
                    <td class="text-center"> 20gr</td>
                    <td class="text-center">+-1</td>
          <td class="text-center">No aplica</td>
          <td class="text-center">29</td>
                </tr>
      </tbody>
    </table>
    <br>
    <div id="amc-header2">                        
                        <strong>El frotis de manos CUMPLE con los parámetros establecidos </strong><br>
            </div>
    
              
        <br>
        <div class="summarys">
            <div class="text-word" id="note">
        <p><strong>Observaciones:</strong></p>
        <p style="font-style: italic; font-size: 10px">
        <br><b>µ </b> = incertidumbre expandida al valor reportado con un factor de cobertura de k=2, para un intervalo de confianza de aproximadamente el 95%
        <br><b>Regla 1 </b>= El resultado obtenido frente a los límites de especificación para dar cumplimiento, NO está influenciado por la incertidumbre del ensayo.
        <br><b>Regla 2 </b>= El resultado obtenido frente a los límites de especificación para dar cumplimiento, está influenciado por la incertidumbre del ensayos

        </p>
           
        <br>  
        
                <p style="font-style: italic; font-size: 10px">
        Solo se puede hacer reproducción parcial o total de este certificado con previa autorización de AMC Análisis de Colombia Ltda.
        <br>El resultado es válido únicamente para la muestra analizada.
        </p>      
        
                <br>
            </div>
        </div>
    <br>
  
    <div id="amc-confirma">                        
      * Confirme la validez de este documento ingresando a www.amc-laboratorios.com y el codigo <strong>UeDaH3QeCWy3<strong>
      <br><strong>AMC Análisis de Colombia Ltda </strong>
      <br>
    </div>
    <table width="100%">
            <tr>
        <td >
          <img src="assets/img/firma.jpg" width="100">
        </td>
        <td >
          <img src="assets/img/firma.jpg" width="100">
        </td>       
      <tr>
      <tr>
        <td >
          Leonardo Espinel Mesa
          <br><strong>Director Técnico</strong>
        </td>
        <td >
          
          <br><strong>Lider de Microbiología</strong>
        </td>       
      <tr>
    </table>      
    <br>
    
    
        
    <div id="amc-header2">                        
                        <strong> - FIN DE INFORME - </strong><br>
    </div>
    
  
            
        
    </div>
</body>

<!-- </html> -->