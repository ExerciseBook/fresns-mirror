# Release Notes

All notable changes to this project will be documented in this file.

## 1.6.0 (2022-05-06)

**Added**

- 安装：检测 `Composer` 版本号
- 控制面板：仪表盘输出 `Composer` 版本和配置信息
- 控制面板：钱包设置新增货币自定义命名、货币单位自定义命名、货币精度
- 命令字：命令字管理器 Code Messages 信息存入 `code_messages` 数据表
- API：帖子和评论列表增加 `isMarkdown` 参数

**Fixes**

- API：修复当用户不存在时，消息接口缺少补位信息。
- 升级：修复升级功能运行错误
- 数据库：为不限长度的 string 字段增加 255 字符长度值，兼容 MySQL 5.7 安装
- 数据库：补齐 block_words 表缺失的 deleted_at 字段
- 配置：修改 session 配置方法，避免单域多站点的 session 冲突

**Changed**

- 框架资源：公共 error 视图文件支持 html 格式
- 控制面板：调整语言文件 tips 内容
- 控制面板：仪表盘时区标识名错误时，展示修改建议
- 控制面板：安装、升级、卸载等操作，不自动刷新页面，点击“关闭”按钮再刷新
- 控制面板：检测版本的时间日志存入数据表
- 框架：Laravel Framework 升级到 v8.83.11
- 框架：Laravel Lang 升级到 v10.8.0


## 1.5.1 (2022-05-02)

**Bug Fixes**

- 安装：获取域错误问题
- 控制面板：插件安装错误问题


## 1.5.0 (2022-05-01)

**Bug Fixes**

- API：用户资料新增 `location` 参数
- API：用户资料新增 `archives` 数组参数
- API：修复话题 `description` 参数多语言输出问题

**Features**

- 框架：Composer 升级到 v2.3.5
- 框架：Laravel Framework 升级到 v8.83.10
- 框架：Bootstrap Icons 升级到 v1.8.1
- 框架：新增 Laravel Lang 包 v10.7.1
- 框架：新增 Alpine JS 包 v3.10.2
- 控制面板：全新控制面板和可视化升级功能
- 安装功能：全新可视化安装

**BREAKING CHANGES**

- 数据库：重定义了账号、用户、贴纸、屏蔽的表命名
- 扩展包：发版插件管理器（[fresns/plugin-manager](https://github.com/fresns/plugin-manager)）
- 扩展包：发版命令字管理器（[fresns/cmd-word-manager](https://github.com/fresns/cmd-word-manager)）
- 扩展包：发版数据传输对象（[fresns/dto](https://github.com/fresns/dto)）
- 命令字：全新封装官方命令字
- 通用支持：开发了各种辅助函数 Helpers
- 通用支持：开发了各种实用程序 Utilities
- 通用支持：开发了各种数据集模型 Models


## 1.4.0 (2022-01-05)

**Bug Fixes**

- API：修改注册关闭时提示不准确
- API：修复配置参数布尔型错误问题
- API：报错时 data 默认返回 null
- API：上传文件 tableId 传参处理问题

**Features**

- 控制台：无设置时不显示按钮
- 框架：Laravel Framework 升级到 v8.78.0
- 框架：Bootstrap Icons 升级到 v1.7.2
- 数据库：初始化多语言配置


## 1.3.0 (2021-11-13)

**Bug Fixes**

- API：上传文件，修复 tableId 传参未做转换的问题

**Features**

- 实现可视化安装和升级
- 实现成员昵称和名称的规则要求
- 框架：Composer 升级到 v2.1.12
- 框架：Laravel Framework 升级到 v8.70.2
- 框架：Bootstrap Icons 升级到 v1.7.0

**BREAKING CHANGES**

- build: laravel migrations
- build: laravel seeders


## 1.2.0 (2021-11-01)

**Bug Fixes**

- API：修复配置接口无法翻页问题
- API：修复内容编辑权限判断错误问题
- API：修复配置信息接口无法翻页问题
- API：修复主帖删除导致评论列表报错问题

**Features**

- API：用户资料接口，增加用户密码和钱包密码状态参数
- API：通知消息增加时间参数
- API：新增身份验证接口
- API：成员修改资料接口，头像传参名变更
    - avatarFileId 修改为 avatarFid
    - avatarFileUrl 修改为 avatarUrl
- API：上传图片返参增加 imageConfigUrl 和 imageAvatarUrl 参数
- 命令字：用户注册功能 avatarFileUrl 参数修改为 avatarUrl


## 1.1.0 (2021-10-28)

**Bug Fixes**

- API：修正帖子和评论 icons 输出有误
- API：修正评论列表和详情页，主帖匿名信息有误
- API：修正发表摘要状态变更
- API：修正有权限要求的帖子输出，按百分比截断
- API：快速发表单个图片文件，修复后缀判断
- API：修复 transactionAmount 参数错误

**Features**

- API：修改评论列表接口子级评论预览结构
- API：涉及成员信息的接口，增加成员主角色 rid 参数
- API：成员列表和详情增加 followMeStatus 参数
- 框架：升级到 Laravel Framework 8.68.1


## 1.0.2 (2021-10-23)

**Bug Fixes**

- API：帖子详情页头像获取错误
- API：修复评论列表主帖作者信息错误
- API：快速发表未通知插件处理文件问题
- API：修复评论输出获取成员图标错误
- 控制台：控制台设置，保存成功后，没有任何提示
- 控制台：删除管理员失败后，没有关闭模态框，导致页面元素被挡住不可再点击

**Features**

- API：帖子和评论详情页 Markdown 格式不解析链接
- API：查看成员资料，如果查看者是自己，也输出 Mark 状态
- API：加大每分钟 API 请求次数限制，增加到 600
- API：涉及成员信息的接口，增加 verifiedDesc 参数
- 命令字：长图计算比例调整为 3 倍
- 内置前端图标字体库 Bootstrap Icons 升级到 1.6.1
- 内置 Base64 转码器升级到 3.7.2


## 1.0.1 (2021-10-18)

**Bug Fixes**

- API：不输出「未启用」的小组分类和小组
- API：获取帖子列表，传参 searchGid 为小组 uuid 字段，因查询 id 字段导致无数据
- API：帖子和评论的详情页 content 参数读取附属表


## 1.0.0 (2021-10-15)

首个正式版
