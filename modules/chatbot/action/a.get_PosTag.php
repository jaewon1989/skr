<script type="text/javascript">
$(document).ready(function(){
    // Note the \ at the end of the first line
	var _text = "<?php echo $_POST['_text']?>";
	var words = new Lexer().lex(_text);
	var taggedWords = new POSTagger().tag(words);
	var pos = {};
	for (i in taggedWords) {
	   var taggedWord = taggedWords[i];
	   var word = taggedWord[0];
	   var tag = taggedWord[1];
	   // Note the use of document.writeln instead of print
	   pos[word] = tag;
	   result = JSON.stringify(pos);
	}
	$('body').find('#tagged_text').text(result);
	});
</script>

<?php
exit;
?>


