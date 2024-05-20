<?php

/**
 * Core controller File
 *
 *
 */

namespace inc;

use inc\Raise;
use src\lib\Helper as H;
use src\lib\Router;
use src\lib\Secure;
//use src\models\AuthLogin;
//use src\models\RememberMe;

class Controller
{

    /**
     *
     * @var String represents the layout file name
     */
    public $layout = 'main';

    /**
     *
     * @var represents the Title of the page
     */
    public $title = '';

    /**
     *
     * @var Array
     */
    public $params = [];

    /**
     *
     * @var String, HTML Based
     */
    public $content = "";

    /**
     *
     * @var String
     */
    public $lang;

    /**
     *
     * @var String
     */
    public $controller = '';
    public $action = '';

    protected $userObj = [];

    protected $isAuth = 0;

    /** The skip session variable is for the case where the controller not require validate session */
    public function __construct($userObj = [])
    {
        if ($userObj && !empty($userObj)) {
            $this->isAuth = true;
            $this->userObj = $userObj;
        }

        $this->lang = 'en';
        $this->checkAuth();
        // $this->isValidCSRF();
    }

    protected function checkAuth() {
         
        if (isset($this->needAuth) && $this->needAuth) {
            if( isset($this->authExclude) && in_array(Raise::$controllerAction,$this->authExclude))
                return ;

            if( isset($this->authInclude) && !in_array(Raise::$controllerAction,$this->authInclude))
                return ;    

            if (!H::checkLogin()) {
                $this->renderAuthApiError();
                die;
            }
        }        
    }

    /**
     *
     * @param String $name
     * @param String $value
     * @return boolean
     * @throws Exception 400 - Bad Request
     */
    protected function isValidCSRF($name = 'rForm')
    {
        $isPost = Router::getReqMethod();
        if ($isPost === 'post') {
            $headers = getallheaders_new();

            foreach ($headers as $k => $v) {
                $newHeader[strtoupper($k)] = $v;
            }

            $headerToken = (array_key_exists('X-CSRF-TOKEN', $newHeader) ? $newHeader['X-CSRF-TOKEN'] : '');
            $formToken = Router::req('rf_cs_' . $name . '_');
            $finalToken = isset($formToken) && !empty($formToken) ? $formToken : $headerToken;
            $sec = new Secure('rForm');
            if (isset($_SESSION[$name]) && $sec->decrypt($_SESSION[$name]) === $sec->decrypt($finalToken)) {

            } else {
                $this->renderAPIError('Bad Request', 400);
                die;
            }
        }
    }

    /**
     *
     * @param Array $results
     * @param Integer $statusCode
     */
    public function renderJSON($results, $statusCode = 200)
    {
        header("Content-Type: application/json");
        $json = json_encode($results);
        http_response_code($statusCode);
        echo $json;
    }

    /**
     *
     * @param Array $results
     * @param Integer $statusCode
     */
    public function renderAPI($data = [], $message = '', $show_alert = 'true', $code = '', $status = 'true', $statusCode = 200)
    {
        if (is_array($data) && isset($data['success'])) {
            $status = $data['success'] ? 'true' : 'false';
            $code = $data['code'];
            $message = $data['message'] ;
            $data = $data['data'];
        }

        // if($code != "" && ($code[0]=="S" || $code[0]=="E")){
        //     $from_file = ($code[0]=="S")?"success_code":"error_code";
        //     $message = Raise::t($from_file, $code);
        // }

        $results = [
            'status' => $status,
            'error_code' => $code,
            'message' => !empty($message) ? $message : '',
            'show_alert' => $show_alert,
            // 'data' => !empty($data) ? $data : (object)[],
        ];
        if (!empty($data)) {
            $results['data'] = $data;
        }
        header("Content-Type: application/json");
        $json = json_encode($results);
        http_response_code($statusCode);
        echo $json;

        die();
    }

    /**
     * renderAPI wrapper for error case
     *
     * @param string $message
     * @param string $code
     * @param integer $statusCode
     * @param array $data
     * @return void
     */
    public function renderAPIError($message = '', $code = '', $show_alert = 'true', $statusCode = 200, $data = [])
    {
        $status = "false";
        if (is_array($message) && isset($message['success']) && !$message['success']) {
            $status = $message['success'] ? 'true' : 'false';
            $code = $message['code'];
            $message = $message['message'];
            $data = isset($message['data']) ? $message['data'] : [];
        }

        $this->renderAPI($data, $message, $show_alert, $code, $status, $statusCode);
    }


    public function renderAuthApiError()
    {
        $message = 'Invalid Token';
        $code = 'EA001';
        $this->renderAPIError($message, $code);

    }

    
    /**
     *
     * @param type $viewFile
     * @param type $params
     */
    public function render($viewFile = '', $params = [])
    {
        $view = 'src/views/';
        $this->params = $params;
        $viewInclude = BASEPATH . $view . $viewFile . '.php';
        if (file_exists($viewInclude)) {
            $this->content = $this->renderPhpFile($viewInclude, $params);
        }

        include_once BASEPATH . $view . 'layouts/' . $this->layout . '.php';
    }

    /**
     *
     * @param type $viewFile
     */
    public function renderAjax($viewFile = '', $params = [])
    {
        $view = 'src/views/';
        $this->params = $params;
        $viewInclude = BASEPATH . $view . $viewFile . '.php';
        if (file_exists($viewInclude)) {
            echo $this->renderPhpFile($viewInclude, $params);
        }
    }

    /*     * *
     * @param string $_file_ the view file.
     * @param array $_params_ the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return string the rendering result
     * @throws \Exception
     * @throws \Throwable
     */

    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}