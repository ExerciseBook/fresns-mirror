# Release Notes

All notable changes to this project will be documented in this file.


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
