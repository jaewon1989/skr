/* =============================================================================================
 * Snap.js
 *
 * Copyright 2013, Jacob Kelley - http://jakiestfu.com/
 * Released under the MIT Licence
 * http://opensource.org/licenses/MIT
 * ==============================================================================================
 * Github:  http://github.com/jakiestfu/Snap.js/
 * Version: 1.9.2
 *===============================================================================================*/

 (function(c,b){var a=a||function(k){var f={element:null,dragger:null,disable:"none",addBodyClasses:true,hyperextensible:true,resistance:0.5,flickThreshold:50,transitionSpeed:0.3,easing:"ease",maxPosition:266,minPosition:-266,tapToClose:true,touchToDrag:true,slideIntent:40,minDragDistance:5},e={simpleStates:{opening:null,towards:null,hyperExtending:null,halfway:null,flick:null,translation:{absolute:0,relative:0,sinceDirectionChange:0,percentage:0}}},h={},d={hasTouch:(b.ontouchstart===null),eventType:function(m){var l={down:(d.hasTouch?"touchstart":"mousedown"),move:(d.hasTouch?"touchmove":"mousemove"),up:(d.hasTouch?"touchend":"mouseup"),out:(d.hasTouch?"touchcancel":"mouseout")};return l[m]},page:function(l,m){return(d.hasTouch&&m.touches.length&&m.touches[0])?m.touches[0]["page"+l]:m["page"+l]},klass:{has:function(m,l){return(m.className).indexOf(l)!==-1},add:function(m,l){if(!d.klass.has(m,l)&&f.addBodyClasses){m.className+=" "+l}},remove:function(m,l){if(f.addBodyClasses){m.className=(m.className).replace(l,"").replace(/^\s+|\s+$/g,"")}}},dispatchEvent:function(l){if(typeof h[l]==="function"){return h[l].call()}},vendor:function(){var m=b.createElement("div"),n="webkit Moz O ms".split(" "),l;for(l in n){if(typeof m.style[n[l]+"Transition"]!=="undefined"){return n[l]}}},transitionCallback:function(){return(e.vendor==="Moz"||e.vendor==="ms")?"transitionend":e.vendor+"TransitionEnd"},canTransform:function(){return typeof f.element.style[e.vendor+"Transform"]!=="undefined"},deepExtend:function(l,n){var m;for(m in n){if(n[m]&&n[m].constructor&&n[m].constructor===Object){l[m]=l[m]||{};d.deepExtend(l[m],n[m])}else{l[m]=n[m]}}return l},angleOfDrag:function(l,o){var n,m;m=Math.atan2(-(e.startDragY-o),(e.startDragX-l));if(m<0){m+=2*Math.PI}n=Math.floor(m*(180/Math.PI)-180);if(n<0&&n>-180){n=360-Math.abs(n)}return Math.abs(n)},events:{addEvent:function g(m,l,n){if(m.addEventListener){return m.addEventListener(l,n,false)}else{if(m.attachEvent){return m.attachEvent("on"+l,n)}}},removeEvent:function g(m,l,n){if(m.addEventListener){return m.removeEventListener(l,n,false)}else{if(m.attachEvent){return m.detachEvent("on"+l,n)}}},prevent:function(l){if(l.preventDefault){l.preventDefault()}else{l.returnValue=false}}},parentUntil:function(n,l){var m=typeof l==="string";while(n.parentNode){if(m&&n.getAttribute&&n.getAttribute(l)){return n}else{if(!m&&n===l){return n}}n=n.parentNode}return null}},i={translate:{get:{matrix:function(n){if(!d.canTransform()){return parseInt(f.element.style.left,10)}else{var m=c.getComputedStyle(f.element)[e.vendor+"Transform"].match(/\((.*)\)/),l=8;if(m){m=m[1].split(",");if(m.length===16){n+=l}return parseInt(m[n],10)}return 0}}},easeCallback:function(){f.element.style[e.vendor+"Transition"]="";e.translation=i.translate.get.matrix(4);e.easing=false;clearInterval(e.animatingInterval);if(e.easingTo===0){d.klass.remove(b.body,"snapjs-right");d.klass.remove(b.body,"snapjs-left")}d.dispatchEvent("animated");d.events.removeEvent(f.element,d.transitionCallback(),i.translate.easeCallback)},easeTo:function(l){if(!d.canTransform()){e.translation=l;i.translate.x(l)}else{e.easing=true;e.easingTo=l;f.element.style[e.vendor+"Transition"]="all "+f.transitionSpeed+"s "+f.easing;e.animatingInterval=setInterval(function(){d.dispatchEvent("animating")},1);d.events.addEvent(f.element,d.transitionCallback(),i.translate.easeCallback);i.translate.x(l)}if(l===0){f.element.style[e.vendor+"Transform"]=""}},x:function(m){if((f.disable==="left"&&m>0)||(f.disable==="right"&&m<0)){return}if(!f.hyperextensible){if(m===f.maxPosition||m>f.maxPosition){m=f.maxPosition}else{if(m===f.minPosition||m<f.minPosition){m=f.minPosition}}}m=parseInt(m,10);if(isNaN(m)){m=0}if(d.canTransform()){var l="translate3d("+m+"px, 0,0)";f.element.style[e.vendor+"Transform"]=l}else{f.element.style.width=(c.innerWidth||b.documentElement.clientWidth)+"px";f.element.style.left=m+"px";f.element.style.right=""}}},drag:{listen:function(){e.translation=0;e.easing=false;d.events.addEvent(f.element,d.eventType("down"),i.drag.startDrag);d.events.addEvent(f.element,d.eventType("move"),i.drag.dragging);d.events.addEvent(f.element,d.eventType("up"),i.drag.endDrag)},stopListening:function(){d.events.removeEvent(f.element,d.eventType("down"),i.drag.startDrag);d.events.removeEvent(f.element,d.eventType("move"),i.drag.dragging);d.events.removeEvent(f.element,d.eventType("up"),i.drag.endDrag)},startDrag:function(n){var m=n.target?n.target:n.srcElement,l=d.parentUntil(m,"data-snap-ignore");if(l){d.dispatchEvent("ignore");return}if(f.dragger){var o=d.parentUntil(m,f.dragger);if(!o&&(e.translation!==f.minPosition&&e.translation!==f.maxPosition)){return}}d.dispatchEvent("start");f.element.style[e.vendor+"Transition"]="";e.isDragging=true;e.hasIntent=null;e.intentChecked=false;e.startDragX=d.page("X",n);e.startDragY=d.page("Y",n);e.dragWatchers={current:0,last:0,hold:0,state:""};e.simpleStates={opening:null,towards:null,hyperExtending:null,halfway:null,flick:null,translation:{absolute:0,relative:0,sinceDirectionChange:0,percentage:0}}},dragging:function(s){if(e.isDragging&&f.touchToDrag){var v=d.page("X",s),u=d.page("Y",s),t=e.translation,o=i.translate.get.matrix(4),n=v-e.startDragX,p=o>0,q=n,w;if((e.intentChecked&&!e.hasIntent)){return}if(f.addBodyClasses){if((o)>0){d.klass.add(b.body,"snapjs-left");d.klass.remove(b.body,"snapjs-right")}else{if((o)<0){d.klass.add(b.body,"snapjs-right");d.klass.remove(b.body,"snapjs-left")}}}if(e.hasIntent===false||e.hasIntent===null){var m=d.angleOfDrag(v,u),l=(m>=0&&m<=f.slideIntent)||(m<=360&&m>(360-f.slideIntent)),r=(m>=180&&m<=(180+f.slideIntent))||(m<=180&&m>=(180-f.slideIntent));if(!r&&!l){e.hasIntent=false}else{e.hasIntent=true}e.intentChecked=true}if((f.minDragDistance>=Math.abs(v-e.startDragX))||(e.hasIntent===false)){return}d.events.prevent(s);d.dispatchEvent("drag");e.dragWatchers.current=v;if(e.dragWatchers.last>v){if(e.dragWatchers.state!=="left"){e.dragWatchers.state="left";e.dragWatchers.hold=v}e.dragWatchers.last=v}else{if(e.dragWatchers.last<v){if(e.dragWatchers.state!=="right"){e.dragWatchers.state="right";e.dragWatchers.hold=v}e.dragWatchers.last=v}}if(p){if(f.maxPosition<o){w=(o-f.maxPosition)*f.resistance;q=n-w}e.simpleStates={opening:"left",towards:e.dragWatchers.state,hyperExtending:f.maxPosition<o,halfway:o>(f.maxPosition/2),flick:Math.abs(e.dragWatchers.current-e.dragWatchers.hold)>f.flickThreshold,translation:{absolute:o,relative:n,sinceDirectionChange:(e.dragWatchers.current-e.dragWatchers.hold),percentage:(o/f.maxPosition)*100}}}else{if(f.minPosition>o){w=(o-f.minPosition)*f.resistance;q=n-w}e.simpleStates={opening:"right",towards:e.dragWatchers.state,hyperExtending:f.minPosition>o,halfway:o<(f.minPosition/2),flick:Math.abs(e.dragWatchers.current-e.dragWatchers.hold)>f.flickThreshold,translation:{absolute:o,relative:n,sinceDirectionChange:(e.dragWatchers.current-e.dragWatchers.hold),percentage:(o/f.minPosition)*100}}}i.translate.x(q+t)}},endDrag:function(m){if(e.isDragging){d.dispatchEvent("end");var l=i.translate.get.matrix(4);if(e.dragWatchers.current===0&&l!==0&&f.tapToClose){d.dispatchEvent("close");d.events.prevent(m);i.translate.easeTo(0);e.isDragging=false;e.startDragX=0;return}if(e.simpleStates.opening==="left"){if((e.simpleStates.halfway||e.simpleStates.hyperExtending||e.simpleStates.flick)){if(e.simpleStates.flick&&e.simpleStates.towards==="left"){i.translate.easeTo(0)}else{if((e.simpleStates.flick&&e.simpleStates.towards==="right")||(e.simpleStates.halfway||e.simpleStates.hyperExtending)){i.translate.easeTo(f.maxPosition)}}}else{i.translate.easeTo(0)}}else{if(e.simpleStates.opening==="right"){if((e.simpleStates.halfway||e.simpleStates.hyperExtending||e.simpleStates.flick)){if(e.simpleStates.flick&&e.simpleStates.towards==="right"){i.translate.easeTo(0)}else{if((e.simpleStates.flick&&e.simpleStates.towards==="left")||(e.simpleStates.halfway||e.simpleStates.hyperExtending)){i.translate.easeTo(f.minPosition)}}}else{i.translate.easeTo(0)}}}e.isDragging=false;e.startDragX=d.page("X",m)}}}},j=function(l){if(l.element){d.deepExtend(f,l);e.vendor=d.vendor();i.drag.listen()}};this.open=function(l){d.dispatchEvent("open");d.klass.remove(b.body,"snapjs-expand-left");d.klass.remove(b.body,"snapjs-expand-right");if(l==="left"){e.simpleStates.opening="left";e.simpleStates.towards="right";d.klass.add(b.body,"snapjs-left");d.klass.remove(b.body,"snapjs-right");i.translate.easeTo(f.maxPosition)}else{if(l==="right"){e.simpleStates.opening="right";e.simpleStates.towards="left";d.klass.remove(b.body,"snapjs-left");d.klass.add(b.body,"snapjs-right");i.translate.easeTo(f.minPosition)}}};this.close=function(){d.dispatchEvent("close");i.translate.easeTo(0)};this.expand=function(l){var m=c.innerWidth||b.documentElement.clientWidth;if(l==="left"){d.dispatchEvent("expandLeft");d.klass.add(b.body,"snapjs-expand-left");d.klass.remove(b.body,"snapjs-expand-right")}else{d.dispatchEvent("expandRight");d.klass.add(b.body,"snapjs-expand-right");d.klass.remove(b.body,"snapjs-expand-left");m*=-1}i.translate.easeTo(m)};this.on=function(l,m){h[l]=m;return this};this.off=function(l){if(h[l]){h[l]=false}};this.enable=function(){d.dispatchEvent("enable");i.drag.listen()};this.disable=function(){d.dispatchEvent("disable");i.drag.stopListening()};this.settings=function(l){d.deepExtend(f,l)};this.state=function(){var l,m=i.translate.get.matrix(4);if(m===f.maxPosition){l="left"}else{if(m===f.minPosition){l="right"}else{l="closed"}}return{state:l,info:e.simpleStates}};j(k)};if((typeof module!=="undefined")&&module.exports){module.exports=a}if(typeof ender==="undefined"){this.Snap=a}if((typeof define==="function")&&define.amd){define("snap",[],function(){return a})}}).call(this,window,document);

