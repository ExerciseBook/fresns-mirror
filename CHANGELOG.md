# Release Notes

All notable changes to this project will be documented in this file.


## 2.5.0 (2023-02-09)

### Added
- API: 通知消息支持标注已读全部类型的消息
- API: 同一个评论产生的回复，如果旧通知已读则再次通知

### Fixes
- API: 删除通知和会话消息，未处理用户面板缓存
- API: 通知消息列表 `status` 布尔参数 0 未被接受
- API: 帖子删除后，还能发表评论
- API: 编辑器配置参数的数字值问题
- Helper: 文件使用的缓存清理
- Panel: 插件卸载失败
- Utilities: 修复文件逻辑删除的封装功能

### Changed
- Utilities: 优化文件上传功能
- 框架: laravel/framework 升级到 v9.51.0
- 框架: fresns/cmd-word-manager 升级到 v1.3.1
- 框架: fresns/plugin-manager 升级到 v2.4.2
- 框架: fresns/theme-manager 升级到 v2.1.1
- 框架: 使用迁移作为数据变更的升级方案

### BREAKING CHANGES
- Data: Fresns 项目不再使用远程资源，局域网也可以使用。
- Data: 配置图和表情图不再使用 Fresns 远程链接，请各位尽快替换。


## 2.4.0 (2023-02-01)

### Added
- API: 当帖子或评论被禁用时，仅作者自己可见
- API: 增加白名单和黑名单 CheckHeader 中间件
- API: 快速发表请求返回草稿 ID 或 fsid
- Panel: 登录后台，可被动触发版本检测

### Fixes
- API: 关注列表的筛选条件必须为数组格式
- API: 内容中没有艾特记录，但也解析了 @ 符号
- API: 回复权配置后，自己无法回复自己帖子
- API: 刚发表的内容，人性化时间为负数

### Changed
- Data: 回调返参查询键由 UUID 改为 ULID
- API: 话题以 slug 为唯一值
- API: headers 登录检测使用黑名单机制
- API: 优化上传文件接口
- Panel: 插件管理，名称链接到应用市场
- 框架: laravel/framework 升级到 v9.49.0
- 框架: fresns/plugin-manager 升级到 v2.4.0


## 2.3.1 (2023-01-21)

### Fixes
- Console: 修复升级命令加载问题

### Changed
- Console: 重构升级功能的数据更新


## 2.3.0 (2023-01-21)

### Added
- Helper: 获取插件主机地址 `PluginHelper::fresnsPluginHostByUnikey($unikey);`

### Fixes
- Console: 编号升级指令无法被执行
- Console: 主程序定时任务未执行问题

### Changed
- Console: 优化命令字 schedule
- Helper: 修改 artisan facades
- Helper: 调整扩展缓存 tag


## 2.2.0 (2023-01-20)

### Added
- Data: 文件表 `files` 新增 `disk` 字段
- Helper: 文件信息增加“文件磁盘”判断
- Command: 升级命令增加 `storage:link` 指令

### Fixes
- API: 登录错误日志计数判断错误
- Subscribe: 兼容订阅项为空

### Changed
- Words: 重构验证路径凭证命令字
- 框架: 由引擎接管 404 页面
- 框架: fresns/cmd-word-manager 升级到 v1.3.0
- 框架: fresns/plugin-manager 升级到 v2.3.4

### BREAKING CHANGES
- API: 重构 headers 参数命名，采用 `X-` 前缀和大驼峰命名


## 2.1.0 (2023-01-18)

### Added
- Helper: 新增按标签清空缓存 `CacheHelper::forgetFresnsTag();`
- Subscribe: 支持订阅账号和用户的登录通知
- 框架: 自定义 404 页面

### Fixes
- API: 修复角色配置的缓存
- API: 评论无法查询到帖子时报错
- API: 内容最后部分的话题解析失效问题
- API: 修复用户主角色为空时报错
- API: 无时区时，日期时间格式问题
- Panel: 命令行安装插件报错问题

### Changed
- API: 子级评论列表支持嵌套显示
- API: 树结构数据为空时输出为 `[]` 空数组格式
- API: 优化内容话题的提取和替换
- 框架: laravel/framework 升级到 v9.48.0
- 框架: laravel/ui 升级到 v4.2.0
- 框架: fresns/plugin-manager 升级到 v2.3.3
- 框架: fresns/theme-manager 升级到 v2.0.8
- 框架: fresns/market-manager 升级到 v2.1.1


## 2.0.1 (2023-01-11)

### Changed
- 优化缓存标签


## 2.0.0 (2023-01-09)

### Added
- Panel: 支持仅清空文件缓存

### Fixes
- API: 评论自己时也会产生通知消息
- API: 变更用户资料后，未清理发表权限缓存
- API: 删除评论时，帖子的评论计数未减少
- API: 验证码登录时自动注册，发送验证码未处理兼容

### Changed
- API: 帖子和评论的创作者信息独立缓存，修改用户资料后，同步变化资料
- Helper: 优化文件查找模型
- 框架: laravel/framework 升级到 v9.46.0


## 2.0.0-beta.8 (2022-12-24)

