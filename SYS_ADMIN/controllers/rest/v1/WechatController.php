<?php
/**
 * User: liwj
 * Date:2018/11/26
 * Time:11:03
 */

namespace SYS_ADMIN\controllers\rest\v1;


use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\Wechat;
use SYS_ADMIN\models\Client;
use yii\web\Controller;

class WechatController extends Controller
{

    public function actionAuthLogin()
    {
        $code = \Yii::$app->request->get('code');
        $appid = Wechat::$APPID;
        $appsecret = Wechat::$APPSECRET;
        if(empty($code)){
            $redirec_url = CommonHelper::getUrl();
            $redirec_url = urldecode($redirec_url);

            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirec_url}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            return $this->redirect($url);
        }

        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
        $auth_info = file_get_contents($token_url);
        $auth_info = json_decode($auth_info, true);
        var_dump($auth_info);
        if(isset($auth_info['access_token'])){
            $user_detail = [];
            $check_info = Client::findOne(['open_id' => $auth_info['open_id']]);
            if(empty($check_info)){
                //获取用户信息
                $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$auth_info['access_token']}&openid={$auth_info['openid']}&lang=zh_CN";
                $user_info = file_get_contents($user_url);
                $user_info = json_decode($user_info, true);

                $user_detail = [
                    'user_name' => $check_info->client_name,
                    'user_img' => $check_info->client_img,
                    'open_id' => $check_info->open_id,
                ];

            } else {
                $user_detail = [
                    'user_name' => $check_info->client_name,
                    'user_img' => $check_info->client_img,
                    'open_id' => $check_info->open_id,
                ];
            }

            $redis = \Yii::$app->redis;
            $redis->set($auth_info['open_id'], json_encode($user_detail));
            $redis->expire($auth_info['open_id'], 7200); // 缓存2小时

            var_dump($user_info);

        } else { // 授权失败
            //return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $auth_info['errmsg']);
            echo "授权失败:".$auth_info['errmsg'];
        }

    }


}