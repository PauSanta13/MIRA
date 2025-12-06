<?php
/** 301 Redirects maps
 * Redirect only when a page is not found,
 * If a page below is created it will stop being redirected
 * */

/* Obtain the current requested url */
$key = (string)$_SERVER["REQUEST_URI"];
if(!empty($key)) {

    /* Remove GET parameters */
    $key = strtok($key, '?');
    
    /* Clean and secure special chars */
    $key = preg_replace("/[^\w\-\.\/]/", '',  $key);
    /* Remove slashes (/) at the beginning and end */
    $key = trim($key,' /');

    $map = array(
        'clients' => 	'/our-clients/',
        'psicologia-salud-dolos-madrid' => 	'/psicologia-salud-dolor-madrid/',
    );

    if(isset($map[$key])) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$map[$key]);
        exit();
    }

}