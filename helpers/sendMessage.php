<?php
	if ($_REQUEST['sendMessage']) {
		$sendConfirm = file_get_contents('helpers/send_confirm.txt');
		if ($sendConfirm == 'send') {
			$start = file_get_contents('helpers/send_start.txt');
			$sendMessageCheck_type = json_decode(file_get_contents('config/json/check_type.json'));
			$extra = " AND (";

			if($sendMessageCheck_type->uz == true){
				$extra .= "(lang='uz') OR";
			}

			if ($sendMessageCheck_type->eng == true) {
			 	$extra .= " (lang='eng') OR";
		 	}

			if($sendMessageCheck_type->ru == true) {
				$extra .= " (lang='ru') OR";
			}
			
			if ($sendMessageCheck_type->not_selected == true) {
				$extra .= " (lang='') OR";
			}

			if ($sendMessageCheck_type->group == true) {
			 	$extra .= " (chat_type='group')";
			}
			$extra .= ")";
			$end = $start + '60';
			$users = $db->selectWhere('users',[
				[
					'id'=>$start,
					'cn'=>'>='
				]
			], " AND id<='" . $end . "'" . $extra);
			$sendMessagetype = json_decode(file_get_contents('config/json/sendMessage.json'));
			if ($users->num_rows) {
				if ($sendMessagetype->message_id) {
					if (!is_null($sendMessagetype->reply_markup)) {
						foreach ($users as $key => $user) {
							bot('sendMessage',[
								'chat_id'=>$user['fromid'],
								'text'=>"reply is true"
							]);
							bot('copyMessage',[
			    				'chat_id'=>$user['fromid'],
			    				'from_chat_id'=>$sendMessagetype->from_chat_id,
			    				'message_id'=>$sendMessagetype->message_id,
			    				'reply_markup'=>json_encode($sendMessagetype->reply_markup)
			    			]);
						}
					}else{
						foreach ($users as $key => $user) {
							bot('copyMessage',[
			    				'chat_id'=>$user['fromid'],
			    				'from_chat_id'=>$sendMessagetype->from_chat_id,
			    				'message_id'=>$sendMessagetype->message_id,
			    			]);
						}
					}
				}
				file_put_contents('helpers/send_start.txt', $end);
			}else{
				$sendAdsById = json_decode(file_get_contents('config/json/sendAdsById.json'));
				bot('sendMessage',[
					'chat_id'=>$sendMessagetype->from_chat_id,
					'text'=>'<b><a href="tg://user?id=' . $sendAdsById->fromid . '">Admin</a> tomonidan ' . date('Y-m-d H:i') . ' da yuburilgan reklama yakunlandi.</b>',
					'parse_mode'=>'html'
				]);
				$sendMessageCheck_type->uz = true;
				$sendMessageCheck_type->ru = true;
				$sendMessageCheck_type->eng = true;
				$sendMessageCheck_type->not_selected = true;
				$sendMessageCheck_type->group = true;

				file_put_contents('config/json/sendAdsById.json', '');
				file_put_contents('helpers/admin/json/' . $sendAdsById->fromid . '.json', '');
				file_put_contents('helpers/send_start.txt', '0');
				file_put_contents('config/json/sendMessage.json', '');
				file_put_contents('config/json/check_type.json', json_encode($sendMessageCheck_type));
			}
		}
	}
?>