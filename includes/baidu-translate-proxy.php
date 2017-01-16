<?php
function translate_from_baidu ($text, $start, $end, $from = 'zh', $to = 'en') {
    // http://fanyi.baidu.com/v2transapi?from=zh&query=%E7%94%A8%E8%BD%A6%E8%B5%84%E8%AE%AF&to=fra
    $url = "http://fanyi.baidu.com/v2transapi";
    $data = array (
        'from' => $from,
        'to' => $to,
        'query' => $text 
    );
    $data = http_build_query ( $data );
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_REFERER, "http://fanyi.baidu.com" );
    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:37.0) Gecko/20100101 Firefox/37.0' );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
    $result = curl_exec ( $ch );
    curl_close ( $ch );

    $res_json = json_encode(
        array_merge(
            json_decode($result, true),
            array('start' => $start, 'end' => $end)
        )
    );
    return $res_json;
}

function get_params_from_server () {
    $settings = get_option('baidu_translator'); 
   
    echo json_encode($settings, JSON_UNESCAPED_UNICODE);
    wp_die();
}

if ($_GET['option'] === 'pxytrans') {
    echo translate_from_baidu($_GET['query'], $_GET['start'], $_GET['end'], $_GET['from'], $_GET['to']);
} else if ($_GET['option'] === 'params') {
    //echo get_params_from_server(); 
}
