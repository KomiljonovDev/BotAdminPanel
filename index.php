<?php
    require('./config/config.php');
    $db = new dbmysqli;
    $db->dbConnect();
    date_default_timezone_set('Asia/Tashkent');
    define('API_KEY', "BOT TOKEN");
    require './helpers/functions.php';
    $update = json_decode(file_get_contents('php://input'));
    if (!is_null($update)) {
        if (!is_null($update->message)) {
            $message = $update->message;
            $chat_id = $message->chat->id;
            $type = $message->chat->type;
            $miid =$message->message_id;
            $name = $message->from->first_name;
            $lname = $message->from->last_name;
            $full_name = $name . " " . $lname;
            $full_name = html($full_name);
            $user = $message->from->username ?? '';
            $fromid = $message->from->id;
            $text = html($message->text);
            $title = $message->chat->title;
            $chatuser = $message->chat->username;
            $chatuser = $chatuser ? $chatuser : "Shaxsiy Guruh!";
            $caption = $message->caption;
            $entities = $message->entities;
            $entities = $entities[0];
            $left_chat_member = $message->left_chat_member;
            $new_chat_member = $message->new_chat_member;
            $photo = $message->photo;
            $video = $message->video;
            $audio = $message->audio;
            $voice = $message->voice;
            $reply = $message->reply_markup;
            $fchat_id = $message->forward_from_chat->id;
            $fid = $message->forward_from_message_id;
        }else if(!is_null($update->callback_query)){
            $callback = $update->callback_query;
            $qid = $callback->id;
            $mes = $callback->message;
            $mid = $mes->message_id;
            $cmtx = $mes->text;
            $cid = $callback->message->chat->id;
            $ctype = $callback->message->chat->type;
            $cbid = $callback->from->id;
            $cbuser = $callback->from->username;
            $data = $callback->data;
        }
    }
    if (!is_null($update)) {
        if (!is_null($update->message)) {
            $user_lang = lang($fromid);
            if ($type == 'private') {
                if ($text == '/start') {
                    $myUser = myUser(['fromid','name','user','chat_type','lang','search_word','del'],[$fromid,$full_name,$user,'private','','',0]);
                    if (channel($fromid)) {
                        if ($myUser) {
                            bot('sendMessage',[
                                'chat_id'=>$fromid,
                                'text'=>$user_lang->start,
                            ]);
                        }else{
                            bot('sendMessage',[
                                'chat_id'=>$fromid,
                                'text'=>"🇺🇿 Tilni tanlang:",
                                'reply_markup'=>json_encode([
                                    'inline_keyboard'=>[
                                        [['text'=>'🇺🇿 O\'zbekcha','callback_data'=>'lang_uz']],
                                        [['text'=>'🇺🇸 English','callback_data'=>'lang_eng']],
                                        [['text'=>'🇺🇿 Русский','callback_data'=>'lang_ru']],
                                    ],
                                ]),
                            ]);
                        }
                    }
                }else if (channel($fromid)) {
                    if ($text == "/lang") {
                        bot('sendMessage',[
                            'chat_id'=>$fromid,
                            'text'=>"🇺🇿 Tilni tanlang:",
                            'reply_markup'=>json_encode([
                                'inline_keyboard'=>[
                                    [['text'=>'🇺🇿 O\'zbekcha','callback_data'=>'lang_uz']],
                                    [['text'=>'🇺🇸 English','callback_data'=>'lang_eng']],
                                    [['text'=>'🇷🇺 Русский','callback_data'=>'lang_ru']],
                                ],
                            ]),
                        ]);
                    }
                }
            }else{
                if ($text == "/start") {
                    myUser(['fromid','name','user','chat_type','lang','search_word','del'],[$chat_id,$title,$chatuser,'group','','',0]);
                }
            }
        }else if(!is_null($update->callback_query)){
            if (channel($cbid)) {
                if ($ctype == 'private') {
                    $user_lang = lang($cbid);
                    if ($data == 'res') {
                        bot('editMessageText',[
                            'chat_id'=>$cbid,
                            'message_id'=>$mid,
                            'text'=>$user_lang->start
                        ]);
                    }
                    if ($data == 'lang_uz') {
                        bot('editMessageText',[
                            'chat_id'=>$cbid,
                            'message_id'=>$mid,
                            'text'=>"Uzbek tili tanlandi."
                        ]);
                        $db->update('users',[
                            'lang'=>"uz",
                        ],[
                            'fromid'=>$cbid,
                            'cn'=>'='
                        ]);
                    }
                    if ($data == 'lang_eng') {
                        bot('editMessageText',[
                            'chat_id'=>$cbid,
                            'message_id'=>$mid,
                            'text'=>"English langugage is selected."
                        ]);
                        $db->update('users',[
                            'lang'=>"eng",
                        ],[
                            'fromid'=>$cbid,
                            'cn'=>'='
                        ]);
                    }
                    if ($data == 'lang_ru') {
                        bot('editMessageText',[
                            'chat_id'=>$cbid,
                            'message_id'=>$mid,
                            'text'=>"Выбран русский язык."
                        ]);
                        $db->update('users',[
                            'lang'=>"ru",
                        ],[
                            'fromid'=>$cbid,
                            'cn'=>'='
                        ]);
                    }
                }
            }
        }
    }
    include 'helpers/admin/admin.php';
    include 'helpers/sendMessage.php';
?>