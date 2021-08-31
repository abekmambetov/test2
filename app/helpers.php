<?php //app/helpers.php

//if (!function_exist(function_name: 'getUrl')) {

    function getUrl($url){
        (function_exists('curl_init')) ? '' : die('cURL Must be installed for geturl function to work. Ask your host to enable it or uncomment extension=php_curl.dll in php.ini');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CrawlBot/1.0.0)');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    
        curl_setopt($curl, CURLOPT_POST, true);

        $html = curl_exec($curl);
        $status = curl_getinfo($curl);

        curl_close($curl);

        return array('html' => $html, 'status' => $status, 'request_method' => 'POST');     
    }
//}