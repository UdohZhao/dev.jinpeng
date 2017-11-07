# 用户表「主键id，用户名，密码，时间，类型（0=>学生，1=>教师），状态（0=>正常，1=>冻结）」
CREATE TABLE `user`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户表主键id',
    `username` varchar(20) NOT NULL COMMENT '用户名',
    `password` char(32) NOT NULL COMMENT '密码',
    `ctime` char(10) NOT NULL COMMENT '时间',
    `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型?0=>学生，1=>教师',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>正常，1=>冻结',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 学生基础信息表「主键id，关联用户表主键id，关联班级表主键id，姓名，累计评分」
CREATE TABLE `students_basicinfo`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '学生基础信息表主键id',
    `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
    `cid` int(11) UNSIGNED NOT NULL COMMENT '关联班级表主键id',
    `name` varchar(20) NOT NULL COMMENT '学生姓名',
    `addup` decimal(14,1) UNSIGNED NOT NULL COMMENT '学生累计评分',
    PRIMARY KEY (`id`),
    KEY (`uid`),
    KEY (`cid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 班级表「主键id，pid父级，名称，排序」
CREATE TABLE `class`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '班级表主键id',
    `pid` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'pid父级',
    `cname` varchar(50) NOT NULL COMMENT '名称',
    `sort` tinyint(3) UNSIGNED NOT NULL COMMENT '排序?数字越小越靠前',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# 过往毕业班级「主键id，pcid，cid，名称，排序，第几届毕业生」
CREATE TABLE `comeandgo_class`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '班级表主键id',
    `pcid` tinyint(3) UNSIGNED NOT NULL COMMENT '关联班级表父主键id',
    `cid` int(11) UNSIGNED NOT NULL COMMENT '关联班级表子主键id',
    `cname` varchar(50) NOT NULL COMMENT '名称',
    `sort` tinyint(3) UNSIGNED NOT NULL COMMENT '排序?数字越小越靠前',
    `graduation` char(4) NOT NULL COMMENT '第几届毕业生',
    PRIMARY KEY (`id`),
    KEY (`pcid`),
    KEY (`cid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 教师基础信息表「主键id，关联用户表主键id，关联班级表主键id，姓名」
CREATE TABLE `teachers_basicinfo`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '教师基础信息表主键id',
    `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
    `cid` int(11) UNSIGNED NOT NULL COMMENT '关联班级表主键id',
    `name` varchar(20) NOT NULL COMMENT '教师姓名',
    PRIMARY KEY (`id`),
    KEY (`uid`),
    KEY (`cid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 周期表「主键id，期数，开始时间，结束时间，状态（0=>最新，1=>过期）」
CREATE TABLE `period`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '周期表主键id',
    `periods` char(7) NOT NULL COMMENT '期数',
    `start_time` char(10) NOT NULL COMMENT '开始时间',
    `end_time` char(10) NOT NULL COMMENT '结束时间',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>最新，1=>过期，2=>正在进行中',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 题库表「主键id，关联周期表主键id，试题，keyss（备选答案序列化字符串），valss（答案得分序列化字符串），性质（0=>单选，1=>多选），时间，排序（数字越小越靠前），类型（0=>低段，1=>中段，2=>高段），状态（0=>新题，1=>老题，2=>弃用）」
CREATE TABLE `question_bank`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '题库表主键id',
    `perid` int(11) UNSIGNED NOT NULL COMMENT '关联周期表主键id',
    `test_questions` varchar(500) NOT NULL COMMENT '试题',
    `keyss` varchar(255) NOT NULL COMMENT '备选答案序列化字符串',
    `valss` varchar(255) NOT NULL COMMENT '答案得分序列化字符串',
    `nature` tinyint(1) UNSIGNED NOT NULL COMMENT '性质?0=>单选，1=>多选',
    `ctime` char(10) NOT NULL COMMENT '时间',
    `sort` int(11) UNSIGNED NOT NULL COMMENT '排序?数字越小越靠前',
    `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型?0=>低段，1=>中段，2=>高段',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>新题，1=>老题，2=>弃用',
    PRIMARY KEY (`id`),
    KEY (`perid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 评价表「主键id，关联用户表主键id，关联班级表主键id，关联周期表主键id，关联题库表主键id，keyss（备选答案序列化字符串），valss（答案得分序列化字符串），type（0=>家人评价，1=>自我评价，2=>同学评价），schoolmate同学uid」
CREATE TABLE `estimate`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评价表主键id',
    `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
    `pcid` tinyint(3) UNSIGNED NOT NULL COMMENT '关联班级表父主键id',
    `cid` int(11) UNSIGNED NOT NULL COMMENT '关联班级表子主键id',
    `perid` int(11) UNSIGNED NOT NULL COMMENT '关联周期表主键id',
    `qid` varchar(255)  NOT NULL COMMENT '关联题库表主键id',
    `keyss` varchar(255) NOT NULL COMMENT '备选答案序列化字符串',
    `valss` varchar(255) NOT NULL COMMENT '答案得分序列化字符串',
    `type` tinyint(1) NOT NULL COMMENT '类型?0=>家人评价，1=>自我评价，2=>同学评价',
    `sm_uid` int(11) UNSIGNED NOT NULL COMMENT '评价人同学uid',
    PRIMARY KEY (`id`),
    KEY (`uid`),
    KEY (`cid`),
    KEY (`perid`),
    KEY (`sm_uid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 补充评价表「主键id，关联用户表主键id，关联班级表主键id，关联周期表主键id，补充内容，时间，type（0=>未读，1=>已读），状态（0=>默认，1=>优，2=>良，3=>中，4=>差），教师评分」
CREATE TABLE `replenish_estimate`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '补充评价表主键id',
    `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
    `pcid` tinyint(3) UNSIGNED NOT NULL COMMENT '关联班级表父主键id',
    `cid` int(11) UNSIGNED NOT NULL COMMENT '关联班级表子主键id',
    `perid` int(11) UNSIGNED NOT NULL COMMENT '关联周期表主键id',
    `content` varchar(500) NOT NULL COMMENT '补充内容',
    `ctime` char(10) NOT NULL COMMENT '时间',
    `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型?0=>未读，1=>已读',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>默认，1=>优，2=>良，3=>中，4=>差',
    `grade` decimal(14,1) UNSIGNED NOT NULL COMMENT '教师评分',
    PRIMARY KEY (`id`),
    KEY (`uid`),
    KEY (`cid`),
    KEY (`perid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 综合评价表「主键id，关联用户表主键id，关联班级表主键id，关联周期表主键id，家长评价总分，自我评价总分，同学评价总分，状态（0=>默认，1=>优，2=>良，3=>中，4=>差），教师评分」
CREATE TABLE `synthesize_estimate`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '综合评价表主键id',
    `uid` int(11) UNSIGNED NOT NULL COMMENT '关联用户表主键id',
    `pcid` tinyint(3) UNSIGNED NOT NULL COMMENT '关联班级表父主键id',
    `cid` int(11) UNSIGNED NOT NULL COMMENT '关联班级表子主键id',
    `perid` int(11) UNSIGNED NOT NULL COMMENT '关联周期表主键id',
    `patriarch` decimal(14,1) UNSIGNED NOT NULL COMMENT '家长评价总分',
    `ego` decimal(14,1) UNSIGNED NOT NULL COMMENT '自我评价总分',
    `schoolmate` decimal(14,1) UNSIGNED NOT NULL COMMENT '同学评价总分',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>默认，1=>优，2=>良，3=>中，4=>差',
    `grade` decimal(14,1) UNSIGNED NOT NULL COMMENT '教师评分',
    PRIMARY KEY (`id`),
    KEY (`uid`),
    KEY (`cid`),
    KEY (`perid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 评价配置表「主键id，优百分比，良百分比，中百分比，差百分比，类型（0=>综合评价，1=>补充评价）」
CREATE TABLE `estimate_config`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评价配置表主键id',
    `y` tinyint(3) UNSIGNED NOT NULL COMMENT '优%',
    `l` tinyint(3) UNSIGNED NOT NULL COMMENT '良%',
    `z` tinyint(3) UNSIGNED NOT NULL COMMENT '中%',
    `c` tinyint(3) UNSIGNED NOT NULL COMMENT '差%',
    `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型?0=>综合评价，1=>补充评价',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 后台用户表「主键id，用户名，密码，时间，类型（0=>超级管理员，1=>管理员），状态（0=>正常，1=>冻结）」
CREATE TABLE `admin_user`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户表主键id',
    `username` varchar(20) NOT NULL COMMENT '用户名',
    `password` char(32) NOT NULL COMMENT '密码',
    `ctime` char(10) NOT NULL COMMENT '时间',
    `type` tinyint(1) UNSIGNED NOT NULL COMMENT '类型?0=>超级管理员，1=>管理员',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>正常，1=>冻结',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 站点配置表「主键id，站点名称，logo地址，版权所有，状态（0=>正常，1=>关闭站点）」
CREATE TABLE `site`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '站点配置表主键id',
    `cname` varchar(255) NOT NULL COMMENT '站点名称',
    `logo`  varchar(255) NOT NULL COMMENT 'logo地址',
    `copyright` varchar(255) NOT NULL COMMENT '版权所有',
    `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态?0=>正常，1=>关闭站点',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
# 备选答案配置表「主键id」
CREATE TABLE `answer`(
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
    `answer_str` varchar(255) NOT NULL COMMENT '备选答案序列化数组',
    `val_str`  varchar(255) NOT NULL COMMENT '答案对应分数序列化数组',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;






