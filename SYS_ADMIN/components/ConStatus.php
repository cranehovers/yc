<?php
/**
 * Created by PhpStorm.
 * User: Wilbur
 * Date: 2018/11/7
 * Time: 09:43.
 */

namespace SYS_ADMIN\components;

class ConStatus
{
    // default page
    public static $PAGE_NUM = 1;
    public static $PAGE_SIZE = 10;

    //分页条数
    public static $INDEX_ROOM_PAGE_SIZE = 8; // 直播列表每页显示数量
    public static $INDEX_VIDEO_PAGE_SIZE = 5; // 视频列表数量


    // common 公共
    public static $STATUS_PENDING = 0;
    public static $STATUS_ENABLE = 1;
    public static $STATUS_DISABLE = 2;
    public static $STATUS_DELETED = 4;

    public static $USER_ENABLE = 10;

    public static $STATUS_LIST = [
        '1' => '正常',
        '2' => '暂停',
        '4' => '删除',
    ];

    // ORDER 订单
    public static $ORDER_PAY = 1; // 已支付
    public static $ORDER_PAY_NONE = 2; // 未支付

    public static $PAY_ONLINE = 1; // 线上支付
    public static $PAY_LOCAL = 2; // 货到付款

    public static $PAY_WAY_WECHAT = 1; // 微信
    public static $PAY_WAY_ALI = 2; // 支付宝

    public static $PAY_WAY = [
        1 => '支付宝支付',
        2 => '微信支付',
    ];

    public static $ORDER_PENDING = 0;  // 待发货
    public static $ORDER_SENDED = 1;   // 已发货
    public static $ORDER_DELIVERY = 2; // 配送中
    public static $ORDER_USER_WAIT_DELIVERY = 3;    // 用户待收货
    public static $ORDER_USER_DELIVERIED = 4;   // 用户确认收货
    public static $ORDER_USER_REJECT = 5;   // 用户拒收
    public static $ORDER_NO_PAY = 6;   // 未付款的订单
    public static $ORDER_CANCEL = 7;   // 用户取消
    public static $ORDER_PAY_FINISH = 8;   // 用户付款

    public static $ORDER_LIST = [
        0 => '待发货',
        1 => '已发货',
        2 => '配送中',
        3 => '用户待收货',
        4 => '用户确认收货',
        5 => '用户拒收',
        6 => '未付款的订单',
        7 => '用户取消',
        8 => '已付款',
    ];

    public static $SEX = [
        1 => '男',
        2 => '女',
    ];

    // COMMENT 评论
    public static $COMMENT_TYPE_ROOM = 1;
    public static $COMMENT_TYPE_PROD = 2;



    // 点赞
    public static $CLIENT_START = [
        1 => '视频点赞',
        2 => '评论点赞',
        3 => '直播间收藏',
    ];

    public static $CLIENT_START_VIDEO = 1;
    public static $CLIENT_START_COMMENT = 2;
    public static $CLIENT_START_ROOM = 3;

    //商品图片数量
    public static $PRODUCT_MAX_NUM = 10;
    public static $ERROR_MSG_IMG_NUM = '最多只能上传10张图片';

    //地址
    public static $ADDR_COMMON = 1;

    // 流状态
    public static $STREAM_STATUS = [
        'publish' => 1, // 推流
        'publish_done' => 2, // 断流
    ];
    public static $TASK_TYPE = [
        1 => '推流',
        2 => '断流',
    ];

    // 存储域名
    public static $STORAGE_DOMAIN = [
        'live' => 'https://ycycc.yunchuanglive.com/',
        'live-hb' => 'https://ycycc-hb.yunchuanglive.com/',
    ];

    public static $DEFAULT_STORAGE = 3;

    // 数字格式化
    public static $NUM_FORMAT_CLIK = 1; // 点击数
    public static $NUM_FORMAT_TIME = 2; // 时长
    public static $NUM_FORMAT_DURATION = 3; // 天 时 分

    public static $SMS_EXPIRE = 300;
    public static $RECEIVER = 'receiver';
    public static $APP_KEY = 'YCLIVE';
    public static $BIND_WECHAT = 'bindWechat:';
    // 轮播图类型
    public static $BANNER_TYPE_SYS = 1;
    public static $BANNER_TYPE_ROOM = 2;
    // 状态码
    public static $ERROR_PARAMS_MSG = '参数错误';
    public static $ERROR_SYS_MSG = '网络错误';
    public static $ERROR_BANNER_IMG = '请上传轮播图';
    public static $ERROR_MOBILE_MSG = '无效手机号码';
    public static $ERROR_MOBILE_CODE_MSG = '无效验证码';

    public static $STATUS_SUCCESS = 200; // 获取成功
    public static $STATUS_ERROR_SYS = 4000; // 服务器错误
    public static $STATUS_ERROR_PARAMS = 4001; //validate 校验不通过
    public static $STATUS_ERROR_ROOMID = 4002; // 无效质检ID
    public static $STATUS_ERROR_Upload = 4003; // 图片上传失败
    public static $STATUS_ERROR_ID = 4004; // ID 不能为空
    public static $STATUS_ERROR_NONE = 4005; // 信息不存在
    public static $STATUS_ERROR_IMG_NUM = 4006; // 信息不存在
    public static $STATUS_ERROR_OPENID = 4007; // 信息不存在
    public static $STATUS_ERROR_USER_EXIT = 4008; // 用户不存在
    public static $STATUS_ERROR_ORDER_CREATE = 4009; // 订单创建失败
    public static $STATUS_ERROR_ORDER_DETAIL = 4010; // 订单详情创建失败
    public static $STATUS_ERROR_BANNER_IMG = 4011; // 缺少轮播图
    public static $STATUS_ERROR_MOBILE = 4012; // 手机号码无效
    public static $STATUS_ERROR_SMS = 4013; // 手机号码无效
}
