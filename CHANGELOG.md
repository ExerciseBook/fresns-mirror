# Release Notes

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