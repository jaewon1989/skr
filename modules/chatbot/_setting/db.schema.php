<?php
if(!defined('__KIMS__')) exit;

//업종 카테고리
$_tmp = db_query( "select count(*) from ".$table[$module.'induCat'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'induCat']." (
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
	KEY depth(depth)) ENGINE=".$DB['type']." CHARSET=UTF8");
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'induCat'],$DB_CONNECT); 
}

//질문 카테고리
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
	KEY depth(depth)) ENGINE=".$DB['type']." CHARSET=UTF8");
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'category'],$DB_CONNECT); 
}

//업체    
$_tmp = db_query( "select count(*) from ".$table[$module.'vendor'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'vendor']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	auth		TINYINT			DEFAULT '0'		NOT NULL,
	gid 		INT				DEFAULT '0'		NOT NULL,
	display		TINYINT			DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	type		TINYINT			DEFAULT '0'		NOT NULL,
	mbruid		INT				DEFAULT '0'		NOT NULL,
	induCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	id          VARCHAR(50)	    DEFAULT ''		NOT NULL,
	name		VARCHAR(100)	DEFAULT ''		NOT NULL,
	service		VARCHAR(100)	DEFAULT ''		NOT NULL,
	intro		VARCHAR(500)	DEFAULT ''		NOT NULL,	
	content     TEXT NOT NULL,
	html		VARCHAR(4)		DEFAULT ''		NOT NULL,
	tel			VARCHAR(45)	    DEFAULT ''		NOT NULL,
	tel2		VARCHAR(45)	    DEFAULT ''		NOT NULL,
	email		VARCHAR(45) 	DEFAULT ''		NOT NULL,
	logo		VARCHAR(100)	DEFAULT ''		NOT NULL,
    upload      VARCHAR(300)	DEFAULT ''		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	KEY mbruid(mbruid),
	KEY id(id), 
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'vendor'],$DB_CONNECT); 
}

//답변 룰      
$_tmp = db_query( "select count(*) from ".$table[$module.'rule'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'rule']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor		INT				DEFAULT '0'		NOT NULL,
    category  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	r_type  	CHAR(1)	    	DEFAULT 'A'		NOT NULL,
    pattern  	VARCHAR(200)	DEFAULT ''		NOT NULL,
	reply     TEXT NOT NULL,
	KEY vendor(vendor),
	KEY category(category),
	KEY pattern(pattern)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'rule'],$DB_CONNECT); 
}


//예상 질문    
$_tmp = db_query( "select count(*) from ".$table[$module.'question'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'question']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	gid 		INT				DEFAULT '0'		NOT NULL,
	display		TINYINT			DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	use_default	TINYINT			DEFAULT '0'		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	r_uid		INT				DEFAULT '0'		NOT NULL,
	r_type  	CHAR(1)	    	DEFAULT ''		NOT NULL,
	quesCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	pattern     VARCHAR(100)    DEFAULT ''		NOT NULL,
	lang        CHAR(3)    DEFAULT ''		NOT NULL,
	content     TEXT NOT NULL,
	morpheme	VARCHAR(200)	DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY r_uid(r_uid), 
	KEY pattern(pattern)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'question'],$DB_CONNECT); 
}

// 답변 데이타     
$_tmp = db_query( "select count(*) from ".$table[$module.'reply'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'reply']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	display		TINYINT			DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	induCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	quesCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	type  	    CHAR(1)	    	DEFAULT ''		NOT NULL,	
	lang        CHAR(3)    DEFAULT ''		NOT NULL,
	content     TEXT NOT NULL,
	KEY vendor(vendor)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'reply'],$DB_CONNECT); 
}

