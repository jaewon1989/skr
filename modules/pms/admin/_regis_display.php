<div class="form-group">
	<label class="col-md-1 control-label">상  태</label>
	<div class="col-md-11">
           <label class="radio-inline">
                <input type="radio" name="dispaly" value="0" <?php if(!$R['display']):?> checked="checked"<?php endif?> />  정상판매
          </label>
           <label class="radio-inline">
                <input type="radio" name="dispaly" value="1" <?php if($R['display']==1):?> checked="checked"<?php endif?> />  임시품절
          </label>
           <label class="radio-inline">
                <input type="radio" name="dispaly" value="2" <?php if($R['display']==2):?> checked="checked"<?php endif?> />  노출중단
          </label>
	 </div>     
</div>
<div class="form-group rb-form">
	<label class="col-md-1 control-label">재고량</label>
      <div class="col-md-4">
		<div class="input-group">
		      <input type="text" name="stock_num" value="<?php echo $R['stock_num']?$R['stock_num']:0?>" size="4" class="form-control" maxlength="5" onkeydown="numFormat(this);" onkeypress="numFormat(this);" />   
		      <span class="input-group-addon">Unit</span>		      
		</div>
	</div>
	<div class="col-md-7">		
		      <label class="checkbox" style="padding-top:0">
		            <input  type="checkbox" name="stock" value="1" <?php if($R['stock']):?> checked="checked"<?php endif?> class="form-control" > <i></i>재고관리 함    	
		            <small> (체크하면 재고관리가 진행되며 재고량에 따라 품절여부를 처리합니다.)</small>
	            </label> 
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