### Added
- API: 请求标头 `contentFormat` 参数，允许获取指定格式的内容
- API: 帖子信息可以预览多条评论
- API: 帖子信息可以预览多条点赞用户
- Panel: 互动配置新增评论预览设置
- Panel: 互动配置新增点赞用户预览设置
- Panel: 引擎远程 API Host 保存时自动处理 `/` 结尾
- Panel: 新增缓存管理页面

### Fixes
- API: 修复帖子编辑后缓存未自动清理问题
- API: 编辑器上传文件时，未判断数量限制
- Panel: 地图设置字段错误

### Changed
- API: 优化缓存机制
- Data: `post_appends->is_allow` 字段默认值改为 `1`
- 框架: composer 升级到 v2.5.1
- 框架: laravel/framework 升级到 v9.45.1
- 框架: fresns/plugin-manager 升级到 v2.3.2


## 2.0.0-beta.7 (2022-12-13)

### Added
- API: 评论信息增加 `latestCommentTime` 子级评论时间参数

### Fixes
- API: 评论发表成功后，帖子 `latest_comment_at` 时间字段错误
- Data: cookies 语言标签未更改成功
- Panel: 站点网址保存失败

### Changed
- API: 账号和用户凭证验证时忽略 App ID
- Data: 重置初始语言包


## 2.0.0-beta.6 (2022-12-12)

### Added
- API: 发表评论后，更新帖子最后评论时间
- API: 帖子和评论列表接口，新增 `allDigest` 和 `following` 参数
- Helper: 根据文件名获取文件类型编号，不区分大小写

### Fixes
- API: 退出登录错误
- API: 验证码模板 ID 不匹配问题
- API: 内容类型筛选大小写匹配
- Panel: 检测版本为空时报错

### Changed
- API: 内容类型命名采用复数 `/api/v2/global/{type}/content-types`
- 框架: fresns/plugin-manager 升级到 v2.3.0


## 2.0.0-beta.5 (2022-12-08)

### Added
- API: 验证 headers deviceInfo 是否格式匹配
- API: 评论列表，当所属帖子已删除，则不跳过

### Fixes
- API: 帖子和评论详情页内容缓存错误
- API: 回复评论时层级错误
- API: 删除帖子和评论时，计数没有回滚

### Changed
- API: 角色发表时间间隔限制单位，由`分钟`修改为`秒`
- API: headers 参数中 `token` 拆分成 `aidToken` 和 `uidToken`
- 框架: laravel/framework 升级到 v9.43.0


## 2.0.0-beta.4 (2022-12-01)

### Added
- API: 新增缓存，提升访问速度
- Panel: 保存 `URL` 和 `Path` 时，过滤左右空格和结尾 `/` 符号
- Model: 文件信息增加 `middle` 选项，从文件名开头拼接图片参数
- Data: 数据表 `user_follows` 增加 `is_enable` 字段
- 主程序内置资源增加 Font Awesome Free 6.2.1

### Fixes
- Panel: 扩展安装失败时提示文案不匹配

### Changed
- `interactive` 修改为 `interaction`
- API: 验证码移至参数格式判断之后，避免格式错误导致验证码提前失效
- API: 签名的 `App Secret` 拼接由 `&key=` 修改为 `&appSecret=`
- Panel: 配置保存时 `foreach` 循环中模型为空时 `continue` 跳过
- 框架: laravel/framework 升级到 v9.42.2


## 2.0.0-beta.3 (2022-11-28)

### Added
- Panel: 引擎 cookie 前缀可选
- Panel: 自动检验并修正插件启用状态
- Panel: 小组发表权限配置，增加选项仅限管理员
- Panel: 仪表盘升级插件增加进度条
- Panel: 发表配置插件上传页增加状态判断

### Features
- Subscribe: 移除订阅表限制，开放所有表

### Fixes
- API: 修复 DTO 提示信息未使用问题
- API: 小组列表 sublevel_public 逻辑处理
- Panel: 中文缺失语言 site_mode_public_register_type_phone
- Panel: 避免小组自定义配置被覆盖

### Changed
- API: 修改小组权限检测的文案 code
- API: 优化验证码登录时，无账号自动注册
- Data: 配置键名 account_cookie_status 修改为 account_cookies_status
- Data: 配置键名 account_cookie 修改为 account_cookies
- Data: 语言包标识名 accountPoliciesCookie 修改为 accountPoliciesCookies
- Panel: 移除控制面板的 ConfigHelper 使用，避免缓存
- Panel: 优化自动和物理升级功能
- 框架: fresns/plugin-manager 升级到 v2.2.0


## 2.0.0-beta.2 (2022-11-23)

### Added
- API: 重构 token 逻辑，有效期增加小时和天数参数
- Panel: 站点设置支持配置“验证码登录时，无账号则自动注册”

### Fixes
- API: 当 site url 未设置时内容链接处理报错
- Panel: 无法从应用市场下载应用
- Panel: 清空缓存报错

### Changed
- 框架: laravel/framework 升级到 v9.41.0
- 框架: fresns/plugin-manager 升级到 v2.1.1
- 框架: fresns/market-manager 升级到 v2.1.0
- 引擎: FresnsEngine 升级到 v2.0.0-beta.2
- 主题: ThemeFrame 升级到 v2.0.0-beta.2


## 2.0.0-beta.1 (2022-11-22)

- 2.x 首个公测版
