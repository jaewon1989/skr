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
	      <textarea class="form-control" name="review" rows="3" id="meta-description-content"  maxlength="155"><?php echo $R['review']?></textarea>
	 </div>   
</div>
<div class="form-group">
	<label class="col-md-1 control-label">가격</label>
	<div class="col-md-11">
		<div class="input-group">
		      <input type="text" class="form-control" name="price" value="<?php echo $R['price']?>" onkeyup="numFormat(this);" >
		      <span class="input-group-addon">원</span>
		 </div>
	 </div>     
</div>	
<div class="form-group">
	<label class="col-md-1 control-label">기타</label>
	<div class="col-md-11">
		 <textarea class="form-control" name="addinfo" rows="5" ><?php echo $R['addinfo']?></textarea>
		 <div class="help-block">
			<small>
				html 태그를 포함한 추가정보를 입력해주세요. 
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
<div class="form-group rb-form">
	<label class="col-md-1 control-label">라벨</label>
	<div class="col-md-11">		
		<?php $idir = $g['path_module'].$module.'/var/icons/'?>
		<?php $dirs = opendir($idir)?>
		<?php while(false !== ($label = readdir($dirs))):?>
		<?php if(!is_file($idir.$label)||$label=='icon-related-06.svg')continue?>	
	   	<label class="checkbox-inline" style="padding-top:0">
			<input type="checkbox" name="iconmembers[]" value="<?php echo $label?>"<?php if(strstr($R['icons'],$label)):?> checked="checked"<?php endif?> class="form-control" /><i></i><img src="<?php echo $g['s'].'/modules/'.$module?>/var/icons/<?php echo $label?>" alt="<?php echo Shop_getLabelName($label)?>" style="width:25px;height:25px;" /> <?php echo Shop_getLabelName($label)?>
		</label>
		<?php endwhile?>
		<?php closedir($dirs)?>	
      </div>
</div>	
<div class="form-group">
	<label class="col-md-1 control-label">상  태</label>
	<div class="col-md-11">
           <label class="radio-inline">
                <input type="radio" name="dispaly" value="0" <?php if(!$R['display']):?> checked="checked"<?php endif?> />  노출
          </label>
           <label class="radio-inline">
                <input type="radio" name="dispaly" value="2" <?php if($R['display']==2):?> checked="checked"<?php endif?> />  숨김
          </label>
	 </div>     
</div>