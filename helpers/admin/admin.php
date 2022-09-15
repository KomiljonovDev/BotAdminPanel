<?php
	if (!is_null($update)) {
		if (file_exists('helpers/admin/json/' . $fromid . '.json')) {
			$admin = json_decode(file_get_contents('helpers/admin/json/' . $fromid . '.json'));
		}
		$admins = json_decode(file_get_contents('config/json/admins.json'));
		$home_keyboard = json_encode([
			'inline_keyboard'=>[
				[['text'=>"Admin qo'shish", 'callback_data'=>'add_admin'],['text'=>"Admin o'chirish", 'callback_data'=>'delete_admin'],],
				[['text'=>"Kanal sozlash",'callback_data'=>'setting_channel'],['text'=>'Reklama yuborish','callback_data'=>'send_ads'],],
			],
		]);
		$calncel_add_admin = json_encode([
			'inline_keyboard'=>[
				[['text'=>"Bekor qilish", 'callback_data'=>'calncel_add_admin'],],
			],
		]);
		$cancel_home = json_encode([
			'inline_keyboard'=>[
				[['text'=>"Ortga", 'callback_data'=>'cancel_home'],],
			],
		]);
		$setting_channel = json_encode([
			'inline_keyboard'=>[
				[['text'=>"Kanal qo'shish", 'callback_data'=>'add_channel'],['text'=>"Kanal o'chirish", 'callback_data'=>'remove_channel'],],
				[['text'=>"Mojburiy azolik On",'callback_data'=>'channel_on'],['text'=>'Mojburiy azolik Off','callback_data'=>'channel_off'],],
			],
		]);
		
		$calncel_send_ads = json_encode([
			'inline_keyboard'=>[
				[['text'=>"Yuborish", 'callback_data'=>'confirm_send_ads'],['text'=>"Bekor qilish", 'callback_data'=>'calncel_send_ads'],],
			],
		]);
		if (!is_null($update->message)) {
		    if (in_array($fromid, $admins)) {
		        if ($text == '/admin') {
		        	file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
		            $users = $db->selectWhere('users',[
		                'id'=>0,
		                'cn'=>'>'
		            ]);
		            $only_users = 0;
		            $active_users = 0;
		            $only_groups = 0;
		            $active_groups = 0;
		            foreach ($users as $key => $value) {
		                if ($value['chat_type'] == 'private') {
		                    $only_users+=1;
		                    if ($value['del']=='0') {
		                        $active_users+=1;
		                    }
		                }else{
		                    $only_groups+=1;
		                    if ($value['del']=='0') {
		                        $active_groups+=1;
		                    }
		                }
		            }
		            bot('sendMessage',[
		                'chat_id'=>$fromid,
		                'text'=>"Bot statistikasi:\n\nGuruh va userlar: " . $users->num_rows  . "ta\nBarcha userlar: " . $only_users . "ta\nActive userlar: " . $active_users . "ta\nBarcha Guruhlar: " . $only_groups . "ta\nActive Guruhlar: " . $active_groups . "ta",
		                'reply_markup'=>$home_keyboard,
		            ]);
		        }else if(mb_stripos($text, '/del_admin_')!==false){
		        	$exp = explode('/del_admin_', $text);
		        	$del_admin_id = $exp[1];
		        	$before_admin = [];
		        	foreach ($admins as $key => $value) {
		        		($value==$del_admin_id) ? true : $before_admin[] = $value;
		        	}
		        	file_put_contents('config/json/admins.json', json_encode($before_admin));
		        	bot('sendMessage',[
		        		'chat_id'=>$fromid,
		        		'text'=>"Admin muoffaqiyatli o'chirildi.",
		        		'reply_markup'=>$home_keyboard
		        	]);
		        }else if(mb_stripos($text, '/del_channel_')!==false){
		        	$exp = explode('/del_channel_', $text);
		        	$del = $db->delete('channels',[
						[
							'id'=>trim($exp[1]),
							'cn'=>'='
						],
					]);
		        	bot('sendMessage',[
		        		'chat_id'=>$fromid,
		        		'text'=>"Kanal muoffaqiyatli o'chirildi.",
		        		'reply_markup'=>$home_keyboard
		        	]);
		        }else{
			    	$bool = true;
			    	if ($admin->menu == 'add_admin' && $admin->step == 0) {
			    		if (!is_null($update->message->forward_from)) {
			    			if (!is_null($update->message->forward_from->id)) {
			    				$bool = false;
			    				$bot_admins = json_decode(file_get_contents('config/json/admins.json'));
			    				$bot_admins[] = $update->message->forward_from->id;
			    				file_put_contents('config/json/admins.json',json_encode($bot_admins));
			    			}
			    		}
			    		if ($bool) {
			    			bot('sendMessage',[
								'chat_id'=>$fromid,
								'text'=>"Admin qo'shilmadi.\nYangi admin qo'shish uchun yangi admin habaridan forward yuboring.\n\nEslatma: Yangi admin sozlamarida uzatilgan habar hamma uchun yoniq bo'lish zarur yo'qsa yangi admin ID sini olishning imkoni bo'lmaydi!",
								'reply_markup'=>$calncel_add_admin
							]);
			    		}else{
			    			if ($update->message->forward_from->id == $fromid) {
			    				bot('sendMessage',[
									'chat_id'=>$fromid,
									'text'=>"Siz o'zingizni o'zingiz admin qila olmaysiz. Avvaldan adminsiz.",
									'reply_markup'=>$calncel_add_admin
								]);
			    			}else{
			    				bot('sendMessage',[
			    					'chat_id'=>$fromid,
			    					'text'=>"Admin muoffaqiyatli qo'shildi!",
			    					'reply_markup'=>$home_keyboard
			    				]);
			    				file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
			    			}
			    		}
			    	}else if($admin->menu == 'add_channel' && $admin->step == 0){
			    		if (mb_stripos($text, "@")!==false) {
			    			$getchat = bot('getChat',[
			                    'chat_id'=>$text
			                ]);
			                $id = $getchat->result->id;
                			$title = $getchat->result->title;
                			$channels = $db->selectWhere('channels',[
								[
									'object'=>$id,
									'cn'=>'='
								],
							]);
                			if (!$channels->num_rows) {
                				$getchatadmin = bot('getChatMember',[
			                        'chat_id'=>$id,
			                        'user_id'=>$fromid
			                    ]);
			                    $status = $getchatadmin->result->status;
			                    if ($status == "administrator" or $status == "creator") {
			                    	if ($db->insertInto('channels',['name'=>'channel','object'=>$id])) {
			                    		bot('sendmessage',[
			                                'chat_id'=>$fromid,
			                                'text'=>"<b>Kanal sozlandi.</b>",
			                                'parse_mode'=>'html',
			                                'reply_markup'=>$home_keyboard
			                            ]);
			                            file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
			                    	}else{
			                    		bot('sendmessage',[
			                                'chat_id'=>$fromid,
			                                'text'=>"<b>Kanal sozlandi.</b>",
			                                'parse_mode'=>'html',
			                                'reply_markup'=>$home_keyboard
			                            ]);
			                            file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
			                    	}
			                    }else{
			                    	bot('sendmessage',[
		                                'chat_id'=>$fromid,
		                                'text'=>"<b>Bot yoki siz kanalda admin emassiz.</b>",
		                                'parse_mode'=>'html',
		                                'reply_markup'=>$home_keyboard
		                            ]);
		                            file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
			                    }
                			}else{
                				bot('sendMessage',[
									'chat_id'=>$fromid,
									'text'=>"<b>Bot 🔴 " . $title . " kanaliga avvaldan ulangan!</b>",
									'parse_mode'=>'html',
									'reply_markup'=>$cancel_home
								]);
								file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
                			}
			    		}else{
			    			bot('sendMessage',[
								'chat_id'=>$fromid,
								'text'=>"Kanal qo'shish uchun kanal usernameni yuboring.\nQuyidagi formatda @okdeveloper",
								'reply_markup'=>$cancel_home
							]);
			    		}
			    	}else if($admin->menu == 'send_ads' && $admin->step == 0){
			    		if (!is_null($update->message)) {
			    			if (!is_null($message->reply_markup)) {
				    			bot('copyMessage',[
				    				'chat_id'=>$fromid,
				    				'from_chat_id'=>$fromid,
				    				'message_id'=>$miid,
				    				'reply_markup'=>json_encode($message->reply_markup),
				    			]);
				    			bot('sendMessage',[
				    				'chat_id'=>$fromid,
				    				'text'=>"Yuborishlikka tayyormi?",
				    				'reply_to_message_id'=>$miid+1,
				    				'reply_markup'=>$calncel_send_ads
				    			]);
				    			file_put_contents('config/json/sendMessage.json', json_encode(array('from_chat_id' => $fromid, 'message_id' => $miid, 'reply_markup' => $update->reply_markup)));
			    			}else{
			    				bot('copyMessage',[
				    				'chat_id'=>$fromid,
				    				'from_chat_id'=>$fromid,
				    				'message_id'=>$miid,
				    			]);
				    			bot('sendMessage',[
				    				'chat_id'=>$fromid,
				    				'text'=>"Yuborishlikka tayyormi?",
				    				'reply_to_message_id'=>$miid+1,
				    				'reply_markup'=>$calncel_send_ads
				    			]);
				    			file_put_contents('config/json/sendMessage.json', json_encode(array('from_chat_id' => $fromid, 'message_id' => $miid)));
			    			}
			    		}
			    	}
			    }
		    }
		}
		if (!is_null($update->callback_query)) {
			if (in_array($cbid, $admins)) {
				if ($data == 'add_admin') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Yangi admin qo'shish uchun yangi admin habaridan forward yuboring.\n\nEslatma: Yangi admin sozlamarida uzatilgan habar hamma uchun yoniq bo'lish zarur yo'qsa yangi admin ID sini olishning imkoni bo'lmaydi!",
						'reply_markup'=>$calncel_add_admin
					]);
					file_put_contents('helpers/admin/json/' . $cbid . '.json', json_encode(array('menu'=>'add_admin','step'=>0)));
				}
				if ($data == 'calncel_add_admin') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Yangi admin qo'shish bekor qilindi!",
						'reply_markup'=>$home_keyboard
					]);
					file_put_contents('helpers/admin/json/' . $cbid . '.json', '');
				}
				if ($data == 'delete_admin') {
					if (file_exists('config/json/admins.json')) {
						$bot_admins = "\n";
					    foreach ($admins as $key => $value) {
					        $bot_admins .= ($key+=1) . " - /del_admin_" . $value . "\n";
					    }
						bot('editMessageText',[
							'chat_id'=>$cbid,
							'message_id'=>$mid,
							'text'=>"Bot adminlari royxati:\n" . $bot_admins,
							'reply_markup'=>$cancel_home
						]);
					}
				}
				if ($data == 'remove_channel') {
					$channels = $db->selectWhere('channels',[
		                'id'=>0,
		                'cn'=>'>'
		            ]);
					if ($channels->num_rows > 1) {
						$bot_channels = "\n";
						$i = -1;
					    foreach ($channels as $key => $value) {
					    	$i++;
					    	if ($i == 0) {
					    		continue;
					    	}
			                $getchat = bot('getChat',[
			                    'chat_id'=>$value["object"],
			                ]);
                			$title = $getchat->result->title;
					        $bot_channels .= $i . ") " . $title . " - /del_channel_" . $value["id"] . "\n";
					    }
						bot('editMessageText',[
							'chat_id'=>$cbid,
							'message_id'=>$mid,
							'text'=>"Botga biriktirilgan kanallar royxati:\nBiror kanal o'chirish uchun /del_channel_ va raqam ustuga bosing\n" . $bot_channels,
							'reply_markup'=>$cancel_home
						]);
					}else{
						bot('editMessageText',[
							'chat_id'=>$cbid,
							'message_id'=>$mid,
							'text'=>"Botga hechqanday kanal ulanmagan.",
							'reply_markup'=>$setting_channel
						]);
					}
				}
				if ($data == 'setting_channel') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Kanal sozlash bo'limi.\nNima qilamiz?",
						'reply_markup'=>$setting_channel
					]);
				}
				if ($data == 'add_channel') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Kanal qo'shish uchun kanal usernameni yuboring.\nQuyidagi formatda @okdeveloper",
						'reply_markup'=>$cancel_home
					]);
					file_put_contents('helpers/admin/json/' . $cbid . '.json', json_encode(array('menu'=>'add_channel','step'=>0)));
				}
				if ($data == 'channel_on') {
					$db->update('channels',[
						'object'=>"on",
					],[
						'name'=>"status",
						'cn'=>'='
					]);
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Majburiy azolik On rejimga o'tkazildi.",
						'reply_markup'=>$setting_channel
					]);
				}
				if ($data == 'channel_off') {
					$db->update('channels',[
						'object'=>"off",
					],[
						'name'=>"status",
						'cn'=>'='
					]);
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Majburiy azolik Off rejimga o'tkazildi.",
						'reply_markup'=>$setting_channel
					]);
				}
				if ($data == 'send_ads') {
					$check_send_lang_type = json_decode(file_get_contents('config/json/check_type.json'));
					$check_send_lang = json_encode([
						'inline_keyboard'=>[
							[['text'=>"🇷🇺 Rus userlarga " . (($check_send_lang_type->ru) ? '✅':'❌'), 'callback_data'=>'check_send_lang_ru'],['text'=>"🇺🇸 Ingliz userlarga " . (($check_send_lang_type->eng) ? '✅':'❌'), 'callback_data'=>'check_send_lang_eng'],],
							[['text'=>"🇺🇿 Uzbek userlarga " . (($check_send_lang_type->uz) ? '✅':'❌'), 'callback_data'=>'check_send_lang_uz'],],
							[['text'=>"Til tanlamaganlar " . (($check_send_lang_type->not_selected) ? '✅':'❌'), 'callback_data'=>'check_send_lang_nolang'],],
							[['text'=>"Guruhlarga " . (($check_send_lang_type->group) ? '✅':'❌'), 'callback_data'=>'check_send_lang_group'],],
							[['text'=>"Ortga", 'callback_data'=>'cancel_home'],],
						],
					]);
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Reklama yuborish uchun ixtiyoriy habar yuboring.",
						'reply_markup'=>$check_send_lang
					]);
					file_put_contents('helpers/admin/json/' . $cbid . '.json', json_encode(array('menu'=>'send_ads','step'=>0)));
					file_put_contents('config/json/check_type.json', json_encode(array('uz'=>true,'ru'=>true,'eng'=>true,'group'=>true,'not_selected'=>true)));
				}
				if ($data == 'cancel_home') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Bosh sahifa.",
						'reply_markup'=>$home_keyboard
					]);
					file_put_contents('helpers/admin/json/' . $cbid . '.json', '');
				}
				if ($data == 'calncel_send_ads') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Bosh sahifa.",
						'reply_markup'=>$home_keyboard
					]);
					file_put_contents('helpers/admin/json/' . $cbid . '.json', '');
					file_put_contents('config/json/sendMessage.json', '');
				}
				if ($data == 'confirm_send_ads') {
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Yuborish boshlanmoqda.",
					]);
					file_put_contents('helpers/send_start.txt', '0');
					file_put_contents('helpers/send_confirm.txt', 'send');
					file_put_contents('helpers/admin/json/' . $fromid . '.json', '');
				}
				if (mb_stripos($data, 'check_send_lang_')!==false) {
					$check_send_lang_type = json_decode(file_get_contents('config/json/check_type.json'));
					if ($data == 'check_send_lang_ru') {
						($check_send_lang_type->ru) ? $check_send_lang_type->ru = false : $check_send_lang_type->ru = true;
					}
					if ($data == 'check_send_lang_eng') {
						($check_send_lang_type->eng) ? $check_send_lang_type->eng = false : $check_send_lang_type->eng = true;
					}
					if ($data == 'check_send_lang_uz') {
						($check_send_lang_type->uz) ? $check_send_lang_type->uz = false : $check_send_lang_type->uz = true;
					}
					if ($data == 'check_send_lang_group') {
						($check_send_lang_type->group) ? $check_send_lang_type->group = false : $check_send_lang_type->group = true;
					}
					if ($data == 'check_send_lang_nolang') {
						($check_send_lang_type->not_selected) ? $check_send_lang_type->not_selected = false : $check_send_lang_type->not_selected = true;
					}
					file_put_contents('config/json/check_type.json', json_encode($check_send_lang_type));
					$check_send_lang_type = json_decode(file_get_contents('config/json/check_type.json'));
					$check_send_lang = json_encode([
						'inline_keyboard'=>[
							[['text'=>"🇷🇺 Rus userlarga " . (($check_send_lang_type->ru) ? '✅':'❌'), 'callback_data'=>'check_send_lang_ru'],['text'=>"🇺🇸 Ingliz userlarga " . (($check_send_lang_type->eng) ? '✅':'❌'), 'callback_data'=>'check_send_lang_eng'],],
							[['text'=>"🇺🇿 Uzbek userlarga " . (($check_send_lang_type->uz) ? '✅':'❌'), 'callback_data'=>'check_send_lang_uz'],],
							[['text'=>"Til tanlamaganlar " . (($check_send_lang_type->not_selected) ? '✅':'❌'), 'callback_data'=>'check_send_lang_nolang'],],
							[['text'=>"Guruhlarga " . (($check_send_lang_type->group) ? '✅':'❌'), 'callback_data'=>'check_send_lang_group'],],
							[['text'=>"Ortga", 'callback_data'=>'cancel_home'],],
						],
					]);
					bot('editMessageText',[
						'chat_id'=>$cbid,
						'message_id'=>$mid,
						'text'=>"Reklama yuborish uchun ixtiyoriy habar yuboring.",
						'reply_markup'=>$check_send_lang
					]);
					bot('answerCallbackQuery',[
						'callback_query_id'=>$qid,
						'text'=>"Userlarga yuborish o'zgartirildi!",
						'show_alert'=>true
					]);
				}
			}
		}
	}
?>