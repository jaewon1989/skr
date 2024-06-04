 <div class="form-group">
	<label class="col-sm-3 control-label">업체</label>
	<div class="col-sm-9">
		<div class="input-group">
			<input class="form-control" placeholder="좌측 업체 리스트에서 선택 " type="text" name="vendor_info" value="<?php echo $vendor_info?>" disabled >
			<span class="input-group-btn">
				<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#vendor-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
			</span>
		</div>
		<p class="help-block collapse alert alert-warning" id="vendor-guide">
			<small> 좌측 업체 리스트에서 선택해주세요.</small>
      </p>
	</div>				
 </div>
 <div class="form-group">
	<label class="col-sm-3 control-label">카테고리</label>
	<div class="col-sm-9">
		<div class="input-group">
			<input class="form-control" placeholder="좌측 카테고리 리스트에서 선택 " type="text" name="catName" value="<?php echo $R['quesCat']?>" disabled>
			<span class="input-group-btn">
				<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#quesCat-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
			</span>
		</div>
		<p class="help-block collapse alert alert-warning" id="quesCat-guide">
			<small>	
				좌측 카테고리 리스트에서 선택해주세요.<br />							
            </small>
        </p>
	</div>				
 </div>
 <div class="form-group">
	<label class="col-sm-3 control-label">답변타입</label>
	<div class="col-sm-9">
		<div class="input-group">
   	   	    <select name="type" class="form-control">
   	   	    	<option value="A" <?php if($R['type']=='A'||!$R['type']):?> selected="selected"<?php endif?>>단답형</option>
          		<option value="S" <?php if($R['type']=='S'):?> selected="selected"<?php endif?>>선택형</option>
			</select>
			<span class="input-group-btn">
				<button class="btn btn-default rb-help-btn" type="button" data-toggle="collapse" data-target="#type-guide" data-tooltip="tooltip" title="도움말"><i class="fa fa-question-circle fa-lg"></i></button>
			</span>
	    </div>

		<p class="help-block collapse alert alert-warning" id="type-guide">
			<small>	
				단답형 : 서술형식의 답변을 바로 제공합니다. <br />
				선택형 : 답변을 선택할 수 있도록 몇 가지 예시를 제공합니다.  
		    </small>
	    </p>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">답변 내용</label>
	<div class="col-sm-9">
		<textarea name="content" rows="10" class="form-control" ><?php echo $R['content']?></textarea>
	</div>				
</div>
