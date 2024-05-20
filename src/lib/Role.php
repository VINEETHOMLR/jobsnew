<?php

namespace src\lib;

use src\models\Services;


/**
 * RBAC Control File
 *
 * 
 */
class Role {
    const AD = 0;
    const SA = 1; //Define each role as a constant here
    const SRAD = 3;
    const L2 = 4;
    const L3 = 5;

    /**
     * 
     * @param Array $roles
     * @return Boolean
     */
    public static function hasAccess($roles = []) {
        if (!empty($roles)) {
            return isset($_SESSION['ADMIN_ROLE']) && in_array((int) $_SESSION['ADMIN_ROLE'], $roles);
        }
        return false;
    }
    
    /**
     * 
     * @param String $slug
     * @return Boolean
     */
    public static function srvAccess($slug){
         $srv = new Services();
        $isPageExists = $srv->findAll(['page' => '#','name'=> $slug]);
        if (count($isPageExists) > 0 && !empty($isPageExists) && in_array((int) $isPageExists[0]['id'], $_SESSION['SERVICES'])) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @return Boolean
     */
    public static function isGuest($roles = []) {
        return isset($_SESSION['ADMIN_ROLE']) && in_array((int) $_SESSION['ADMIN_ROLE'], $roles);
    }

}
