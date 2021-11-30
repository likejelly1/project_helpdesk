<?php

function site() {
    $site = [   "0301" => "KIDECO",
                "0302" => "BONTANG",
                "0303" => "TARAKAN",
                "0305" => "SIMS",
                "0306" => "IP TABANG",
                "0307" => "MELAK",
                "0308" => "MUTU",
                "0309" => "KDC DT",
                "0310" => "PPA HMU",
                "03A1" => "HO JAKARTA",
                "03A2" => "HO BALIKPAPAN"
    ];

    return $site;
}

function status() {
    $status = [     "0" => "<div class='badge badge-info'>In Progress</div>",
                    "R" => "<div class='badge badge-warning'>Resolved</div>",
                    "A" => "<div class='badge badge-primary'>Approved</div>",
                    "1" => "<div class='badge badge-success'>Complete</div>",
                    "X" => "<div class='badge badge-danger'>Rejected</div>"];

    return $status;
}

function telegram_bot() {
    return "1843042921:AAFTzEFTvEKquvoXPgAhYGj8SvLCMsHx0tc";
}

function tg_message($msg, $to = array()) {
    $telegrambot = telegram_bot();
    $session = \Config\Services::session();
    $telegramchatid = $session->chat_id;

    if($telegramchatid && empty($to)) {
        $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';
        $data=array('chat_id'=>$telegramchatid,'text'=>$msg, 'parse_mode' => "html");

        $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);

        $context=stream_context_create($options);
        $result=file_get_contents($url,false,$context);
        return $result;
    } elseif ($to) {
        foreach ($to as $key => $value) {
            $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';
            $data=array('chat_id'=>$value,'text'=>$msg, 'parse_mode' => "html");

            $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);

            $context=stream_context_create($options);
            $result=file_get_contents($url,false,$context);
        }
    }
}

function emoji_score($key) {
    $score = [      "1" => "angry",
                    "2" => "frown",
                    "3" => "meh",
                    "4" => "smile",
                    "5" => "grin-stars"];

    $text = "";
    if($key)
        $text = '<i class="far fa-'.$score[$key].' fa-lg"></i>';

    return $text;
}

function isHO() {
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    $sql = "SELECT * FROM ms_ho WHERE company LIKE '".$session->get("company")."' AND site LIKE '".$session->get("site")."'";
    $model = $db->query($sql)->getRowArray();

    if(!$model)
        return false;

    if(in_array($session->get("site"), $model))
        return true;
    else
        return false;
}

function isApproveArr() {
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    $sql = "SELECT * FROM approve_sr WHERE nik LIKE '".$session->get("nik")."'";

    $model = $db->query($sql)->getResultArray();

    if($model)
        return $model;
    else
        return false;
}

function isPendingApprove() {
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    $sql = "SELECT * FROM detail_approve WHERE nik LIKE '".$session->get("nik")."' AND status IN ('0', '.') LIMIT 1";
    $model = $db->query($sql)->getRowArray();

    if($model)
        return true;
    else
        return false;
}

function svc() {
    $db = \Config\Database::connect();
    $sql = "SELECT id, name FROM ms_svc";
    $model = $db->query($sql)->getResultArray();

    $temp_model = array();
    foreach ($model as $key => $value) {
        $temp_model[$value['id']] = $value['name'];
    }
    
    return $temp_model;
}

function db_escape($arr) {
	$db = \Config\Database::connect();
	foreach ($arr as $key => $value) {
        if(!is_array($value))
		  $arr[$key] = $db->escapeString($value);
        else {
            foreach ($value as $_key => $_value) {
                $arr[$key][$_key] = $db->escapeString($_value);
            }
        }
	}

	return $arr;
}

function wp_redirect($link) {
    if($link != "default")
        $url = site_url($link);
    else {
        $session = \Config\Services::session();
        if($session->has("it_staff"))
            $url = "SR/my_service";
        else
            $url = "SR/my_request";
    }

    header("Location: $url");
    exit(0);
}

?>