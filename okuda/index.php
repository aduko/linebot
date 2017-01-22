<?php

//phpinfo();

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');

$channelAccessToken = '4DkhzSlqXv4/H/HlF4T2kFXfUGHFVzZNFUlCDV7+hkrazr6Pae87VVRjLeTrHgZAXRoAb1AMhA2/yccy7j5rzket9JfRxIeSF9cJ/JPh1ZpsHoHHi1BBUcyXvdza0C7zBq1239Tqoj91OlUtWWProgdB04t89/1O/w1cDnyilFU=';
$channelSecret = '51e67fddafcbad5cdedec827449a4951';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            $source  = $event['source'];
            switch ($message['type']) {
                case 'text':
                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                //'text' => $message['text']
                                'text' => $source['userId']
                            )
                        )
                    ));
//                    break;
                    //Push Message Smaple 
//                    $client->pushMessage(array(
//                        "to"      => "Uc205010d69da4c4e840da6c78c04d2ee",
//                        'messages' => array(
//                            array(
//                                'type' => 'text',
//                                //'text' => $message['text']
//                                //'text' => $source['userId']
//                                'text' => "TEST!!"
//                            )
//                        )
//                    ));
                    //Confirm Message Sample
                     $client->pushMessage(array(
                        "to"      => "Uc205010d69da4c4e840da6c78c04d2ee",
                        'messages' => array(
                                array(
                                'type' => 'template',
                                'altText' => 'test',
                                'template' => array(
                                   'type' =>  'confirm',
                                   'text' => 'Please chioce!!' ,
                                   'actions' => array(
                                       array(
                                         'type' => 'message' ,
                                         'label' => 'Yes' ,
                                         'text' => 'yes' 
                                       ),
                                       array(
                                         'type' => 'message' ,
                                         'label' => 'No' ,
                                         'text' => 'no' 
                                       )
                                    )
                                 )
                                )
                        )
                    ));
                    //Carousel Message Sample
                     $client->pushMessage(array(
                        "to"      => "Uc205010d69da4c4e840da6c78c04d2ee",
                        'messages' => array(
                                array(
                                'type' => 'template' ,
                                'altText' => 'test' ,
                                'template' => array(
                                   'type' =>  'carousel' ,
                                   'columns' => array(
                                        array(
                                           'title' => 'Sample1' ,
                                           'text'  => 'test1' ,
                                           'actions' => array(
                                                        array(
                                                           'type' => 'postback' ,
                                                           'label' => 'Yes' ,
                                                           'data' => 'yes' 
                                            ) ,
                                            array(
                                                            'type' => 'postback' ,
                                                            'label' => 'No' ,
                                                            'data' => 'no'
                                            ))
                                         ) ,
                                         array(
                                         'title' => 'Sample2' ,
                                         'text'  => 'test2' ,
                                         'actions' => array(
                                                      array(
                                                         'type' => 'postback' ,
                                                         'label' => 'Yes' ,
                                                         'data' => 'yes' 
                                            ) ,
                                            array(
                                                         'type' => 'postback' ,
                                                         'label' => 'No' ,
                                                         'data' => 'no'
                                            ))
                                         )
                                   )
                                )
                               ))
                           ));
                    break;
                case 'confirm':
                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                //'text' => $message['text']
                                'text' => $message
                            )
                        )
                    ));
                    break; 
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
