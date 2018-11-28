<?php
/**
 * User: liwj
 * Date:2018/11/26
 * Time:11:03
 */

namespace SYS_ADMIN\controllers\rest\v1;


use abei2017\wx\Application;
use Codeception\Module\Cli;
use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\components\Wechat;
use SYS_ADMIN\models\Client;
use SYS_ADMIN\models\Order;
use yii\web\Controller;

class WechatController extends CommonController
{
    public $enableCsrfValidation = false;

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

        if(isset($auth_info['access_token'])){
            $user_detail = [];
            $check_info = Client::findOne(['open_id' => $auth_info['openid']]);
            if(empty($check_info)){
                //获取用户信息
                $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$auth_info['access_token']}&openid={$auth_info['openid']}&lang=zh_CN";
                $user_info = file_get_contents($user_url);
                $user_info = json_decode($user_info, true);

                $model = new Client();
                $model->client_name = $user_info['nickname'];
                $model->client_img = $user_info['headimgurl'];
                $model->open_id = $user_info['openid'];
                $model->client_name = $user_info['nickname'];
                $model->sex = $user_info['sex'];
                $model->city = $user_info['city'];
                $model->save();

                $user_detail = [
                    'user_name' => $user_info['nickname'],
                    'user_img' => $user_info['headimgurl'],
                    'open_id' => $user_info['openid'],
                    'uid' => $model->id,
                ];

            } else {
                $user_detail = [
                    'user_name' => $check_info->client_name,
                    'user_img' => $check_info->client_img,
                    'open_id' => $check_info->open_id,
                    'uid' => $check_info->id,
                ];
            }

            $redis = \Yii::$app->redis;
            $redis->set($auth_info['openid'], json_encode($user_detail));
            $redis->expire($auth_info['openid'], 7200); // 缓存2小时

            var_dump($user_info);

        } else { // 授权失败
            //return $this->errorInfo(ConStatus::$STATUS_ERROR_PARAMS, $auth_info['errmsg']);
            echo "授权失败:".$auth_info['errmsg'];
        }

    }

    public function actionNotify()
    {

        $conf = \Yii::$app->params['wx']['mp'];
        $pay = (new Application(['conf'=>$conf]))->driver("mp.pay");

        $response = $pay->handleNotify(function($notify,$isSuccess){
            if($isSuccess){
                CommonHelper::writeOrderLog($notify);
                //$notify_data = json_decode($notify, true);

                $order_id = $notify['out_trade_no'];
                $order_info = Order::find()
                    ->where(['order_id' => $order_id])
                    ->one();

                $total_fee = $order_info->real_total_money * 100;
                if($total_fee !=  $notify['total_fee']){
                    CommonHelper::writeOrderLog(['order_id' => $order_id, 'msg' => 'fee error', 'data' => $notify]);
                    return false;
                }

                if($order_info->is_pay != ConStatus::$ORDER_PAY){ // 更新订单状态
                    $order_info->is_pay = ConStatus::$ORDER_PAY;
                    $order_info->order_status = ConStatus::$ORDER_PAY_FINISH;
                    $order_info->pay_from = ConStatus::$PAY_WAY_WECHAT;
                    $order_info->trade_no = $notify['transaction_id'];
                    if($order_info->save()){
                        return true;
                    } else {
                        CommonHelper::writeOrderLog(['order_id' => $order_id, 'msg' => 'fee error', 'data' => $order_info->getFirstErrors()]);
                        return false;
                    }

                }

                return true;

            }
        });

        return $response;
    }

    public  function actionTest()
    {
        //$notify = '{"appid":"wx2e4c11f43a7669eb","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1313582001","nonce_str":"BN404ck_pWaHatr-nknOMa0ALRZ5m7Qw","openid":"omIqUv9pP6EaM3tqd4UoAs4J4Ncw","out_trade_no":"2018112892002","result_code":"SUCCESS","return_code":"SUCCESS","sign":"9F5707EA90BA5CE2AB0EB83B7B99473E","time_end":"20181128203928","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000220201811287915942939"}';


    }

    public function actionJssdk()
    {
        $url = \Yii::$app->request->post('url');
        $apis = \Yii::$app->request->post('apis');
        $js = (new Application(['conf' => \Yii::$app->params['wx']['mp']]))->driver("mp.jssdk");

        $apis = explode(',', $apis);
        $sdk = $js->buildConfigJs($apis, false, $url);
        return $this->successInfo($sdk);
    }
}