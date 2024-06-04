<?php
$M1=getDbArray($table['s_menu'],"parent=53 and hidden=0",'uid,name,id,is_child','gid','asc','',1);
$_page = str_replace('adm/','',$page);
?>
<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <ul class="nav" id="side-menu">
            <li class="no-menu">
                 <a href="<?php echo $g['s'].'/suAdm/config'?>">
                    <?php echo $getListBot['bot_avatar'];?>
                    <span class="bot-config"><i class="fa fa-cog" aria-hidden="true"></i></span>
                </a>

            </li>
            <?php while($_M1=db_fetch_array($M1)):?>
            <li class="sideMenu">
                <a href="<?php echo $g['s'].'/suAdm/'.$_M1['id']?>" class="waves-effect<?php if($_M1['is_child']):?> has-arrow<?php endif?>">       
                    <i class="fa <?php echo $menu_icon[str_replace('a_','',$_M1['id'])]?> fa-fw" aria-hidden="true"></i>
                    <span><?php echo $_M1['name']?></span>

                </a>
                <?php if($_M1['is_child']):?>
                <?php $M2=getDbArray($table['s_menu'],"parent='".$_M1['uid']."' and hidden=0",'uid,name,id,is_child','gid','asc','',1);?>
                <ul class="slimScrollDiv submenu">
                    <?php while($_M2=db_fetch_array($M2)):?>
                    <li <?php if($_page ==$_M2['id']):?>class="active"<?php endif?>>
                        <?php if($_M2['id']=='intent'||$_M2['id']=='entity'||$_M2['id']=='context'||$_M2['id']=='node'):?>
                         <a href="##" class="waves-effect">
                            <i class="fa <?php echo $menu_icon[str_replace('a_','',$_M2['id'])]?> fa-fw" aria-hidden="true"></i>       
                              <span><?php echo $_M2['name']?></span>       
                         </a>
                        <?php else:?>
                        <a href="<?php echo $g['s'].'/suAdm/'.$_M2['id']?>" class="waves-effect">
                            <?php if($_M2['id']=='stt'||$_M2['id']=='tts'||$_M2['id']=='rtt'):?>
                               <i class="fa fa-refresh fa-fw" aria-hidden="true"></i>
                            <?php else:?>
                              <i class="fa <?php echo $menu_icon[str_replace('a_','',$_M2['id'])]?> fa-fw" aria-hidden="true"></i>
                            <?php endif?>

                            <span><?php echo $_M2['name']?></span>       
                         </a>
                        <?php endif?>

                    </li>
                    <?php endwhile?>
                </ul>
                <?php endif?>
            </li>
            <?php endwhile?>
        </ul>
    </div>
</div>
<!-- Left navbar-header end -->
