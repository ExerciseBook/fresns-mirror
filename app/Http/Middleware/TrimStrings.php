<?php

namespace App\Http\Middleware;

use App\Helpers\DateHelper;
use App\Http\Share\AmGlobal\GlobalService;
use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;
use Closure;

// 数据转换层
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // TODO 取token信息需要更新
        $token = $request->header('bdToken');
        
        if($request->has('is_enable')){
            $isEnable = $request->input('is_enable');

            if(!is_numeric($isEnable)){
                if ($isEnable == 'true'){
                    $isEnable = 1;
                } else {
                    $isEnable = 0;
                }
            }
            $request->offsetSet('is_enable', $isEnable);
        }

        // 切换时间
        DateHelper::initTimezone();

        // 切换语言
        LangHelper::initLocale();

       // 初始化全局数据
        GlobalService::loadData();

        return $next($request);
    }

}
