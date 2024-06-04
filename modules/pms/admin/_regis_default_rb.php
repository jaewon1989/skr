<div class="form-group">
	<label class="col-md-1 control-label">분  류</label>
	<div class="col-md-11">
		<select name="category" class="form-control">
			<option value="">+ 선택하세요.</option>
			<option value="">------------------</option>
			<?php $cat=$R['category']?>
			<?php getCategoryShowSelect($table[$module.'category'],0,0,0,0,0)?>
		</select>
      </div>
</div>
<div class="form-group">
	<label class="col-md-1 control-label">상품명</label>
	<div class="col-md-11">
	    <input type="text" class="form-control" name="name" value="<?php echo $R['name']?>">
	 </div>   
</div>
<div class="form-group">
	<label class="col-md-1 control-label">리  뷰</label>
	<div class="col-md-11">
	      <textarea class="form-control" name="review" rows="5" id="meta-description-content"  maxlength="155"><?php echo $R['review']?></textarea>
	 </div>   
</div>	
<div class="form-group">
	 <label class="col-md-1 control-label">원산지</label>
      <div class="col-md-5">
	     <input type="text" class="form-control" name="country" value="<?php echo $R['country']?>">
      </div>
	<label class="col-md-1 control-label">제조사</label>
	<div class="col-md-5">
		<select name="maker" class="form-control">
			<option value="">+ 선택하세요.</option>
			<option value="">------------</option>
			<?php $_makerset=explode(',',implode('',file($g['path_module'].$module.'/var/set.maker.txt')))?>
			<?php foreach($_makerset as $_maker):if(!trim($_maker))continue?>
			<option value="<?php echo $_maker?>"<?php if($R['maker']==$_maker):?> selected="selected"<?php endif?>>ㆍ<?php echo $_maker?></option>
			<?php endforeach?>
		</select>
	</div>
</div>	
<div class="form-group">
	<label class="col-md-1 control-label">모델명</label>
	<div class="col-md-5">
		<input type="text" class="form-control" name="model" value="<?php echo $R['model']?>">
	</div>
	<label class="col-md-1 control-label">브랜드</label>
	<div class="col-md-5">
		<select name="brand" class="form-control">
			<option value="">+ 선택하세요.</option>
				<option value="">-------------</option>
				<?php $_brandset=explode(',',implode('',file($g['path_module'].$module.'/var/set.brand.txt')))?>
				<?php foreach($_brandset as $_brand):if(!trim($_brand))continue?>
				<option value="<?php echo $_brand?>"<?php if($R['brand']==$_brand):?> selected="selected"<?php endif?>>ㆍ<?php echo $_brand?></option>
				<?php endforeach?>
		</select>
	</div>
</div>	
<div class="form-group">
	<label class="col-md-1 control-label">부가정보</label>
	<div class="col-md-11">
		 <textarea class="form-control" name="addinfo" rows="5" ><?php echo $R['addinfo']?></textarea>
		 <div class="help-block">
			<small>
				정보제목=정보값 을 콤마(,)로 구분해서 등록해 주세요. (예 : 칼라=검정,원산지=중국)
			</small>   
		</div>
	</div>
</div>	
<div class="form-group">
	<label class="col-md-1 control-label">태 그 </label>
	<div class="col-md-11">
		 <input type="text" class="form-control" id="meta-tag-content" data-role="tagsinput"  name="tags" value="<?php echo $R['tags']?>">
		 <div class="help-block">
			<small>
				콤마(,)로 구분해서 등록해 주세요. (예 : 태그1,태그2,태그3 )
			</small>   
		</div>
	</div>
</div>	