<p align="center"><a href="https://fresns.cn" target="_blank"><img src="https://cdn.fresns.cn/images/logo.png" width="300"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-%5E8.0-green" alt="PHP">
<img src="https://img.shields.io/badge/MySQL-%5E5.7%7C%5E8.0-orange" alt="MySQL">
<img src="https://img.shields.io/badge/License-Apache--2.0-blue" alt="License">
</p>

## 介绍

Fresns 是一款免费开源的社交网络服务软件，专为跨平台而打造的通用型社区产品，支持灵活多样的内容形态，可以满足多种运营场景，符合时代潮流，更开放且更易于二次开发。

- [点击了解产品 16 个功能特色](https://fresns.cn/guide/features.html)
- 使用者请阅读[安装教程](https://fresns.cn/guide/install.html)和[运营文档](https://fresns.cn/guide/operating.html)
- 扩展插件开发者请阅读[扩展文档](https://fresns.cn/extensions/)和[数据字典](https://fresns.cn/database/)
- 客户端开发者（网站端、小程序、App）请阅读 [API 文档](https://fresns.cn/api/)

## 免责申明

Fresns 是一款支持多语言和跨时区的免费开源软件，研发和生态建设以开源组织方式协作，我们不为任何运营主体提供技术背书，不参与任何项目运营，不承担任何法律责任。由于下载代码即可使用，所以我们无法得知你的用途，但是请在使用时遵守所在国家和地区的法律法规，禁止用于违法违规业务。

## 环境要求

| 配置 | 要求支持或启用 |
| --- | --- |
| 软件包管理器 | Composer 2.x |
| PHP 版本 | 8.x |
| PHP 扩展 | `fileinfo` `exif` |
| PHP 函数 | `putenv` `symlink` `readlink` `proc_open` `passthru` |
| 数据库和版本 | MySQL 5.7 or 8.x |

## 使用说明

本仓库为研发代码仓库，没有 vendor 引用库文件，如果使用本仓库代码包安装，需要基于命令行执行 composer 命令安装 vendor 引用库文件。如果觉得麻烦，也可以到官网[下载完整包](https://fresns.cn/guide/install.html)，官网安装包已经包含引用库文件，无需再执行命令行安装。

**部署流程**

- 1、下载本仓库[发行版代码包](https://gitee.com/fresns/fresns/releases)，上传到业务服务器解压；
- 2、在「主程序根目录」终端执行 composer 命令，下载 vendor 引用库文件；
    - 开发环境部署 `composer install`
    - 生产环境部署 `composer install --optimize-autoloader --no-dev`
- 3、在「主程序根目录」终端执行 php artisan 指令，配置管理器；
    - `php artisan vendor:publish --provider="Fresns\PluginManager\Providers\PluginServiceProvider"`
    - `php artisan vendor:publish --provider="Fresns\ThemeManager\Providers\ThemeServiceProvider"`
    - `php artisan vendor:publish --provider="Fresns\MarketManager\Providers\MarketServiceProvider"`
- 4、根据官网[安装教程](https://fresns.cn/guide/install.html)配置 Web 服务器；
- 5、访问 `网址/install` 执行安装。

## 加入我们

Fresns 的开源社区正在急速增长中，如果你认可我们的开源软件，有兴趣为 Fresns 的发展做贡献，竭诚欢迎[加入我们](https://fresns.cn/community/join.html)一起开发完善。无论是[报告错误](https://fresns.cn/guide/feedback.html)或是 Pull Request 开发，那怕是修改一个错别字也是对我们莫大的帮助。

贡献指南：[https://fresns.cn/contributing/](https://fresns.cn/contributing/)

## 联系信息

- 官方网站：[https://fresns.cn](https://fresns.cn/)
- 项目发起人：[唐杰](https://tangjie.me/)
- 电子邮箱：[support@fresns.org](mailto:support@fresns.org)
- QQ 群：[5980111](https://qm.qq.com/cgi-bin/qm/qr?k=R2pfcPUd4Nyc87AKdkuHP9yJ0MhddUaz&jump_from=webapi)
- 微信群：*扫描下方二维码加唐杰微信，告之你要加「使用交流群」或「开发者群」，稍后唐杰会拉你进群。*

<img src="https://cdn.fresns.cn/wiki/images/wechat.jpg" width="200">

## 许可协议

Fresns 主程序是根据 [Apache-2.0](https://github.com/fresns/fresns/blob/main/LICENSE) 授权的开源软件。
