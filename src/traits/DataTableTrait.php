<?php
/**
 * @author ESien <esien@raise88.com>
 */

namespace src\traits;

trait DataTableTrait
{
	static function limit ( $request, $columns )
	{
		$limit = '';
		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = "LIMIT ".intval((integer)$request['start']).", ".intval((integer)$request['length']);
		}
		return $limit;
	}

	static function order ( $request, $columns )
	{
		$order = '';
		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $column = $columns[$columnIdx];

                $dir = $request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
                $orderBy[] = '`'.$column.'` '.$dir;
            }
            if ( count( $orderBy ) ) {
                $order = ' ORDER BY '.implode(', ', $orderBy);
            }
		}
		return $order;
	}
}