//챗팅 내역   
$_tmp = db_query( "select count(*) from ".$table[$module.'chat'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'chat']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	auth		TINYINT			DEFAULT '0'		NOT NULL,
	gid 		INT				DEFAULT '0'		NOT NULL,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	display		TINYINT			DEFAULT '0'		NOT NULL,
	user_display  TINYINT		DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	notice		TINYINT			DEFAULT '0'		NOT NULL,
	name		VARCHAR(30)		DEFAULT ''		NOT NULL,
	nic			VARCHAR(50)		DEFAULT ''		NOT NULL,
	mbrid       VARCHAR(50)	    DEFAULT ''		NOT NULL,
	mbruid		INT				DEFAULT '0'		NOT NULL,
	botuid		INT				DEFAULT '0'		NOT NULL,	
	botid       VARCHAR(100)    DEFAULT ''		NOT NULL,
	induCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	quesCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	content		TEXT			NOT NULL,
	likes		INT				DEFAULT '0'		NOT NULL,
	unlikes 	INT				DEFAULT '0'		NOT NULL,
	report		INT				DEFAULT '0'		NOT NULL,
	point		INT				DEFAULT '0'		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	d_modify	VARCHAR(14)		DEFAULT ''		NOT NULL,
	upload		TEXT			NOT NULL,
	ip			VARCHAR(25)	 	DEFAULT ''		NOT NULL,
	agent	 	VARCHAR(150)	DEFAULT ''		NOT NULL,
	sync		VARCHAR(250)	DEFAULT ''		NOT NULL,
	by_who	 	VARCHAR(45)	DEFAULT ''		NOT NULL,
	KEY display(display),
	KEY hidden(hidden),
	KEY vendor(vendor),
	KEY notice(notice),
	KEY mbruid(mbruid),
	KEY botid(botid), 
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'chat'],$DB_CONNECT); 
}

//챗봇 리스트 
$_tmp = db_query( "select count(*) from ".$table[$module.'bot'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'bot']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	gid     	INT				DEFAULT '0'		NOT NULL,
	auth		TINYINT			DEFAULT '0'		NOT NULL,
	vendor  	INT				DEFAULT '0'		NOT NULL,
	induCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	display		TINYINT			DEFAULT '0'		NOT NULL,
	name		VARCHAR(30)		DEFAULT ''		NOT NULL,
	service		VARCHAR(150)	DEFAULT ''		NOT NULL,
	intro		VARCHAR(300)	DEFAULT ''		NOT NULL,
	mbruid		INT				DEFAULT '0'		NOT NULL,
	id			VARCHAR(20)		DEFAULT ''		NOT NULL,
	content		TEXT			NOT NULL,
	html		VARCHAR(4)		DEFAULT ''		NOT NULL,
	tag		    VARCHAR(300)	DEFAULT ''		NOT NULL,	
	lang        VARCHAR(3)		DEFAULT ''		NOT NULL,
    hit 		INT				DEFAULT '0'		NOT NULL,	
	likes		INT				DEFAULT '0'		NOT NULL,
	report		INT				DEFAULT '0'		NOT NULL,
	point		INT				DEFAULT '0'		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	d_modify	VARCHAR(14)		DEFAULT ''		NOT NULL,
	avatar		VARCHAR(50)		NOT NULL,
	KEY gid(gid),
	KEY vendor(vendor),
	KEY hidden(hidden),
	KEY hit(hit),
	KEY likes(likes),
	KEY mbruid(mbruid),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'bot'],$DB_CONNECT); 
}

//추천 상품 
$_tmp = db_query( "select count(*) from ".$table[$module.'goods'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'goods']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor  	INT				DEFAULT '0'		NOT NULL,
	bot     	INT				DEFAULT '0'		NOT NULL,
	induCat  	VARCHAR(20)		DEFAULT ''		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	name		VARCHAR(30)		DEFAULT ''		NOT NULL,
	link  	    VARCHAR(200)	DEFAULT ''      NOT NULL,
	f_img		VARCHAR(200)	DEFAULT ''     NOT NULL,
	KEY vendor(vendor),
	KEY bot(bot)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'goods'],$DB_CONNECT); 
}

//업로드  
$_tmp = db_query( "select count(*) from ".$table[$module.'upload'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'upload']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	gid			INT				DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	tmpcode		VARCHAR(20)		DEFAULT ''		NOT NULL,
	parent		VARCHAR(20)		DEFAULT ''		NOT NULL,
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
	KEY parent(parent),
	KEY tmpcode(tmpcode),
	KEY mbruid(mbruid),
	KEY type(type),
	KEY name(name),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'upload'],$DB_CONNECT); 
}

