<!--slimscroll JavaScript -->
<script type="text/javascript">
    // 글로벌 변수 세팅
    var mxBasePath = '/modules/chatbot/lib/mxDraw/src/';
    var mxBaseUrl = '/modules/chatbot/lib/mxDraw/examples/editors';
    var vendor ='<?php echo $V['uid']?>';
    var bot = '<?php echo $bot?>';
    var botId = '<?php echo $botId?>';
    var dialog = '<?php echo $dialog?>';
    var dialog_gid = '<?php echo $dialog_gid?>';
    var module = '<?php echo $m?>';
    var dialogSpace = '#dialog-workspace'; // 노드 세팅 패널
    var sescode ='<?php echo $sescode?>';
    // dialog 패널에서 사용될 template 최초에 세팅하고
    // 패널 오픈/클로즈시에는 재설정하지 않는다.
    var template = {};
    var startNodeName = '<?php echo $startNodeName?>';
    var callNode = '<?php echo $callNode?>';
    var callIntent = '<?php echo $callIntent?>';
    var callEntity = '<?php echo $callEntity?>';
    var callContext = '<?php echo $callContext?>';
    var dialogNodeJson = <?=($dialogNodeJson ? $dialogNodeJson : '{"dialogNodeUid":"0","nodeid":"1","name":"Welcome","parent":"0"}')?>;
    var container = document.getElementById('graphContainer');
    var graph; // 변수 graph 초기화
    var dialogNode = typeof dialogNodeJson == 'string' ? JSON.parse(dialogNodeJson) : dialogNodeJson;
    var layout;
    var vertex = {name: '대화주제', width: 110, height: 40};
    var first_node; // 첫번째 노드
    var lastNodeBg ="#fac7c7"; // 마지막 대화상자 bg
    var selectedNodeBg = "#d4edfa"; // 선택된 대화상자 bg
    var shopTemplete = <?=($shopTemplete ? 1 : 0)?>;
    var bottype = '<?=$Bot['bottype']?>';
</script>

<link href="<?php echo $g['url_module_skin']?>/css/nodeMenu.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/atwho.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/mxDraw/mxClient.min.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/mxDraw/mxClient.extend.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/panel_action.js?'.date('ymdhi')?>"></script>

