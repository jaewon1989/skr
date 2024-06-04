<?php

class DootawizMapper
{
    private $_dbSession;

    public function __construct($sessionFactory)
    {
        $this->_dbSession = $sessionFactory;
    }

    public function getBotSettingByBotUid($botUid)
    {
        $sql = "
                SELECT      tts_vendor.value AS tts_vendor,
                            tts_audio.value AS tts_audio,
                            tts_pitch.value AS tts_pitch
                FROM        rb_chatbot_botSettings AS tts_vendor
                LEFT JOIN   rb_chatbot_botSettings AS tts_audio
                        ON  tts_vendor.bot = tts_audio.bot
                        AND tts_audio.name = 'tts_audio'
                LEFT JOIN   rb_chatbot_botSettings AS tts_pitch
                        ON  tts_vendor.bot = tts_pitch.bot
                        AND tts_pitch.name = 'tts_pitch'
                WHERE       tts_vendor.bot = " . $botUid . "
                AND         tts_vendor.name = 'tts_vendor'
                ";

        return mysqli_query($this->_dbSession, $sql);
    }
}