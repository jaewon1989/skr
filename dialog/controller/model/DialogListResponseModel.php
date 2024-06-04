<?php

class DialogListResponseModel
{
    use GetSetGenerator;
    use RepositoryTrait;

    private $uid;
    private $name;
    private $active;
    private $d_regis;
    private $is_temp_del;

    public static function of(DialogDTO $dialogDTO): DialogListResponseModel
    {
        $instance = new self();

        $instance->setUid($dialogDTO->uid);
        $instance->setName($dialogDTO->name);
        $instance->setActive($dialogDTO->active);
        $instance->setD_regis($dialogDTO->d_regis);
        $instance->setIs_temp_del($dialogDTO->is_temp_del);

        return $instance;
    }

    public function toArray(): array
    {
        return [
            'uid' => $this->getUid(),
            'name' => $this->_makeDialogName()
        ];
    }

    private function _makeDialogName()
    {
        return $this->getName() . '_' .
            date('Ymd', strtotime($this->getD_regis())) .
            ('1' === $this->getActive() ? ' (운영버전)' : '') .
            ('Y' === $this->getIs_temp_del() ? ' (X)' : '');
    }

}