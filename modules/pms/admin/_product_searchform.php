<form name="procForm" action="<?php echo $g['s']?>/" method="get" class="form-horizontal rb-form">
	<input type="hidden" name="r" value="<?php echo $r?>" />
	<input type="hidden" name="m" value="<?php echo $m?>" />
	<input type="hidden" name="module" value="<?php echo $module?>" />
	<input type="hidden" name="front" value="<?php echo $front?>" />
	<?php if($front=='search_product'):?>
      <input type="hidden" name="iframe" value="Y" />
      <?php endif?>
      <div class="rb-heading well well-sm"> 
		<div class="form-group">
			 <label class="col-sm-1 control-label">필터</label>
			 <div class="col-sm-10">
			 	<div class="row">
					<div class="col-sm-4">
						 <select class="form-control" name="cat" onchange="this.form.submit();">
							 <option value="">&nbsp;+ 카테고리(전체)</option>
							 <option value="">------------------</option>
							 <?php getCategoryShowSelect($table[$module.'category'],0,0,0,0,0)?>
						 </select>
					 </div>
					 <div class="col-sm-3">
						<select class="form-control" name="display" onchange="this.form.submit();">
							<option value=""<?php if($display==''):?> selected="selected"<?php endif?>>노출상태</option>
							<option value="">--------</option>
							<option value="0"<?php if($display=='0'):?> selected="selected"<?php endif?>>정상판매</option>
							<option value="2"<?php if($sort=='2'):?> selected="selected"<?php endif?>>노출중단</option>
						</select>
					 </div>
					 <div class="col-sm-3">
					 	<select name="recnum" onchange="this.form.submit();" class="form-control input-sm">
					 		<option value="" >출력갯수</option>
							<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20</option>
							<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35</option>
							<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50</option>
							<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75</option>
							<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90</option>
						</select>						
					 </div>
				
						
				</div>
			</div> <!-- .col-sm-10 -->
		</div>	<!-- .form-group -->		
	      <!-- 고급검색 시작 -->
		<div id="search-more" class="collapse<?php if($_SESSION['sh_productlist']):?> in<?php endif?>">
			
			<div class="form-group">
				<label class="col-sm-1 control-label">검색</label>
				<div class="col-sm-10">
					<div class="input-group input-group-sm">
						<span class="input-group-btn hidden-xs" style="width:165px">
							<select name="where" class="form-control btn btn-default">
								<option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>상품명</option>
								<option value="tags"<?php if($where=='tags'):?> selected="selected"<?php endif?>>태그</option>
							</select>
						</span>
						<input type="text" name="keyw" value="<?php echo stripslashes($keyw)?>" class="form-control">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit">검색</button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-1 control-label">출력</label>
				<div class="col-sm-10">
					<div class="row">
						<div class="col-sm-2">
							<select name="recnum" onchange="this.form.submit();" class="form-control input-sm">
								<option value="20"<?php if($recnum==20):?> selected="selected"<?php endif?>>20</option>
								<option value="35"<?php if($recnum==35):?> selected="selected"<?php endif?>>35</option>
								<option value="50"<?php if($recnum==50):?> selected="selected"<?php endif?>>50</option>
								<option value="75"<?php if($recnum==75):?> selected="selected"<?php endif?>>75</option>
								<option value="90"<?php if($recnum==90):?> selected="selected"<?php endif?>>90</option>
							</select>
						</div>
						<div class="col-sm-2">
						</div>
					</div>
				</div>
			</div>
		</div><!-- 고급검색 끝 -->	
		<div class="form-group">
			<div class="col-sm-offset-1 col-sm-10">
				<button type="button" class="btn btn-link rb-advance<?php if(!$_SESSION['sh_productlist']):?> collapsed<?php endif?>" data-toggle="collapse" data-target="#search-more" onclick="sessionSetting('sh_productlist','1','','1');">고급검색<small></small></button>
					<a href="<?php echo $g['adm_href']?>" class="btn btn-link">초기화</a>
			</div>
		</div>
	</div>	<!-- .well -->	
</form>