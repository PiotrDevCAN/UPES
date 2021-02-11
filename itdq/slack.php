<?php
namespace itdq;


class slack {

    protected $url;

    const CHANNEL_UPES_AUDIT   = 'upes_audit';


    function sendMessageToChannel($message=null,$channel=null){
        $url = $_ENV[$channel];
        $ch = curl_init( $url );

        $messageToSlack = '{"text":"' . $message . '"}';

        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Content-Length: ' . strlen($messageToSlack)));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.
        $result = curl_exec($ch);
        return $result;
    }

//     function slackJoinChannel($channel){
//         $url = "https://slack.com/api/conversations.join";
//         $ch = curl_init( $url );

//         $messageToSlack = 'token=' . self::TOKEN_VENTUS_SRE . '&channel=' . $channel ;

//         curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
//         curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','Content-Length: ' . strlen($messageToSlack)));

//         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//         curl_setopt( $ch, CURLOPT_POST, true);
//         # Send request.

//         $result = curl_exec($ch);

//         return $result;
//     }


//     function slackApiPostMessage($channel,$text){
//         // https://slack.com/api/chat.postMessage?token=xoxb-xxxxxxxxxxxxxxxxxxxxxxxxxx&channel=polytest&text=Emoji%20This&pretty=1(

//         $url = "https://slack.com/api/chat.postMessage";
//         $ch = curl_init( $url );

//         $tokenVentusSre = $_ENV['token_ventus_sre'];

//         $messageToSlack = 'token=' . $tokenVentusSre . '&channel=' . $channel . '&text=' . urlencode($text);

//         curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
//         curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','Content-Length: ' . strlen($messageToSlack)));

//         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//         curl_setopt( $ch, CURLOPT_POST, true);
//         # Send request.

//         $result = curl_exec($ch);

//         return $result;

//     }

//     function slackAddReaction($channel,$name,$timestamp){
//         //  https://slack.com/api/reactions.add?token=xoxb-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx&channel=C8DLE1DFH&name=thumbsup&timestamp=1585225528.000700&pretty=1

//         $url = "https://slack.com/api/reactions.add";
//         $ch = curl_init( $url );

//         $tokenVentusSre = $_ENV['token_ventus_sre'];

//         $messageToSlack = 'token=' . $tokenVentusSre . '&channel=' . $channel . '&name=' . $name . '&timestamp=' . $timestamp;

//         curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
//         curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','Content-Length: ' . strlen($messageToSlack)));

//         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//         curl_setopt( $ch, CURLOPT_POST, true);
//         # Send request.

//         $result = curl_exec($ch);

//         return $result;

//     }

//     function slackPostMessageWithEmoji($channel, $text ,array $names){
//         $postResult = $this->slackApiPostMessage($channel, $text);
//         $postResultObj = json_decode($postResult);

//         if($postResultObj->ok){
//             foreach ($names as $name) {
//                 $reactionResult = $this->slackAddReaction($channel, $name,$postResultObj->ts );
//                 $reactionResultObj = json_decode($reactionResult);
//                 if(!$reactionResultObj->ok){
//                     echo "<pre>";
//                     var_dump($postResultObj);
//                     throw new \Exception("Adding Reaction " . $name . " to Slack channel " . $channel . " Failed");
//                 }
//             }
//         } else {
//             echo "<pre>";
//             var_dump($postResultObj);
//             throw new \Exception("Write to Slack channel " . $channel . " Failed");
//         }
//     }
}
