<?php
namespace src\lib\captcha;

use src\lib\captcha\securimage\Securimage;
use src\lib\captcha\securimage\Securimage_Color;

class Captcha
{

    public static $font_path = BASEPATH . '/web/captcha/fonts/';

    public static $font_array = array(
        BASEPATH . '/web/captcha/fonts/times_new_yorker.ttf',
        // BASEPATH . '/web/captcha/fonts/Amatic-Bold.ttf',
        // BASEPATH . '/web/captcha/fonts/Akronim-Regular.ttf',
        BASEPATH . '/web/captcha/fonts/DancingScript-Bold.ttf',
        // BASEPATH . '/web/captcha/fonts/IndieFlower-Regular.ttf',
        // BASEPATH . '/web/captcha/fonts/Michroma.ttf',
        BASEPATH . '/web/captcha/fonts/ShadowsIntoLight.ttf',
        BASEPATH . '/web/captcha/fonts/ZCOOLKuaiLe-Regular.ttf',
        // BASEPATH . '/web/captcha/fonts/CaviarDreams_Italic.ttf',
    );

    public static $colorArray = array('#333366',
        '#006633',
        '#333300',
        '#663333',
        '#cc00ff',
        '#669966',
        '#6666cc');

    public static $secondaryColorArray = array('#333366',
        '#006500',
        '#333900',
        '#663933',
        '#cc99ff',
        '#668866',
        '#6611cc');

    public static function getCaptchaCode()
    {
        if (isset($_SESSION['CAPTCHA'])) {
            return $_SESSION['CAPTCHA'];
        }

        return null;
    }
    public static function secureCaptha()
    {

        $img = new Securimage();

        $img->image_width = CAPTCHA_CONFIG['secureOption']['image_width'];
        $img->image_height = CAPTCHA_CONFIG['secureOption']['image_height'];
        $img->text_color = new Securimage_Color(self::$colorArray[rand(0, 5)]);
        $img->line_color = new Securimage_Color(self::$secondaryColorArray[rand(0, 5)]);
        $randFont = self::$font_array[rand(0, sizeof(self::$font_array) - 1)];
        $img->ttf_file = $randFont;

        // $img->image_width =200;
        // $img->image_height = 120;
        $img->code_length = CAPTCHA_CONFIG['min_length'];
        $img->perturbation = 0.1;
        $img->num_lines = 0;
        $img->charset = CAPTCHA_CONFIG['characters'];

        if (isset(CAPTCHA_CONFIG['secureOption']['math']) && CAPTCHA_CONFIG['secureOption']['math']) {
            $img->captcha_type = Securimage::SI_CAPTCHA_MATHEMATIC;
        }

        $response = $img->show();

        return $response;

    }

    public function phpCaptcha($config = array())
    {

        // Check for GD library
        if (!function_exists('gd_info')) {
            throw new Exception('Required GD library is missing');
        }

        $bg_path = BASEPATH . '/web/captcha/backgrounds/';

        // Default values
        $captcha_config = array(
            'code' => '',
            'min_length' => CAPTCHA_CONFIG['min_length'],
            'max_length' => CAPTCHA_CONFIG['max_length'],
            'backgrounds' => array(
                $bg_path . '45-degree-fabric.png',
                $bg_path . 'cloth-alike.png',
                $bg_path . 'grey-sandbag.png',
                $bg_path . 'kinda-jean.png',
                $bg_path . 'polyester-lite.png',
                $bg_path . 'stitched-wool.png',
                $bg_path . 'white-carbon.png',
                $bg_path . 'white-wave.png',
            ),
            'fonts' => self::$font_array,
            'characters' => CAPTCHA_CONFIG['characters'],
            'min_font_size' => CAPTCHA_CONFIG['phpOption']['min_font_size'],
            'max_font_size' => CAPTCHA_CONFIG['phpOption']['max_font_size'],
            'color' => self::$colorArray[rand(0, 6)]
            ,
            'angle_min' => 1,
            'angle_max' => 12,
            'shadow' => true,
            'shadow_color' => '#fff',
            'shadow_offset_x' => -1,
            'shadow_offset_y' => 1,
        );

        // Overwrite defaults with custom config values
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                $captcha_config[$key] = $value;
            }

        }

        // Restrict certain values
        if ($captcha_config['min_length'] < 1) {
            $captcha_config['min_length'] = 1;
        }

        if ($captcha_config['angle_min'] < 0) {
            $captcha_config['angle_min'] = 0;
        }

        if ($captcha_config['angle_max'] > 12) {
            $captcha_config['angle_max'] = 12;
        }

        if ($captcha_config['angle_max'] < $captcha_config['angle_min']) {
            $captcha_config['angle_max'] = $captcha_config['angle_min'];
        }

        if ($captcha_config['min_font_size'] < 10) {
            $captcha_config['min_font_size'] = 10;
        }

        if ($captcha_config['max_font_size'] < $captcha_config['min_font_size']) {
            $captcha_config['max_font_size'] = $captcha_config['min_font_size'];
        }

        // Generate CAPTCHA code if not set by user
        if (empty($captcha_config['code'])) {
            $captcha_config['code'] = '';
            $length = mt_rand($captcha_config['min_length'], $captcha_config['max_length']);
            while (strlen($captcha_config['code']) < $length) {
                $captcha_config['code'] .= substr($captcha_config['characters'], mt_rand() % (strlen($captcha_config['characters'])), 1);
            }
        }

        // Generate HTML for image src
        if (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT'])) {
            $image_src = substr(__FILE__, strlen(realpath($_SERVER['DOCUMENT_ROOT']))) . '?_CAPTCHA&amp;t=' . urlencode(microtime());
            $image_src = '/' . ltrim(preg_replace('/\\\\/', '/', $image_src), '/');
        } else {
            $_SERVER['WEB_ROOT'] = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);
            $image_src = substr(__FILE__, strlen(realpath($_SERVER['WEB_ROOT']))) . '?_CAPTCHA&amp;t=' . urlencode(microtime());
            $image_src = '/' . ltrim(preg_replace('/\\\\/', '/', $image_src), '/');
        }

        $_SESSION['_CAPTCHA']['config'] = serialize($captcha_config);

        return [
            'code' => $captcha_config['code'],
            'image_src' => BASEURL . 'captcha/showPhp?_CAPTCHA&amp;t=' . urlencode(microtime()),
        ];
    }

    public function getCaptha()
    {

        $provider = CAPTCHA_CONFIG['vendor'];
        $randomProvider = $provider[rand(0, sizeof($provider) - 1)];
        // $randomProvider = 'phpCaptcha';
        //error_log('r ' . $randomProvider);
        if ($randomProvider == 'phpCaptcha') {
            $captcha['captcha'] = $this->$randomProvider();
            $getimage = $captcha['captcha']['image_src'];
            $_SESSION['CAPTCHA'] = $captchaCode = $captcha['captcha']['code'];
        } else {
            $getimage = BASEURL . 'captcha/showSecure';
        }
        return $getimage;
    }

}