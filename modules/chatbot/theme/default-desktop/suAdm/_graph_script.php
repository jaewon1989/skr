<script type="text/javascript">
    // 글로벌 변수 세팅 
	var mxBasePath = './modules/chatbot/lib/mxDraw/src/';
	var mxBaseUrl = '/modules/chatbot/lib/mxDraw/examples/editors';
	var vertex_width = 110;
	var vertex_height = 40;
	var vertex_name = '대화주제';
	var vendor ='<?php echo $V['uid']?>';
	var bot = '<?php echo $bot?>';
	var botId = '<?php echo $botId?>';
	var dialog = '<?php echo $dialog?>';
	var module = '<?php echo $m?>';
	var dialogSpace = '#dialog-workspace'; // 노드 세팅 패널 
    var sescode ='<?php echo $sescode?>';    
    // dialog 패널에서 사용될 template 최초에 세팅하고 
    // 패널 오픈/클로즈시에는 재설정하지 않는다.
	var template = {};
	var callNode = '<?php echo $callNode?>';   
	var callIntent = '<?php echo $callIntent?>';
	var callEntity = '<?php echo $callEntity?>';
	var callContext = '<?php echo $callContext?>';
	var defaultXml = '<mxGraphModel><root><mxCell id="0"/><mxCell id="1" parent="0"/><mxCell id="2" value="인사" uid="1" parent="0" vertex="1"><mxGeometry y="35" width="110" height="40" as="geometry"/></mxCell></root></mxGraphModel>';
		
</script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/atwho.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/mxDraw/mxClient.js'?>"></script>
<script type="text/javascript" src="<?php echo $g['url_module'].'/lib/js/panel_action.js'?>"></script>


