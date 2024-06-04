<?php
if(!defined('__KIMS__')) exit;


//게시판리스트
$_tmp = db_query( "select count(*) from ".$table[$module.'list'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'list']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
gid			INT				DEFAULT '0'		NOT NULL,
id			VARCHAR(30)		DEFAULT ''		NOT NULL,
name		VARCHAR(200)	DEFAULT ''		NOT NULL,
category	TEXT			NOT NULL,
num_r		INT				DEFAULT '0'		NOT NULL,
d_last		VARCHAR(14)		DEFAULT ''		NOT NULL,
d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
imghead		VARCHAR(100)	DEFAULT ''		NOT NULL,
imgfoot		VARCHAR(100)	DEFAULT ''		NOT NULL,
puthead		VARCHAR(20)		DEFAULT ''		NOT NULL,
putfoot		VARCHAR(20)		DEFAULT ''		NOT NULL,
addinfo		TEXT			NOT NULL,
writecode	TEXT			NOT NULL,
KEY gid(gid),
KEY id(id)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'list'],$DB_CONNECT); 
}

//게시판인덱스
$_tmp = db_query( "select count(*) from ".$table[$module.'idx'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'idx']." (
site		INT				DEFAULT '0'		NOT NULL,
notice		TINYINT			DEFAULT '0'		NOT NULL,
bbs			INT				DEFAULT '0'		NOT NULL,
gid			double(11,2)	DEFAULT '0.00'	NOT NULL,
KEY site(site),
KEY notice(notice),
KEY bbs(bbs,gid),
KEY gid(gid)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'idx'],$DB_CONNECT); 
}

//게시판데이터
$_tmp = db_query( "select count(*) from ".$table[$module.'data'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'data']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
site		INT				DEFAULT '0'		NOT NULL,
gid			double(11,2)	DEFAULT '0.00'	NOT NULL,
bbs			INT				DEFAULT '0'		NOT NULL,
bbsid		VARCHAR(30)		DEFAULT ''		NOT NULL,
depth		TINYINT			DEFAULT '0'		NOT NULL,
parentmbr	INT				DEFAULT '0'		NOT NULL,
display		TINYINT			DEFAULT '0'		NOT NULL,
hidden		TINYINT			DEFAULT '0'		NOT NULL,
notice		TINYINT			DEFAULT '0'		NOT NULL,
name		VARCHAR(30)		DEFAULT ''		NOT NULL,
nic			VARCHAR(50)		DEFAULT ''		NOT NULL,
mbruid		INT				DEFAULT '0'		NOT NULL,
id			VARCHAR(16)		DEFAULT ''		NOT NULL,
pw			VARCHAR(50)		DEFAULT ''		NOT NULL,
category	VARCHAR(100)	DEFAULT ''		NOT NULL,
subject		VARCHAR(200)	DEFAULT ''		NOT NULL,
content		MEDIUMTEXT		NOT NULL,
html		VARCHAR(4)		DEFAULT ''		NOT NULL,
tag			VARCHAR(200)	DEFAULT ''		NOT NULL,
hit			INT				DEFAULT '0'		NOT NULL,
down		INT				DEFAULT '0'		NOT NULL,
comment		INT				DEFAULT '0'		NOT NULL,
oneline		INT				DEFAULT '0'		NOT NULL,
trackback	INT				DEFAULT '0'		NOT NULL,
score1		INT				DEFAULT '0'		NOT NULL,
score2		INT				DEFAULT '0'		NOT NULL,
singo		INT				DEFAULT '0'		NOT NULL,
point1		INT				DEFAULT '0'		NOT NULL,
point2		INT				DEFAULT '0'		NOT NULL,
point3		INT				DEFAULT '0'		NOT NULL,
point4		INT				DEFAULT '0'		NOT NULL,
d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
d_modify	VARCHAR(14)		DEFAULT ''		NOT NULL,
d_comment	VARCHAR(14)		DEFAULT ''		NOT NULL,
d_trackback	VARCHAR(14)		DEFAULT ''		NOT NULL,
upload		TEXT			NOT NULL,
ip			VARCHAR(25)	 	DEFAULT ''		NOT NULL,
agent	 	VARCHAR(150)	DEFAULT ''		NOT NULL,
sns			VARCHAR(100)	DEFAULT ''		NOT NULL,
adddata		TEXT			NOT NULL,
likes      INT             DEFAULT '0'     NOT NULL,
featured_img    INT   DEFAULT '0'     NOT NULL,
KEY site(site),
KEY gid(gid),
KEY bbs(bbs),
KEY bbsid(bbsid),
KEY parentmbr(parentmbr),
KEY display(display),
KEY notice(notice),
KEY mbruid(mbruid),
KEY category(category),
KEY subject(subject),
KEY tag(tag),
KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'data'],$DB_CONNECT); 
}
//게시판월별수량
$_tmp = db_query( "select count(*) from ".$table[$module.'month'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'month']." (
date		CHAR(6)			DEFAULT ''		NOT NULL,
site		INT				DEFAULT '0'		NOT NULL,
bbs			INT				DEFAULT '0'		NOT NULL,
num			INT				DEFAULT '0'		NOT NULL,
KEY date(date),
KEY site(site),
KEY bbs(bbs)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'month'],$DB_CONNECT); 
}
//게시판일별수량
$_tmp = db_query( "select count(*) from ".$table[$module.'day'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'day']." (
date		CHAR(8)			DEFAULT ''		NOT NULL,
site		INT				DEFAULT '0'		NOT NULL,
bbs			INT				DEFAULT '0'		NOT NULL,
num			INT				DEFAULT '0'		NOT NULL,
KEY date(date),
KEY site(site),
KEY bbs(bbs)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'day'],$DB_CONNECT); 
}
//확장데이터
$_tmp = db_query( "select count(*) from ".$table[$module.'xtra'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'xtra']." (
parent		INT				DEFAULT '0'		NOT NULL,
site		INT				DEFAULT '0'		NOT NULL,
bbs			INT				DEFAULT '0'		NOT NULL,
down		TEXT			NOT NULL,
score1		TEXT			NOT NULL,
score2		TEXT			NOT NULL,
singo		TEXT			NOT NULL,
KEY parent(parent),
KEY site(site),
KEY bbs(bbs)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'xtra'],$DB_CONNECT); 
}

