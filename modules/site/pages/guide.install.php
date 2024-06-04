<div class="rb-guide-wrapper">
	<div class="rb-guide-wrapper-inner">
		<div class="container">
			<h1>
				<i class="kf kf-bi-03 fa-5x text-muted" title="KimsQ is Kind" data-tooltip="tooltip"></i>
				<br>
				<br>
				<?php echo _LANG('s1001','site')?>
			</h1>
			<p class="text-muted">
				<?php echo sprintf(_LANG('s1002','site'),$my['name'])?>
				<br class="hidden-xs">
				<?php echo _LANG('s1003','site')?>
				<br class="hidden-xs">
			</p>
			<p>
				<br>
				<br>
				<a class="btn btn-primary" href="./?r=<?php echo $r?>&amp;panel=Y&amp;_admpnl_=<?php echo urlencode('./?r='.$r.'&m=admin&module=site')?>">
					<i class="glyphicon glyphicon-ok"></i>
					<?php echo _LANG('s1004','site')?>
				</a>
			</p>
		</div>
	</div>
</div>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-55876126-3', 'auto');
ga('send', 'pageview');
</script>
