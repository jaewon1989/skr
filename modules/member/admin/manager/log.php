<?php
$sort	= $sort ? $sort : 'uid';
$orderby= $orderby ? $orderby : 'desc';
$recnum	= $recnum && $recnum < 200 ? $recnum : 10;

$sqlque = 'mbruid='.$_M['uid'];
if ($siteuid) $sqlque .= ' and site='.$siteuid;
if ($d_start) $sqlque .= ' and d_regis > '.str_replace('/','',$d_start).'000000';
if ($d_finish) $sqlque .= ' and d_regis < '.str_replace('/','',$d_finish).'240000';
if ($where && $keyw)
{
	$sqlque .= getSearchSql($where,$keyw,$ikeyword,'or');
}
$RCD = getDbArray($table['s_referer'],$sqlque,'*',$sort,$orderby,$recnum,$p);
$NUM = getDbRows($table['s_referer'],$sqlque);
$TPG = getTotalPage($NUM,$recnum);
?>

<div class="manager-list">
	<p>
		<small><?php echo sprintf('총 %d건',$NUM)?>  (<?php echo $p?>/<?php echo $TPG?> page<?php if($TPG>1):?>s<?php endif?>)</small>
	</p>

  <form name="searchForm" class="form-horizontal" action="<?php echo $g['s']?>/" method="get">
	<input type="hidden" name="r" value="<?php echo $r?>">
	<input type="hidden" name="m" value="<?php echo $m?>">
	<input type="hidden" name="module" value="<?php echo $module?>">
	<input type="hidden" name="front" value="<?php echo $front?>">
	<input type="hidden" name="tab" value="<?php echo $tab?>">
	<input type="hidden" name="uid" value="<?php echo $_M['uid']?>">
	<input type="hidden" name="p" value="<?php echo $p?>">
	<input type="hidden" name="iframe" value="<?php echo $iframe?>">

	   <div class="well well-sm">
	   	<div class="form-group">
	   		 <label class="col-sm-1 control-label">필터</label>
				 <div class="col-sm-11">	
						<div class="col-sm-2">
							<select name="siteuid" class="form-control input-sm" onchange="this.form.submit();">
							<option value="">사이트</option>
							<?php $SITES = getDbArray($table['s_site'],'','*','gid','asc',0,$p)?>
							<?php while($S = db_fetch_array($SITES)):?>
							<option value="<?php echo $S['uid']?>"<?php if($S['uid']==$siteuid):?> selected<?php endif?>><?php echo $S['name']?> (<?php echo $S['id']?>)</option>
							<?php endwhile?>
							</select>
						</div>
						<div class="col-sm-2">
							<select name="bid" class="form-control input-sm" onchange="this.form.submit();">
							<option value="">게시판</option>
							<?php $_BBSLIST = getDbArray($table['bbslist'],'','*','gid','asc',0,1)?>
							<?php while($_B=db_fetch_array($_BBSLIST)):?>
							<option value="<?php echo $_B['uid']?>"<?php if($_B['uid']==$bid):?> selected="selected"<?php endif?>>ㆍ<?php echo $_B['name']?>(<?php echo $_B['id']?>)</option>
							<?php endwhile?>
							<?php if(!db_num_rows($_BBSLIST)):?>
							<option value="">등록된 게시판이 없습니다.</option>
							<?php endif?>
							</select>
						</div>
						<div class="col-sm-5">
							<div class="input-daterange input-group input-group-sm" id="datepicker">
								<input type="text" class="form-control" name="d_start" placeholder="시작일" value="<?php echo $d_start?>">
								<span class="input-group-addon">~</span>
								<input type="text" class="form-control" name="d_finish" placeholder="종료일" value="<?php echo $d_finish?>">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
								</span>
							</div>
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-link" data-toggle="collapse" data-target="#search-more-bbs" onclick="sessionSetting('sh_bbslist','1','','1');">엔터티 검색<small></small></button>
						</div>
				 </div> <!-- .col-sm-11 -->	 
			 </div><!-- .form-group -->	 
			 <div id="search-more-bbs" class="collapse<?php if($_SESSION['sh_bbslist']):?> in<?php endif?>">			
                <div class="form-group">
                     <label class="col-sm-1 control-label">검색</label> 
			            <div class="col-sm-11"> 
			            	<div class="col-sm-11">
									 <div class="input-group input-group-sm">
											<span class="input-group-btn">
												<select name="where" class="btn btn-default">
													<option<?php if($where=='ip'):?> selected<?php endif?>>아이피</option>
							                  <option<?php if($where=='referer'):?> selected<?php endif?>>접속경로</option>
												</select>
											 </span>
											 <input type="text" name="keyw" class="form-control" placeholder="검색어를 입력해주세요." value="<?php echo $keyw?>">
											 <span class="input-group-btn" style="margin-bottom:0;">
											  	 <button class="btn btn-default" type="submit"><i class="fa fa-search"></i>검색</button>
											 	 <button class="btn btn-default hidden-xs" type="button" onclick="this.form.keyw.value='';this.form.submit();">리셋</button>
											 </span>
									 </div>
							   </div>
							</div>		
					</div> <!-- .form-group -->
			  </div> 	<!-- .고급검색  -->
		</div> <!-- .well -->
   </form>

	<table class="table table-hover" style="border-bottom:#ccc solid 1px;">
		<thead>
			<tr>
				<th>번호</th>
				<th>아이피</th>
				<th class="rb-url">접속경로</th>
				<th>브라우져</th>
				<th>기기</th>
				<th>날짜</th>
			</tr>
		</thead>
		<tbody>
			<?php while($R=db_fetch_array($RCD)):?>
			<?php $_browzer=getBrowzer($R['agent'])?>
			<?php $_deviceKind=isMobileConnect($R['agent'])?>
			<?php $_deviceType=getDeviceKind($R['agent'],$_deviceKind)?>
			<tr>
				<td><?php echo $NUM-((($p-1)*$recnum)+$_rec++)?></td>
				<td><?php echo $R['ip']?></td>
				<td class="rb-url"><a href="<?php echo $R['referer']?>" target="_blank"><?php echo getDomain($R['referer'])?></a></td>
				<td><?php echo strtoupper($_browzer)?></td>
				<td>
					<?php if($_browzer=='Mobile'):?>
					<small class="label label-<?php echo $_deviceType=='tablet'?'danger':'warning'?>" data-tooltip="tooltip" title="<?php echo $_deviceKind?>"><?php echo $_deviceType?></small>
					<?php else:?>
					<small class="label label-default">desktop</small>
					<?php endif?>
				</td>
				<td class="rb-update">
					<time class="timeago" data-toggle="tooltip" datetime="<?php echo getDateFormat($R['d_regis'],'c')?>" data-tooltip="tooltip" title="<?php echo getDateFormat($R['d_regis'],'Y.m.d H:i')?>"></time>	
				</td>
			</tr>
			<?php endwhile?>
		</tbody>
	</table>
	<?php if(!$NUM):?>
	<div class="rb-none">접속기록이 없습니다.</div>
	<?php endif?>

	<div class="panel-body text-center">
		<ul class="pagination pagination-sm">
		<script type="text/javascript">getPageLink(5,<?php echo $p?>,<?php echo $TPG?>,'');</script>
		<?php //echo getPageLink(5,$p,$TPG,'')?>
		</ul>
	</div>
</div>

