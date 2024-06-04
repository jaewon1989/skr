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
	<label class="col-sm-3 control-label">질문</label>
	<div class="col-sm-9">
		<textarea name="question" rows="10" class="form-control" ><?php echo $R['question']?></textarea>
	</div>				
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">답변</label>
	<div class="col-sm-9">
		<textarea name="reply" rows="10" class="form-control" ><?php echo $R['reply']?></textarea>
	</div>				
</div>
