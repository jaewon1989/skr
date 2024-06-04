try {
  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  var recognition = new SpeechRecognition();

}
catch(e) {
  console.error(e);
  $('.no-browser-support').show();
  $('.app').hide();
}


var noteTextarea = $('#note-textarea');
var instructions = $('#recording-instructions');
var notesList = $('ul#notes');

var noteContent = '';

// Get all notes from previous sessions and display them.
var notes = getAllNotes();
renderNotes(notes);



/*-----------------------------
      Voice Recognition 
------------------------------*/

// If false, the recording will stop after a few seconds of silence.
// When true, the silence period is longer (about 15 seconds),
// allowing us to keep recording even when the user pauses. 
recognition.continuous = true;

// This block is called every time the Speech APi captures a line. 
recognition.onresult = function(event) {

  // event is a SpeechRecognitionEvent object.
  // It holds all the lines we have captured so far. 
  // We only need the current one.
  var current = event.resultIndex;

  // Get a transcript of what was said.
  var transcript = event.results[current][0].transcript;

  // Add the current transcript to the contents of our Note.
  // There is a weird bug on mobile, where everything is repeated twice.
  // There is no official solution so far so we have to handle an edge case.
  var mobileRepeatBug = (current == 1 && transcript == event.results[0][0].transcript);

  if(!mobileRepeatBug) {
    noteContent += transcript;
    noteTextarea.val(noteContent);
  }
};
recognition.onaudiostart = function(event) {
    console.log(event);
}
recognition.onstart = function() { 
  instructions.text('음성 인식이 활성화되었습니다. 마이크에 대고 말하십시오.');
}

recognition.onspeechend = function() {
  instructions.text('잠시 동안 조용히 있었기 때문에 음성 인식이 해제되었습니다.');
}

recognition.onerror = function(event) {
  if(event.error == 'no-speech') {
    instructions.text('음성이 감지되지 않았습니다. 다시 시도하십시오..');  
  };
}

/*-----------------------------
      Check RTC 
------------------------------*/

function checkMic() {
    var mic =0 ;
    if(DetectRTC.audioInputDevices.length) {
        mic++;
    }

    return mic;     
}




/*-----------------------------
      App buttons and input 
------------------------------*/

$('#start-record-btn').on('click', function(e) {
    if (noteContent.length) {
        noteContent += ' ';
    }
    recognition.start();
    // var has_mic = checkMic();
    // if(has_mic) recognition.start();
    // else instructions.text('마이크가 인식되지 않습니다.');
});


$('#pause-record-btn').on('click', function(e) {
  recognition.stop();
  instructions.text('음성 인식이 일시 중지되었습니다.');
});

// tts 추가 
$('#start-tts').on('click', function(e) {
    var content = noteTextarea.val();
    readOutLoud(content);
   
});
// rtt 추가
$('#start-rtt').on('click', function(e) {
    if (noteContent.length) {
        noteContent += ' ';
    }
    recognition.start();
   
});  

// var recorder = document.getElementById('recorder');
// var player = document.getElementById('player');

// recorder.addEventListener('change', function(e) {
//     var file = e.target.files[0]; 
//     // Do something with the audio file.
//     player.src =  URL.createObjectURL(file);
    
//     var handleSuccess = function(stream) {
//         var context = new AudioContext();
//         var input = context.createMediaStreamSource(stream)
//         var processor = context.createScriptProcessor(1024,1,1);

//         input.connect(processor);
//         processor.connect(context.destination);

//         processor.onaudioprocess = function(e){
//            // Do something with the data, i.e Convert this to WAV
//             //console.log(e);
//             var transcript = e.inputBuffer;

//             // noteContent += transcript;
//             // noteTextarea.val(noteContent);
//         };
//     };

//     navigator.mediaDevices.getUserMedia({ audio: true, video: false }).then(handleSuccess);
    
//     setTimeout(function(){
//         player.play();
//     },20);
    

// });


