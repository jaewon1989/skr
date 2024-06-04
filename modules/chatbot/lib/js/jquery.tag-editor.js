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
                vendor: null,
                bot: null,
                module: null,
                randomColor: true, //false to off random color 
                tagicon: true, //false to turn off tags icon
                tagURL: "#", //url that will be assigned to new tags when user enter a tag name
        }, options);
  
        return this.each(function() {           
             var $target,tagsManager,newTag;
             var module = setting.module;
             var tagFA_UL = '[data-role="tagFA-UL"]';
           
            tagsManager = []; //an array to store new tag name and URL
            newTag = -1;
            $target = this;

            $($target).addClass(setting.jTagMode);

            
            //function to make tags colorful
            var coloredTags = function(){
                var totalTags = $(".cloud-tags").find("li").length; //to find total cloud tags
                var mct = $(".cloud-tags").find("li");  //select all tags links to make them colorful

                /*Array of Colors */
                var tagColor = ["#093145", "#107896","#829356","#bca136","#c2571A","#9a2617"];
                var tag = 0; var color = 0; //assign colors to tags with loop, unlimited number of tags can be added
                do {
                     
                    //if(color > 21) {color = 0;} //Start again array index if it reaches at last
                    if (setting.randomColor == true){
                        var $rc = Math.floor(Math.random() * (tagColor.length-1));
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

            var getTagFaTpl = function(data){
                var tagName = data.tagName;
                var uid = data.uid;
                var result ='';

                result+='<li>';
                  result+='<span><a data-role="tagFA-item" data-tag="'+tagName+'">'+tagName+'</a></span>';
                  result+='<span class="del-faTag" data-role="del-tagFA" data-uid="'+uid+'">&times;</span>';
                result+='</li>'; 
              
                return result;
            }

            var linkServerData = function(data){
                var linkType =data.linkType;
                var _data = {};
                _data['vendor'] = setting.vendor; 
                _data['bot'] = setting.bot;
                
                if(linkType =='add-tagFA') _data['tagName'] = tagName = data.tagName;
                else if(linkType =='del-tagFA') _data['uid'] = data.uid;

               
                //console.log(_data);
                $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_VendorAction',{
                   linkType: linkType,
                   data: _data
                },function(response) {
                    var result = $.parseJSON(response); 
                    if(linkType =='add-tagFA'){
                        if(!result.error){
                            var uid = result['uid'];
                            var tagLi_data = {tagName: tagName, uid: uid};
                            var tagLi = getTagFaTpl(tagLi_data);
                            $(tagFA_UL).prepend(tagLi);    
                        }else{
                            alert(result.error_msg);
                        }
                        
                    }else if(linkType =='get-tagFA'){
                        var listArray = result.listArray;
                        $.each(listArray,function(i,item){
                             var uid = item.uid;
                             var tagName = item.tagName;
                             var tagLi_data = {tagName: tagName, uid: uid};
                             var tagLi = getTagFaTpl(tagLi_data);
                             $(tagFA_UL).append(tagLi); 
                             
                        });

                    }
                    
               
                }); 
                 
            } 
            
            // 태그 리스트 초기화 
            var initTagFA = function(){
                var data = {linkType: 'get-tagFA'};
                linkServerData(data); 
            }
            
            $(tagFA_UL).on('click','[data-role="del-tagFA"]',function(e){
                 var target = e.currentTarget;
                 var uid = $(target).attr("data-uid");
                 var data = {linkType: "del-tagFA", uid: uid};
                 linkServerData(data);
                 setTimeout(function(){
                     $(target).parent().remove();
                 },100);

            });

            $('[data-role="ta-addFA"]').on('keypress', function(e){
                 var ta = $(this); 
                 var tagName = $(ta).val();
                 var tagComplete = tagName.search(",");
                 var tagArr = tagName.split(",");
                 var tagLi =''; 

                if(e.which==13){
                    for(var i=0; i<tagArr.length;i++){
                        newTag = i;
                        var newTagName = $.trim(tagArr[i]).slice(0, 500);
                        //var newTagName = tagArr[i];
                        if(newTagName.length>0){
                             var $thisTag = {
                                 'tagName' : newTagName,
                                 'tagURL' :  setting.tagURL+newTagName.toLowerCase(),
                            };
                             
                            tagsManager.push($thisTag);
                            var data = {linkType: "add-tagFA", tagName: newTagName};
                            setTimeout(function(){
                                linkServerData(data);    
                            },200);
                            
                        }
                       
                    }// for
                    setTimeout(function(e){
                         $(ta).val(''); 
                    },100)
                    
                }                    
                    
            }); //end tags input function


                //add font awesome icon
            if (setting.tagicon == true){
                var eachTag = $($target).find("a");
                var $ti = document.createElement("i");
                $($ti).addClass("fa fa-tags").prependTo(eachTag);

            }
           
            //coloredTags();
            
            initTagFA(); 

        });
    };

 })(jQuery);