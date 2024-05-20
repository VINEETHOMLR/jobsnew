<?php

namespace inc;

use src\lib\Role;
use src\models\Services;

/**
 * Description of Filter
 *
 * 
 */
trait Filter {

    /**
     *
     * @var String
     */
    private $actn;

    /**
     *
     * @var String
     */
    private $ctlr;

    /**
     * Method to defines the access control of the user with controllers and actions
     * @param Array $rules
     */
    public function accessControl($rules = []) {
        // Auth Users
        if ($this->isAuth() !== false) {
            $this->authAccess($rules);
        } else {
            if (array_key_exists('*', $rules) && !in_array($this->actn, $rules['*'])) {
                $this->redirect('index/login');
            }
        }
    }

    /**
     * Method to deny or grant access based on the rules in services
     * @param Array $rules
     * @return Boolean
     */
    private function authAccess($rules) {
        //Check for * access i.e free action access to all users
        if (array_key_exists('*', $rules) && in_array($this->actn, $rules['*'])) {
            return true;
        }
        //Check for services got access lines
        $srv = new Services();
        $isPageExists = $srv->findAll(['page' => strtolower($this->ctlr) . '/' . strtolower($this->actn)]);
        if (count($isPageExists) > 0 && !empty($isPageExists) && in_array((int) $isPageExists[0]['id'], $_SESSION['SERVICES'])) {
            return true;
        }
        //If not matching the record then just redirect to no access page
        $this->redirect('index/noaccess');
    }

    /**
     * method to return Guest or Auth User
     * @return Boolean
     */
    public function isAuth() {
        if (isset($_SESSION['ADMIN_ROLE'])) {
            return $_SESSION['ADMIN_ROLE'];
        }
        return false;
    }

    /**
     * Method to assing controller and action names
     * @param String $ctlr
     * @param String $action
     * @return NULL
     */
    public function setup($ctlr, $action) {
        $this->actn = $action;
        $this->ctlr = $ctlr;
    }

    /**
     * 
     * @param String $ctlr
     * @param String $action
     * @return boolean
     */
    public function checkAccess($ctlr, $action) {
        $access = Role::access();
        if (isset($_SESSION['ADMIN_ROLE'])) {
            if ($ctlr . '/' . $action === 'index/noaccess') {
                return true;
            }
            $role = $_SESSION['ADMIN_ROLE'];
            if (array_key_exists((int) $role, $access) && in_array($ctlr . '/' . $action, $access[$role])) {
                return true;
            } else {
                $this->redirect('index/noaccess');
            }
        }
        return true;
    }

}
