
var KRE_Drawer = {

    // Instance variables
    // ==================

    // Default options
    getDefaultOptions: function() {
        return {     
            filterBox : $('[data-role="filterBox"]'), // 필터 선택박스 
            right_drawer : $('[data-role="drawer-right"]'),// 우측 drawer           
            openTrigger : $('[data-role="open-drawer"]'), // 오픈 trigger
            closeTrigger: $('[data-role="close-drawer"]'), // 닫기 trigger
            respondScroll: '[data-role="respond-scroll"]',
            direction: 'right',
            backdrop: true, // backdrop 생성 여부 
            backdropClose: true, // backdrop 클릭으로 닫기 허용 
            drawerSpeed: 0.37, // drawer 스피드 
            backdropOpacity: 0.5, // backdrop opacity 
            backdropGS: 370, // backdrop gradation speed
            backdropContainer: $('body'), // backdrop 추가 container    
        }            
    },
    
    // Initialization
    init: function(options) {

        var self = this;
        // Init options
        this.options = $.extend(true, {}, this.getDefaultOptions(), options);
        this.$el = $(this.options.selector);
        this.isShown = false;
        this.direction = this.options.direction; // drawer 방향 설정
        this.filterBox = this.options.filterBox; //

        // 답변 패널부분 height 자동 세팅 
        var respondScroll = this.options.respondScroll;
        var dh = window.innerHeight-parseInt(50); // dynamic height
        $(respondScroll).css('height', dh+'px');
          
        // backdrop 사용하면 생성 : default ==> true
        if(this.options.backdrop){
            this.createBackDrop(this.options.backdropContainer);
        }
    
    },

    // show drawer and backdrop
    openDrawer: function() {
        var self = this;
        
        //console.log(this.$el);
        $(this.$el.selector).css('margin-right','0');
        $(this.$el.selector).addClass('opened');
        var e = $.Event('shown.ke.drawer');
        $(this.$el.selector).trigger(e); 

        // drawer opened 
        this.isShown=true;  
    },
    
    // click backdrop to close 
    backDropClose: function(){
  
        if(this.isShown) this.closeDrawer();
        else return;
    },

    // hide drawer and backdrop
    closeDrawer: function() {
        var self = this;
        var width = this.options.width;

        $(this.$el.selector).css('margin-right','-'+width+'%');
        
        this.isShown=false;
    },
    
    // backdrop 생성    
    createBackDrop : function(parent_Container){
        this.backdrop = $('<div/>',{id: 'kre-backdrop',"data-role":"drawer-backdrop"});
        $(this.backdrop).css({
            'display': 'none',
            'position' : 'fixed',
            'top': 0,
            'bottom': 0,
            'left': 0,
            'opacity': 0,
            'width': '100%',
            'height': '100%',
            'background-color' : '#000',
            'z-index': 99  
        });

        $(this.backdrop).appendTo(parent_Container);
        
    },

   
};

// 초기화 
var drawer = Object.create(KRE_Drawer);
var drawer_options =({
    backdrop: false,
    selector : $('#setNodePanel'),
    width: 48 // 패널 출력 넓이 
});

drawer.init(drawer_options);


  