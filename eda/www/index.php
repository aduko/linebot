<?php

ini_set('display_errors', 'On');


include ('web-config.php');
include(LIBPATH.'common/initialize.php');

/**
 * オブジェクト生成
 * ------------------------------------------------------------------ */
$LineBotDAO = new LineBotDAO;
$RawDataDAO = new RawDataDAO;
$ScheduleDAO = new ScheduleDAO;

$question = array(
  0 => array('title' => '今から', 'limit' => 5),
  1 => array('title' => '今日の夜', 'limit' => 5),
  2 => array('title' => '今週末', 'limit' => 5),
  3 => array('title' => '今月末', 'limit' => 5),
);

//print_r($LineBotDAO->echo_word('a'));

//// パラメータ設定
//$ins = array();
//$ins['timestamp'] = date("Y-m-d H:i:s");
//$ins['userid']    = '';
//$ins['keyword']   = 'abc';
//
//// DB登録
//$LineBotDAO->insert($ins);

//$keyword = 'グループ5に入れてー';
//$members = $LineBotDAO->getGroupList($keyword);
//
//print_r($members);
//exit();


$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient( CHANNELACCESSTOKEN);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => CHANNELSECRET]);

//POST
$input = file_get_contents('php://input');
$json = json_decode($input);
//$event = $json->events[0];

foreach ($json->events as $event) {

  // rawデータ保存
//  $RawDataDAO->insert(($event));

  //イベントタイプ判別
  if ("message" == $event->type) {            //一般的なメッセージ(文字・イメージ・音声・位置情報・スタンプ含む)

    if ("text" == $event->message->type) {

      // 初期化
      $message_text = $event->message->text;

      if(preg_match('/に入れてー\z/', $message_text)){

        // パラメータ設定
        $ins = array();
        $ins['timestamp'] = $event->timestamp;
        $ins['userid']    = $event->source->userId;
        $ins['keyword']   = $event->message->text;

        // Profile取得
//        $profile = json_decode($LineBotDAO->getProfile($event->source->userId));
////        syslog(LOG_EMERG, print_r($profile, true));
//        $ins['displayName'] = $profile['displayName'];
//        $ins['pictureUrl'] = $profile['pictureUrl'];
//        $ins['statusMessage'] = $profile['statusMessage'];

        // DB登録
        $LineBotDAO->insert($ins);

        // 結果格納
        $rep_message = "入れたよー。";

        // メッセージ作成
//        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($profile);
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($rep_message);

      }elseif(preg_match('/^飲み行かない？/', $message_text)){

        // レスポンス: 「誰と?」

        // 結果格納
        $rep_message = "誰と？";







        // メッセージ作成
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($rep_message);

      }elseif(preg_match('/と\z/', $message_text)){

        // グループリスト取得
        $keyword = mb_substr($message_text, 0, -1);
        syslog(LOG_EMERG, print_r($event->message->text, true));
        syslog(LOG_EMERG, print_r($keyword, true));

        $members = $LineBotDAO->getGroupList($keyword);

        // 結果格納
        $rep_message = "OK！グループの全員に通知するよー.(=>タイマースタート)";
        // メッセージ作成(Reply)
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($rep_message);

        syslog(LOG_EMERG, print_r($members, true));

        // 全員: メッセージ送信
        foreach((array)$members as $key => $val) {

          // パマラータ設定(回答リスト)


          // 送信
//          $LineBotDAO->pushMessage($members[$key]['userid'], json_encode($members, true));
          $LineBotDAO->pushMessage($members[$key]['userid'], $members[$key]['displayname']);

          // データ設定
          $data[] = array(
            'userid' => $members[$key]['userid'],
            'q0' => NULL,
            'q1' => NULL,
            'q2' => NULL,
            'q3' => NULL,
          );

        }

        // スケジューラ登録
        $ins = array();
        $ins['keyword'] = $keyword;
        $ins['data'] = json_encode($data);
        $ScheduleDAO->insert($ins);

      }else {

        // 日付設定: タイマー作動 & true / false
        if(preg_match('/^true/', $message_text) || preg_match('/^false/', $message_text)){

          // 結果登録
          $schedule = $ScheduleDAO->findByKeyword("");
          foreach((array)$schedule['data'] as $key => $val){
            $userid = $schedule['data'][$key]['userid'];
            if($userid == $event->source->userId){
//              foreach(array(0,1,2,3) as $v2){
//                if(is_null($val['q'.$v2])) $schedule['data'][$key]['q'.$v2] = $message_text;
//              }
              if(is_null($val['q0'])) $schedule['data'][$key]['q0'] = $message_text;
              elseif(is_null($val['q1'])) $schedule['data'][$key]['q1'] = $message_text;
              elseif(is_null($val['q2'])) $schedule['data'][$key]['q2'] = $message_text;
              else                        $schedule['data'][$key]['q3'] = $message_text;
            }
          }

          syslog(LOG_EMERG, print_r($schedule, true));



          // 次のメッセージ送信(終わり以外)


          // TODO cronで締め切りメッセージ送信 & 状態変更(日付調整中 -> お店調整中) & 集計 & 集計結果の送信

        }else{ // 日付設定: タイマー作動 & お店調整中 & true / false

        }





      }

    }

//    $LineBotDAO->pushMessage($event->source->userId, 'ok.');






    //} elseif ("follow" == $event->type) {        //お友達追加時
    //  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("よろしくー");
    //} elseif ("join" == $event->type) {           //グループに入ったときのイベント
    //  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('こんにちは よろしくー');
    //} elseif ('beacon' == $event->type) {         //Beaconイベント
    //  $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Godanがいんしたお(・∀・) ');
  } else {
    //なにもしない
  }

  $response = $bot->replyMessage($event->replyToken, $textMessageBuilder);
//  syslog(LOG_EMERG, print_r($event->replyToken, true));
//  syslog(LOG_EMERG, print_r($response, true));
  return;


}