// 추가한 챗봇      
$_tmp = db_query( "select count(*) from ".$table[$module.'added'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'added']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	mbruid		INT			DEFAULT '0'		NOT NULL,
	botuid		INT			DEFAULT '0'		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	memo       TEXT NOT NULL,
	KEY mbruid(mbruid),
	KEY botuid(botuid),
	KEY vendor(vendor)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'added'],$DB_CONNECT); 
}

//알림 
$_tmp = db_query( "select count(*) from ".$table[$module.'notification'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'notification']." (
	uid			INT			PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	mbruid		INT				DEFAULT '0'		NOT NULL,
	type	    VARCHAR(50)		DEFAULT ''		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	frommodule	VARCHAR(50)		DEFAULT ''		NOT NULL,
	frommbr		INT				DEFAULT '0'		NOT NULL,
	message		TEXT			NOT NULL,
	referer		VARCHAR(250)	DEFAULT ''		NOT NULL,
	target		VARCHAR(20)		DEFAULT ''		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	d_read		VARCHAR(14)		DEFAULT ''		NOT NULL,
	KEY mbruid(mbruid),
	KEY vendor(vendor),
	KEY frommbr(frommbr),
	KEY d_read(d_read)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'notification'],$DB_CONNECT); 
}

// 메니져  
$_tmp = db_query( "select count(*) from ".$table[$module.'manager'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'manager']." (
	uid			INT			PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	mbruid		INT				DEFAULT '0'		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	parentmbr	INT				DEFAULT '0'		NOT NULL,
    role	    VARCHAR(50)		DEFAULT ''		NOT NULL,
	role_intro	TEXT			NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	KEY mbruid(mbruid),
	KEY vendor(vendor)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'manager'],$DB_CONNECT); 
}

// 레퍼러  
$_tmp = db_query( "select count(*) from ".$table[$module.'referer'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'referer']." (
	uid			INT			PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	botuid       VARCHAR(200)	DEFAULT ''		NOT NULL,
	mbruid		INT			DEFAULT '0'		NOT NULL,
	mbrsex		TINYINT(4)	DEFAULT '0'		NOT NULL,
	mbrage		TINYINT(4)	DEFAULT '0'		NOT NULL,
	ip          VARCHAR(15)		DEFAULT ''		NOT NULL,
	referer     VARCHAR(200)	DEFAULT ''		NOT NULL,
	agent       VARCHAR(200)	DEFAULT ''		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	KEY botuid(botuid),
	KEY mbruid(mbruid),	
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'referer'],$DB_CONNECT); 
}

// 일간    
$_tmp = db_query( "select count(*) from ".$table[$module.'dcounter'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'dcounter']." (
	uid			INT			PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor		INT			DEFAULT '0'		NOT NULL,
	botuid		INT			DEFAULT '0'		NOT NULL,
	type        TINYINT(4)	DEFAULT '0'		NOT NULL,
	page		INT     	DEFAULT '0'		NOT NULL,
	male		INT     	DEFAULT '0'		NOT NULL,
	female		INT     	DEFAULT '0'		NOT NULL,
	age_10		INT     	DEFAULT '0'		NOT NULL,
	age_20		INT     	DEFAULT '0'		NOT NULL,
	age_30		INT     	DEFAULT '0'		NOT NULL,
	age_40		INT     	DEFAULT '0'		NOT NULL,
	age_50		INT     	DEFAULT '0'		NOT NULL,
	age_60		INT     	DEFAULT '0'		NOT NULL,
	d_regis 	VARCHAR(10)		DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY botuid(botuid),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'dcounter'],$DB_CONNECT); 
}

