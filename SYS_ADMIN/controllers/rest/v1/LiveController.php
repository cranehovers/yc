<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/26
 * Time: 12:17
 */

namespace SYS_ADMIN\controllers\rest\v1;

use SYS_ADMIN\components\CommonHelper;
use SYS_ADMIN\components\ConStatus;
use SYS_ADMIN\models\Activity;
use SYS_ADMIN\models\Banner;
use SYS_ADMIN\models\Category;
use SYS_ADMIN\models\LiveRoom;
use SYS_ADMIN\models\Pictrue;
use SYS_ADMIN\models\ShoppingMall;
use SYS_ADMIN\models\Video;

/**
 * Class LiveController
 * @package SYS_ADMIN\controllers\rest\v1
 * 系统信息
 */
class LiveController extends  CommonController
{
    /**
     * @return array
     * 轮播图
     */
    public function actionBanner()
    {
        $lists = Banner::find()
            ->select(['title', 'cover_img', 'links'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->andWhere(['banner_type' => ConStatus::$BANNER_TYPE_SYS])
            ->limit(4)
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $picIds = array_column($lists, 'cover_img');
            $picLists = Pictrue::getPictrueList($picIds);

            foreach ($lists as &$item) {
                $item['cover'] = $picLists[$item['cover_img']]['pic_path'] ?? '';
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 最新活动，只取一条
     */
    public function actionActivity()
    {
        $info = Activity::find()
            ->select(['title', 'activity_time', 'activity_url'])
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->one();

        return $this->successInfo($info);
    }

    /**
     * 分类
     */
    public function actionCategory()
    {
        $lists = Category::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        return $this->successInfo($lists);
    }

    /**
     * 直播间列表
     * @param integer vtype  查询类型 2 首页推荐  1 列表
     * @param integer category 类型
     * @param integer page 分页
     */
    public function actionRoom()
    {
        $room_type = \Yii::$app->request->post('room_type', 1);
        $category = \Yii::$app->request->post('category', 0);
        $room_name = \Yii::$app->request->post('room_name', '');
        $page = \Yii::$app->request->post('page', ConStatus::$PAGE_NUM);

        $model = LiveRoom::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE]);

        if ($room_type == 2) { // 首页推荐，显示直播间  排序值为 1~10之间
            $model->andWhere(['<=', 'sort_num', 10]);
        } else {
            if (!in_array($category, [1, 2])) { // 去除特殊分类id
                $model->andWhere(['category_id' => $category]);
            }

            if (!empty($room_name)) {
                $model->andWhere(['like', 'room_name', $room_name]);
            }
        }

        $offset = ($page - 1) * ConStatus::$INDEX_ROOM_PAGE_SIZE;
        $lists = $model->offset($offset)
            ->limit(ConStatus::$INDEX_ROOM_PAGE_SIZE)
            ->select(['id as room_id', 'room_name', 'click_num', 'templet_id', 'category_id', 'online_cover', 'logo_img'])
            ->orderBy('sort_num asc, id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $picIds = array_column($lists, 'logo_img');
            $picList = Pictrue::getPictrueList($picIds);

            $roomIds = array_column($lists, 'room_id');
            $malls = ShoppingMall::find()
                ->select(['title', 'introduction', 'room_id'])
                ->where(['status' => ConStatus::$STATUS_ENABLE])
                ->andWhere(['in', 'room_id', $roomIds])
                ->indexBy('room_id')
                ->asArray()
                ->all();

            foreach ($lists as &$item) {
                $item['title'] = $malls[$item['room_id']]['title'] ?? $item['room_name'];
                $item['intro'] = $malls[$item['room_id']]['introduction'] ?? $item['room_name'];
                $item['click_num'] = CommonHelper::numberFormat($item['click_num']);
                $item['logo_img'] = $picList[$item['logo_img']]['pic_path'] ?? CommonHelper::getDefaultLogo();
            }
        }

        return $this->successInfo($lists);
    }

    /**
     * 所有视频列表
     */
    public function actionVideo()
    {
        $page = \Yii::$app->request->post('page', ConStatus::$PAGE_NUM);

        $model = Video::find()
            ->where(['status' => ConStatus::$STATUS_ENABLE]);

        $offset = ($page - 1) * ConStatus::$INDEX_VIDEO_PAGE_SIZE;
        $lists = $model->offset($offset)
            ->limit(ConStatus::$INDEX_VIDEO_PAGE_SIZE)
            ->select(['id', 'room_id', 'video_name', 'cover_img',
                'video_length', 'video_url', 'click_num', 'video_url as vurl'])
            ->orderBy('id desc')
            ->asArray()
            ->all();

        if (count($lists)) {
            $roomIds = array_column($lists, 'room_id');
            $rooms = LiveRoom::find()
                ->select(['room_name', 'id as room_id', 'logo_img'])
                ->andWhere(['in', 'id', $roomIds])
                ->indexBy('room_id')
                ->asArray()
                ->all();

            $malls = ShoppingMall::find()
                ->select(['title', 'introduction', 'room_id'])
                ->where(['status' => ConStatus::$STATUS_ENABLE])
                ->andWhere(['in', 'room_id', $roomIds])
                ->indexBy('room_id')
                ->asArray()
                ->all();

            $picIds = array_column($rooms, 'logo_img');
            $picList = Pictrue::getPictrueList($picIds);
            foreach ($lists as &$item) {
                $logo_img = $rooms[$item['room_id']]['logo_img'];
                $item['title'] = isset($malls[$item['room_id']]) ?
                    $malls[$item['room_id']]['title'] : $rooms[$item['room_id']]['room_name'];
                $item['click_num'] = CommonHelper::numberFormat($item['click_num']);
                $item['video_length'] = CommonHelper::numberFormat($item['video_length'], 2);
                $item['logo_img'] = !empty($logo_img) ? $picList[$logo_img]['pic_path'] : CommonHelper::getDefaultLogo();
            }
        }

        return $this->successInfo($lists);
    }
}