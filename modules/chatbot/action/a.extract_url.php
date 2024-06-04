<?php
if(isset($_POST["url"]))
{
 $get_url = $_POST["url"]; 
  
  //Include PHP HTML DOM parser (requires PHP 5 +)
  include_once $g['path_module'].'feed/lib/simple_html_dom.inc.php';
  
  //get URL content
  $get_content = file_get_html($get_url); 
  
  //Get Page Title 
  foreach($get_content->find('title') as $element) 
  {
   $page_title = $element->plaintext;
  }
  
  //Get Body Text
  foreach($get_content->find('body') as $element) 
  {
   $page_body = trim($element->plaintext);
   $pos=strpos($page_body, ' ', 200); //Find the numeric position to substract
   $page_body = substr($page_body,0,$pos ); //shorten text to 200 chars
  }
 
  $image_urls = array();
  
  //get all images URLs in the content
  foreach($get_content->find('img') as $element) 
  {
    /* check image URL is valid and name isn't blank.gif/blank.png etc..
    you can also use other methods to check if image really exist */
    if(!preg_match('/blank.(.*)/i', $element->src) && filter_var($element->src, FILTER_VALIDATE_URL))
    {
     $image_urls[] =  $element->src;
    }
  }

  //prepare for JSON 
  $output = array('title'=>$page_title, 'images'=>$image_urls, 'description'=> $page_body);
  echo json_encode($output); //output JSON data
  exit;
}

// $url = trim($_POST['url']);
// $url = check_url($url);

// function check_url($value)
// {
//   $value = trim($value);
//   if (get_magic_quotes_gpc()) 
//   {
//     $value = stripslashes($value);
//   }
//   $value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
//   $value = strip_tags($value);
//   $value = htmlspecialchars($value);
//   return $value;
// } 

// function file_get_contents_curl($url)
// {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//     $data = curl_exec($ch);
//   $info = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  
//   //checking mime types
//   if(strstr($info,'text/html')) {
//     curl_close($ch);
//       return $data;
//   } else {
//     return false;
//   }
// }

// //fetching url data via curl
// $html = file_get_contents_curl($url);

// if($html) {
// //parsing begins here:
// $doc = new DOMDocument();
// @$doc->loadHTML($html);
// $nodes = $doc->getElementsByTagName('title');

// //get and display what you need:
// $title = $nodes->item(0)->nodeValue;
// $metas = $doc->getElementsByTagName('meta');
// $image_urls=array();
// for ($i = 0; $i < $metas->length; $i++)
// {
//     $meta = $metas->item($i);
//     if($meta->getAttribute('name') == 'og:description'){
//       $description = $meta->getAttribute('content');
//     }
//     if($meta->getAttribute('name') == 'og:image'){
//       $image_urls['og'] = $meta->getAttribute('content');
//     }
//     if($meta->getAttribute('name') == 'twitter:image'){
//       $image_urls['twitter'] = $meta->getAttribute('content');
//     }        
// }
// $result=array('title'=>$title,'description'=>$description,'images'=>$image_urls);
// echo json_encode($result,true);
// exit;
// }
?>
