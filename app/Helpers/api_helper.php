<?php 
	function API ($ruta){
        $url = 'https://tecnoneeds.com/api_AMC/public/'.$ruta;
        // $url = 'https://amc-laboratorios.xyz/api/public/'.$ruta;
        // $url = 'http://localhost:8080/Wilfredo/amc_analisis_cpanel/api/public/'.$ruta;
        return $url;
    }
    function API_POST($url, $data){

        $postdata = http_build_query(
            $data
        );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        $result = json_decode($result);

        return $result;

    }