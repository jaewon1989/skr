<footer class="bar bar-footer bar-light bg-faded p-x-0">
    <section id="footer">
        <div class="dm-actual-body">
            <div class="dm-takeup-space">
                <div class="dm-footer-item">
                    <span class="dm-icon dm-icon-homeblack" data-href="/play"></span>
                </div>
                <div class="dm-footer-item">
                    <span class="dm-icon dm-icon-listgray" data-toggle="drawer" data-target="#myDrawer"></span>
                </div>
                <div class="dm-footer-item">
                    <div class="dm-lens-box">
                        <span class="dm-icon dm-icon-lens"<?php if(!$my['uid']) echo ' data-status="nologin"'; else echo ' data-href=""';?>></span>
                    </div>

                </div>
                <div class="dm-footer-item">
                    <span class="dm-icon dm-icon-persongray"<?php if(!$my['uid']) echo ' data-status="nologin"'; else echo ' data-href=""';?>></span>
                </div>
                <div class="dm-footer-item">
                    <span class="dm-img dm-img-market" data-href="/"></span>
                </div>
            </div>
        </div>
    </section>
</footer>