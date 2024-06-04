<?php
if(!defined('__KIMS__')) exit;
//카테고리
$_tmp = db_query( "select count(*) from ".$table[$module.'category'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("
CREATE TABLE ".$table[$module.'category']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
gid			INT				DEFAULT '0'		NOT NULL,
isson		TINYINT			DEFAULT '0'		NOT NULL,
parent		INT				DEFAULT '0'		NOT NULL,
depth		TINYINT			DEFAULT '0'		NOT NULL,
hidden		TINYINT			DEFAULT '0'		NOT NULL,
reject		TINYINT			DEFAULT '0'		NOT NULL,
name		VARCHAR(50)		DEFAULT ''		NOT NULL,
layout		VARCHAR(50)		DEFAULT ''		NOT NULL,
skin		VARCHAR(50)		DEFAULT ''		NOT NULL,
skin_mobile	VARCHAR(50)		DEFAULT ''		NOT NULL,
imghead		VARCHAR(100)	DEFAULT ''		NOT NULL,
imgfoot		VARCHAR(100)	DEFAULT ''		NOT NULL,
puthead		TINYINT			DEFAULT '0'		NOT NULL,
putfoot		TINYINT			DEFAULT '0'		NOT NULL,
recnum		INT				DEFAULT '0'		NOT NULL,
num			INT				DEFAULT '0'		NOT NULL,
sosokmenu	  VARCHAR(50)		DEFAULT ''		NOT NULL,
review	 VARCHAR(50)		DEFAULT ''		NOT NULL,
tags 	VARCHAR(50)		DEFAULT ''		NOT NULL,
featured_img  INT DEFAULT '0'		NOT NULL,
KEY gid(gid),
KEY parent(parent),
KEY depth(depth),
KEY hidden(hidden)) ENGINE=".$DB['type']." CHARSET=UTF8");
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'category'],$DB_CONNECT); 
}


//상품데이터
$_tmp = db_query( "select count(*) from ".$table[$module.'product'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("
CREATE TABLE ".$table[$module.'product']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
gid			INT				DEFAULT '0'		NOT NULL,
display		TINYINT			DEFAULT '0'		NOT NULL,
category	VARCHAR(20)	DEFAULT ''		NOT NULL,
name		VARCHAR(200)	DEFAULT ''		NOT NULL,
price		INT				DEFAULT '0'		NOT NULL,
price1		INT				DEFAULT '0'		NOT NULL,
point		INT				DEFAULT '0'		NOT NULL,
price_x		TINYINT			DEFAULT '0'		NOT NULL,
country		VARCHAR(30)		DEFAULT ''		NOT NULL,
maker		VARCHAR(30)		DEFAULT ''		NOT NULL,
brand		VARCHAR(30)		DEFAULT ''		NOT NULL,
model		VARCHAR(30)		DEFAULT ''		NOT NULL,
stock		TINYINT			DEFAULT '0'		NOT NULL,
stock_num	INT				DEFAULT '0'		NOT NULL,
addinfo		TEXT			NOT NULL,
addoptions	TEXT			DEFAULT ''		NOT NULL,
icons		TEXT			NOT NULL,
tags		VARCHAR(200)	DEFAULT ''		NOT NULL,
content		MEDIUMTEXT		DEFAULT	''		NOT NULL,
html		VARCHAR(4)		DEFAULT ''		NOT NULL,
ext			VARCHAR(3)		DEFAULT ''		NOT NULL,
upload		TEXT			NOT NULL,
comment		INT				DEFAULT '0'		NOT NULL,
vote		INT				DEFAULT '0'		NOT NULL,
qna			INT				DEFAULT '0'		NOT NULL,
hit			INT				DEFAULT '0'		NOT NULL,
wish		INT				DEFAULT '0'		NOT NULL,
buy			INT				DEFAULT '0'		NOT NULL,
d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
vendor		INT				DEFAULT '0'		NOT NULL,
md			INT				DEFAULT '0'		NOT NULL,
num1		INT				DEFAULT '0'		NOT NULL,
num2		INT				DEFAULT '0'		NOT NULL,
code		VARCHAR(13)		DEFAULT ''		NOT NULL,
namekey		CHAR(1)			DEFAULT ''		NOT NULL,
d_make		VARCHAR(8)		DEFAULT ''		NOT NULL,
is_free		TINYINT			DEFAULT '0'		NOT NULL,
is_cash		TINYINT			DEFAULT '0'		NOT NULL,
halin_event	VARCHAR(30)		DEFAULT ''		NOT NULL,
halin_mbr	VARCHAR(200)	DEFAULT ''		NOT NULL,
joint		TEXT			NOT NULL,
featured_img  INT DEFAULT '0'		NOT NULL,
review TEXT NOT NULL,
KEY gid(gid),
KEY display(display),
KEY category(category),
KEY name(name),
KEY price(price),
KEY point(point),
KEY country(country),
KEY maker(maker),
KEY brand(brand),
KEY model(model),
KEY stock(stock),
KEY stock_num(stock_num),
KEY tags(tags),
KEY hit(hit),
KEY wish(wish),
KEY buy(buy),
KEY d_regis(d_regis),
KEY vendor(vendor),
KEY md(md),
KEY code(code)) ENGINE=".$DB['type']." CHARSET=UTF8");
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'product'],$DB_CONNECT); 
}




