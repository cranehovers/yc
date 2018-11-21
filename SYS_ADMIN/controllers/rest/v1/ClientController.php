<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 19:50
 */

namespace SYS_ADMIN\controllers\rest\v1;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\ClientAddr;

/**
 * Class ClientController
 * @package SYS_ADMIN\controllers\rest\v1
 * 用户信息相关
 */
class ClientController extends CommonController
{
    /**
     *  用户地址管理
     */
    public function actionAddrList()
    {
        $uid = 1;
        $addr_list = [];
        $addr = ClientAddr::find()
            ->where(['user_id' => $uid])
            ->asArray()
            ->all();

        if(count($addr)){
            foreach ($addr as $v){
                $addr_list[] = [
                    'sex' => $v['client_sex'],
                    'name' => $v['client_name'],
                    'mobile' => $v['mobile'],
                    'addr' => $v['addr'],
                    'detail' => $v['detail'],
                    'common' => $v['common'],
                ];
            }
        }

        return $this->successInfo($addr_list);
    }

    /**
     * 设置默认地址
     */
    public function actionAddrDefault()
    {
        $aid = \Yii::$app->request->post('aid');
    }

    /**
     * 获取当前收货地址
     */
    public function actionAddr()
    {
        $user_id = 1;
        $add_info = [];
        $add_info = ClientAddr::find()
            ->where(['user_id' => $user_id])
            ->asArray()
            ->one();

        if($add_info){
            $common_addr =  ClientAddr::find()
                ->where(['user_id' => $user_id])
                ->where(['common' => 1])
                ->asArray()
                ->one();

            if($common_addr){
                $add_info = $common_addr;
            }
        }

        if($add_info){
            $add_info['sex'] = ConStatus::$SEX[$add_info['client_sex']];
        }
        return $this->successInfo($add_info);
    }

    /**
     * 新增 | 编辑地址
     */
    public function actionAddrSave()
    {
        $uid = 1;
        $aid = \Yii::$app->request->post('aid');
        $name = \Yii::$app->request->post('client_name');
        $mobile = \Yii::$app->request->post('mobile');
        $sex = \Yii::$app->request->post('client_sex');
        $addr = \Yii::$app->request->post('addr');
        $detail = \Yii::$app->request->post('detail');
        $common = \Yii::$app->request->post('common', 0);

        $model = new ClientAddr();
        $model->attributes = \Yii::$app->request->post();
        if(!$model->validate()){
            $errors = implode($model->getFirstErrors(), "\r\n");
            return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $errors);
        }

        if(!empty($aid)){
            $model = ClientAddr::findOne($aid);
            if($model->user_id != $uid || empty($model)){
                return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, ConStatus::$ERROR_PARAMS_MSG);
            }
        }

        $model->client_name = $name;
        $model->client_sex = $sex;
        $model->user_id = $uid;
        $model->mobile = $mobile;
        $model->addr = $addr;
        $model->detail = $detail;
        $model->common = $common;

        if($model->save()){
            return $this->successInfo(true);
        } else {
            return $this->errorInfo(ConStatus::$STATUS_ERROR_SYS);
        }
    }


}