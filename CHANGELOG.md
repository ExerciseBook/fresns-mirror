# Release Notes

All notable changes to this project will be documented in this file.


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
