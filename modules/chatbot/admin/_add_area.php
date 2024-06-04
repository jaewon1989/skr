<div class="form-group">
    <label class="col-sm-2 control-label" style="padding:7px;padding-bottom:0">카테고리 </label>
    <div class="col-sm-10">
        <select name="sido" class="form-control">
            <?php $CD=getDbArray($table[$module.'category'],'hidden=0','*','gid','asc','',1);?>
              <option value="">+ 카테고리 선택</option>
            <?php while($C=db_fetch_array($CD)):?>
          <option value="<?php echo $C['uid']?>"<?php if($CD['uid']==$R['category']):?> selected<?php endif?>><?php echo $CD['name']?></option>';
           <?php endwhile?>
        </select>
    </div>
</div>        
<div class="form-group">
	<label class="col-sm-2 control-label" style="padding:7px;padding-bottom:0">지역 선택 </label>
	<div class="col-sm-10">
		<select name="sido" class="form-control" onchange="getAddr(this.value,'sido','#gugun-wrap');">
			<?php $AD=getDbArray($table[$module.'zipcode'],'','distinct(sido)','uid','asc','',1);?>
        	  <option value="">+ 시도 선택</option>
          	<?php while($A=db_fetch_array($AD)):?>
       	  <option value="<?php echo $A['sido']?>"<?php if($A['sido']==$R['sido']):?> selected<?php endif?>><?php echo $A['sido']?></option>';
           <?php endwhile?>
		</select>
        <div class="help-block" id="gugun-wrap">
        </div>
        <div class="help-block" id="dong-wrap">
        </div>
   </div>
</div>



			<select name="gugun" class="form-control" id="gugun-wrap" onchange="getAddr(this.value,'gugun','#dong-wrap');">
   	   	    	<option value="">+ 구/군 선택</option>
   	   	        <?php if($uid):?>
   	   	        <?php $sido=$R['sido']?>
   	   	    	<?php $AD=getDbArray($table[$module.'zipcode'],"sido='".$sido."'",'distinct(gugun)','uid','asc','',1);?>
   	   	    	<?php while($A=db_fetch_array($AD)):?>
   	   	    	  <option value="<?php echo $A['gugun']?>"<?php if($A['gugun']==$R['gugun']):?> selected<?php endif?>><?php echo $A['gugun']?></option>';	
   	   	        <?php endwhile?>
   	   	        <?php endif?>
			</select>
			<span class="input-group-addon">></span>
			<select name="dong" class="form-control" id="dong-wrap">
   	   	    	<option value="">+ 동 선택 </option>
   	   	    	<?php if($uid):?>
   	   	        <?php $gugun=$R['gugun']?>
   	   	    	<?php $AD=getDbArray($table[$module.'zipcode'],"sido='".$sido."' and gugun='".$gugun."'",'distinct(dong)','uid','asc','',1);?>
   	   	    	<?php while($A=db_fetch_array($AD)):?>
   	   	    	  <option value="<?php echo $A['dong']?>"<?php if($A['dong']==$R['dong']):?> selected<?php endif?>><?php echo $A['dong']?></option>';	
   	   	        <?php endwhile?>
   	   	        <?php endif?>
        	</select>

		</div>
	</div>				
 </div>