// Function : Initialize Snap.js
var RC_initDrawer=function(){
    if(window.snapper==undefined){
        // Initialize Snap.js
        window.snapper = new Snap({
            element: $('[data-extension="drawer"]')[0]
        });
    } else {
        // Snap.js already exists, we just need to re-bind events
        window.snapper.enable();
    }
    var snap_update={
        tapToClose: false,
        touchToDrag: false,
        dragger :true
    }
 
    window.snapper.settings(snap_update);
}

window.addEventListener('push',RC_initDrawer);

/* ========================================================================
 * RC - Drawer.js
 * ========================================================================
 * Copyright 2016 redblock inc.
 * Author kiere (kiere@kimsq.com)
 * Licensed under MIT.
 * ======================================================================== */

!(function ($) {
  'use strict';

      // Drawer CLASS DEFINITION
      // ======================

      var Drawer = function (element, options) {
            this.options          = options
            this.$body            = $(document.body)
            this.$element       = $(element)
            this.title               = this.options.title?this.options.title:null
            this.url               = this.options.url?this.options.url:null
            this.isShown             = null
            this.showType           = this.options.showtype?this.options.showtype:'default'
            this.direction          = this.options.direction?this.options.direction:'left'
            this.snap_update = {
			    transitionSpeed: this.options.speed?this.options.speed:0.3,
                easing: this.options.animation?this.options.animation:'ease',
	        }
	        snapper.settings(this.snap_update);
      }

      Drawer.VERSION  = '1.1.0'
      Drawer.DEFAULTS = {
            show: true,
            afterDrawer : true,
            history : false,
            backdrop : true,
            bcontainer : '.snap-content'
      }

      Drawer.prototype.toggle = function (_relatedTarget) {
            return this.isShown ? this.hide() : this.show(_relatedTarget)
      }

      Drawer.prototype.show = function (_relatedTarget) {
            var $this = this
            var e    = $.Event('show.rc.drawer', { relatedTarget: _relatedTarget })
            var title =this.title;
            var drawer=this.options.target?this.options.target:'#'+this.$element.attr('id'); // 엘리먼트 클릭(target) & script 오픈 2 가지
            var url =this.url;
            if(url!=null) url=url.toString();
            var animation=this.options.animation?this.options.animation:'';
            var template=this.options.template;
            var bcontainer=this.options.bcontainer;
            var tplContainer=this.options.tplcontainer?drawer+' '+this.options.tplcontainer:drawer;
            this.$element.trigger(e);
            this.isShown = true
          
           // init Utility
            var utility=new Utility(drawer,this.options).init();
            if(!template){
                 utility.setdataVal(drawer,$this.options); // data 값 세팅하는 전용함수 사용한다.
            }else{
               $(tplContainer).load(template,$.proxy(function(){
                    utility.setdataVal(drawer,$this.options); // data 값 세팅하는 전용함수 사용한다.
                    this.afterTemplate(this,_relatedTarget);
               },this));
            }

            this.$element.on('tap.dismiss.rc.drawer', '[data-dismiss="drawer"]', $.proxy(this.hide, this))

            if(this.options.backdrop) this.backdrop();// add backdrop

            // drawer open
            if(this.showType=='default') snapper.open(this.direction)
            else if(this.showType=='expand') snapper.expand(this.direction)
            else if(this.showType=='toggle') {
                if(snapper.state().state==this.direction) this.hide()
                else snapper.open(this.direction)
            }

            if(this.options.history){
                // 브라우저 history 객체에 추가
                var object = {'type': 'drawer','target': {'id':drawer,'bcontainer':bcontainer,'backdrop':this.options.backdrop}}
                utility.addHistoryObject(object,title,url);
            }

           this.afterDrawer(this,_relatedTarget);
      }

      Drawer.prototype.afterTemplate=function(obj,_relatedTarget){
            var e = $.Event('loaded.rc.drawer', { relatedTarget: _relatedTarget })
            obj.$element.trigger('focus').trigger(e);
      }

      Drawer.prototype.afterDrawer=function(obj,_relatedTarget){
            var e = $.Event('shown.rc.drawer', { relatedTarget: _relatedTarget })
            obj.$element.trigger('focus').trigger(e);
      }

      Drawer.prototype.hide = function (e) {
           if(this.options.history) history.back();
           else this.nonHistoryHide();
           var backdrop=$(this.options.bcontainer).find('.backdrop');
           $(backdrop).remove();
      }

      Drawer.prototype.historyHide = function (e) {
            this.isShown = false
            var direction=this.options.direction=='left'?'right':'left';
            snapper.close(direction)
            if (e) e.preventDefault()
            var e    = $.Event('hide.rc.drawer');
            this.$element.trigger(e)
            this.afterHide();
      }

      Drawer.prototype.nonHistoryHide = function () {
            this.isShown = false
            var direction=this.options.direction=='left'?'right':'left';
            snapper.close(direction)
            var drawer=this.$element;
            var e    = $.Event('hide.rc.drawer');
            $(drawer).trigger(e)
            this.afterHide();
      }

      Drawer.prototype.afterHide=function(){
           var e = $.Event('hidden.rc.drawer');
           this.$element.trigger(e);
      }

      Drawer.prototype.backdrop = function (callback) {
          if (this.isShown && this.options.backdrop) {
               this.$backdrop = $(document.createElement('div'))
                  .addClass('backdrop')
                  .appendTo(this.options.bcontainer)
               this.$backdrop.on('tap.dismiss.rc.drawer', $.proxy(function (e) {
                    if (this.ignoreBackdropClick) {
                      this.ignoreBackdropClick = false
                      return
                    }
                    if (e.target !== e.currentTarget) return
                    this.options.backdrop == 'static'
                    ? this.$element[0].focus()
                    : this.hide()
               }, this))
          }
     }

      var old = $.fn.drawer

      $.fn.drawer             = Plugin
      $.fn.drawer.Constructor = Drawer


        // DRAWER NO CONFLICT
        // =================

      $.fn.drawer.noConflict = function () {
            $.fn.drawer = old
            return this
      }

      // DRAWER PLUGIN DEFINITION
      // =======================

      function Plugin(option, _relatedTarget) {
      	    return this.each(function () {
                var $this   = $(this)
                var options = $.extend({}, Drawer.DEFAULTS, $this.data(), typeof option == 'object' && option)

                var data = new Drawer(this, options)
                if (typeof option == 'string' && option!='toggle') data[option](_relatedTarget)
                else if (options.show) data.show(_relatedTarget)
            })
       }

      // DRAWER DATA-API
      // ==============

     $(document).on('tap.rc.drawer.data-api', '[data-toggle="drawer"]', function (e) {
          var $this   = $(this)
          var href    = $this.attr('href')
          var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
          var option  = $target.data('rc.drawer') ? 'toggle' : $.extend({}, $target.data(), $this.data())

          if ($this.is('a')) e.preventDefault()

          $target.one('show.rc.drawer', function (showEvent) {
                if (showEvent.isDefaultPrevented()) return // only register focus restorer if Popup will actually get shown
                $target.one('hidden.rc.drawer', function () {
                   $this.is(':active') && $this.trigger('focus')
                })
          })
          Plugin.call($target, option, this)
      })

}(jQuery));