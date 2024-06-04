<nav class="mt-2 text-xs-center">
    <div class="row">
      <div class="col-xs-12 text-center">
           <nav class="rb-pagination">
                <ul class="pagination pagination-md">
                    <?php echo getPageLink($d['theme']['pagenum'],$p,$TPG,'')?>
                </ul>
            </nav>
      </div>
    </div>
    <?php if($Ltype=='list'):?>
    <form class="form-inline" name="bbssearchf" action="<?php echo $g['s']?>">
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
        <div class="form-group">
            <label class="sr-only" for="">검색조건</label>
            <select class="form-control custom-select" name="where">
              <option value="subject|tag"<?php if($where=='subject|tag'):?> selected="selected"<?php endif?>>제목+태그</option>
                <option value="content"<?php if($where=='content'):?> selected="selected"<?php endif?>>본문</option>
                <option value="name"<?php if($where=='name'):?> selected="selected"<?php endif?>>이름</option>
                <option value="nic"<?php if($where=='nic'):?> selected="selected"<?php endif?>>닉네임</option>
                <option value="id"<?php if($where=='id'):?> selected="selected"<?php endif?>>아이디</option>
                <option value="term"<?php if($where=='term'):?> selected="selected"<?php endif?>>등록일</option>
                <option>전체</option>
            </select>
        </div>
        <div class="form-group">
            <label class="sr-only" for="">검색어입력</label>
            <input type="text" name="keyword" class="form-control" id="" placeholder="" value="<?php echo $_keyword?>">
        </div>
        <button type="submit" class="btn btn-secondary">검색</button>
    </form>
    <?php endif?>
</nav>