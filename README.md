<p align="center"><a href="https://fresns.cn" target="_blank"><img src="https://cdn.fresns.cn/images/logo.png" width="300"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/Fresns-1.x-yellow" alt="Fresns">
<img src="https://img.shields.io/badge/PHP-%5E8.0-blue" alt="PHP">
<img src="https://img.shields.io/badge/MySQL-%5E5.7%7C%5E8.0-orange" alt="MySQL">
<img src="https://img.shields.io/badge/License-Apache--2.0-green" alt="License">
</p>

## 介绍

Fresns 是一款免费开源的社交网络服务软件，专为跨平台而打造的通用型社区产品，支持灵活多样的内容形态，可以满足多种运营场景，符合时代潮流，更开放且更易于二次开发。

- [点击了解产品 16 个功能特色](https://fresns.cn/guide/features.html)
- 使用者请阅读[安装教程](https://fresns.cn/guide/install.html)和[运营文档](https://fresns.cn/operating/)；
- 扩展插件开发者请阅读[扩展文档](https://fresns.cn/extensions/)和[数据字典](https://fresns.cn/database/)；
- 客户端开发者（网站端、小程序、App）请阅读 [API 文档](https://fresns.cn/api/)。

## 免责申明

Fresns 是一款支持多语言和跨时区的免费开源软件，研发和生态建设以开源组织方式协作，我们不为任何运营主体提供技术背书，不参与任何项目运营，不承担任何法律责任。由于下载代码即可使用，所以我们无法得知你的用途，但是请在使用时遵守所在国家和地区的法律法规，禁止用于违法违规业务。

## 技术框架

| 框架 | 版本 | 用途 |
| --- | --- | --- |
| [Composer](https://github.com/composer/composer) | 2.1.11 | 软体包管理系统 |
| [Laravel Framework](https://github.com/laravel/framework) | 8.69.0 | 主程序框架 |
| [Bootstrap](https://getbootstrap.com/) | 5.1.3 | 内置前端框架 |
| [Bootstrap Icons](https://icons.getbootstrap.com/) | 1.7.0 | 内置前端图标字体库 |
| [jQuery](https://github.com/jquery/jquery) | 3.6.0 | 内置 JS 库 |
| [Base64 JS](https://github.com/dankogai/js-base64) | 3.7.2 | 内置 Base64 转码器 |

| 配置 | 要求支持或启用 |
| --- | --- |
| PHP 扩展 | `fileinfo` |
| PHP 函数 | `putenv` `symlink` `readlink` `proc_open` |

| 数据库 | MySQL 5.7 | MySQL 8.x |
| --- | --- | --- |
| 排序规则 | `utf8mb4_unicode_520_ci` | `utf8mb4_0900_ai_ci` |
| 存储引擎 | InnoDB | InnoDB |

## 使用说明

本仓库为研发代码仓库，没有 vendor 引用库文件，如果使用本仓库代码包安装，需要基于命令行执行 composer 命令安装 vendor 引用库文件。如果觉得麻烦，也可以到官网[下载完整版安装包](https://apps.fresns.cn/)，官网安装包已经包含引用库文件，无需再执行命令行安装。

*请确保服务器已经安装了 Composer 软体包管理工具*

### 开发部署

- 1、下载本仓库发行版代码包，上传到业务服务器解压；
- 2、将主程序根目录 `.env.debug` 文件重命名为 `.env`，根据官网[安装教程](https://fresns.cn/guide/install.html)配置数据库信息；
- 3、在「主程序根目录」执行命令行 `composer install`；
- 4、其余配置流程同官网[安装教程](https://fresns.cn/guide/install.html)一致。

### 生产部署

- 1、下载本仓库发行版代码包，上传到业务服务器解压；
- 2、将主程序根目录 `.env.example` 文件重命名为 `.env`，根据官网[安装教程](https://fresns.cn/guide/install.html)配置数据库信息；
- 3、在「主程序根目录」执行命令行 `composer install --optimize-autoloader --no-dev`；
- 4、其余配置流程同官网[安装教程](https://fresns.cn/guide/install.html)一致。

## 加入我们

Fresns 的开源社区正在急速增长中，如果你认可我们的开源软件，有兴趣为 Fresns 的发展做贡献，竭诚欢迎[加入我们](https://fresns.cn/community/join.html)一起开发完善。无论是[报告错误](https://fresns.cn/guide/feedback.html)或是 Pull Request 开发，那怕是修改一个错别字也是对我们莫大的帮助。

贡献指南：[https://fresns.cn/contributing/](https://fresns.cn/contributing/)

## 联系信息

- 官方网站：[https://fresns.cn](https://fresns.cn/)
- 项目发起人：[唐杰](https://tangjie.me/)
- 电子邮箱：[jarvis.okay@gmail.com](mailto:jarvis.okay@gmail.com)
- QQ 群：[5980111](https://qm.qq.com/cgi-bin/qm/qr?k=R2pfcPUd4Nyc87AKdkuHP9yJ0MhddUaz&jump_from=webapi)
- Telegram 群：[https://t.me/fresns_zh](https://t.me/fresns_zh)
- 微信群：[点击查看加群二维码](https://tangjie.me/media/wechat/fresns.jpg)

## 许可协议

Fresns 是根据 [Apache-2.0](https://opensource.org/licenses/Apache-2.0) 授权的开源软件。