//첨부파일데이터
$_tmp = db_query( "select count(*) from ".$table[$module.'upload'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("

CREATE TABLE ".$table[$module.'upload']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
gid			INT				DEFAULT '0'		NOT NULL,
hidden		TINYINT			DEFAULT '0'		NOT NULL,
tmpcode		VARCHAR(20)		DEFAULT ''		NOT NULL,
site		INT				DEFAULT '0'		NOT NULL,
mbruid		INT				DEFAULT '0'		NOT NULL,
type		TINYINT			DEFAULT '0'		NOT NULL,
ext			VARCHAR(4)		DEFAULT '0'		NOT NULL,
fserver		TINYINT			DEFAULT '0'		NOT NULL,
url			VARCHAR(150)	DEFAULT ''		NOT NULL,
folder		VARCHAR(30)		DEFAULT ''		NOT NULL,
name		VARCHAR(250)	DEFAULT ''		NOT NULL,
tmpname		VARCHAR(100)	DEFAULT ''		NOT NULL,
thumbname	VARCHAR(100)	DEFAULT ''		NOT NULL,
size		INT				DEFAULT '0'		NOT NULL,
width		INT				DEFAULT '0'		NOT NULL,
height		INT				DEFAULT '0'		NOT NULL,
caption		TEXT			NOT NULL,
down		INT				DEFAULT '0'		NOT NULL,
d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
d_update	VARCHAR(14)		DEFAULT ''		NOT NULL,
cync		VARCHAR(250)	DEFAULT ''		NOT NULL,
KEY gid(gid),
KEY tmpcode(tmpcode),
KEY site(site),
KEY mbruid(mbruid),
KEY type(type),
KEY ext(ext),
KEY name(name),
KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'upload'],$DB_CONNECT); 
}
?>