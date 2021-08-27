<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use Faker\Factory;

// 随机数生成
class RandomHelper
{
    public $faker = null;

    const RAND_INT = 'randInt';
    const RAND_TEXT = 'randText';
    const RAND_REMARK = 'randRemark';
    const RAND_DATE = 'randDate';
    const RAND_JSON = 'randMoreJson';
    const RAND_FILE = 'randFile';
    const RAND_BOOL = 'randBool';
    const RAND_STRING = 'randString';
    const RAND_NAME = 'randName';
    const RAND_USERNAME = 'randUserName';
    const RAND_PHONE = 'randPhone';
    const RAND_EMAIL = 'randEmail';
    const RAND_HTML = 'randHtml';
    const RAND_COLOR = 'randColor';

    public function __construct($lang = 'en')
    {
        $this->faker = Factory::create($lang);
    }

    public function getFaker()
    {
        return $this->faker;
    }

    // 整数
    public function randInt($min = 0, $max = 999999)
    {
        return $this->faker->randomDigitNotNull;
    }

    public function randIntByLength($length = 6)
    {
        return $this->faker->numberBetween(100000, 999999);
    }

    // 电话
    public function randPhone($min = 0, $max = 999999)
    {
        return $this->faker->phoneNumber;
    }

    // 邮箱
    public function randEmail($min = 0, $max = 999999)
    {
        return $this->faker->email;
    }

    // Html
    public function randHtml($min = 0, $max = 999999)
    {
        return $this->faker->randomHtml();
    }

    // 字符串
    public function randString($length = 20, $prefix = '')
    {
        return $prefix.StrHelper::randString($length);
    }

    // url
    public function randUrl()
    {
        return $this->faker->randomElement(self::COVERS);
    }

    // file
    public function randFile()
    {
        return $this->faker->randomElement(self::COVERS);
    }

    // 头像
    public function randAvatar()
    {
        return $this->faker->randomElement(self::AVATARS);
    }

    // 人名
    public function randUserName()
    {
        return $this->faker->randomElement(self::USER_NAME);
    }

    // 名称
    public function randName()
    {
        return $this->faker->name();
    }

    // 布尔
    public function randBool()
    {
        return $this->faker->boolean;
    }

    // text
    public function randText()
    {
        return $this->faker->text();
    }

    // remark
    public function randRemark()
    {
        return $this->faker->randomElement(self::DESC);
    }

    // json
    public function randMoreJson()
    {
        $arr = [
            StrHelper::randString(3),
            StrHelper::randString(5),
            StrHelper::randString(8),
        ];
        $jsonArr = [];
        foreach ($arr as $key) {
            $jsonArr[$key] = StrHelper::randString(8);
        }

        $jsonArr['list'] = $this->faker->randomElements();

        return json_encode($jsonArr);
    }

    // date
    public function randDate()
    {
        return $this->faker->dateTimeBetween('-12 days', '+3 days');
    }

    // color
    public function randColor()
    {
        return $this->faker->hexColor;
    }

    const TITLES = [
        'Alipay',
        'Angular',
        'Ant Design',
        'Ant Design Pro',
        'Bootstrap',
        'React',
        'Vue',
        'Webpack',
    ];

    // 头像
    const AVATARS = [
        'https://gw.alipayobjects.com/zos/rmsportal/WdGqmHpayyMjiEhcKoVE.png', // Alipay
        'https://gw.alipayobjects.com/zos/rmsportal/zOsKZmFRdUtvpqCImOVY.png', // Angular
        'https://gw.alipayobjects.com/zos/rmsportal/dURIMkkrRFpPgTuzkwnB.png', // Ant Design
        'https://gw.alipayobjects.com/zos/rmsportal/sfjbOqnsXXJgNCjCzDBL.png', // Ant Design Pro
        'https://gw.alipayobjects.com/zos/rmsportal/siCrBXXhmvTQGWPNLBow.png', // Bootstrap
        'https://gw.alipayobjects.com/zos/rmsportal/kZzEzemZyKLKFsojXItE.png', // React
        'https://gw.alipayobjects.com/zos/rmsportal/ComBAopevLwENQdKWiIn.png', // Vue
        'https://gw.alipayobjects.com/zos/rmsportal/nxkuOJlFJuAUhzlMTCEe.png', // Webpack
    ];

    const COVERS = [
        'https://gw.alipayobjects.com/zos/rmsportal/uMfMFlvUuceEyPpotzlq.png',
        'https://gw.alipayobjects.com/zos/rmsportal/iZBVOIhGJiAnhplqjvZW.png',
        'https://gw.alipayobjects.com/zos/rmsportal/iXjVmWVHbCJAyqvDxdtx.png',
        'https://gw.alipayobjects.com/zos/rmsportal/gLaIAoVWTtLbBWZNYEMg.png',
    ];
    const DESC = [
        '那是一种内在的东西， 他们到达不了，也无法触及的',
        '希望是一个好东西，也许是最好的，好东西是不会消亡的',
        '生命就像一盒巧克力，结果往往出人意料',
        '城镇中有那么多的酒馆，她却偏偏走进了我的酒馆',
        '那时候我只会想自己想要什么，从不想自己拥有什么',
    ];

    const USER_NAME = [
        '付小小',
        '曲丽丽',
        '林东东',
        '周星星',
        '吴加好',
        '朱偏右',
        '鱼酱',
        '乐哥',
        '谭小仪',
        '仲尼',
    ];
}
