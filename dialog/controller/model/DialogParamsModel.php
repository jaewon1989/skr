<?php

class DialogParamsModel
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $mode;
    private $botUid;
    private $dialogUid;
    private $dialogName;
}