<div class="row">
	<div class="col-md-6">
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<form name="sosokForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return regisCheck(this);">
				<input type="hidden" name="r" value="<?php echo $r?>" />
				<input type="hidden" name="m" value="<?php echo $module?>" />
				<input type="hidden" name="a" value="sosok_regis" />		
				<div class="panel-heading rb-icon">
					<div class="icon">
						<i class="fa fa-arrows-h fa-2x"></i>
					</div>
					<h4 class="panel-title">회원그룹 <small>(Group)</small></h4>
				</div>
				<div class="panel-collapse collapse in" id="collapseOne">
					<div class="panel-body guide small">
						<ul class="kiere-ul">
						  <li>회원관리에 필요한 그룹을 신규등록 및 그룹명을 변경할 수 있습니다.</li>
						  <li>순서를 변경하실 경우 드래그해서 위치를 변경하신 후 저장하시면 됩니다.</li>
						 </ul>
					</div>
					<div class="panel-body">
						<ul id="group-list" class="list-inline">
							<?php $i=1?>
							<?php $RCD=getDbArray($table['s_mbrgroup'],'','*','gid','asc',0,1)?>
							<?php while($R=db_fetch_array($RCD)): $i++?>
							  <li class="group-box">
								   <input type="checkbox" name="sosokmembers[]" value="<?php echo $R['uid']?>" checked="checked" class="hidden">
									<span class="fa-stack fa-3x">
										<i class="fa fa-square fa-stack-2x"></i>
										<i class="fa fa-users fa-stack-1x fa-inverse"></i>
									</span>
									<span class="label label-default"><?php echo $R['num']?number_format($R['num']):''?></span>
									<div class="delbtn">
										 <a href="#" id="<?php echo $R['uid'].'-'.$R['num']?>" class="del-a" data-toggle="tooltip" title="삭제"><small><i class="fa fa-times"></i></small></a></div>
									<div class="name" style="padding-top:5px;"><input type="text" name="name_<?php echo $R['uid']?>" value="<?php echo $R['name']?>" class="form-control"></div>
							  	</li>
							<?php endwhile?>
						</ul>
					</div>
					<div class="panel-footer">
						<div class="text-center">
							<input type="submit" class="btn btn-primary" value="그룹명/순서 변경" />
							<a href="#" class="btn btn-info pull-right" id="btn-new-group" data-toggle="modal" data-target="#modal-group-add"><i class="fa fa-plus"></i> 새 그룹 추가</a>
						</div>
					</div>
				</div>
				<!-- Modal-그룹  추가 -->
				<div aria-hidden="true" aria-labelledby="myModalLabel" class="modal fade" id="modal-group-add" role="dialog" tabindex="-1">
				    <div class="modal-dialog">
				        <div class="modal-content kimsq">
				            <form>
				                <div class="modal-header">
				                    <button aria-hidden="true" class="close" data-dismiss="modal" type="button">&times;</button>
				                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus-circle fa-lg"></i>  새 그룹 추가</h4>
				                </div>
				                <div class="modal-body">
				                    <div class="row">
				                        <div class="col-sm-5 text-center hidden-xs"><br><br>
				                            <i class="fa fa-users fa-3x" style="font-size:180px"></i>
				                        </div>
				                        <div class="col-sm-7">
				                            <div class="page-header">
				                                <h4>
				                                    새 그룹을 추가 하시겠습니까?
				                                </h4>
				                            </div>
				                            <dl class="well well-sm">
				                                <div class="form-group">
				                                    <label for="group-name"><i class="fa fa-file fa-lg"></i> 그룹명</label>
				                                    <input type="text" class="form-control" name="name" id="group-name" placeholder="한글 및 영문 입력가능">
				                                </div>
				                            </dl>
				                            <ul style="margin-left:0;padding-left:15px">
				                                <li>그룹명 예시는 아래와 같습니다. </li>
				                                <li class="unstyled">(보기) 준회원,정회원,특별회원,관리자 등</li>
				                            </ul>
				                        </div>
				                    </div>
				                </div>
				                <div class="modal-footer">
				                    <button class="btn btn-default pull-left no-new-group" data-dismiss="modal" type="button">취소</button>
				                    <button class="btn btn-primary" type="submit">추가하기</button>
				                </div>
				            </form>
				        </div><!-- /.modal-content -->
				    </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
			  </form>
			</div>
		</div>
		<hr class="visible-xs visible-sm">
	</div>
   <!-- 회원등급 시작 -->
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading rb-icon">
				<div class="icon">
					<i class="fa fa-arrows-v fa-2x"></i>
				</div>
				<h4 class="panel-title">회원등급 <small>(Level)</small></h4>
			</div>
			<?php $levelnum = getDbData($table['s_mbrlevel'],'gid=1','*')?>
			<div class="panel-body level">
			  <form name="levelForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" onsubmit="return levelCheck(this);">
	         <input type="hidden" name="r" value="<?php echo $r?>" />
	         <input type="hidden" name="m" value="<?php echo $module?>" />
	         <input type="hidden" name="a" value="level_regis" />
				<table class="table table-hover">
					<colgroup>
						<col width="10%"></col>
						<col width="20%"></col>
						<col width="25%"></col>
						<col width="15%"></col>
						<col width="15%"></col>
						<col width="15%"></col>
					</colgroup>
					<thead>
						<tr>
							<th rowspan="2">등급</th>
							<th rowspan="2">회원수</th>
							<th rowspan="2">명칭</th>
							<th colspan="3"><span class="text-primary">등급 자동갱신 기준</span></th>
						</tr>
						<tr>
							<th><small>접속수</small></th>
							<th><small>게시물수</small></th>
							<th><small>댓글수</small></th>
						</tr>
					</thead>
					<tbody>
	               <?php $i=0?>
	               <?php $RCD=getDbArray($table['s_mbrlevel'],'','*','uid','asc',$levelnum['uid'],1)?>
	               <?php while($R=db_fetch_array($RCD)):?>
						<tr>
							<td><span class="label label-default"><?php echo $R['uid']?></span></td>
							<td><p class="form-control-static"><code><?php echo $R['num']?number_format($R['num']):'0'?></code></p></td>
							<td><input type="text" name="name_<?php echo $R['uid']?>" value="<?php echo $R['name']?>" class="form-control"></td>
							<td><input type="text" name="login_<?php echo $R['uid']?>" value="<?php echo $R['login']?$R['login']:''?>" class="form-control" onkeyup="numFormat1(this);" onkeypress="numFormat1(this);"<?php if(!$i):?> onblur="autoNumber(this);" title="입력하시면 자동완성됩니다."<?php endif?>></td>
							<td><input type="text" name="post_<?php echo $R['uid']?>" value="<?php echo $R['post']?$R['post']:''?>" class="form-control" onkeyup="numFormat1(this);" onkeypress="numFormat1(this);"<?php if(!$i):?> onblur="autoNumber(this);" title="입력하시면 자동완성됩니다."<?php endif?>></td>
							<td><input type="text" name="comment_<?php echo $R['uid']?>" value="<?php echo $R['comment']?$R['comment']:''?>" class="form-control" onkeyup="numFormat1(this);" onkeypress="numFormat1(this);"<?php if(!$i):?> onblur="autoNumber(this);" title="입력하시면 자동완성됩니다."<?php endif?>></td>
						</tr>
						<?php $i++; endwhile?>
					</tbody>
				</table>
			</div>
			<div class="panel-footer">
				<div class="form-group">
					<label class="sr-only" for="levelnum">사용할 등급수</label>
					<div class="input-group">
					  	 <div class="input-group-btn">
							  	 <select name="num" id="levelnum" class="form-control">
							   	<?php $levelnum = getDbData($table['s_mbrlevel'],'gid=1','*')?>
									<?php for($i=5; $i < 101; $i=$i+5):?>
									<option value="<?php echo $i?>"<?php if($i==$levelnum['uid']):?> selected="selected"<?php endif?>>사용등급 : <?php echo $i?></option>
									<?php endfor?>
								</select>
						 </div>
						 <div class="input-group-btn">
						 	  <button type="input" class="btn btn-primary pull-right">저장</button>
						 </div>
					</div>					
				 </div>  <!-- .form-group -->
			</div> <!-- .panel-footer -->
		 </form>	
		</div>
	</div>
