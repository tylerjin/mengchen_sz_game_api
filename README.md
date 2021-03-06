# 管理后台
## 环境依赖
- php >= 5.6  
- nginx打开ssi  
- redis >= 2.8
- composer  
- supervisor  

```
supervisor配置文件模版：
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
directory={code_ducument_root}   ;低版本不支持此指令
command=/usr/bin/php {code_ducument_root}/artisan queue:work --delay=3 --sleep=1 --tries=3 --timeout=60
autostart=true
autorestart=true
startretries=3
user=nginx
numprocs=1
redirect_stderr=true
stdout_logfile=/data/log/supervisor/%(program_name)s.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10
```  
- node & npm

```
安装node和npm环境：
curl --silent --location https://rpm.nodesource.com/setup_8.x | sudo bash -
yum -y install nodejs
```

## 单元测试与代码覆盖率
测试并生成代码覆盖率报表：  
```
cd ${code_ducument_root}
./vendor/bin/phpunit --coverage-html public/test/
```
查看代码覆盖率：
```
URI: /test/index.html
```

## 生产环境代码发布  

```
cd ${code_ducument_root}
git pull                #获取最新代码
composer install        #安装laravle依赖
cp .env.example .env    #配置文件(根据生产环境配置对应的参数)
php artisan storage:link    #创建符号链接到文件上传目录
chmod -R {phpfpm_runner}.{phpfpm_runner} ./ #更改代码目录的权限为phpfpm程序的运行用户
chmod +x vendor/phpunit/phpunit/phpunit #添加执行权限
./vendor/bin/phpunit    #代码测试

cd client       #进入js开发目录
npm install     #安装npm包
npm run build   #编译js代码
```  

### cron计划任务
```
crontab -e
* * * * * php {code_ducument_root}/artisan schedule:run >> /dev/null 2>&1  

#注意：.env里面正确配置好日志输出文件"CRON_TASK_LOG"

运行容器时命令：
* * * * * /usr/local/bin/docker-compose -f /data/docker/docker-compose.yml exec -T php /bin/bash -c "runuser -u www-data php /data/www/mengchen_sz/artisan schedule:run" >> /dev/null 2>&1
```  

**任务列表**  

### 使用post-merge钩子脚本  
```
#!/bin/sh

codeDir=$(cd $(dirname $0); pwd)'/../../'

service supervisord restart     #重启队列

cd $codeDir
composer install

cd client
npm install
npm run build
```

## 开发环境规范
### 开发环境使用pre-push钩子
```
#!/bin/sh

codeDir=$(cd $(dirname $0); pwd)'/../../'
cd $codeDir
./vendor/bin/phpunit
```

## 接口列表
### for web(网站后台)
| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| /records | GET | 获取所有战绩信息 |
| /records | POST | 查询单个玩家战绩 |
| /record-info | POST | 根据战绩id查询单条战绩详情 |
| /players | GET | 获取所有玩家信息 |
| /players/online/amount | GET | 获取实时在线玩家数量 |
| /players/online/peak | GET | 获取指定日期的当日玩家最高在线数量 | 
| /players/in-game/peak | GET | 获取指定日期的游戏中玩家最高数量 | 
| /players/in-game | GET | 获取实时游戏中玩家数量 | 
| /players | POST | 查询玩家 | 
| /players/find | POST | 通过uid精确查找玩家 | 
| /players/batch-find | POST | 通过uids批量查找玩家 | 
| /top-up | POST | 玩家充值 | 
| /room | POST | 创建游戏房间 | 
| /room/open | GET | 查看正在玩的房间 | 
| /room/history | GET | 查看已经结束的房间 | 
| /card/consumed | GET | 查看指定日期的房卡消耗数据 | 
| /card/consumed/total | GET | 查看截止指定日期的房卡总消耗 | 
| /currency/log | GET | 获取道具消费记录 |
| /activities/list | GET | 获取活动列表 | 
| /activities/add | POST | 添加活动 | 
| /activities/modify | POST | 编辑活动 | 
| /activities/delete | POST | 删除活动 | 
| /activities/reward/list | GET | 获取活动奖品列表 | 
| /activities/reward/log | GET | 获取活动奖品抽取日志(抽取数量) | 
| /activities/reward/add | POST | 添加活动奖品 | 
| /activities/reward/modify | POST | 编辑活动奖品 | 
| /activities/reward/delete | POST | 删除活动奖品 | 
| /activities/goods-type/list | GET | 获取任务奖品道具列表 | 
| /activities/goods-type/add | POST | 添加任务奖品道具 | 
| /activities/goods-type/modify | POST | 修改任务奖品道具 | 
| /activities/goods-type/delete | POST | 删除任务奖品道具 | 
| /activities/task/list | GET | 获取任务列表 | 
| /activities/task/add | POST | 添加任务 | 
| /activities/task/modify | POST | 编辑任务 | 
| /activities/task/delete | POST | 删除任务 | 
| /activities/task-type/list | GET | 获取任务类型列表 | 
| /activities/user-goods/list | GET | 获取user_goods列表 | 
| /activities/user-goods/add | POST | 添加user_goods | 
| /activities/user-goods/modify | POST | 编辑user_goods | 
| /activities/user-goods/delete | POST | 删除user_goods |
| /activities/user-goods/reset | POST | 重置user_goods |
| /activities/tasks-player/list | GET | 获取tasks_player列表 | 
| /activities/tasks-player/add | POST | 添加tasks_player | 
| /activities/tasks-player/modify | POST | 编辑tasks_player | 
| /activities/tasks-player/delete | POST | 删除tasks_player |
| /activities/tasks-player/reset | POST | 重置tasks_player |
| /activities/log-activity-reward | GET | 查看玩家中奖记录 |
| /wechat/official-account/unionid-openid/create | POST | 创建微信unionid和公众号openid记录 | 
| /wechat/official-account/unionid-openid/delete | POST | 删除微信unionid和公众号openid记录 | 
| /wechat/red-packet/send-list | GET | 获取待发送红包列表 | 
| /wechat/red-packet/update | POST | 更新待发送红包状态 | 
| /community/record/search | POST | 查询社区玩家战绩 | 
| /community/record/mark | POST | 标记战绩为已读/未读 | 
| /community/room/open | POST | 获取社团开房信息(正在玩的房间) | 

