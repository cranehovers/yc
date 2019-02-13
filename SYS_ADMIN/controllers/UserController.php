<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/12
 * Time: 23:02
 */

namespace SYS_ADMIN\controllers;

use abei2017\wx\Application;

/**
 * Class UserController
 * @package SYS_ADMIN\controllers
 * 管理员个人中心
 */
class UserController extends CommonController
{
    //微信扫码绑定
    public function actionBindWechat()
    {
        $userInfo = \Yii::$app->user->getIdentity();
        $bindInfo = [
            'wechat_name' => $userInfo->wechat_name,
            'wechat_img' => $userInfo->wechat_img,
            'wechat_openid' => $userInfo->wechat_openid,
        ];

        // 生成二维码
        $conf = \Yii::$app->params['wx']['mp'];
        $qrcode = (new Application(['conf' => $conf]))->driver("mp.qrcode");
        $qrInfo = $qrcode->strForver('bindWechat_'.$userInfo->getId());
        return $this->render('wechat', [
            'wechat' => $bindInfo,
            'title' => '微信绑定',
            'qrcode' => $qrInfo['url'],
        ]);
    }
}