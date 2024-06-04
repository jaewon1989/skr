/*  Plugin: prettyTag (Auto Colored Tags)
 *   Frameworks: jQuery 3.3.1 
 *   Author: Asif Mughal
 *   GitHub: https://github.com/CodeHimBlog
 *   URL: https://www.codehim.com
 *   License: MIT License
 *   Copyright (c) 2018 - Asif Mughal
 */
/* File: jquery.prettytag.js */

(function($){
    $.fn.prettyTag = function(options) {
        var setting = $.extend({
                randomColor: true, //false to off random color 
                tagicon: true, //false to turn off tags icon
                tagURL: "#", //url that will be assigned to new tags when user enter a tag name
        }, options);
  
        return this.each(function() {           
             var $target,tagsManager,newTag;


            tagsManager = []; //an array to store new tag name and URL
            newTag = -1;
            $target = this;

            $($target).addClass(setting.jTagMode);

            //function to make tags colorful
            var coloredTags = function(){
                var totalTags = $(".cloud-tags").find("li").length; //to find total cloud tags
                var mct = $(".cloud-tags").find("a");  //select all tags links to make them colorful

                /*Array of Colors */
                var tagColor = ["#093145", "#107896","#829356","#bca136","#c2571A","#9a2617"];
                var tag = 0; var color = 0; //assign colors to tags with loop, unlimited number of tags can be added
                do {
                    if(color > 21) {color = 0;} //Start again array index if it reaches at last
                    if (setting.randomColor == true){
                        var $rc = Math.floor(Math.random() *22);
                        $(mct).eq(tag).css({
                             //tags random color 
                            'background' : tagColor[$rc] 
                        });     
                    
                    }else {
                        $(mct).eq(tag).css({
                           //tags color in a sequence
                           'background' : tagColor[color] 
                        });
                    }

                    tag++;
                    color++;
                } while( tag <= totalTags)

            }

            // close tag 
            var closeTag = function(){

                var closeAbleTag = $(".cloud-tags").find("span");

                $(closeAbleTag).html("&times;");
                
                $(closeAbleTag).click(function(){

                $(this).parent().remove();

                });
            }


            $(".codehim-input-tags").on('keypress', function(e){
              
                //creating new cloud tags 
                var tList = document.createElement("li");
                //to set link for new tags 
                var tagLink = document.createElement("a");
                var tagName = $(this).val();
                var tagComplete = tagName.search(",");

                if ( tagComplete > 0){

                    
                    newTag += 1;
                    var newTagName = tagName.slice(0, tagComplete).concat(" ");
                    var $thisTag = {
                             'tagName' : newTagName,
                             'tagURL' :  setting.tagURL+newTagName.toLowerCase(),
                    };
                         
                    tagsManager.push($thisTag);
                    $(tagLink)
                       .attr("href", tagsManager[newTag].tagURL)
                       .html(tagsManager[newTag].tagName)
                       .appendTo(tList);

                    $(tList).append("<span></span>");
                    $(".tags").append(tList);

                    coloredTags();
                    closeTag();
                    $(this).val('');
                    tagComplete = null;

                }     
            }); //end tags input function


                //add font awesome icon
            if (setting.tagicon == true){
                var eachTag = $($target).find("a");
                var $ti = document.createElement("i");
                $($ti).addClass("fa fa-tags").prependTo(eachTag);

            }
           
            coloredTags();
     

        });
    };

 })(jQuery);