<?php
/**
 * Nucleon Plus - Phlpost
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComPhlpostModelShippingrates extends KObject
{
    /**
     * Get the rate based on destination and weight of the package
     *
     * @param string  $destination
     * @param integer $weight
     *
     * @return bool|float
     */
    public function getRate($destination, $weight)
    {
        $result = false;

        // Get rate per grams
        $identifier = $destination == 'manila' ? 'intra-province' : 'inter_land';
        $result     = $this->_getRate($identifier, $weight);

        return $result;
    }

    protected function _getRate($destination, $weight)
    {
        $table = $this->getObject('com://admin/phlpost.database.table.destinations');
        $query = $this->getObject('database.query.select')
            ->table('phlpost_destinations AS tbl')
            ->columns('tbl.phlpost_destination_id, _rates.rate AS rate')
            ->join(array('_rates' => 'phlpost_rates'), 'tbl.phlpost_destination_id = _rates.destination_id')
            ->where('tbl.destination = :destination')->bind(['destination' => $destination])
            ->where(':weight <= _rates.max_weight')->bind(['weight' => $weight])
            ->order('_rates.max_weight')
            ->limit(1)
        ;

        $result = $table->select($query);

        return (float) $result->rate;
    }
}
