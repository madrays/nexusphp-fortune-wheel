# NexusPHP 幸运转盘插件

一个为NexusPHP开发的幸运转盘插件，用户可以消耗魔力抽取各种奖品。

## 功能特性

- 🎰 **转盘抽奖**: 精美的转盘界面，支持多种奖品类型
- 🏆 **多种奖品**: 支持魔力、VIP、上传量、下载量、邀请名额、勋章等奖品
- 📊 **概率控制**: 管理员可自由配置每个奖品的中奖概率
- 📈 **数量管理**: 支持限量奖品，数量用完自动下架
- 📋 **记录追踪**: 完整的抽奖记录和统计信息
- 🎯 **限制机制**: 支持每日抽奖次数限制、用户等级限制
- 💰 **魔力消耗**: 每次抽奖消耗指定数量的魔力
- 🎁 **VIP特权**: VIP用户中VIP奖品时可获得额外魔力奖励

## 奖品类型

- **魔力 (bonus)**: 直接增加用户魔力值
- **VIP (vip)**: 升级用户为VIP或延长VIP时间
- **上传量 (upload)**: 增加用户上传量
- **下载量 (download)**: 增加用户下载量  
- **邀请名额 (invitation)**: 增加用户邀请名额
- **勋章 (medal)**: 给用户颁发勋章
- **谢谢参与 (nothing)**: 安慰奖，不给予任何奖励

## 安装指南

### 1. 下载插件

将插件下载到站点根目录的 `packages` 文件夹：

```bash
# 创建 packages 目录（如果不存在）
mkdir -p packages

# 将下载的插件解压到 packages 目录
# 确保目录结构为：packages/nexusphp-fortune-wheel/
```

### 2. 修改 Composer 配置

在站点根目录的 `composer.json` 中添加：

```json
{
    "repositories": {
        "fortune-wheel": {
            "type": "path",
            "url": "./packages/nexusphp-fortune-wheel"
        }
    },
    "require": {
        "madrays/nexusphp-fortune-wheel": "dev-main"
    }
}
```

### 3. 安装插件

```bash
# 重新生成自动加载文件
composer dump-autoload

# 安装插件
composer require madrays/nexusphp-fortune-wheel

# 执行插件安装
php artisan plugin install madrays/nexusphp-fortune-wheel
```

### 4. 运行数据库迁移

```bash
# 创建幸运转盘表
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_01_01_000001_create_fortune_wheel_tables.php --force

# 添加奖品字段
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_01_01_000002_add_prize_fields.php --force

# 移除未使用字段
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_01_01_000003_remove_unused_fields.php --force

# 更新奖品类型注释
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_07_29_100000_update_prize_type_comment.php --force

# 添加奖品名称到记录表
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_07_29_160000_add_prize_name_to_records_table.php --force

# 创建导航菜单表
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_07_29_180000_create_navigations_table_v2.php --force

# 添加结果字段到记录表
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_07_29_200000_add_result_fields_to_records_table.php --force

# 添加外部链接支持
php artisan migrate --path=packages/nexusphp-fortune-wheel/database/migrations/2024_07_29_210000_add_is_external_to_navigations.php --force
```

### 5. 清除缓存

```bash
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

## 配置说明

安装完成后，插件会自动创建以下默认配置：

- **启用状态**: 默认启用
- **每次消耗魔力**: 1000 (1GB)
- **VIP额外奖励**: 500 (500MB)
- **每日限制**: 10次
- **最低等级**: 1级

## 默认奖品

插件安装时会自动创建以下默认奖品：

1. **魔力 100G** - 概率30%
2. **魔力 500G** - 概率15%  
3. **VIP 7天** - 概率10%
4. **上传量 50G** - 概率20%
5. **谢谢参与** - 概率25%

## 使用方法

### 用户端
1. 访问 `/fortune-wheel` 页面
2. 确保有足够的魔力和抽奖次数
3. 点击"开始抽奖"按钮
4. 等待转盘停止，查看中奖结果
5. 在页面下方查看抽奖记录

### 管理员端
1. 在后台管理中找到"幸运转盘"设置
2. 配置基本参数（消耗魔力、限制等）
3. 管理奖品（添加、编辑、删除）
4. 查看抽奖统计数据

## 技术特性

- 基于NexusPHP插件系统开发
- 使用Hook机制集成到主程序
- 响应式设计，支持移动端
- 平滑的CSS3动画效果
- AJAX异步抽奖，用户体验良好
- 完整的错误处理和日志记录

## 兼容性

- **NexusPHP版本**: >= 1.7.21
- **PHP版本**: >= 8.2
- **数据库**: MySQL/MariaDB

## 文件结构

```
nexusphp-fortune-wheel/
├── src/                                  # 源代码目录
│   ├── FortuneWheelServiceProvider.php   # 服务提供者
│   └── FortuneWheelRepository.php        # 主要业务逻辑
├── database/migrations/                  # 数据库迁移
├── resources/                            # 资源文件
│   ├── assets/                          # 前端资源
│   ├── views/                           # 视图文件
│   └── lang/                           # 语言文件
├── public/                              # 公共文件
└── README.md                            # 说明文档
```

## 更新日志

### v1.0.0
- 初始版本发布
- 基础转盘抽奖功能
- 多种奖品类型支持
- 完整的管理后台
- 用户统计和记录功能

## 许可证与授权

本插件采用商业许可证，需要有效的授权才能使用。

### 授权验证

插件包含多层安全验证机制：
- **代码混淆保护**：核心业务逻辑已混淆处理
- **多点验证**：验证逻辑分布在多个位置
- **机器码绑定**：绑定特定服务器环境
- **渐进式失效**：验证失败时逐步限制功能

### 获取授权

如需使用本插件，请联系开发者 **madrays** 获取正式授权：

- **联系方式**：通过GitHub Issues或邮件联系
- **授权流程**：提供服务器信息 → 生成专属许可证 → 交付授权文件
- **技术支持**：授权用户可获得完整的技术支持

### 授权说明

- 每个授权仅限一台服务器使用
- 授权包含完整的源代码和技术支持
- 未经授权的使用将受到法律保护

## 支持

如有问题或建议，请提交Issue或联系开发者 **madrays**。