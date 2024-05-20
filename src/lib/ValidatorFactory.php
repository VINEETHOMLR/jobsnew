<?php
namespace src\lib;

use inc\Raise;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class ValidatorFactory
{

    public $lang;
    public $group;
    public $factory;
    public $namespace;
    // Translations root directory
    public $basePath;
    public static $translator;

    // sample code

    /*

    //  key value array or $_POST
    $input_data = ['username' => ''];
    // validation rule, can refer to https://laravel.com/docs/5.8/validation#available-validation-rules
    // only support those not database dependant.
    $v_rules = ['username' => 'required'];

    $factory = new ValidatorFactory();
    $validator = $factory->make($input_data, $v_rules);

    $label_override = ['username' => 'User'];
    // IF WANT TO overwrite attribute name
    $validator->setAttributeNames($label_override);

    //this to print all error with field & error message
    print_r($validator->errors());
    //cond check
    if ($validator->fails()) {

    die('Validation failed');
    }

     */

    public function __construct($namespace = '', $lang = null, $group = 'validation')
    {
        $this->lang = $lang ?? Raise::$lang;
        $this->group = $group;
        $this->namespace = $namespace;
        $this->basePath = $this->getTranslationsRootPath();
        $this->factory = new Factory($this->loadTranslator());
    }
    public function translationsRootPath(string $path = '')
    {
        if (!empty($path)) {
            $this->basePath = $path;
            $this->reloadValidatorFactory();
        }
        return $this;
    }
    private function reloadValidatorFactory()
    {
        $this->factory = new Factory($this->loadTranslator());
        return $this;
    }
    public function getTranslationsRootPath(): string
    {
        return dirname(__FILE__) . '/i18n/validation';
    }
    public function loadTranslator(): Translator
    {
        $loader = new FileLoader(new Filesystem(), $this->basePath . $this->namespace);
        $loader->addNamespace($this->namespace, $this->basePath . $this->namespace);
        $loader->load($this->lang, $this->group, $this->namespace);
        return static::$translator = new Translator($loader, $this->lang);
    }
    public function __call($method, $args)
    {
        return call_user_func_array([$this->factory, $method], $args);
    }

    /**
     *getLangLabel
     *@param array $validateArr
     *@param string $langDir
     *@return array i18n field label
     */
    public static function getLangLabel($validateArr, $langDir)
    {
        $validateLabelArr = [];
        foreach ($validateArr as $key => $field) {
            $validateLabelArr[$key] = t($langDir, 'l_' . $key);
        }
        return $validateLabelArr;
    }

}