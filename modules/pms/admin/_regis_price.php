<div class="form-group">
	<label class="col-md-1 control-label">판매가</label>
	<div class="col-md-11">
		<div class="input-group">
		      <input type="text" class="form-control" name="price" value="<?php echo $R['price']?>" onkeydown="numFormat(this);" onkeypress="numFormat(this);" >
		      <span class="input-group-addon">원</span>
		 </div>
	 </div>     
</div>
<div class="form-group">
	<label class="col-md-1 control-label">시중가</label>
      <div class="col-md-11">
		<div class="input-group">
		      <input type="text" class="form-control" name="price1" value="<?php echo $R['price1']?>" onkeydown="numFormat(this);" onkeypress="numFormat(this);" >
		      <span class="input-group-addon">원</span>
		 </div>
	 </div>     
</div>								
<div class="form-group">
	<label class="col-md-1 control-label">적립P</label>
	<div class="col-md-11">
		<div class="input-group">
		      <input type="text" class="form-control" name="point" value="<?php echo $R['point']?>">
		      <span class="input-group-addon">Point</span>
	      </div> 
      </div>
</div>	