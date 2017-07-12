<?php

namespace Application\Most\Adapter;

use Application\Model\DbTable\Attr\Attribute;

use Zend_Db_Table_Abstract;
use Zend_Db_Table_Select;

class Power extends AbstractAdapter
{
    protected $attribute;

    protected $order;

    public function setAttributes(array $value)
    {
        $this->attributes = $value;
    }

    public function setOrder($value)
    {
        $this->order = $value;
    }

    public function getCars(Zend_Db_Table_Select $select, $language)
    {
        $itemTable = $select->getTable();

        $attributes = new Attribute();

        $powerAttr = $attributes->find($this->attributes['power'])->current();

        $specService = $this->most->getSpecs();

        $valuesTable = $specService->getValueDataTable($powerAttr->type_id);
        $valuesTableName = $valuesTable->info(Zend_Db_Table_Abstract::NAME);

        $select
            ->join($valuesTableName, 'item.id = '.$valuesTableName.'.item_id', null)
            ->where($valuesTableName.'.attribute_id = ?', $powerAttr->id)
            ->where($valuesTableName.'.value > 0')
            ->group('item.id')
            ->order($valuesTableName.'.value ' . $this->order)
            ->limit($this->most->getCarsCount());

        $cars = $itemTable->fetchAll($select);

        $result = [];
        foreach ($cars as $car) {
            $html = '';
            $value = $specService->getActualValue($powerAttr->id, $car->id);
            $turbo = $specService->getActualValueText($this->attributes['turbo'], $car->id, $language);
            switch ($turbo) {
                case 'нет':
                    $turbo = null;
                    break;
                case 'есть':
                    $turbo = 'турбонаддув';
                    break;
                default:
                    if ($turbo) {
                        $turbo = 'турбонаддув ' . $turbo;
                    }
                    break;
            }
            $volume = $specService->getActualValue($this->attributes['volume'], $car->id);
            $cylindersLayout = $specService->getActualValueText(
                $this->attributes['cylindersLayout'],
                1,
                $car->id,
                $language
            );
            $cylindersCount = $specService->getActualValue($this->attributes['cylindersCount'], $car->id);
            $valvePerCylinder = $specService->getActualValue($this->attributes['valvePerCylinder'], $car->id);

            $cyl = $this->cylinders($cylindersLayout, $cylindersCount, $valvePerCylinder);


            $html .= $value;
            $html .= ' <span class="unit">л.с.</span>';


            if (strlen($cyl) || $turbo || $volume) {
                $a = [];

                if ($cyl) {
                    $a[] = htmlspecialchars($cyl);
                }

                if ($volume > 0) {
                    $a[] = sprintf('%0.1f <span class="unit">л</span>', $volume / 1000);
                }

                if ($turbo) {
                    $a[] = $turbo;
                }

                $html .= '<p class="note">'.implode(', ', $a).'</p>';
            }

            $result[] = [
                'car'         => $car,
                'valueHtml'    => $html,
            ];
        }

        return [
            'unit' => null,//$attribute->findParentAttrs_Units(),
            'cars' => $result,
        ];
    }

    protected function cylinders($layout, $cylinders, $valvePerCylinder = null)
    {
        if ($layout) {
            if ($cylinders) {
                $result = $layout.$cylinders;
            } else {
                $result = $layout.'?';
            }
        } else {
            if ($cylinders) {
                $result = $cylinders;
            } else {
                $result = '';
            }
        }

        if ($valvePerCylinder) {
            $result .= '/' . $valvePerCylinder;
        }

        return $result;
    }
}
