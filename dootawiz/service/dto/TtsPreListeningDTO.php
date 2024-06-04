<?php

class TtsPreListeningDTO
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $bot_id;
    private $speaker;
    private $ment;
    private $volume;
    private $speed;
    private $pitch;
}