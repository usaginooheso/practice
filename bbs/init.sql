-- create database bbs_php;

grant all on bbs_php.* to dbuser@localhost identified by 'sample';

-- quit;
-- mysql -u dbuser -p bbs_php
-- use bbs_php

create table posts (
  id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username varchar(32) NOT NULL,
  title varchar(32),
  body varchar(100) NOT NULL,
  path varchar(255) DEFAULT NULL,
  created datetime,
  modified datetime
);

-- create table comments (
--   id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   post_id int NOT NULL,
--   body text
-- );

-- desc posts;
