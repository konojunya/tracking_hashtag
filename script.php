<?php

require_once 'lib/TwistOAuth.php';
require_once 'lib/TwistException.php';
require_once 'settings.php';

set_time_limit(0);

while (ob_get_level()) {
    ob_end_clean();
}

try {
	$to = new TwistOAuth('yT577ApRtZw51q4NPMPPOQ', '3neq3XqN5fO3obqwZoajavGFCUrC42ZfbrLXy5sCv8');
  $to = $to->renewWithAccessTokenX($screen_name, $password);
} catch (TwistException $e) {
	$error = $e->getMessage();
  echo $error.PHP_EOL;
}

$list_id_str = $to->post("lists/create",array("name"=>$list_name))->id_str;
echo "list ".$list_name." を作りました。\nlisten ".$target_hashtag."\n";

$to->streaming('statuses/filter', function ($status) use ($to,$list_id_str) {
	if (isset($status)) {
		try {
			$to->post("lists/members/create",array("list_id" => $list_id_str,"screen_name" => $status->user->screen_name));
			echo $status->user->name." - @".$status->user->screen_name." をlistに追加しました！\n";
		}catch (TwistException $e){
			$error = $e->getMessage();
			echo $error.PHP_EOL;
		}
		flush();
	}
},array('track' => $target_hashtag));
