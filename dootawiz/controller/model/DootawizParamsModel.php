<?php

class DootawizParamsModel
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $mode;
    private $botUid;
    private $ttsSpeed;
    private $ttsMsg;
}