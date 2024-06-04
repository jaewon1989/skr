<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
    <?php include $g['dir_layout'].'/_includes/_import.head.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.control.php' ?>  
</head>
<body class="cb-body">
    <div class="snap-drawers">
        <div class="snap-drawer snap-drawer-left component" id="myDrawer">
             <?php include $g['dir_layout'].'/_includes/drawer-left.php' ?>
        </div>
    </div>
    <div class="snap-content" data-extension="drawer">
            <?php if($is_home):?>
              <?php include $g['dir_layout'].'/_includes/header_home.php' ?>
            <?php elseif($c || ($m=='chatbot' && $page!='view' && $page!='chat')):?>
              <?php include $g['dir_layout'].'/_includes/header_default.php' ?>
            <?php endif?>

              <?php if($is_home||$page=='view'||$page=='chat'):?>
                <?php include __KIMS_CONTENT__ ?>
              <?php else:?>
              <div class="content"> 
                 <div class="content-<?php echo $_CA[0]=='intro'?'nopadded':padded?>">
                     <?php include __KIMS_CONTENT__ ?>
                 </div>
              </div>   
              <?php endif?>
           </div>
    </div>
    <?php include $g['dir_layout'].'/_includes/footer.php' ?>
    <?php include $g['dir_layout'].'/_includes/modals.php' ?>
    <?php include $g['dir_layout'].'/_includes/_import.foot.php' ?> 
    <script>
     $(document).ready(function() {
             
       snapper = new Snap({
          element: $("#myDrawer")[0],
          maxPosition: 1,
          minPosition: -1,
          transitionSpeed: 0.5,
        })

       RC_initDrawer(); 
     })   
    </script>
 
</body>
</html>
