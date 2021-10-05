# Release Notes

## Beta (2021-08-05)

**Bug Fixes**

- 控制台：修复多语言标识名不准确，以语言文件为准
- 控制台：当 plugins > setting_path 字段为空时，设置按钮置灰
- 命令字：涉及手机号的命令字参数，缺少国际区号参数值

**Features**

- API：系统配置信息接口 info/configs 新增 itemStatus 参数
- API：编辑器 editor/update 接口，验证 pluginUnikey 参数的插件是否存在以及是否启用
- 控制台：登录页面删除验证码功能
- 控制台：打开扩展设置页，全部以 iframe 内联框架载入

**BREAKING CHANGES**

- 数据库：修改插件开发者名称和链接的字段名
- 清理 users 表冗余字段

## Alpha (2021-07-05)

**Bug Fixes**

- API：修复接口参数值与数据库不一致问题
- API：修复设备信息未存储问题，以及删除 info/uploadLog 接口重复 deviceInfo 参数
- API：修复小组权限信息未全量输出问题

**Features**

- API：快速发表 editor/publish 接口 body 参数增加 postGid 和 postTitle 参数
- API：状态码和状态信息，采用数据表记录，支持多语言
- API：member/markLists 和 post/follows 接口逻辑调整
- API：删除注册和登录接口的第三方互联相关功能
- API：新增全局概述接口，合并未读消息数
- API：完善主角色权限继承说明
- 命令字：将内容入主表的逻辑封装为 fresns_cmd_direct_release_content 命令字
- 数据库：角色表 permission 权限字段，JSON 结构修改

**BREAKING CHANGES**

- 配置表 item_type 键类型写法调整
- 清理冗余表和字段