</div>
<hr>
<!--그룹 삭제 폼 -->
<form name="delsosokForm" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" style="height:0">
<input type="hidden" name="r" value="<?php echo $r?>" />
<input type="hidden" name="m" value="<?php echo $module?>" />
<input type="hidden" name="a" value="sosok_delete" />
<input type="hidden" name="uid" value="" />
</form>

<!-- 그룹 순서변경시 jquery-ui sortable 사용하기 위해 import 및 파폭버그 설정  -->
<?php getImport('jquery-ui','jquery-ui.min',false,'js')?>
<style>
/* 그룹 선택시 */
 .ui-state-highlight {
    background-color: transparent;
    border: 1px dashed #d5d5d5;
    border-radius: 4px;
    height:120px;
    width: 100px;
    box-sizing: border-box;
    background-image: -webkit-linear-gradient(top, #428bca 0%, #3278b3 100%);
    background-image: linear-gradient(to bottom, #428bca 0%, #3278b3 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff428bca', endColorstr='#ff3278b3', GradientType=0);
    background-repeat: repeat-x;
    border-color: #3278b3;
    -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
    box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
  }
</style>
<!-- 그룹 순서변경시 jquery-ui sortable 사용하기 위해 import 및 파폭버그 설정  -->

<script type="text/javascript">
//<![CDATA[

//그룹순서 변경 이벤트 
$(function() {
   $( "#group-list" ).sortable({
   	  revert:150, // 부드럽게 변경
    	  placeholder: "ui-state-highlight" // 해당 아이템 클릭후 이동시 표시해주기
   });
});


// 새그룹 추가 버튼 클릭 이벤트 
$('#btn-new-group').on('click',function(){
    $('input[name="is_new_group"]').val(1);
});

// 그룹 삭제 이벤트 
$('.del-a').on('click',function(e){
   e.preventDefault();
   var id=$(this).attr('id');
   var id_arr=id.split('-');
   var uid=id_arr[0];
   var num=id_arr[1];
   var f=document.delsosokForm;
   sosokDelCheck(f,uid,num); // 그룹삭제 폼 전송함수 호출

});
var nvisible = false;
function autoNumber(obj)
{
	if (!obj.value) return false;
	
	var znum = obj.name == 'login_1' ? 2 : 1;
	var f = obj.form;
	var levelnum = <?php echo $levelnum['uid']?>;
	var i;
	var exp;
	for	(i = 1; i < levelnum; i++)
	{
		exp = obj.name.split('_');
		eval('f.'+exp[0]+'_'+(i+1)).value = (parseInt(obj.value) * znum * i) + parseInt(eval('f.'+exp[0]+'_'+i).value);
	}

}
function sosokDelCheck(f,uid,n)
{
	if (n > 0)
	{
		alert('소속회원이 존재하는 그룹은 삭제할 수 없습니다. ');
		return false;
	}
	if (confirm('정말로 삭제하시겠습니까?      '))
	{
		f.uid.value=uid;
       getIframeForAction(f);
	}
	return false;
}
function regisSosok()
{
	if (nvisible == false)
	{
		getId('ninput').style.visibility = 'visible';
		document.sosokForm.name.focus();
		nvisible = true;
	}
	else {
		getId('ninput').style.visibility = 'hidden';
		nvisible = false;
	}
}
function levelCheck(f)
{
	if (confirm('정말로 실행하시겠습니까?   '))
	{
		getIframeForAction(f);
	}
}
function regisCheck(f)
{
	var add_modal=$('#modal-group-add');

	if ($(add_modal).css('display')=='block' && f.name.value == '')
	{
			alert('추가할 그룹명을 입력해 주세요.   ');
			f.name.focus();
			return false;
	}

	if (confirm('정말로 실행하시겠습니까?   '))
	{
		getIframeForAction(f);
	}else{
		return false;
	}
}
function numFormat1(obj)
{
	if (!getTypeCheck(obj.value,'0123456789'))
	{
		alert('숫자만 입력해 주세요.');
		obj.value = '';
		obj.focus();
		return false;
	}
}

// 툴팁 이벤트 
$(document).ready(function() {
   $('[data-toggle=tooltip]').tooltip();
}); 
//]]>
</script>

