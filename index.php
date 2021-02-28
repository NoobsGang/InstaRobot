<?php
error_reporting(0);
$tok = getenv("BOT_TOKEN");
function botaction($method, $data){
	global $tok;
	global $dadel;
	global $dueto;
    $url = "https://api.telegram.org/bot$tok/$method";
    $curld = curl_init();
    curl_setopt($curld, CURLOPT_POST, true);
    curl_setopt($curld, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curld, CURLOPT_URL, $url);
    curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curld);
    curl_close($curld);
    $dadel = json_decode($output,true);
    $dueto = $dadel['description'];
		if($dueto){
			Logger($dueto);
		}
    return $output;
}
function startsWith($content,$startString)
{
$con_arr = explode(' ',$content);
	if($con_arr['0'] == $startString)
	{
	return true;
	}
	else
	{
	return false;
	}
}

$update = file_get_contents('php://input');
$update = json_decode($update, true);


$mid = $update['message']['message_id'];
$cid = $update['message']['chat']['id'];
$uid = $update['message']['chat']['id'];
$cname = $update['message']['chat']['username'];
$fid = $update['message']['from']['id'];
$fname = $update['message']['from']['first_name'];
$lname = $update['message']['from']['last_name'];
$uname = $update['message']['from']['username'];
$typ = $update['message']['chat']['type'];
$texts = $update['message']['text'];
$text = strtolower($update['message']['text']);
$fullname = ''.$fname.' '.$lname.'';

################## NEW MEMBER DATA ################
$new_member = $update['message']['new_chat_member'];
$gname = $update['message']['chat']['title'];
$nid = $update['message']['new_chat_member']['id'];
$nfname = $update['message']['new_chat_member']['first_name'];
$nlname = $update['message']['new_chat_member']['last_name'];
$nuname = $update['message']['new_chat_member']['username'];
$nfullname = ''.$nfname.' '.$nlname.'';
#################################################
$lfname = $update['message']['left_chat_member']['first_name'];
$llname = $update['message']['left_chat_member']['last_name'];
$luname = $update['message']['left_chat_member']['username'];
$reply_message = $update['message']['reply_to_message'];
$reply_message_id = $update['message']['reply_to_message']['message_id'];
$reply_message_user_id = $update['message']['reply_to_message']['from']['id'];
$reply_message_text = $update['message']['reply_to_message']['text'];
$reply_message_user_fname = $update['message']['reply_to_message']['from']['first_name'];
$reply_message_user_lname = $update['message']['reply_to_message']['from']['last_name'];
$reply_message_user_uname = $update['message']['reply_to_message']['from']['username'];
$bot_username = json_decode(file_get_contents("https://api.telegram.org/bot$tok/getMe"),true);
$bot_username = $bot_username['result']['username'];

if($typ == 'private'){
  if($text == '/start'){
    botaction("sendMessage",['chat_id'=>$cid,'text'=>"<b>Hi $fname, Well I Am Instagram Photo Video And Reels Downloader..Just Send Me Any Instagram Post OR Reel Link..Thats It I Will See The Reset\n\nBot Made By : @NoobsGang</b>",'parse_mode'=>'HTML','reply_to_message_id'=>$mid]);
  }
  elseif ($text[0] == '@') {
    $usernamee = rawurlencode(explode('@',$texts)[1]);
    $instaa = json_decode(file_get_contents("https://api.noobgang.online/Insta/story.php?username=$usernamee"),true);
    $profile_image = $instaa['profile_image'];
    $bio = $instaa['bio'];
    $bio = "$bio\n\n";
    botaction("sendMediaGroup",['chat_id'=>$cid,'media'=>'[{"type":"photo","media":"'.$profile_image.'","caption":"'.$bio.'Downloaded With : @'.$bot_username.'"}]']);

    if($instaa['stories']){
      $count = count($instaa['stories']);
      $la = "";
      $sen = "'s Story\n\nDownloaded With @$bot_username";
      for ($i=0; $i <$count ; $i++) {
        $urr = $instaa['stories'][$i];
        $urr = explode('||',$urr);
        $type = $urr[1];
        $urr = $urr[0];
        $la = '{"type":"'.$type.'","media":"'.$urr.'","caption":"'.$usernamee.''.$sen.'"}';
        botaction("sendMediaGroup",['chat_id'=>$cid,'media'=>"[$la]"]);
      }
    }
    elseif ($instaa['info']) {
      $info = $instaa['info'];
      botaction("sendMessage",['chat_id'=>$cid,'text'=>$info]);
    }
  }
  else {
      $api = json_decode(file_get_contents("https://api.noobgang.online/Insta/api.php?post_link=$texts"),true);
      $error = $api['error'];
      if($error){
        botaction("sendMessage",['chat_id'=>$cid,'text'=>"$error OR Send Me A Insta Username Such As @google To Get The Profile Photo And Stories Of The Account."]);
      }
      else {
        $images = $api['data']['images'];
        $videos = $api['data']['videos'];
        $caption = $api['description'];
        $ci = count($images);
        $cv = count($videos);
        $js = "";
        for ($i=0; $i <$ci ; $i++) {
          $ur = $images[$i];
          $js .= '{"type":"photo","media":"'.$ur.'"},';
        }
        for ($i=0; $i <$cv ; $i++) {
          $ur = $videos[$i];
          $js .= '{"type":"video","media":"'.$ur.'"},';
        }
        $js = rtrim($js,',');
        $js = "[$js]";
        if($caption){
          botaction("sendMediaGroup",['chat_id'=>$cid,'media'=>$js,'caption'=>$caption]);
          botaction("sendMessage",['chat_id'=>$cid,'text'=>"$caption\n\nDownloaded With : @$bot_username"]);
        }
        else {
          botaction("sendMediaGroup",['chat_id'=>$cid,'media'=>$js]);
        }
      }
    }
}