// 주간,월간 카운팅   
$_tmp = db_query( "select count(*) from ".$table[$module.'counter'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'counter']." (
	uid			INT			PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor		INT			DEFAULT '0'		NOT NULL,
	botuid		INT			DEFAULT '0'		NOT NULL,
	type        TINYINT(4)	DEFAULT '0'		NOT NULL,
	page		INT     	DEFAULT '0'		NOT NULL,
	male		INT     	DEFAULT '0'		NOT NULL,
	female		INT     	DEFAULT '0'		NOT NULL,
	age_10		INT     	DEFAULT '0'		NOT NULL,
	age_20		INT     	DEFAULT '0'		NOT NULL,
	age_30		INT     	DEFAULT '0'		NOT NULL,
	age_40		INT     	DEFAULT '0'		NOT NULL,
	age_50		INT     	DEFAULT '0'		NOT NULL,
	age_60		INT     	DEFAULT '0'		NOT NULL,
	d_regis 	VARCHAR(8)	DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY botuid(botuid),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'counter'],$DB_CONNECT); 
}

// keyword 테이블       
$_tmp = db_query( "select count(*) from ".$table[$module.'keyword'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'keyword']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	gid  		INT				DEFAULT '0'		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	bot 		INT				DEFAULT '0'		NOT NULL,
    is_child	TINYINT			DEFAULT '0'		NOT NULL,
	parent 		INT				DEFAULT '0'		NOT NULL,
	depth		TINYINT			DEFAULT '0'		NOT NULL,
	hidden		TINYINT			DEFAULT '0'		NOT NULL,
	mobile		TINYINT			DEFAULT '0'		NOT NULL,
	printType	TINYINT			DEFAULT '0'		NOT NULL,
	keyword     VARCHAR(70)    DEFAULT ''		NOT NULL,    
	KEY vendor(vendor)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'keyword'],$DB_CONNECT); 
}

// keyword 정보 테이블       
$_tmp = db_query( "select count(*) from ".$table[$module.'keywordInfo'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$module.'keywordInfo']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	kwd_uid		INT				DEFAULT '0'		NOT NULL,
	vendor		INT				DEFAULT '0'		NOT NULL,
	bot  		INT				DEFAULT '0'		NOT NULL, 
	title       VARCHAR(150)    DEFAULT ''		NOT NULL,
	summary     VARCHAR(250)    DEFAULT ''		NOT NULL,
	content     TEXT NOT NULL,
	price1      INT				DEFAULT '0'		NOT NULL,
	price2      INT				DEFAULT '0'		NOT NULL,
	vote    	TINYINT			DEFAULT '0'		NOT NULL,
    upload      VARCHAR(200)    DEFAULT ''		NOT NULL,
    featured_img    INT			DEFAULT '0'		NOT NULL,
 	img_url   VARCHAR(250)      DEFAULT ''		NOT NULL,
 	link1     VARCHAR(250)      DEFAULT ''		NOT NULL,
 	link2     VARCHAR(250)      DEFAULT ''		NOT NULL,
 	link3     VARCHAR(250)      DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY bot(bot),
	KEY kwd_uid(kwd_uid)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$module.'keywordInfo'],$DB_CONNECT); 
}

//사용자 챗 로그   
$_tmp = db_query( "select count(*) from ".$table[$m.'chatLog'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$m.'chatLog']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	bot      	INT				DEFAULT '0'		NOT NULL,
	userNam		VARCHAR(60)		DEFAULT ''		NOT NULL,
	userId      VARCHAR(100)	    DEFAULT ''		NOT NULL,
	userUid		INT				DEFAULT '0'		NOT NULL,
    printType   CHAR(1)         DEFAULT 'T'     NOT NULL,
	chatType    CHAR(1)         DEFAULT 'Q'     NOT NULL,
	content		TEXT			NOT NULL,
	ip			VARCHAR(25)	 	DEFAULT ''		NOT NULL,
	agent	 	VARCHAR(150)	DEFAULT ''		NOT NULL,
	intent  	VARCHAR(250)	DEFAULT ''		NOT NULL,
	entity  	VARCHAR(250)	DEFAULT ''		NOT NULL,
	emotion  	VARCHAR(250)	DEFAULT ''		NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY bot(bot),
	KEY userId(userId),
	KEY userUid(userUid),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'chatLog'],$DB_CONNECT); 
}

