<?php if(!defined('__KIMS__')) exit?>
<form name="procForm" class="form-horizontal rb-form" role="form" action="<?php echo $g['s']?>/" method="post" target="_action_frame_<?php echo $m?>" >
      <input type="hidden" name="r" value="<?php echo $r?>" />
      <input type="hidden" name="m" value="<?php echo $module?>" />
      <input type="hidden" name="a" value="config" />
      <input type="hidden" name="type" value="<?php echo $type?>" />
       
     	<div class="page-header">
			<h4> 기본환경 설정 </h4>
	    </div>
        
        <div class="form-group">
			<label class="col-md-2 control-label">퍼블리싱 Path(P)</label>
			<div class="col-md-10">
			    <input type="text" class="form-control" name="PB_path_desktop" value="<?php echo $d['shop']['PB_path_desktop']?>">
			 </div>   
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">퍼블리싱 Path(M)</label>
			<div class="col-md-10">
			    <input type="text" class="form-control" name="PB_path_mobile" value="<?php echo $d['shop']['PB_path_mobile']?>">
			 </div>   
		</div>
        <div class="form-group">
			<label class="col-sm-2 control-label">대표 레이아웃 </label>
			<div class="col-sm-10">
				<div class="input-group">
			   	      <select name="layout" class="form-control">
					 	<option value="">&nbsp;+ 사이트 대표레이아웃</option>
						<option value="">--------------------------------</option>
							<?php $dirs = opendir($g['path_layout'])?>
							<?php while(false !== ($tpl = readdir($dirs))):?>
							<?php if($tpl=='.' || $tpl == '..' || is_file($g['path_layout'].$tpl))continue?>
							<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
							<option value="">--------------------------------</option>
							<?php while(false !== ($tpl1 = readdir($dirs1))):?>
							<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
							<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['shop']['layout']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
							<?php endwhile?>
							<?php closedir($dirs1)?>
							<?php endwhile?>
							<?php closedir($dirs)?>
					</select>
					<span class="input-group-btn">
						<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#layout-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
					</span>
			     </div>
				<p class="help-block collapse alert alert-warning" id="layout-guide">
				      <small>	
					  	 지정된 레이아웃은 매장등록시 <strong>desktop</strong> 레이아웃을 지정하지 않은 경우 자동으로 적용됩니다.<br />
			                   가장 많이 사용하는 레이아웃을 지정해 주세요.
		                 </small>
		            </p>     
			</div>				
	  </div>
	  <div class="form-group">
			<label class="col-sm-2 control-label">모바일 레이아웃</label>
			<div class="col-sm-10">
		   	      <div class="input-group">
			   	      <select name="layout_m" class="form-control">
					 	<option value="">&nbsp;+ 사이트 대표레이아웃</option>
						<option value="">--------------------------------</option>
							<?php $dirs = opendir($g['path_layout'])?>
							<?php while(false !== ($tpl = readdir($dirs))):?>
							<?php if($tpl=='.' || $tpl == '..' || $tpl == '_blank' || is_file($g['path_layout'].$tpl))continue?>
							<?php $dirs1 = opendir($g['path_layout'].$tpl)?>
							<option value="">--------------------------------</option>
							<?php while(false !== ($tpl1 = readdir($dirs1))):?>
							<?php if(!strstr($tpl1,'.php') || $tpl1=='_main.php')continue?>
							<option value="<?php echo $tpl?>/<?php echo $tpl1?>"<?php if($d['shop']['layout_m']==$tpl.'/'.$tpl1):?> selected="selected"<?php endif?>>ㆍ<?php echo getFolderName($g['path_layout'].$tpl)?>(<?php echo str_replace('.php','',$tpl1)?>)</option>
							<?php endwhile?>
							<?php closedir($dirs1)?>
							<?php endwhile?>
							<?php closedir($dirs)?>
					</select>
					<span class="input-group-btn">
						<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#mlayout-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
					</span>
			      </div>
				<p class="help-block collapse alert alert-warning" id="mlayout-guide">
				      <small>	
					  	 지정된 레이아웃은 매장등록시 <strong>mobile</strong> 레이아웃을 지정하지 않은 경우 자동으로 적용됩니다.<br />
			                   가장 많이 사용하는 레이아웃을 지정해 주세요.
		                 </small>
		            </p>     
			</div>		
	  </div>
	 <div class="form-group">
	  	<label class="col-sm-2 control-label">대표 테마 </label> 
	     <div class="col-sm-10">
	     	      <div class="input-group">
				 <select name="skin_main" class="form-control">
					<?php $tdir = $g['path_module'].$module.'/theme/'?>
					<?php $dirs = opendir($tdir)?>
					<?php while(false !== ($skin = readdir($dirs))):?>
					<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
					<option value="<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['shop']['skin_main']==$skin):?> selected="selected"<?php endif?>><?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
					<?php endwhile?>
					<?php closedir($dirs)?>
				</select>
				<span class="input-group-btn">
						<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#theme-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
				</span>
			</div>
			<p class="help-block collapse alert alert-warning" id="theme-guide">
			      <small>	
					  	 지정된 테마은 매장등록시 <strong>desktop</strong> 테마를 지정하지 않은 경우 자동으로 적용됩니다.<br />
			                   가장 많이 사용하는 테마를 지정해 주세요.
		           </small>
		      </p>     						
		</div> <!-- .col-sm-10  -->
	</div> <!-- .form-group  -->
	<div class="form-group">
	  	<label class="col-sm-2 control-label text-muted">모바일 테마</label> 
	      <div class="col-sm-10">
	      	<div class="input-group">
			  	 <select name="skin_mobile" class="form-control">
					<?php $tdir = $g['path_module'].$module.'/theme/'?>
					<?php $dirs = opendir($tdir)?>
					<?php while(false !== ($skin = readdir($dirs))):?>
					<?php if($skin=='.' || $skin == '..' || is_file($tdir.$skin))continue?>
					<option value="<?php echo $skin?>" title="<?php echo $skin?>"<?php if($d['shop']['skin_mobile']==$skin):?> selected="selected"<?php endif?>><?php echo getFolderName($tdir.$skin)?>(<?php echo $skin?>)</option>
					<?php endwhile?>
					<?php closedir($dirs)?>
			      </select>	
			      <span class="input-group-btn">
						<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#mtheme-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
				</span>
			 </div>
			 <p class="help-block collapse alert alert-warning" id="mtheme-guide">
			      <small>	
					  	 지정된 테마은 매장등록시 <strong>mobile</strong> 테마를 지정하지 않은 경우 자동으로 적용됩니다.<br />
			                   가장 많이 사용하는 테마를 지정해 주세요.
		           </small>
		      </p>
			      			
		</div> <!-- .col-sm-10  -->
	</div> <!-- .form-group  -->
	<div class="form-group">
			<label class="col-sm-2 control-label">연결메뉴</label>
			<div class="col-sm-10">
				<div class="input-group">
				   	 <select name="sosokmenu" class="form-control">
						 	<option value="">&nbsp;+ 사용 안함</option>
							<option value="">----------------------</option>
						   <?php include_once $g['path_core'].'function/menu1.func.php'?>
						   <?php $cat=$d['shop']['sosokmenu']?>
						   <?php getMenuShowSelect($s,$table['s_menu'],0,0,0,0,0,'')?>
						</select>
						<span class="input-group-btn">
							<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#sosok_menu-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
						</span>
			   </div>
			   <p class="help-block collapse alert alert-warning" id="sosok_menu-guide">
					<small> 
					이 카달로그를 메뉴에 연결하였을 경우 해당메뉴를 지정해 주세요.<br />
				     연결메뉴를 지정하면 상품수,로케이션이 동기화됩니다.<br />
				   </small>
		      </p>
			</div>				
	 </div>
	 
	<div class="form-group">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fa fa-check fa-lg"></i> 저장하기 </button>
		</div>
	</div>
</form>