//상품문의
$_tmp = db_query( "select count(*) from ".$table[$module.'qna'], $DB_CONNECT );
if ( !$_tmp ) {
$_tmp = ("
CREATE TABLE ".$table[$module.'qna']." (
uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
hidden		TINYINT			DEFAULT '0'		NOT NULL,
product		INT				DEFAULT '0'		NOT NULL,
mbruid		INT				DEFAULT '0'		NOT NULL,
subject		VARCHAR(200)	DEFAULT ''		NOT NULL,
content		MEDIUMTEXT		NOT NULL,
html1		VARCHAR(4)		DEFAULT ''		NOT NULL,
reply		MEDIUMTEXT		NOT NULL,
html2		VARCHAR(4)		DEFAULT ''		NOT NULL,
hit			INT				DEFAULT '0'		NOT NULL,
state		TINYINT			DEFAULT '0'		NOT NULL,
d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
d_modify	VARCHAR(14)		DEFAULT ''		NOT NULL,
KEY hidden(hidden),
KEY product(product),
KEY mbruid(mbruid),
KEY subject(subject),
KEY hit(hit)) ENGINE=".$DB['type']." CHARSET=UTF8");
db_query($_tmp, $DB_CONNECT);
db_query("OPTIMIZE TABLE ".$table[$module.'qna'],$DB_CONNECT); 
}

//업로드 데이타  
$_tmp = db_query( "select count(*) from ".$table[$module.'upload'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'upload']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	gid			INT				DEFAULT '0'		NOT NULL,
	pid			INT				DEFAULT '0'		NOT NULL,
	parent		VARCHAR(20)		DEFAULT ''		NOT NULL,
	category	INT				DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	tmpcode		VARCHAR(20)		DEFAULT ''		NOT NULL,
	site		INT				DEFAULT '0'		NOT NULL,
	mbruid		INT				DEFAULT '0'		NOT NULL,
	type		TINYINT			DEFAULT '0'		NOT NULL,
	ext			VARCHAR(4)		DEFAULT '0'		NOT NULL,
	fserver		TINYINT			DEFAULT '0'		NOT NULL,
	url			VARCHAR(150)	DEFAULT ''		NOT NULL,
	folder		VARCHAR(30)		DEFAULT ''		NOT NULL,
	name		VARCHAR(100)	DEFAULT ''		NOT NULL,
	tmpname		VARCHAR(100)	DEFAULT ''		NOT NULL,
	thumbname	VARCHAR(100)	DEFAULT ''		NOT NULL,
	size		INT				DEFAULT '0'		NOT NULL,
	width		INT				DEFAULT '0'		NOT NULL,
	height		INT				DEFAULT '0'		NOT NULL,
	alt			VARCHAR(50)		DEFAULT ''		NOT NULL,
	caption		TEXT			NOT NULL,
	description	TEXT			NOT NULL,
	src			TEXT			NOT NULL,
	linkto		TINYINT			DEFAULT '0'		NOT NULL,
	license		TINYINT			DEFAULT '0'		NOT NULL,
	down		INT				DEFAULT '0'		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	d_update	VARCHAR(14)		DEFAULT ''		NOT NULL,
	sync		VARCHAR(250)	DEFAULT ''		NOT NULL,
	linkurl		VARCHAR(250)	DEFAULT ''		NOT NULL,
	KEY gid(gid),
	KEY category(category),
	KEY tmpcode(tmpcode),
	KEY mbruid(mbruid),
	KEY type(type),
	KEY name(name),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'upload'],$DB_CONNECT); 
}



?>