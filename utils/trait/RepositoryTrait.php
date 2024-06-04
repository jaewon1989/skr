<?php

trait RepositoryTrait
{
    public function getColumns()
    {
        $properties = $this->_getProperties();

        $columns = array();
        foreach ($properties as $property) {
            $columns[] = $property->getName();
        }

        return $columns;
    }

    public function getColumnsWithoutUid()
    {
        $columns = $this->getColumns();
        $uidIndex = array_search('uid', $columns, true);
        if (false !== $uidIndex) {
            unset($columns[$uidIndex]);
        }

        return $columns;
    }

    public function changeSelectColumnVal($changeSelectColumnInfo)
    {
        $columns = $this->getColumnsWithoutUid();

        return array_map(static function ($column) use ($changeSelectColumnInfo) {
            foreach ($changeSelectColumnInfo as $targetColumn => $columnValue) {
                $column = $targetColumn === $column ? $columnValue : $column;
            }
            return $column;
        }, $columns);
    }

    public function getColumnsAndValues()
    {
        $properties = $this->_getProperties();

        $columns = [];
        $values = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $value = $this->{$propertyName};

            if (isset($value)) {
                $columns[] = $propertyName;
                $values[] = $value;
            }
        }

        return [
            'columns' => $columns,
            'values' => $values
        ];
    }

    public function getConditionForSet($exclusionColumns)
    {
        $columnsAndValues = $this->getColumnsAndValues();
        $setCondition = [];
        foreach ($columnsAndValues['columns'] as $idx => $column) {
            if (!in_array($column, $exclusionColumns, true)) {
                $setCondition[] = $column . ' = ' .
                    (is_numeric($columnsAndValues['values'][$idx]) ?
                        $columnsAndValues['values'][$idx] :
                        '"' . $columnsAndValues['values'][$idx] . '"');
            }
        }

        return $setCondition;
    }

    public function toEntity($dto)
    {
        $properties = $this->_getProperties();

        foreach ($dto as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            } else {
                foreach ($properties as $property) {
                    if ($property->getName() === $key) {
                        $property->setAccessible(true);
                        $property->setValue($this, $value);
                        break;
                    }
                }
            }
        }

        return $this;
    }

    public static function of($data)
    {
        $instance = new self();

        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }

    public function toArray()
    {
        $result = [];
        $properties = $this->_getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }

    private function _getProperties()
    {
        return (new ReflectionClass($this))->getProperties(
            ReflectionProperty::IS_PRIVATE |
            ReflectionProperty::IS_PROTECTED |
            ReflectionProperty::IS_PUBLIC);
    }

}