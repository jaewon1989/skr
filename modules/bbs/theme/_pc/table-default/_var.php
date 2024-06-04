<?php
//목록
$d['theme']['use_rss'] = "1"; //rss발행사용(사용=1/사용안함=0)
$d['theme']['show_catnum'] = "1"; //분류별등록수출력(출력=1/감춤=0)
$d['theme']['pagenum'] = "5"; //페이지스킵숫자갯수
$d['theme']['search'] = "1"; //검색폼출력(출력=1/감춤=0)

//본문
$d['theme']['date_viewf'] = "Y.m.d H:i"; //날짜포맷
$d['theme']['use_singo'] = "1"; //신고사용(사용=1/사용안함=0)
$d['theme']['use_print'] = "1"; //인쇄사용(사용=1/사용안함=0)
$d['theme']['use_scrap'] = "1"; //스크랩사용(사용=1/사용안함=0)
$d['theme']['use_font'] = "1"; //글꼴사용(사용=1/사용안함=0)
$d['theme']['use_trackback'] = "1"; //엮인글사용(사용=1/사용안함=0)
$d['theme']['use_reply'] = "1"; //답변사용(사용=1/사용안함=0)
$d['theme']['use_autoresize'] = "1"; //이미지 자동리사이즈(사용=1/사용안함=0)
$d['theme']['show_tag'] = "1"; //태그출력(출력=1/감춤=0)
$d['theme']['show_upfile'] = "1"; //첨부파일출력(출력=1/감춤=0)
$d['theme']['show_score1'] = "1"; //공감출력(출력=1/감춤=0)-회원전용
$d['theme']['show_score2'] = "1"; //공감출력(출력=1/감춤=0)-회원전용
$d['theme']['show_list'] = "0"; //열람시리스트출력(출력=1/감춤=0)
$d['theme']['snsping'] = "1"; //SNS보내기출력(출력=1/감춤=0)

//글쓰기
$d['theme']['edit_html'] = "0"; //위지위그에디터 사용등급(레벨넘버이상,0이면 비회원허용)
$d['theme']['edit_height'] = "300"; //글쓰기폼높이(픽셀)
$d['theme']['show_edittool2'] = "1"; //편집기아이콘출력(출력=1/감춤=0)
$d['theme']['file_upload_show'] = "1"; //파일 업로드 출력 여부 (출력=1/감춤=0)
$d['theme']['file_upload_qty'] = "2"; //파일 업로드 출력 갯수 
$d['theme']['perm_upload'] = "1"; //파일첨부권한(등급이상)
$d['theme']['perm_photo'] = "1"; //사진첨부권한(등급이상)
$d['theme']['show_wtag'] = "1"; //태그필드출력(출력=1/감춤=0)
$d['theme']['show_trackback'] = "1"; //엮인글필드출력(출력=1/감춤=0)
$d['theme']['use_hidden'] = "1"; //비밀글(사용안함=0/유저선택사용=1/무조건비밀글=2)

// 댓글 
$d['comment']['badword'] = "시발,씨발,개새끼,개세끼,개쉐이,지랄,니미,좆,좃,조낸,죽어,쪽바리,짱개,떼놈,";
$d['comment']['badword_action'] = "0";
$d['comment']['badword_escape'] = "*";
$d['comment']['singo_del'] = "";
$d['comment']['singo_del_num'] = "20";
$d['comment']['singo_del_act'] = "0";
$d['comment']['onelinedel'] = "";
$d['comment']['perm_write'] = "1";
$d['comment']['perm_upfile'] = "10";
$d['comment']['perm_photo'] = "1";
$d['comment']['edit_height'] = "50";
$d['comment']['edit_tool'] = "1";
$d['comment']['use_hidden'] = "0";
$d['comment']['sort'] = "uid";
$d['comment']['recnum'] = "5";
$d['comment']['use_subject'] = "";
$d['comment']['give_point'] = "0";
$d['comment']['give_opoint'] = "0";
$d['comment']['snsconnect'] = "social/inc/sns_joint01.php";
$d['comment']['orderby1'] = "asc";
$d['comment']['orderby2'] = "asc";
$d['comment']['show_page'] = "1"; // 댓글 페이징 노출여부 (노출=1/숨김 =0) 
$d['comment']['show_more'] = "1"; // 더보기 버튼 노출여부(노출=1/숨김 =0)
$d['comment']['show_sort'] = "1"; // sorting 버튼 노출여부(노출=1/숨김 =0)

// 한줄의견 
$d['oneline']['sort'] = "uid";
$d['oneline']['orderby'] = "desc";
?>