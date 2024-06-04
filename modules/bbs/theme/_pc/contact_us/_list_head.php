 <div class="bbs-header clearfix">
    <h2 class="float-xs-left"><i class="fa fa-circle-o text-primary" aria-hidden="true"></i> <?php echo $B['name']?></h2>
    <?php if($Ltype=='list'):?>
    <form class="float-xs-right mr-1" name="bbssearchf" action="<?php echo $g['s']?>">
        <input type="hidden" name="r" value="<?php echo $r?>">
        <input type="hidden" name="c" value="<?php echo $c?>">
        <input type="hidden" name="m" value="<?php echo $m?>">
        <input type="hidden" name="bid" value="<?php echo $bid?>">
        <input type="hidden" name="mod" value="<?php echo $mod?>">
        <input type="hidden" name="cat" value="<?php echo $cat?>">
        <input type="hidden" name="sort" value="<?php echo $sort?>">
        <input type="hidden" name="orderby" value="<?php echo $orderby?>">
        <input type="hidden" name="recnum" value="<?php echo $recnum?>">
        <input type="hidden" name="type" value="<?php echo $type?>">
        <input type="hidden" name="iframe" value="<?php echo $iframe?>">
        <input type="hidden" name="skin" value="<?php echo $skin?>">
        <input type="hidden" name="where" value="<?php echo $where?>">
        <input type="hidden" name="keyword" value="<?php echo $keyword?>">

        <select class="form-control custom-select" name="recnum" onchange="this.form.submit();">
           <option value="10"<?php if($recnum==10):?> selected<?php endif?>>10개씩 보기</option>
           <option value="20"<?php if($recnum==20):?> selected<?php endif?>>20개씩 보기</option>
        </select>
    </form>
    <?php endif?>
</div>