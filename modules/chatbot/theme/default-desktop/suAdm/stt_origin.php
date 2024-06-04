


<div class="container-fluid table-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><?php echo $pageTitle?></h4>
        </div>
    </div>
    <!-- /.row -->
    <div class="table-container">
        <div class="container">

            <h1>Voice Controlled Notes App</h1>
            <p class="page-description">A tiny app that allows you to take notes by recording your voice</p>
            <p><a class="tz-link" href="https://tutorialzine.com/2017/08/converting-from-speech-to-text-with-javascript">Read the full article on Tutorialzine »</a></p>

            <h3 class="no-browser-support">Sorry, Your Browser Doesn't Support the Web Speech API. Try Opening This Demo In Google Chrome.</h3>

            <div class="app"> 
                <h3>Add New Note</h3>
                <div class="input-single">
                    <textarea id="note-textarea" placeholder="Create a new note by typing or using voice recognition." rows="6"></textarea>
                </div>         
                <button id="start-record-btn" title="Start Recording">Start Recognition</button>
                <button id="pause-record-btn" title="Pause Recording">Pause Recognition</button>
                <button id="save-note-btn" title="Save Note">Save Note</button>   
                <p id="recording-instructions">Press the <strong>Start Recognition</strong> button and allow access.</p>
                
                <h3>My Notes</h3>
                <ul id="notes">
                    <li>
                        <p class="no-notes">You don't have any notes.</p>
                    </li>
                </ul>

            </div>

        </div>
        
        <script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/voice.recog.js'?>"></script>
   
    </div>
</div>

