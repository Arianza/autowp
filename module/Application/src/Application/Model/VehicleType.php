<?php

namespace Application\Model;

use Application\Model\DbTable;

class VehicleType
{
    /**
     * @var DbTable\Item
     */
    private $vehicleTable;

    /**
     * @var DbTable\Vehicle\Type
     */
    private $vehicleTypeTable;

    /**
     * @var DbTable\Vehicle\VehicleType
     */
    private $vehicleVehicleTypeTable;

    public function __construct()
    {
        $this->vehicleTable = new DbTable\Item();
        $this->vehicleTypeTable = new DbTable\Vehicle\Type();
        $this->vehicleVehicleTypeTable = new DbTable\Vehicle\VehicleType();
    }

    public function removeVehicleType($vehicleId, $type)
    {
        $deleted = $this->vehicleVehicleTypeTable->delete([
            'vehicle_id = ?'      => (int)$vehicleId,
            'vehicle_type_id = ?' => (int)$type,
            'not inherited'
        ]);

        if ($deleted > 0) {
            $this->refreshInheritance($vehicleId);
        }
    }

    public function addVehicleType($vehicleId, $type)
    {
        $changed = $this->setRow($vehicleId, $type, false);

        if ($changed) {
            $this->refreshInheritance($vehicleId);
        }
    }

    public function setVehicleTypes($vehicleId, array $types)
    {
        $vehicleId = (int)$vehicleId;
        $inherited = false;

        if (! $types) {
            $types = $this->getInheritedIds($vehicleId);
            $inherited = true;
        }

        $changed = $this->setRows($vehicleId, $types, $inherited);

        if ($changed) {
            $this->refreshInheritance($vehicleId);
        }
    }

    private function setRows($vehicleId, array $types, $inherited)
    {
        $changed = false;

        foreach ($types as $type) {
            $rowChanged = $this->setRow($vehicleId, $type, $inherited);
            if ($rowChanged) {
                $changed = true;
            }
        }

        $filter = [
            'vehicle_id = ?' => (int)$vehicleId
        ];
        if ($types) {
            $filter['vehicle_type_id not in (?)'] = $types;
        }

        $deleted = $this->vehicleVehicleTypeTable->delete($filter);
        if ($deleted > 0) {
            $changed = true;
        }

        return $changed;
    }

    private function setRow($vehicleId, $type, $inherited)
    {
        $inherited = (bool)$inherited;
        $changed = false;

        $row = $this->vehicleVehicleTypeTable->fetchRow([
            'vehicle_id = ?'      => (int)$vehicleId,
            'vehicle_type_id = ?' => (int)$type
        ]);
        if (! $row) {
            $changed = true;
            $row = $this->vehicleVehicleTypeTable->createRow([
                'vehicle_id'      => (int)$vehicleId,
                'vehicle_type_id' => (int)$type,
                'inherited'       => $inherited ? 1 : 0
            ]);
            $row->save();
        }

        if ($inherited !== (bool)$row->inherited) {
            $changed = true;
            $row->inherited = $inherited ? 1 : 0;
            $row->save();
        }

        return $changed;
    }

    private function getInheritedIds($vehicleId)
    {
        $vehicleId = (int)$vehicleId;

        $db = $this->vehicleVehicleTypeTable->getAdapter();

        $ids = $db->fetchCol(
            $db->select()
                ->distinct()
                ->from('vehicle_vehicle_type', ['vehicle_type_id'])
                ->join('item_parent', 'vehicle_vehicle_type.vehicle_id = item_parent.parent_id', null)
                ->where('item_parent.item_id = ?', (int)$vehicleId)
        );

        return $ids;
    }

    public function refreshInheritanceFromParents($vehicleId)
    {
        $typeIds = $this->getVehicleTypes($vehicleId);

        $this->setVehicleTypes($vehicleId, $typeIds);
    }

    public function refreshInheritance($vehicleId)
    {
        $vehicleId = (int)$vehicleId;

        $db = $this->vehicleVehicleTypeTable->getAdapter();

        $ids = $db->fetchCol(
            $db->select()
                ->from('item_parent', ['item_id'])
                ->where('item_parent.parent_id = ?', $vehicleId)
        );

        foreach ($ids as $id) {
            $this->refreshInheritanceFromParents($id);
        }
    }

    public function getVehicleTypes($vehicleId)
    {
        $db = $this->vehicleVehicleTypeTable->getAdapter();

        return $db->fetchCol(
            $db->select()
                ->from('vehicle_vehicle_type', ['vehicle_type_id'])
                ->where('vehicle_id = ?', (int)$vehicleId)
                ->where('not inherited')
        );
    }
}