### for game(游戏后端)
| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| /game/community/member/application | POST | 玩家申请加入牌艺馆 |
| /game/community/member/invitation/{player} | GET | 获取入群邀请(和申请纪录)列表 |
| /game/community/member/approval-invitation/{invitation} | POST | 玩家同意入群 |
| /game/community/member/decline-invitation/{invitation} | POST | 玩家拒绝入群 |
| /game/community/member/quit | POST | 玩家退出社区 |
| /game/community/involved/{player} | GET | 此玩家有关联的所有社区id |
| /game/community/card/consumption/{community} | POST | 社团耗卡 |
| /game/community/info/{communityId} | GET | 查询社团信息 |
| /game/community/room/open/{communityId} | POST | 获取社团开房信息(正在玩的房间) |
| /game/community/member/quit | POST | 退出牌艺馆 |

## 接口调用规范
### 参数签名计算方法
用户提交的参数除sign外，都要参与签名。  

首先，将待签名字符串按照参数名进行排序(首先比较所有参数名的第一个字母，按abcd顺序排列，若遇到相同首字母，则看第二个字母，以此类推)。  

例如：对于如下的参数进行签名  
```
parameters={"api_key=hello-world","uid=10000"};
```   
生成待签名的字符串:   
```
api_key=hello-world&uid=10000
```  

然后，将待签名字符串尾部添加私钥参数生成最终待签名字符串。
例如:  
```
api_key=hello-world&uid=10000&secret_key=secretKey
```  
注意，"&secret_key=secretKey"为签名必传参数。  
最后，利用32位MD5算法，对最终待签名字符串进行签名运算，然后将计算结果中的字母转为大写，从而得到签名结果字符串(该字符串赋值于参数sign)  

参考如下代码：
```
protected function getSign(Array $param = null)
{
    $param['api_key'] = $this->apiKey;
    ksort($param);
    $sign = "";
    foreach ($param as $key => $value) {
        $sign .= "{$key}={$value}&";
    }
    $sign .= "secret_key={$this->secretKey}";
    return strtoupper(md5($sign));
}
``` 

### 接口返回规范(for web) 
正常返回时：  
```
{
    "result": true,
    "data": $data
}
```
异常返回时：  
```
{
    "result": false,
    "code": 0,
    "errorMsg": 'ERROR'
}
```

### 接口返回规范(for 游戏后端) 
正常返回时：  
```
{
    "code": -1,
    "data": "msg"
}
```
异常返回时：  
```
{
    "result": false,
    "code": 2000,
    "errorMsg": "已经发送过申请请求"
}
```

## 游戏端接口
> **https://down.yxx.max78.com/casino/back/htmls/agentx/**

| URI | Method | Description |
| ----  | :-----: | ----------: |
| users.php | GET | 获取玩家列表 |
| user.php| POST | 获取指定玩家信息 |
| recharge.php | POST | 给指定玩家充值 |
| room_create.php | POST | 游戏房间创建 |

## 游戏后端接口(新)
> **http://112.124.112.64/casino/back/htmls/agentx/call.php**

| P | F | Description |
| ----  | :-----: | ----------: |
| activity | modify | 增加、修改activity |
| activity | remove | 删除activity |
| activity_reward | modify | 增加、修改activity_reward |
| activity_reward | remove | 删除activity_reward |
| task | modify_task | 增加、修改tasks |
| task | remove_task | 删除tasks |
| task | modify_user | 增加、修改tasks_player |
| task | remove_user | 删除tasks_player |
| task | modify_type | 增加、修改task_type |
| task | remove_type | 删除task_type |
| item | modify_type | 增加、修改goods_type |
| item | remove_type | 删除goods_type |
| item | modify_user | 增加、修改user_goods |
| item | remove_user | 删除user_goods |

