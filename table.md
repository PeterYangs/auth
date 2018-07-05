**建表SQL**

    CREATE TABLE `think_auth_group` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组（角色）名',
      `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
      `rules` char(80) NOT NULL DEFAULT '' COMMENT '权限表id,用逗号分开',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='权限用户（角色）组表';

    CREATE TABLE `think_auth_group_access` (
    `uid` mediumint(8) unsigned NOT NULL,
      `group_id` mediumint(8) unsigned NOT NULL,
      UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
      KEY `uid` (`uid`),
      KEY `group_id` (`group_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户明显表（用户属于哪个用户组）';
    
    
      CREATE TABLE `think_auth_rule` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
      `name` char(80) NOT NULL DEFAULT '',
      `title` char(20) NOT NULL DEFAULT '',
      `type` tinyint(1) NOT NULL DEFAULT '1',
      `status` tinyint(1) NOT NULL DEFAULT '1',
      `condition` char(100) NOT NULL DEFAULT '',
      PRIMARY KEY (`id`),
      UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='权限规则表';