//봇 챗 로그      
$_tmp = db_query( "select count(*) from ".$table[$m.'botChatLog'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$m.'botChatLog']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	bot      	INT				DEFAULT '0'		NOT NULL,
	user      	INT				DEFAULT '0'		NOT NULL,
	chat       	INT				DEFAULT '0'		NOT NULL,
	printType   CHAR(1)         DEFAULT 'T'     NOT NULL,
	chatType    CHAR(1)         DEFAULT 'R'     NOT NULL,
	content		TEXT			NOT NULL,
	d_regis		VARCHAR(14)		DEFAULT ''		NOT NULL, 
	KEY vendor(vendor),
	KEY bot(bot),
	KEY user(user),
	KEY chat(chat),
	KEY d_regis(d_regis)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'botChatLog'],$DB_CONNECT); 
}

//단어 카운팅    
$_tmp = db_query( "select count(*) from ".$table[$m.'chatWordLog'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$m.'chatWordLog']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	bot      	INT				DEFAULT '0'		NOT NULL,
	keyword		VARCHAR(50)	    DEFAULT ''		NOT NULL,
	hit         INT				DEFAULT '0'		NOT NULL, 
	date        CHAR(8)         DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY bot(bot),
	KEY keyword(keyword),
	KEY date(date)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'chatWordLog'],$DB_CONNECT); 
}

//단어 관계    
$_tmp = db_query( "select count(*) from ".$table[$m.'chatWordRelation'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$m.'chatWordRelation']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	bot      	INT				DEFAULT '0'		NOT NULL,
	user      	INT				DEFAULT '0'		NOT NULL,
	chat       	INT				DEFAULT '0'		NOT NULL,
	keyword        INT				DEFAULT '0'		NOT NULL,
	date        CHAR(8)         DEFAULT ''		NOT NULL, 
	KEY vendor(vendor),
	KEY bot(bot),
	KEY user(user),
	KEY chat(chat),
	KEY keyword(keyword)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'chatWordRelation'],$DB_CONNECT); 
}

//문장 카운팅    
$_tmp = db_query( "select count(*) from ".$table[$m.'chatStsLog'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$m.'chatStsLog']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	bot      	INT				DEFAULT '0'		NOT NULL,
	sentence	VARCHAR(250)    DEFAULT ''		NOT NULL,
	hit         INT				DEFAULT '0'		NOT NULL,
	success     INT				DEFAULT '0'		NOT NULL,
	fail        INT				DEFAULT '0'		NOT NULL,
	date        CHAR(8)         DEFAULT ''		NOT NULL,
	KEY vendor(vendor),
	KEY bot(bot),
	KEY sentence(sentence),
	KEY date(date)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'chatStsLog'],$DB_CONNECT); 
}


//문장 관계     
$_tmp = db_query( "select count(*) from ".$table[$m.'chatStsRelation'], $DB_CONNECT );
if ( !$_tmp ) {
	$_tmp = ("
	CREATE TABLE ".$table[$m.'chatStsRelation']." (
	uid			INT				PRIMARY KEY		NOT NULL AUTO_INCREMENT,
	vendor   	INT				DEFAULT '0'		NOT NULL,
	bot      	INT				DEFAULT '0'		NOT NULL,
	user      	INT				DEFAULT '0'		NOT NULL,
	chat       	INT				DEFAULT '0'		NOT NULL,
	sentence    INT				DEFAULT '0'		NOT NULL,
	date        CHAR(8)         DEFAULT ''		NOT NULL, 
	KEY vendor(vendor),
	KEY bot(bot),
	KEY user(user),
	KEY chat(chat),
	KEY sentence(sentence)) ENGINE=".$DB['type']." CHARSET=UTF8");                            
	db_query($_tmp, $DB_CONNECT);
	db_query("OPTIMIZE TABLE ".$table[$m.'chatStsRelation'],$DB_CONNECT); 
}

?>
