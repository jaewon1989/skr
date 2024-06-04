<?php include_once $g['path_module'].'social/var/var.php'?>
<?php $_snsuse1=explode(',',$g['mysns'][0])?>
<?php $_snsuse2=explode(',',$g['mysns'][1])?>
<?php $_snsuse3=explode(',',$g['mysns'][2])?>
<?php $_snsuse4=explode(',',$g['mysns'][3])?>

<input type="hidden" name="snsCallBack" value="social/lang.korean/action/a.snssend.php" />

<div class="ment">
<img src="<?php echo $g['img_core']?>/_public/ico_notice.gif" alt="" />SNS등록
<a href="<?php echo $g['s']?>/?r=<?php echo $r?>&amp;m=aframe&amp;mod=myhub&amp;page=config&amp;type=account" target="_blank" onclick="return isLogin();">[설정]</a>
</div>
<ul>
<?php if($d['social']['use_t']):?>
<li><label><input type="checkbox" name="sns_t" id="snsInp_t" value="1"<?php if($_snsuse1[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse1[0]?>',0);" />
<img id="snsImg_t" src="<?php echo $g['img_core']?>/_public/sns_t0.gif" alt="twitter" title="트위터" /> twitter</label></li>
<?php endif?>

<?php if($d['social']['use_f']):?>
<li><label><input type="checkbox" name="sns_f" id="snsInp_f" value="1"<?php if($_snsuse2[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse2[0]?>',1);" />
<img id="snsImg_f" src="<?php echo $g['img_core']?>/_public/sns_f0.gif" alt="facebook" title="페이스북" /> facebook</label></li>
<?php endif?>

<?php if($d['social']['use_m']):?>
<li><label><input type="checkbox" name="sns_m" id="snsInp_m" value="1"<?php if($_snsuse3[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse3[0]?>',2);" />
<img id="snsImg_m" src="<?php echo $g['img_core']?>/_public/sns_m0.gif" alt="me2day" title="미투데이" /> me2day</label></li>
<?php endif?>

<?php if($d['social']['use_y']):?>
<li><label><input type="checkbox" name="sns_y" id="snsInp_y" value="1"<?php if($_snsuse4[0]=='on'):?> checked="checked"<?php endif?> onclick="snsCheck1(this,'<?php echo $_snsuse4[0]?>',3);" />
<img id="snsImg_y" src="<?php echo $g['img_core']?>/_public/sns_y0.gif" alt="yozm" title="요즘" /> yozm</label></li>
<?php endif?>

</ul>

<script type="text/javascript">
//<![CDATA[
function snsCheck1(obj,token,n)
{
	if (token == '')
	{
		var result = getHttprequest(rooturl+'/?r='+raccount+'&m=aframe&mod=myhub/logcheck&n='+n);
		if(getAjaxFilterString(result,'RESULT')=='')
		{
			alert('소셜계정이 설정되지 않았습니다.  ');
			obj.checked = false;
		}
	}
}
//]]>
</script>
