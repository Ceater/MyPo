<?php
function makeRequest($endpoint, $options = []){
    $res    = new \GuzzleHttp\Client();
    $res    = $res->request('POST', $endpoint, $options);
    return $res;
}
?>