<script type="text/javascript">
    //aramjo
    mxVertexHandler.prototype.rotationEnabled = false;
    mxGraphHandler.prototype.guidesEnabled = true;
    mxEdgeHandler.prototype.snapToTerminals = true;

    mxConstants.VERTEX_SELECTION_COLOR = 'none';

    // 오른쪽 마우스 (컨텍스트 메뉴) 막음
    mxEvent.disableContextMenu(container);

    // 줌인
    var zoomIn = document.getElementById('zoomIn');
    mxEvent.addListener(zoomIn, 'click', function() {
    	graph.zoomIn();
    });

    // 줌아웃
    var zoomOut = document.getElementById('zoomOut');
    mxEvent.addListener(zoomOut, 'click', function() {
    	graph.zoomOut();
    });

    // 가운데 이동
    var zoomCenter = document.getElementById('zoomCenter');
    mxEvent.addListener(zoomCenter, 'click', function() {
    	getBoundCenter();
    });

    // 엑셀 다운로드
    var zoomExcel = document.getElementById('zoomExcel');
    mxEvent.addListener(zoomExcel, 'click', function() {
    	getGraphDownload();
    });

    mxEvent.addMouseWheelListener(function (evt, up) {
        //console.log(evt);
    });

    // 노드 컨테이너 제스쳐 (panning) 이벤트
    mxEvent.addGestureListeners(container, function(e) {
    	// 대화상자 메뉴 모두 닫기
        removeAllnodeMenu();
    });

    // 노드 컨테이너 클릭 > 패널 닫기
    mxEvent.addListener(container,'click', function(e) {
    	if (!mxEvent.isMultiTouchEvent(e)) {
    	    if(e.target.localName=='svg'){
    	    	var data = {"act":"closeSetNodePanel"};
    			initSettingPanel(data); // 패널 동기화

    			// 대화상자 메뉴 모두 닫기
    			removeAllnodeMenu();
    			graph.clearSelection();
            }
        }
    });

    // graph 인스턴스 초기화
    var initGraph = function(container){
    	// Checks if browser is supported
    	if (!mxClient.isBrowserSupported()) {
    		// Displays an error message if the browser is
    		// not supported.
    		mxUtils.error('Browser is not supported!', 200, false);
    	} else {
    	    $(container).html(''); // container 비움

    	    graph = new mxGraph(container);

    	    // 스타일: 버텍스
    		var vStyle = graph.getStylesheet().getDefaultVertexStyle();
    		vStyle[mxConstants.STYLE_ROUNDED] = true;
    		vStyle[mxConstants.STYLE_STROKECOLOR] = 'gray';
    		vStyle[mxConstants.STYLE_STROKEWIDTH] = '1';
    		vStyle[mxConstants.STYLE_FILLCOLOR] = '#fff';
    		vStyle[mxConstants.STYLE_FONTCOLOR] = '#000';
    		vStyle[mxConstants.STYLE_FONTSIZE] = '12';
    		vStyle[mxConstants.STYLE_FONTSTYLE] = 1;

    		// 스타일: 엣지
    		var eStyle = graph.getStylesheet().getDefaultEdgeStyle();
    		eStyle[mxConstants.STYLE_STROKECOLOR] = '#999';
    		eStyle[mxConstants.STYLE_STROKEWIDTH] = '1';
    		eStyle[mxConstants.STYLE_VERTICAL_ALIGN] = mxConstants.ALIGN_MIDDLE;
    		eStyle[mxConstants.STYLE_ENDARROW] = mxConstants.ARROW_CLASSIC;
    		eStyle[mxConstants.STYLE_EDGE] = mxEdgeStyle.SideToSide;

    		// 레이아웃(트리) 생성
    		layout = new mxCompactTreeLayout(graph);

    		layout.useBoundingBox = false;
    		layout.edgeRouting = false;
    		layout.levelDistance = 60;
    		layout.nodeDistance = 9;

    		//------ for graph init setting -------//
    		graph.setEnabled(false); // 그래프: 비활성화 - 사용자가 그래프를 직접 조작할 수 없도록

    		graph.panningHandler.ignoreCell = true;
    		graph.setPanning(true); // 패닝: 활성화

    		//------ for page scroll -----------//
    		graph.scrollTileSize = new mxRectangle(0, 0, 400, 400);

    		graph.getPagePadding = function() {
    		    return new mxPoint(Math.max(0, Math.round(graph.container.offsetWidth - 34)), Math.max(0, Math.round(graph.container.offsetHeight - 34)));
    		};

    		graph.getPageSize = function() {
    		    return (this.pageVisible) ? new mxRectangle(0, 0, this.pageFormat.width * this.pageScale, this.pageFormat.height * this.pageScale) : this.scrollTileSize;
    		};

    		graph.getPageLayout = function() {
    		    var size = (this.pageVisible) ? this.getPageSize() : this.scrollTileSize;
				var bounds = this.getGraphBounds();

				if (bounds.width == 0 || bounds.height == 0) {
					return new mxRectangle(0, 0, 1, 1);
				} else {
					// 그래프 바운드 박스
					var x = Math.ceil(bounds.x / this.view.scale - this.view.translate.x);
					var y = Math.ceil(bounds.y / this.view.scale - this.view.translate.y);
					var w = Math.floor(bounds.width / this.view.scale);
					var h = Math.floor(bounds.height / this.view.scale);

					var x0 = Math.floor(x / size.width);
					var y0 = Math.floor(y / size.height);
					var w0 = Math.ceil((x + w) / size.width) - x0;
					var h0 = Math.ceil((y + h) / size.height) - y0;

					return new mxRectangle(x0, y0, w0, h0);
				}
			};

			graph.view.getBackgroundPageBounds = function() {
				var layout = this.graph.getPageLayout();
				var page = this.graph.getPageSize();

				return new mxRectangle(this.scale * (this.translate.x + layout.x * page.width),
						this.scale * (this.translate.y + layout.y * page.height),
						this.scale * layout.width * page.width,
						this.scale * layout.height * page.height);
			};

			graph.getPreferredPageSize = function(bounds, width, height) {
				var pages = this.getPageLayout();
				var size = this.getPageSize();

				return new mxRectangle(0, 0, pages.width * size.width, pages.height * size.height);
			};

			var graphViewValidate = graph.view.validate;
			graph.view.validate = function() {
				if (this.graph.container != null && mxUtils.hasScrollbars(this.graph.container)) {
					var pad = this.graph.getPagePadding();
					var size = this.graph.getPageSize();

					var tx = this.translate.x;
					var ty = this.translate.y;
					this.translate.x = pad.x / this.scale - (this.x0 || 0) * size.width;
					this.translate.y = pad.y / this.scale - (this.y0 || 0) * size.height;
				}

				graphViewValidate.apply(this, arguments);
			};

			var graphSizeDidChange = graph.sizeDidChange;
			graph.sizeDidChange = function() {
				if (this.container != null && mxUtils.hasScrollbars(this.container)) {
					var pages = this.getPageLayout();
					var pad = this.getPagePadding();
					var size = this.getPageSize();

					var minw = Math.ceil(2 * pad.x / this.view.scale + pages.width * size.width);
					var minh = Math.ceil(2 * pad.y / this.view.scale + pages.height * size.height);

					var min = graph.minimumGraphSize;
					if (min == null || min.width != minw || min.height != minh) {
						graph.minimumGraphSize = new mxRectangle(0, 0, minw, minh);
					}

					var dx = pad.x / this.view.scale - pages.x * size.width;
					var dy = pad.y / this.view.scale - pages.y * size.height;

					if (!this.autoTranslate && (this.view.translate.x != dx || this.view.translate.y != dy)) {
						this.autoTranslate = true;
						this.view.x0 = pages.x;
						this.view.y0 = pages.y;

                        var tx = graph.view.translate.x;
                        var ty = graph.view.translate.y;
						graph.view.setTranslate(dx, dy);
						graph.container.scrollLeft += (dx - tx) * graph.view.scale;
						graph.container.scrollTop += (dy - ty) * graph.view.scale;

						this.autoTranslate = false;
						return;
					}
					graphSizeDidChange.apply(this, arguments);
				}
			};

    		new mxRubberband(graph);

    		//------ for page scroll -----------//

    		// 복사 기능 대비  > copyNode(act,cell) 함수에서 갱신
    		graph['cell_copied'] = null; // 복사 대상 cell(node) 초기화
    		graph['copyCell_type'] = null; // 복사 타입 초기화 (only_node : 대화상자만  , with_res : 응답 포함)

    		// 버텍스: 선택했을때 리사이즈 표시 안나오도록
    		graph.isCellResizable = function() {
    			return false;
    		}

    		// 버텍스: 커서
    		graph.getCursorForCell = function(cell) {
    			if (graph.getModel().isVertex(cell)) return 'pointer';
    			else return null;
    		}

    		// 뷰패널: 초기 위치
    		//graph.view.setTranslate(80, 0);

    		// 버텍스: 클릭
    		graph.addListener(mxEvent.CLICK, function(sender, evt) {
    		    // 버텍스 BG 초기화
    		    getResetCellBg();

    			// 버텍스 선택표시 칼러 지정
    			mxConstants.VERTEX_SELECTION_COLOR = '#00ff00';

    			var node = evt.getProperty('cell');
    			if (node != null && graph.getModel().isVertex(node)) {
    			    node['selected'] = 1;
                    graph.model.setStyle(node, "fillColor="+selectedNodeBg);

    				graph.setSelectionCell(node);
    				var data ={"act": "openSetNodePanel","nodeObj": node}; // 패널 열고 해당 node 정보 가져간다.
    				initSettingPanel(data); // 패널 동기화

    			} else{
    				graph.clearSelection();
    			}
    			evt.consume();
    		});

    		// 더블크릭 오류 임시 처방
    		graph.addListener(mxEvent.DOUBLE_CLICK, function(sender, evt){
    			 var node = evt.getProperty('cell');
    			 node.removeEventListener('click',evt);
    		});

    		// 버텍스: 호버
    		graph.addMouseListener({
    			mouseUp: function(sender, me) {},
    			mouseDown: function(sender, me) {},
    			mouseMove: function(sender, me) {
    				if (this.hoverState != null && this.hoverState.invalid)
    					this.hoverState = null;

    				if (this.hoverState != null && me.getState() == this.hoverState)
    					return;

    				var meState = graph.view.getState(me.getCell());

    				if (graph.isMouseDown ||
    					(meState != null && !graph.getModel().isVertex(meState.cell))) {
    					meState = null;
    				}

    				if (meState != this.hoverState) {
    					if (this.hoverState != null) {
    						this.hoverLeave(me.getEvent(), this.hoverState);
    					}

    					this.hoverState = meState;

    					if (this.hoverState != null) {
    						this.hoverEnter(me.getEvent(), this.hoverState);
    					}
    				}
    			},
    			hoverState: null,
    			hoverEnter: function(evt, state) {
    				state.style = mxUtils.clone(graph.getStylesheet().getDefaultVertexStyle());

    				this.hoverStyle(state, true);

    				var edges = state.cell.edges;
    				for (var i=0; edges != null && i< edges.length; i++) {
    					var eState = graph.view.getState(edges[i]);
    					eState.style = mxUtils.clone(graph.getStylesheet().getDefaultEdgeStyle());
    					this.hoverStyle(eState, true);
    				}
    			},
    			hoverLeave: function(evt, state) {
    				state.style = graph.getStylesheet().getDefaultVertexStyle();
    				var cell = state.cell;
         		    this.hoverStyle(state, false);
    				var edges = state.cell.edges;
    				for (var i=0; edges != null && i< edges.length; i++) {
    					var eState = graph.view.getState(edges[i]);
    					if(eState){
    						eState.style = graph.getStylesheet().getDefaultEdgeStyle();
    					    this.hoverStyle(eState, false);
    					}
    				}
    			},
    			hoverStyle: function(state, hover) {
    				var cell = state.cell;
    				if(cell.is_unknown != 1 && cell.selected != 1) {
    				    state.style[mxConstants.STYLE_STROKEWIDTH] = (hover) ? '2' : '1';
    				    state.style[mxConstants.STYLE_FILLCOLOR] = '#ffffff';
    				    state.shape.apply(state);
    				    state.shape.redraw();
    				}
    			}
    		});
    	}
    }

    // 전체 메뉴 삭제
    function removeAllnodeMenu(){
        var nodeMenu = '.mxPopupMenu';
        if($(nodeMenu)[0]){
           $(nodeMenu).remove();
        }
    }

    // 메뉴 아이콘 추가 > 대화상자
    function menuOverlay(cell) {
    	var overlay = new mxCellOverlay(new mxImage('/modules/chatbot/lib/mxDraw/src/images/menu.png', 30, 21), 'menu');
    	overlay.cursor = 'pointer';
    	overlay.verticalAlign = mxConstants.ALIGN_MIDDLE;
    	overlay.align = mxConstants.ALIGN_RIGHT;
    	overlay.tooltip = 'menu';

    	overlay.addListener(mxEvent.CLICK, function(sender, evt) {
    		//createPopupMenu(evt,cell);
     		   // 메뉴 출력

     		// 버텍스 선택칼러 삭제
     		mxConstants.VERTEX_SELECTION_COLOR = 'none';

     		// 대화상자 메뉴 전체 삭제
     		removeAllnodeMenu(cell);

     		// 해당 대화상자 메뉴만 노출
     		setTimeout(function(){
                var state = graph.view.getState(cell);
    		    var menu = new mxVertexToolHandler(state); // 최상단 참조

           },10);
     	});

    	graph.addCellOverlay(cell, overlay);
    }

    // ############# Menu 시작 ################################################
    function mxVertexToolHandler(state) {
    	mxVertexHandler.apply(this, arguments);
    };

    mxVertexToolHandler.prototype = new mxVertexHandler();
    mxVertexToolHandler.prototype.constructor = mxVertexToolHandler;

    mxVertexToolHandler.prototype.domNode = null;

    mxVertexToolHandler.prototype.init = function() {
    	mxVertexHandler.prototype.init.apply(this, arguments);

    	// In this example we force the use of DIVs for images in IE. This
    	// handles transparency in PNG images properly in IE and fixes the
    	// problem that IE routes all mouse events for a gesture via the
    	// initial IMG node, which means the target vertices
    	var self = this;
    	this.domNode = document.createElement('ul');
    	this.domNode.style.position = 'absolute';
    	this.domNode.setAttribute("class","mxPopupMenu");

    	var menuArr = {
            "하위 대화상자 추가": "A2S",
            "위에 추가": "A2A",
            "아래에 추가": "A2B",
            "위로 이동": "M2A",
          	"아래로 이동": "M2B",
            "위에 붙여넣기": "C2A",
          	"아래에 붙여넣기": "C2B",
        	"복사(대화상자만)": "copy",
        	"복사(응답 포함)": "copyWR", // copy With Response
        	"삭제": "del",
    	};

    	// 메뉴 li 생성 함수
    	function createLi(_this,text) {
    		var self = _this;
    		var li = document.createElement("li");
    		var span = document.createElement("span");
            var textnode = document.createTextNode(text);
            var cell = _this.state.cell; // 해당 대화상자 정보
            var nodeId = cell.uid; // 2 : '시작' 대화상자 (두번째 대화상자)
            var menuCode = menuArr[text]; // 메뉴 코드
            var doMenuAct = function(act){

                // 오픈 메뉴 삭제
                self.destroy();

                // 해당 메뉴박스 초기화(삭제)
                setTimeout(function(){
                    // 외부 함수 호출
    	            if(act =='del'){
    	                if(nodeId=='2'){
    	                	show_Notify('#graphContainer-wrapper', '시작 대화상자는 삭제할 수 없습니다.');
    	                	return false;
    	                }else{
    	                    deleteNode(cell);
    	                }
    	            }
    	            else if(act =='A2S') addNode(cell); // 하위 노드 추가
    	            else if(act =='copy'|| act=='copyWR'){
                        show_Notify('#graphContainer-wrapper', '대화상자가 복사 되었습니다.');
                        setTimeout(function(){
                           copyNode(act,cell);
                        },10);
    	            }
    	            else{
                        // 나머지는 그래프 다시 그린다.
                        var _data ={cell:cell,act:act};
    	                reDrawNode(_data);
    	            }
                },50);
            };

            // li (메뉴) 세팅
          	span.appendChild(textnode);
           	li.appendChild(span);

            if(menuCode=='C2A' || menuCode=='C2B'){
            	var cell_copied = this.graph['cell_copied'];
            	if(menuCode=='C2B'){
                    if(cell_copied===null) li.setAttribute("class","mxPopupMenuItem bb dsb"); // border-top disabled
            	    else li.setAttribute("class","mxPopupMenuItem bb "); // border-top

            	}else if(menuCode =='C2A'){
            		if(cell_copied===null) li.setAttribute("class","mxPopupMenuItem bt dsb"); // border-bottom disabled
            	    else li.setAttribute("class","mxPopupMenuItem bt "); // border-bottom
            	}
            }
            else if(menuCode =='M2A') li.setAttribute("class","mxPopupMenuItem bt"); // border-top
            else if(menuCode =='M2B') li.setAttribute("class","mxPopupMenuItem bb"); // border-bottom
            else if(menuCode =='del'){
            	if(nodeId=='2') li.setAttribute("style","display:none"); // 시작 대화상자 인 경우 '삭제' 기능 제거
                else li.setAttribute("class","mxPopupMenuItem del");
            }
            else li.setAttribute("class","mxPopupMenuItem");

            // 메뉴 클릭 이벤트 바인딩
    		mxEvent.addGestureListeners(li, mxUtils.bind(this, function(evt) {
    		    _this.graph.graphHandler.cellWasClicked = true;
    		    _this.graph.isMouseDown = true;
    		    _this.graph.isMouseTrigger = mxEvent.isMouseEvent(evt);
    		    mxEvent.consume(evt);

    		    var text = $(li).find('span').text(); // 메뉴명
    		    var menuAct = menuArr[text]; // 메뉴 코드
    		    var cell_copied = self.graph['cell_copied'];
    		    if(menuAct =='C2A'||menuAct =='C2B'){
    		        if(cell_copied == null){
    		            alert('복사를 먼저 해주세요');
    		            return false;
    		        }else{
    		            // menu 실행 함수
    		            doMenuAct(menuAct);
    		        }
    		    }else{
    		        // menu 실행 함수
    		        doMenuAct(menuAct);
    		    }
    		    $("#nodeSClose").click();
    		}));

    		return li;
    	};

    	// 메뉴 리스트 세팅
        $.each(menuArr,function(name,code){
            var li = createLi(self, name);
     		self.domNode.appendChild(li);
        });

    	this.graph.container.appendChild(this.domNode);
    	this.redrawTools();
    };

    // 메뉴 출력
    mxVertexToolHandler.prototype.redrawTools = function() {
    	if (this.state != null && this.domNode != null) {
    		var dy = (mxClient.IS_VML && document.compatMode == 'CSS1Compat') ? 20 : 10;
    		this.domNode.style.left = (this.state.x + this.state.width + 22) + 'px';
    		this.domNode.style.top = (this.state.y + this.state.height + dy) + 'px';
    	}
    };

    // 메뉴 삭제
    mxVertexToolHandler.prototype.destroy = function(sender, me) {
    	mxVertexHandler.prototype.destroy.apply(this, arguments);

    	if (this.domNode != null) {
    		this.domNode.parentNode.removeChild(this.domNode);
    		this.domNode = null;
    	}
    };
    // ############# Menu 끝 ################################################


    // 추가 버튼을 붙이고, 이벤트를 정의한다
    function addOverlay(cell) {
    	var overlay = new mxCellOverlay(new mxImage('/modules/chatbot/lib/mxDraw/examples/images/add3.png', 20, 20), '추가');
    	overlay.cursor = 'hand';
    	overlay.tooltip = '추가';

    	overlay.addListener(mxEvent.CLICK, function(sender, evt2) {
             addNode(cell);
    	});

    	graph.addCellOverlay(cell, overlay);
    }
    //----------------------------------------------------------------
    // 노드 그리기
    function jsonToVertex(dialogNode, source) {
    	var target;
    	var nodeName = dialogNode.name;
    	//if(shopTemplete && (nodeName == '상품문의' || nodeName == '주문내역확인')) return false;
    	var maxByte = 12, totalByte = 0, strLen = 0;
    	for(var i=0; i < nodeName.length; i++) {
    	    if(escape(nodeName.charAt(i)).length > 4) totalByte += 2;
    	    else totalByte++;
    	    if(totalByte <= maxByte) strLen = (i+1);
    	}
    	nodeName = totalByte > maxByte ? nodeName.substr(0, strLen)+'..' : nodeName;

    	if (!source) {
    		target = graph.insertVertex(graph.getDefaultParent(), null, nodeName, 0, 0, vertex.width, vertex.height);
    		target.name = dialogNode.name;
    		target.uid = dialogNode.nodeid;
    		target.bumo = dialogNode.parent;
    	} else {
    		var geo = graph.getCellGeometry(source);
            var vtxStyle; // 대화상자 스타일
            var uid = dialogNode.nodeid;
    		var bumo = dialogNode.parent;
    		var is_unknown = dialogNode.is_unknown;

    	    if(is_unknown=='1') vtxStyle ="fillColor="+lastNodeBg;
    	    else vtxStyle ="";

    		target = graph.insertVertex(graph.getDefaultParent(), null, nodeName, geo.x, geo.y, vertex.width, vertex.height, vtxStyle);
    		target.name = dialogNode.name;
    	    target.uid = dialogNode.nodeid;
    		target.bumo = dialogNode.parent;
    		target.is_unknown = dialogNode.is_unknown;

            // 마지막 대화상자 아닌 경우 > 메뉴 버튼 추가 (추가/삭제/복사 일괄 처리)
    		if(target.is_unknown!='1') menuOverlay(target);
    		graph.insertEdge(graph.getDefaultParent(), null, '', source, target);
    	}

    	if (dialogNode.children) {
    		$.each(dialogNode.children, function(i, child) {
    			jsonToVertex(child, target);
    		});
    	}

    	// 아래 executeLayout 함수를 통해서 add 과정을 에니메이션 처리 하기 위해서 최상위 node 를  인식시켜준다.
    	first_node = target;
    }

    // 대화상자 호출 함수
    // dialogNode : 상단에서 정의 < graph.php 페이지에서 $chatbot->getNodeTreeJson() 로 얻는 대화상자 데이터(json)
    function draw(dialogNode) {
    	// 그래프 그리기
    	if (dialogNode) {
    		executeLayout(function() {
    			jsonToVertex(dialogNode);
    		}, function() {
    			//graph.center();
    		});
    	} else {
    		executeLayout(function() {
    			var root = graph.insertVertex(graph.getDefaultParent(), null, '대화주제', 0, 0, vertex.width, vertex.height);
    			addOverlay(root);
    		}, function() {
    			//graph.view.setTranslate(20, 20);
    		});
    	}

    	// Sets initial scrollbar positions
    	getBoundCenter();
    }

    // 대화상자 다시 그리는 함수 : 상단/하단에 추가/ 붙여넣기 등
    function reDrawNode(data){
    	var cell_copied = graph['cell_copied'];
    	var act = data.act;
    	var cell = data.cell;
    	var nodeId = data.cell.uid;
    	var nodeParent = data.cell.bumo;
    	var nodeId_copied = cell_copied!==null?cell_copied.uid:null;
    	var copyCell_type = graph['copyCell_type']!==null?graph['copyCell_type']:null;
        var _data = {
        	linkType: 'do-nodeMenu',
        	vendor: vendor,
        	bot: bot,
        	dialog: dialog,
        	botId: botId,
        	menuAct: act,
        	nodeId: nodeId,
        	nodeParent: nodeParent,
        	nodeId_copied: nodeId_copied,
        	copyCell_type: copyCell_type,
        	newNodeName: '대화상자',
        };
        var loaderContainer = container; // 대화상장 출력 div (#graphContainer) 상단에서 지정
        var showLoader = function(){
            var loaderIcon = '<div class="cssload-speeding-wheel"></div>';
            var loader = $('<div/>', {
                id: 'nodeLoader-wrapper',
                class: 'preloader',
                style: 'position:absolute;top: 0;left:0;width: 100%;height:100%;z-index:99999;background: #fff;opacity: 0.7',
                html: loaderIcon}
                ).prependTo(loaderContainer);
        };

        var hideLoader = function(){
            $(loaderContainer).find('#nodeLoader-wrapper').remove();
        };

        // graph 객체 초기화
        initGraph(container);

        // loader 출력
        showLoader();

        $.ajax({
            url: rooturl+'/?r='+raccount+'&m='+module+'&a=do_dialogPanelAction',
            type: 'post',
            data: _data,
            cache: false,
            success: function(response){
                var result=$.parseJSON(response);
                var error = result.error;
                var dialogNodeJson = result.dialogNodeJson;
                var dialogNode = JSON.parse(dialogNodeJson);

                draw(dialogNode);

               // loader 지우기
                setTimeout(function(){
                    hideLoader();
                    setTimeout(function(){
                        if(error){
    	                    var msg = result.msg;
    		                show_Notify('#graphContainer-wrapper', msg);
    		            }
                    },50);
                },500);
            }
        });
    }

    // 모델을 변경하고, 레이아웃을 갱신한다
    var executeLayout = function(change, post){
    	graph.getModel().beginUpdate();
    	try {
    	    if (change != null) change();
    		layout.execute(graph.getDefaultParent(),first_node); // first_node 가 기준이 된다
    	}
    	catch (e) {
    		throw e;
    	}
    	finally {
    	    graph.getModel().endUpdate();
    	    if (post != null) post();
    	}
    }
    //----------------------------------------------------------------

    // 추가 버튼을 붙이고, 이벤트를 정의한다
    function addNode(cell) {
    	graph.clearSelection();
    	var preGeo = graph.getCellGeometry(cell);
    	var v2;

    	if(cell.uid == undefined || cell.uid == '') {
    	    alert('현재 대화상자의 데이터가 입력되지 않았습니다.\n대화상자를 클릭하여 데이터를 입력해주세요.'); return false;
    	}
    	// execute : 기존방식 > 추가/삭제 되는 과정 안이쁨
    	executeLayout(function() {
    		v2 = graph.insertVertex(graph.getDefaultParent(), null, vertex.name, preGeo.x, preGeo.y, vertex.width, vertex.height);
    		v2.bumo = cell.uid;
    		menuOverlay(v2);
    		graph.insertEdge(graph.getDefaultParent(), null, '', cell, v2);

    		var data = {"act":"addNode","nodeObj":v2,"nodeName":vertex.name,"nodeParent":cell.uid};
    		initSettingPanel(data);

    	}, function() {
    	    //aramjo
    	    //graph.scrollCellToVisible(v2);
    	});
    }

    // 대화상자 삭제 함수
    var deleteNode = function(node) {
        if (node.edges.length > 1) {
            alert('하위의 대화상자를 먼저 삭제하셔야 합니다');
            return;
        }
        if (confirm('설정내용도 함께 삭제됩니다')) {
            executeLayout(function() {
                // 대화상자 삭제
                graph.removeCells([node]);

                // 데이터에서 삭제
                var data = {"act": "deleteNode","nodeObj": node};
                initSettingPanel(data);
            });
        }
    }

    // 알림 출력
    var show_Notify = function(container,message){
        var container = container?container:'body';
        var style;
        if(container =='[data-role="topicTabs-wrapper"]'){
            style = 'style="width: 50%"';
        }
        var notify_msg ='<div id="kiere-menuNotify-msg" '+style+'>'+message+'</div>';
        var notify = $('<div/>', { id: 'kiere-menuNotify', html: notify_msg})
              .addClass('active')
              .appendTo(container)
        setTimeout(function(){
            $(notify).removeClass('active');
            $(notify).remove();
        }, 1500);
    }

    // 대화상자 복제 함수
    var copyNode = function(act,cell) {
        graph['cell_copied'] = cell;

        if(act =='copy') graph['copyCell_type'] ='only_node'; // 대화상자만
        else if(act =='copyWR') graph['copyCell_type'] = 'with_res';// 응답 포함
    }

    // settingPanel 연동 함수
    function initSettingPanel(data) {
        var act = data.act;
        var nodeObj = data.nodeObj;
        var mxAct = data.mxAct===false?false:true;
        $(dialogSpace).KRE_Panel({
            module: module,
            vendor: vendor,
            bot: bot,
            botId : botId,
            dialog: dialog,
            dialog_gid: dialog_gid,
            graph: graph, // mxGraph 객체 를 세팅한다.
            nodeObj: nodeObj,
            mxData: data,
            callBackGraph: function(res){
            	//console.log(res);
                if(res.uid) nodeObj['uid'] = res.uid;
                if(res.nodeName) nodeObj['name'] = res.nodeName;
                if(res.pact=='delete') deleteNode(nodeObj);
            },
            mxAct: mxAct
        });
        if(data.act =='closeSetNodePanel'){
            $('body').find('.tooltip').remove();
        }
    }

    // 초기함수
    function init_func(){
    	// graph 객체 초기화
    	initGraph(container);

    	// tooltip 초기화
        $('body').tooltip({
    		selector: '[data-tooltip=tooltip]',
    		container: 'body'
    	});

    	$('#guide-input').tooltip({
    		placement: "right",
    		html: true,
            title: '<div class="guid-input"><h6>사용자 입력문장에서 체크된 값</h6><ul class="guide-ul"><li><strong>'+callIntent+'</strong> : 문장에 포함된 인텐트</li><li><strong>'+callEntity+'</strong> : '+callIntent+'와 관련된 핵심 단어</li></ul></div>',
        });

        // panel_action.js 초기화
        var data ={nodeObj:null,act:null,mxAct:false,initMod:"start"};
        initSettingPanel(data);
    }

    // 그래프 가운데 이동
    function getBoundCenter(x, y) {
        var x = x == undefined || x == "" ? 0 : x;
        var y = y == undefined || y == "" ? 0 : y;
        window.setTimeout(function() {
    	    var bounds = graph.getGraphBounds();
			var width = Math.max(bounds.width, graph.scrollTileSize.width * graph.view.scale);
			var height = Math.max(bounds.height, graph.scrollTileSize.height * graph.view.scale);
			var targetHeight = ((graph.container.clientHeight - height) / 2);
			var targetWidth = ((graph.container.clientWidth - width) / 2) - x;

			if(y) {
			    if(y > (bounds.y/2)) {
			        var scrollTop = Math.floor((bounds.y + y)-100);
			    } else {
			        var scrollTop = Math.floor(Math.max(0, bounds.y - Math.max(20, targetHeight)));
			    }
			} else {
			    var scrollTop = Math.floor(Math.max(0, bounds.y - Math.max(20, targetHeight)));
			}
			graph.container.scrollTop = scrollTop;
			graph.container.scrollLeft = Math.floor(Math.max(0, bounds.x - Math.max(0, targetWidth)));
		}, 0);
    }

    // 대화상자 정보 다운로드
    function getGraphDownload() {
        var html = '<form name="exportGraph" id="exportGraph" action="/" method="post" target="_action_frame_'+module+'">';
        html +='<input type="hidden" name="r" value="'+raccount+'" />';
        html +='<input type="hidden" name="m" value="'+module+'" />';
        html +='<input type="hidden" name="vendor" value="'+vendor+'" />';
        html +='<input type="hidden" name="bot" value="'+bot+'" />';
        html +='<input type="hidden" name="dialog" value="'+dialog+'" />';
        html +='<input type="hidden" name="a" value="do_dialogPanelAction">';
        html +='<input type="hidden" name="linkType" value="graphExport"/>';
        html +='</form>';
        if($('#exportGraph').length == 0) $('body').append(html);
        $('#exportGraph').submit();
    }

    // 검색
    $("#zoomSearch").on("click", function() {
        $("#graphSearch").toggle();
        if($("#graphSearch").css("display") == "none") $("#cell_slist").hide(); //$("#ul_cell_slist").html("");
        else $("[data-role='cell-search']").focus();
    });
    $("#nodeSClose").on("click", function() {
        $("#graphSearch").hide();
        $("#cell_slist").hide();
    });
    $("[data-role='cell-search']").on("keyup", function(e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == 13) {
            var keyword = $.trim($(this).val());
            if(!keyword) return false;
            getSearchCells(keyword);
        }
    });
    $("[data-role='btn-cell-search']").on("click", function(e) {
        var keyword = $.trim($("[data-role='cell-search']").val());
        if(!keyword) return false;
        getSearchCells(keyword);
    });
    $(document).on("click", "#ul_cell_slist li", function() {
        getResetCellBg();
        var cell = graph.model.getCell($(this).attr("cell_id"));
        cell['selected'] = 1;
        graph.model.setStyle(cell, "fillColor="+selectedNodeBg);

        getBoundCenter(cell.geometry.x, cell.geometry.y);

        if($("[data-role='setNodePanel']").hasClass("opened")) {
            graph.setSelectionCell(cell);
            var data ={"act": "openSetNodePanel","nodeObj": cell}; // 패널 열고 해당 node 정보 가져간다.
            initSettingPanel(data); // 패널 동기화
        }
    });
    // 테스트 분석 판넬의 대화상자 링크(응답에서 내려준 node id는 그래프 cell의 id가 아님)
    $(document).on("click", "[data-role='test_node_link']", function() {
        var node_id = $(this).attr("node_id");
        if(!node_id) return false;
        var parent = graph.getDefaultParent();
        var aCells = graph.getChildVertices(parent);
        var cellObj = aCells.filter(function(obj) {
            if(obj.uid == node_id) return obj;
        });
        var cell = graph.model.getCell(cellObj[0].id);
        cell['selected'] = 1;
        graph.model.setStyle(cell, "fillColor="+selectedNodeBg);

        graph.setSelectionCell(cell);
        var data ={"act": "openSetNodePanel","nodeObj": cell}; // 패널 열고 해당 node 정보 가져간다.
        initSettingPanel(data); // 패널 동기화
    });
    function getSearchCells(keyword) {
        $("#cell_slist").hide();
        $("#ul_cell_slist").html("");
        $("[data-role='cell-search']").val("").blur();
        var parent = graph.getDefaultParent();
        var aCells = graph.getChildVertices(parent);
        aCells.filter(function(cell) {
            if(cell.name.indexOf(keyword) > -1) {
                $("#ul_cell_slist").append("<li cell_id='"+cell.id+"'>"+cell.name+"</li>");
            }
        });
        if($("#ul_cell_slist").html()) $("#cell_slist").show();
    }
    function getResetCellBg() {
        var parent = graph.getDefaultParent();
        var aCells = graph.getChildVertices(parent);
        aCells.filter(function(cell) {
            if(cell.is_unknown == 1) {
                graph.model.setStyle(cell, "fillColor="+lastNodeBg);
            } else {
                delete cell['selected'];
                graph.model.setStyle(cell, "fillColor=#fff");
            }
        });
    }
    function getNodeJson() {
        $.post(rooturl+'/?r='+raccount+'&m='+module+'&a=do_dialogPanelAction', {
            linkType: "getNodeJson", vendor: vendor, bot: bot, dialog: dialog
        }, function(data) {
            if(data) {
                console.log(data);
            }
        }, "json");
    }

    $(document).ready(function(){
        var dh = screen.height;
        var height_n = dh - 350; // setNodePanel
        var height_b = dh - 560;
        var height_a = dh - 600; // setApiPanel
        var scroll_n = $('#setNodePanel .panelScroll');
        var scroll_a = $('#setApiPanel .panelScroll');
        var panelScroll = $('.panelScroll');
        panelScroll.css({'height': height_b+'px'});
        scroll_n.css({'height': height_n+'px'});
        scroll_a.css({'height': height_a+'px'});

        // 그래프 영역 높이 재설정
        if($('#graphContainer').length > 0) {
            $("footer").remove();
            $('#graphContainer').css('height', ($(window).height() - ($("#header").outerHeight(true)+$(".navbar-fixed-top").height()) - 50)+'px');
        }

        // 초기함수 실행
        init_func(); // graph 초기화 함수 포함

        // 대화 그래프 출력
        draw(dialogNode);
    });
</script>