// Sync the text inside the text area with the noteContent variable.
noteTextarea.on('input', function() {
  noteContent = $(this).val();
})

$('#save-note-btn').on('click', function(e) {
  recognition.stop();

  if(!noteContent.length) {
    instructions.text('Could not save empty note. Please add a message to your note.');
  }
  else {
    // Save note to localStorage.
    // The key is the dateTime with seconds, the value is the content of the note.
    saveNote(new Date().toLocaleString(), noteContent);

    // Reset variables and update UI.
    noteContent = '';
    renderNotes(getAllNotes());
    noteTextarea.val('');
    instructions.text('Note saved successfully.');
  }
      
})


notesList.on('click', function(e) {
  e.preventDefault();
  var target = $(e.target);

  // Listen to the selected note.
  if(target.hasClass('listen-note')) {
    var content = target.closest('.note').find('.content').text();
    readOutLoud(content);
  }

  // Delete note.
  if(target.hasClass('delete-note')) {
    var dateTime = target.siblings('.date').text();  
    deleteNote(dateTime);
    target.closest('.note').remove();
  }
});



/*-----------------------------
      Speech Synthesis 
------------------------------*/
function readOutLoud(message) {
    var chunkLength = 80;
    var pattRegex = new RegExp('^[\\s\\S]{' + Math.floor(chunkLength / 2) + ',' + chunkLength + '}[.!?,]{1}|^[\\s\\S]{1,' + chunkLength + '}$|^[\\s\\S]{1,' + chunkLength + '} ');
    var arr = [];
    var txt = message;
    var u = null;

    while (txt.length > 0) {
        arr.push(txt.match(pattRegex)[0]);
        txt = txt.substring(arr[arr.length - 1].length);
    }
    $.each(arr, function () {
        console.log(this);
        var u = new SpeechSynthesisUtterance(this.trim());
        window.speechSynthesis.speak(u);
    });
    // //speechUtteranceChunker(message);
    // var utterance = new SpeechSynthesisUtterance(message);

    // //modify it as you normally would
    // var voiceArr = speechSynthesis.getVoices();
    // utterance.voice = voiceArr[2];

    // //pass it into the chunking function to have it played out.
    // //you can set the max number of characters by changing the chunkLength property below.
    // //a callback function can also be added that will fire once the entire text has been spoken.
    // speechUtteranceChunker(utterance, {
    //     chunkLength: 15000
    // }, function () {
    //     //some code to execute when done
    //     console.log('done');
    // });
  //   var speech = new SpeechSynthesisUtterance();

  // // Set the text and voice attributes.
    // speech.text = message;
    // speech.volume = 1;
    // speech.rate = 1;
    // speech.pitch = 1;
    // window.speechSynthesis.speak(speech);
}





/*-----------------------------
      Helper Functions 
------------------------------*/

function renderNotes(notes) {
  var html = '';
  if(notes.length) {
    notes.forEach(function(note) {
      html+= `<li class="note">
        <p class="header">
          <span class="date">${note.date}</span>
          <a href="#" class="listen-note" title="Listen to Note">Listen to Note</a>
          <a href="#" class="delete-note" title="Delete">Delete</a>
        </p>
        <p class="content">${note.content}</p>
      </li>`;    
    });
  }
  else {
    html = '<li><p class="content">You don\'t have any notes yet.</p></li>';
  }
  notesList.html(html);
}


function saveNote(dateTime, content) {
  localStorage.setItem('note-' + dateTime, content);
}


function getAllNotes() {
  var notes = [];
  var key;
  for (var i = 0; i < localStorage.length; i++) {
    key = localStorage.key(i);

    if(key.substring(0,5) == 'note-') {
      notes.push({
        date: key.replace('note-',''),
        content: localStorage.getItem(localStorage.key(i))
      });
    } 
  }
  return notes;
}


function deleteNote(dateTime) {
  localStorage.removeItem('note-' + dateTime); 
}
