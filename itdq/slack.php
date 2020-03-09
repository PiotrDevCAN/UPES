<?php
namespace itdq;


class slack {

    protected $url;

    const CHANNEL_GENERAL = 'general';
    const CHANNEL_UPES_AUDIT = 'upes_audit';

    function __construct(){
        $this->url[self::CHANNEL_GENERAL]      = 'https://hooks.slack.com/services/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';
        $this->url[self::CHANNEL_UPES_AUDIT]   = 'https://hooks.slack.com/services/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';
    }

    function sendMessageToChannel($message,$channel){
        if(empty($this->url[trim($channel)])){
            throw new \Exception($channel . " unknown channel, message can't be sent");
        }

        $url = $this->url[trim($channel)];
        $ch = curl_init( $url );

        $messageToSlack = '{"text":"' . trim($message) . '[' . $_SERVER['environment'] . ']"}';

        set_time_limit(25);
        curl_setopt( $ch, CURLOPT_POSTFIELDS,$messageToSlack );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Content-Length: ' . strlen($messageToSlack)));

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true);
        # Send request.
        $result = curl_exec($ch);
        return $result;
    }
}
