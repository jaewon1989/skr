<?php
$recnum = 10;
$catque = 'uid';
if ($_keyw) $catque .= " and ".$where." like '".$_keyw."%'";
$PAGES = getDbArray($table[$smodule.'list'],$catque,'*','gid','asc',$recnum,$p);
$NUM = getDbRows($table[$smodule.'list'],$catque);
$TPG = getTotalPage($NUM,$recnum);
$tdir = $g['path_module'].$smodule.'/theme/';
?>


<div id="mjointbox">

	<div class="title">
		<form name="bbsSform" action="<?php echo $g['s']?>/">
		<input type="hidden" name="system" value="<?php echo $system?>" />
		<input type="hidden" name="r" value="<?php echo $r?>" />
		<input type="hidden" name="iframe" value="<?php echo $iframe?>" />
		<input type="hidden" name="dropfield" value="<?php echo $dropfield?>" />
		<input type="hidden" name="smodule" value="<?php echo $smodule?>" />
		<input type="hidden" name="cmodule" value="<?php echo $cmodule?>" />
		<input type="hidden" name="p" value="<?php echo $p?>" />

		<select name="where">
		<option value="name"<?php if($where == 'name'):?> selected="selected"<?php endif?>>블로그제목</option>
		<option value="id"<?php if($where == 'id'):?> selected="selected"<?php endif?>>블로그ID</option>
		</select>
		
		<input type="text" name="_keyw" size="15" value="<?php echo addslashes($_keyw)?>" class="input" />
		<input type="submit" value=" 검색 " class="btngray" />
		<input type="button" value=" 리셋 " class="btngray" onclick="thisReset();" />	
		</form>

	</div>


	<table>
		<?php while($R = db_fetch_array($PAGES)):?>
		<tr>
		<td>
			<input type="hidden" id="cat<?php echo $R['id']?>" value="" />
			<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=<?php echo $smodule?>&amp;blog=<?php echo $R['id']?>" target="_blank" title="블로그보기"><?php echo $R['name']?></a><span>(<?php echo $R['id']?>)</span>
		</td>
		<td class="aply">
			<input type="button" value="블로그메인" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&blog=<?php echo $R['id']?>');" />
			<input type="button" value="포스트" class="btnblue" onclick="dropJoint('<?php echo $g['s']?>/?r=<?php echo $r?>&m=<?php echo $smodule?>&blog=<?php echo $R['id']?>&front=list');" />
		</td>
		</tr>
		<?php endwhile?>
	</table>

	<div class="pagebox01">
		<script type="text/javascript">getPageLink(10,<?php echo $p?>,<?php echo $TPG?>,'<?php echo $g['img_core']?>/page/default');</script>
	</div>

</div>


<style type="text/css">
#mjointbox {}
#mjointbox .title {border-bottom:#dfdfdf dashed 1px;padding:0 0 10px 0;margin:0 0 20px 0;}
#mjointbox .title .cat {width:120px;}
#mjointbox table {width:98%;}
#mjointbox table .name {width:160px;}
#mjointbox table .name span {font-size:11px;font-family:arial;color:#c0c0c0;padding:0 0 0 3px;}
#mjointbox table .aply {text-align:right;}
#mjointbox .pagebox01 {text-align:center;padding:15px 0 15px 0;margin:15px 0 0 0;border-top:#efefef solid 1px;}

</style>

<script type="text/javascript">
//<![CDATA[
function thisReset()
{
	var f = document.bbsSform;
	f.p.value = 1;
	f._keyw.value = '';
	f.submit();
}
//]]>
</script>