<script type="text/javascript">
// Program starts here. Creates a sample graph in the
// DOM node with the specified ID. This function is invoked
// from the onLoad event handler of the document (see below).
function main(container)
{
  	// Checks if the browser is supported
	if (!mxClient.isBrowserSupported())
	{
		// Displays an error message if the browser is not supported.
		mxUtils.error('Browser is not supported!', 200, false);
	}
	else
	{ 
		// 회전 및 가이드 
		mxVertexHandler.prototype.rotationEnabled = false;
		mxGraphHandler.prototype.guidesEnabled = true;
	    mxGuide.prototype.isEnabledForEvent = function(evt)
	    {
	    	return !mxEvent.isAltDown(evt);
	    };
		mxEdgeHandler.prototype.snapToTerminals = true;
         
  		//mxEvent.disableContextMenu(container);

		var mxCellRendererInstallCellOverlayListeners = mxCellRenderer.prototype.installCellOverlayListeners;
		mxCellRenderer.prototype.installCellOverlayListeners = function(state, overlay, shape)
		{
			mxCellRendererInstallCellOverlayListeners.apply(this, arguments);

			mxEvent.addListener(shape.node, (mxClient.IS_POINTER) ? 'pointerdown' : 'mousedown', function (evt)
			{
				overlay.fireEvent(new mxEventObject('pointerdown', 'event', evt, 'state', state));
			});
			
			if (!mxClient.IS_POINTER && mxClient.IS_TOUCH)
			{
				mxEvent.addListener(shape.node, 'touchstart', function (evt)
				{
					overlay.fireEvent(new mxEventObject('pointerdown', 'event', evt, 'state', state));
				});
			}
		};
		
		// Creates the graph inside the given container
		var graph = new mxGraph(container);
		graph.setPanning(true);
		graph.panningHandler.useLeftButtonForPanning = true;
		graph.setAllowDanglingEdges(true);
		graph.connectionHandler.select = false;
		graph.view.setTranslate(20, 20);
        
       
		// 스타일 설정 
		var style = [];
		style[mxConstants.STYLE_SHAPE] = mxConstants.SHAPE_RECTANGLE;
		style[mxConstants.STYLE_PERIMETER] = mxPerimeter.RectanglePerimeter;
		style[mxConstants.STYLE_STROKECOLOR] = 'gray';
		style[mxConstants.STYLE_ROUNDED] = true;
		style[mxConstants.STYLE_FILLCOLOR] = '#fff';
		//style[mxConstants.STYLE_GRADIENTCOLOR] = '#000';
		style[mxConstants.STYLE_FONTCOLOR] = '#000';
		style[mxConstants.STYLE_ALIGN] = mxConstants.ALIGN_CENTER;
		style[mxConstants.STYLE_VERTICAL_ALIGN] = mxConstants.ALIGN_MIDDLE;
		style[mxConstants.STYLE_FONTSIZE] = '12';
		style[mxConstants.STYLE_FONTSTYLE] = 1;
		graph.getStylesheet().putDefaultVertexStyle(style);

		// Creates the default style for edges
		style = [];
		style[mxConstants.STYLE_SHAPE] = mxConstants.SHAPE_CONNECTOR;
		style[mxConstants.STYLE_STROKECOLOR] = '#999';
		style[mxConstants.STYLE_ALIGN] = mxConstants.ALIGN_CENTER;
		style[mxConstants.STYLE_VERTICAL_ALIGN] = mxConstants.ALIGN_MIDDLE;
		style[mxConstants.STYLE_EDGE] = mxEdgeStyle.ElbowConnector;
		style[mxConstants.STYLE_ENDARROW] = mxConstants.ARROW_CLASSIC;
		graph.getStylesheet().putDefaultEdgeStyle(style); 

		// Enables rubberband selection
		new mxRubberband(graph);

        // 그래프 저장 함수   
		var autoSaveGraph = function(){
            var data = {"act": "saveGraph"}; 
			initSettingPanel(data);
        }

		// 하이라이트 : Adds a highlight on the cell under the mousepointer
		//new mxCellTracker(graph);
		
		// Gets the default parent for inserting new cells. This
		// is normally the first child of the root (ie. layer 0).
		var parent = graph.getDefaultParent();
		var addOverlay = function(cell)
		{
			// Creates a new overlay with an image and a tooltip
			var overlay = new mxCellOverlay(new mxImage('/modules/chatbot/lib/mxDraw/examples/images/add3.png', 20, 20), 'Add outgoing');
			overlay.cursor = 'hand';

			// Installs a handler for clicks on the overlay							
			overlay.addListener(mxEvent.CLICK, function(sender, evt2)
			{
				graph.clearSelection();
				var geo = graph.getCellGeometry(cell);
				var v2;
				
				executeLayout(function()
				{
					
					v2 = graph.insertVertex(parent, 2, vertex_name, geo.x, geo.y, vertex_width, vertex_height);
					v2["bumo"] = cell.uid; // parent 값을 줄 경우 전체 레이아웃이 틀어져서 별도의 bumo 값을 지정해준다.
					//v2["uid"] = parseInt(cell.uid)+1;
					var data = {"act":"addNode","nodeObj":v2,"nodeName":vertex_name,"nodeParent":cell.uid};
					initSettingPanel(data);
			      	addOverlay(v2);
					graph.view.refresh(v2);
					var e1 = graph.insertEdge(parent, null, '', cell, v2);
				}, function()
				{
					graph.scrollCellToVisible(v2);
				});

                // 변경 그래프 자동저장  
				autoSaveGraph();
			});
			
			// Special CMS event
			overlay.addListener('pointerdown', function(sender, eo)
			{
				var evt2 = eo.getProperty('event');
				var state = eo.getProperty('state');
				
				graph.popupMenuHandler.hideMenu();
				graph.stopEditing(false);
				
				var pt = mxUtils.convertPoint(graph.container,
						mxEvent.getClientX(evt2), mxEvent.getClientY(evt2));
				graph.connectionHandler.start(state, pt.x, pt.y);
				graph.isMouseDown = true;
				graph.isMouseTrigger = mxEvent.isMouseEvent(evt2);
				mxEvent.consume(evt2);
			});
			
			// Sets the overlay for the cell in the graph
			graph.addCellOverlay(cell, overlay);
		}

		// 그래프 그리기 
		graph.getModel().beginUpdate();
		var cell;
		var first_node;
		try
		{
            //var xml = '<mxGraphModel><root><mxCell id="0"/><mxCell id="1" parent="0"/><mxCell id="2" value="인사" uid="2" parent="0" vertex="1"><mxGeometry y="35" width="110" height="40" as="geometry"/></mxCell></root></mxGraphModel>';  
          	// Loads the mxGraph file format (XML file)
          	//var xml ='<mxGraphModel><root><mxCell id="0"/><mxCell id="1" parent="0"/><mxCell id="2" value="인사" parent="1" vertex="1"><mxGeometry width="110" height="40" as="geometry"/></mxCell><mxCell id="3" value="대화주제" vertex="1" parent="1" bumo="2"><mxGeometry x="210" width="110" height="40" as="geometry"/></mxCell><mxCell id="4" value="" style="noEdgeStyle=1;orthogonal=1;" edge="1" parent="1" source="2" target="3"><mxGeometry relative="1" as="geometry"><Array as="points"><mxPoint x="122" y="20"/><mxPoint x="198" y="20"/></Array></mxGeometry></mxCell></root></mxGraphModel>';
          	<?php if($R['graph']):?>
		    var xml = '<?php echo $R['graph']?>';
		    <?php else:?>
		    var xml = defaultXml;
		    <?php endif?>
		    var doc = mxUtils.parseXml(xml); // dialog 테이블에서 추출한 값 
            var codec = new mxCodec(doc);
            var elt = doc.documentElement.firstChild.firstChild;
            var cells = [];
            var node = [];
            var i=0;
            
            while (elt != null){  
            	cell = codec.decodeCell(elt);
            	//graph.insertVertex(parent,null,cell.value,0,0,100,40);
                cells.push(cell);
                elt = elt.nextSibling;
                if(!cell.source && !cell.target){
            	    // 추가 버튼
				    addOverlay(cell);  
                    // 노드 추출 
				    if(cell.value){
				        node[i] = cell;	
				        i++;
				    }  
				    
				}
	        }
	    
            // xml 파싱한 노드 출력 
            graph.addCells(cells);

            // 첫번째 노드 지정 ==> 아래 layout.excute() 에서 사용됨 
            first_node = node[0];
		}
		finally
		{
			// Updates the display
			graph.getModel().endUpdate();
		}

		var layout = new mxHierarchicalLayout(graph, mxConstants.DIRECTION_WEST);	
        
        var executeLayout = function(change, post)
		{
			graph.getModel().beginUpdate();
			try
			{
				if (change != null)
				{
					change();
				}
							
    			layout.execute(graph.getDefaultParent(), first_node); // 상단 xml 파싱과정에서 추출 
			}
			catch (e)
			{
				throw e;
			}
			finally
			{
				// New API for animating graph layout results asynchronously
				var morph = new mxMorphing(graph);
				morph.addListener(mxEvent.DONE, mxUtils.bind(this, function()
				{
					graph.getModel().endUpdate();
					
					if (post != null)
					{
						post();
						//autoSaveGraph();
					}
				}));
				
				morph.startAnimation();

			}
		}

		// settingPanel 연동 함수 
		var initSettingPanel = function(data){
			var act = data.act;
			var nodeObj = data.nodeObj;
			var mxAct = data.mxAct===false?false:true;
	  		$(dialogSpace).KRE_Panel({
				module: module,
				vendor: vendor,
				bot: bot,
				botId : botId,
				dialog: dialog,
			    graph: graph, // mxGraph 객체 를 세팅한다.
			    nodeObj: nodeObj,
			    mxData: data,
			    callBackGraph: function(res){
                   var uid = res.uid;
                   if(act=='addNode'){
                   	  nodeObj['uid'] = uid;
                   	  // 변경 그래프 자동저장  
				      autoSaveGraph();
                   	  //console.log(nodeObj);
                   } 
			    },
			    mxAct: mxAct
			});
		    if(data.act =='closeSetNodePanel'){
		    	$('body').find('.tooltip').remove();
		    }
         	//if(data.act!=null) $('#dialog-workspace').tooltip('disable');
			// $(panelEle).find('input[name="node_name"]').val(cell.value);
	        // $(panelEle).find('input[name="nodeId"]').val(cell.id);
		}
        
        // XML 소스보기 
  //       var sourceInput = document.getElementById('source');
  //       mxEvent.addListener(sourceInput, 'click', function()
		// {
		// 	var encoder = new mxCodec();
		// 	var node = encoder.encode(graph.getModel());
		// 	mxUtils.popup(mxUtils.getXml(node), true);
		// }); 

		// Removes cells when [DELETE] is pressed
		var keyHandler = new mxKeyHandler(graph);
		keyHandler.bindKey(46, function(evt)
		{
			
			if(confirm(callNode +'설정내용도 함께 삭제됩니다.')){
				if (graph.isEnabled())
				{
					var del = graph.removeCells();
                    var nodeObj = del[0];
                    console.log(del);
			        var data = {"act": "deleteNode","nodeObj": nodeObj};
			        initSettingPanel(data);
			        setTimeout(function(){
                       autoSaveGraph(); // graph 저장  
			        },10)
			                             
                    
				}
		    }
		});

        // 확대/축소 
        graph.centerZoom = false;
		var zoomIn = document.getElementById('zoomIn');
		var zoomOut = document.getElementById('zoomOut');
        mxEvent.addListener(zoomIn, 'click', function()
		{
			graph.zoomIn();
		}); 
		mxEvent.addListener(zoomOut, 'click', function()
		{
			graph.zoomOut();
		});

		// // Undo/redo
		// var undoManager = new mxUndoManager();
		// var listener = function(sender, evt)
		// {
		// 	undoManager.undoableEditHappened(evt.getProperty('edit'));

		// };
		// graph.getModel().addListener(mxEvent.UNDO, listener);
		// graph.getView().addListener(mxEvent.UNDO, listener);
	
		// var Undo = document.getElementById('Undo');
		// //var Redo = document.getElementById('Redo');
  //       mxEvent.addListener(Undo, 'click', function()
		// {
		// 	undoManager.undo();
		// 	autoSaveGraph(); // 취소된 것 저장 
		// }); 
		// mxEvent.addListener(Redo, 'click', function()
		// {
		// 	undoManager.redo();
		// });
     
  //       // 그래프 소스 저장하기 이벤트 
  //       var saveGraph = document.getElementById('save-graph');
  //       mxEvent.addListener(saveGraph, 'click', function()
		// {
		// 	 autoSaveGraph(); // 소스 저장              
		// });  

        // 노드 클릭 > 패널 열기          
		graph.addListener(mxEvent.CLICK, function(sender, evt)
		{
			if (!mxEvent.isMultiTouchEvent(evt))
			{
				var me = evt.getProperty('event');
				var node = evt.getProperty('cell');
		        var meTargetLocalName = me.target.localName;
				if(meTargetLocalName=='text'||meTargetLocalName=='rect'){
					var data ={"act": "openSetNodePanel","nodeObj": node}; // 패널 열고 해당 node 정보 가져간다.
					//console.log(node);
			    	initSettingPanel(data); // 패널 동기화 
	 			}
			}
		});

		// 노드 컨테이너 클릭 > 패널 닫기 
		mxEvent.addListener(container,'click', function(e)
		{
			if (!mxEvent.isMultiTouchEvent(e))
			{
			    if(e.target.localName=='svg'){
			    	var data = {"act":"closeSetNodePanel"};
					initSettingPanel(data); // 패널 동기화 
			    }
				
			}
		});
        
        // panel_action.js 초기화
        var data ={nodeObj:null,act:null,mxAct:false,initMod:"start"}; 
        initSettingPanel(data); 


	}
};

// 초기함수  
var init_func =function(){
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
}
$(document).ready(function(){
    
    // 초기함수 실행 
    init_func();
   
    // 대화 그래프 출력 
    main(document.getElementById('graphContainer'));

    var dh = screen.height;
    var height_n = dh - 530;
    var height_b = dh - 560;     
    var scroll_n = $('#setNodePanel .panelScroll');
    var panelScroll = $('.panelScroll');
    panelScroll.css({'height': height_b+'px'});
    scroll_n.css({'height': height_n+'px'});

});
</script>	