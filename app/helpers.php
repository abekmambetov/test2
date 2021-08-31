<?php //app/helpers.php

if (!function_exists('getUrl')) {

    function getUrl($url){
        (function_exists('curl_init')) ? '' : die('cURL не установлен');

        $ch = curl_init();

        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CrawlBot/1.0.0)',
            CURLOPT_POST => true,
        );

        curl_setopt_array( $ch, $options );
        $response = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ( $httpCode != 200 ) {
            return array('response' => '', 'httpCode' => $httpCode, 'request_method' => 'POST');    
        } else {
            return array('response' => $response, 'httpCode' => $httpCode, 'request_method' => 'POST');   
        }

        curl_close($ch);

          
    }
}
