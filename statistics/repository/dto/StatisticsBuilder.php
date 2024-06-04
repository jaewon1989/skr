<?php

class StatisticsBuilder
{
    private $botuid;
    private $page;
    private $is_unknown;
    private $type;
    private $d_regis;

    public function build(): Statistics
    {
        return new Statistics($this);
    }

    /**
     * @return mixed
     */
    public function getBotuid()
    {
        return $this->botuid;
    }

    /**
     * @param mixed $botuid
     */
    public function setBotuid($botuid)
    {
        $this->botuid = $botuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsUnknown()
    {
        return $this->is_unknown;
    }

    /**
     * @param mixed $is_unknown
     */
    public function setIs_unknown($is_unknown)
    {
        $this->is_unknown = $is_unknown;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDRegis()
    {
        return $this->d_regis;
    }

    /**
     * @param mixed $d_regis
     */
    public function setD_regis($d_regis)
    {
        $this->d_regis = $d_regis;
        return $this;
    }

    public function invokeSetter(array $dto): self
    {
        foreach ($dto as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            }
        }
        return $this;
    }

}