<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/8
 * Time: 17:23
 */

namespace SYS_ADMIN\components;


use kartik\select2\Select2;

class SearchWidget
{
    private static $_instance = null;

    public static function instance()
    {
        if(self::$_instance == null){
            self::$_instance = new SearchWidget();
        }
        return self::$_instance;
    }

    public function liveRoom($name = 'room_id', $defaultValue = '', $placeholder = '')
    {
        $data = BaseDataBuilder::instance('UserLiveRoom', true);
        $result = Select2::widget([
            'name'    => $name,
            'data'    => $data,
            'theme'   => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => $placeholder],
            'value'   => $defaultValue ?? "",
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);
        return $result;
    }

    /**
     * 快递公司
     */
    public function express($name = 'express_id', $defaultValue = '', $placeholder = '')
    {
        $data = Express::$EXPRESS;
        $result = Select2::widget([
            'name'    => $name,
            'data'    => $data,
            'theme'   => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => $placeholder],
            'value'   => $defaultValue ?? "",
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);
        return $result;
    }

    public function userList($name = 'user_id',  $defaultValue = '', $placeholder = '')
    {
        $data = BaseDataBuilder::instance('User', true);
        $result = Select2::widget([
            'name'    => $name,
            'data'    => $data,
            'theme'   => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => $placeholder],
            'value'   => $defaultValue ?? "",
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);
        return $result;
    }

    public function mallList($name = 'mall_id', $defaultValue = '', $placeholder = '')
    {
        $data = BaseDataBuilder::instance('ShoppingMall', true);
        $result = Select2::widget([
            'name'    => $name,
            'data'    => $data,
            'theme'   => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => $placeholder],
            'value'   => $defaultValue ?? "",
            'pluginOptions' => [
                'allowClear' => false
            ],
        ]);
        return $result;
    }
}