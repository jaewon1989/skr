$.fn.sortable = function(opt) {
    var MARGIN_ORDER = ['top', 'right', 'bottom', 'left'];
    var flow = opt.flow || 'vertical';
    var timeout = opt.timeout || 100;
    var container = opt.container || this.parent();
    var wrapWidth;
    var wrapHeight;
    var wrapPadding;
    var elWidth;
    var elHeight;
    var elMargin;
    var elementFilter = opt.filter || function() {
        return true;
    };
    var oriElements = this;
    var placeholder;
    var currentElements;
    var currentStatus;
    var output = {};

    function parseMatrix(arr) {
        var top, right, bottom, left;
        if (!arr || arr.length === 0) {
            top = right = bottom = left = 0;
        } else if (arr.length === 1) {
            top = right = bottom = left = parseInt(arr[0]) || 0;
        } else if (arr.length === 2) {
            top = bottom = parseInt(arr[0]) || 0;
            right = left = parseInt(arr[1]) || 0;
        } else if (arr.length === 3) {
            top = parseInt(arr[0]) || 0;
            right = left = parseInt(arr[1]) || 0;
            bottom = parseInt(arr[2]) || 0;
        } else {
            top = parseInt(arr[0]) || 0;
            right = parseInt(arr[1]) || 0;
            bottom = parseInt(arr[2]) || 0;
            left = parseInt(arr[3]) || 0;
        }
        return {
            top: top,
            right: right,
            bottom: bottom,
            left: left,
            vertical: top + bottom,
            horizontal: left + right
        };
    }

    function createPlaceholder() {
        var placeholder = $('<div class="placeholder"></div>');
        $.each(elMargin, function(key, value) {
            if (key !== 'vertical' && key !== 'horizontal') {
                placeholder.css('margin-' + key, value + 'px');
            }
        });
        placeholder.css('position', 'absolute');
        placeholder.css('display', 'none');
        container.append(placeholder);
        return placeholder;
    }

    function initSize() {
        switch (flow) {
            case 'vertical':
                elWidth = opt.elWidth;
                if (elWidth === 'auto') {
                    elWidth = container.innerWidth() -
                        wrapPadding.horizontal - elMargin.horizontal;
                } else {
                    elWidth = parseInt(elWidth) || 0;
                }
                break;
            case 'horizontal':
                elHeight = opt.elHeight;
                if (elHeight === 'auto') {
                    elHeight = container.innerHeight() -
                        wrapPadding.vertical - elMargin.vertical;
                } else {
                    elHeight = parseInt(elHeight) || 0;
                }
                break;
            case 'v-flow':
                elWidth = parseInt(opt.elWidth) || oriElements.outerWidth();
                elHeight = parseInt(opt.elHeight) || oriElements.outerHeight();
                wrapHeight = container.innerHeight() - wrapPadding.vertical;
                break;
            case 'h-flow':
                elWidth = parseInt(opt.elWidth) || oriElements.outerWidth();
                elHeight = parseInt(opt.elHeight) || oriElements.outerHeight();
                wrapWidth = container.innerWidth() - wrapPadding.horizontal;
                break;
        }
    }

    function initElements() {
        $.each(oriElements, function(i, el) {
            var elPadding = {};
            el = $(el);
            el.data('ori-index', i).data('current-index', i);
            if (flow === 'vertical' && elWidth) {
                elPadding.horizontal = el.outerWidth() - el.width();
                el.css('width', elWidth - elPadding.horizontal + 'px');
            } else if (flow === 'horizontal' && elHeight) {
                elPadding.vertical = el.outerHeight() - el.height();
                el.css('height', elHeight - elPadding.vertical + 'px');
            }
        });
    }

    function initContainer() {
        if (flow === 'vertical' || flow === 'h-flow') {
            container.css('height', currentStatus.totalHeight + 'px');
        } else if (flow === 'horizontal' || flow === 'v-flow') {
            container.css('width', currentStatus.totalWidth + 'px');
        }
    }

    function initStatus(elements) {
        var nextLeft = wrapPadding.left;
        var nextTop = wrapPadding.top;
        var totalWidth = nextLeft;
        var totalHeight = nextTop;
        var result = {};
        var poseList = [];
        $.each(elements, function(i, el) {
            var width = $(el).outerWidth();
            var height = $(el).outerHeight();
            var pose = {
                width: width,
                height: height
            };
            pose.left = nextLeft;
            pose.top = nextTop;
            switch (flow) {
                case 'vertical':
                    pose.vHalf = nextTop + elMargin.top + Math.floor(height / 2);
                    nextTop += height + elMargin.vertical;
                    break;
                case 'horizontal':
                    pose.hHalf = nextLeft + elMargin.left + Math.floor(width / 2);
                    nextLeft += width + elMargin.horizontal;
                    break;
                case 'v-flow':
                    nextTop += elHeight + elMargin.vertical;
                    if (nextTop + elHeight + elMargin.vertical > wrapHeight + wrapPadding.top) {
                        nextTop = wrapPadding.top;
                        nextLeft += elWidth + elMargin.horizontal;
                    }
                    break;
                case 'h-flow':
                    nextLeft += elWidth + elMargin.left + elMargin.right;
                    if (nextLeft + elWidth + elMargin.horizontal > wrapWidth + wrapPadding.left) {
                        nextLeft = wrapPadding.left;
                        nextTop += elHeight + elMargin.vertical;
                    }
                    break;
            }
            poseList[i] = pose;
        });
        result.poseList = poseList;
        switch (flow) {
            case 'vertical':
                totalHeight = nextTop - wrapPadding.top;
                result.totalHeight = totalHeight;
                result.bottom = nextTop;
                break;
            case 'horizontal':
                totalWidth = nextLeft - wrapPadding.left;
                result.totalWidth = totalWidth;
                result.right = nextLeft;
                break;
            case 'v-flow':
                totalWidth = nextLeft + elWidth + elMargin.horizontal - wrapPadding.left;
                result.totalWidth = totalWidth;
                result.right = nextLeft + elWidth + elMargin.horizontal;
                break;
            case 'h-flow':
                totalHeight = nextTop + elHeight + elMargin.vertical - wrapPadding.top;
                result.totalHeight = totalHeight;
                result.bottom = nextTop + elHeight + elMargin.vertical;
                break;
        }
        return result;
    }

    function updateCurrentStatus() {
        currentElements = [];
        $.each(oriElements, function(i, el) {
            var currentIndex = $(el).data('current-index');
            $(el).find('input[name="respondGroup_index[]"]').val(currentIndex);
            currentElements[currentIndex] = el;
        });
        currentStatus = initStatus(currentElements);
        updatePose();
    }

    function updatePose() {
        $.each(currentElements, function(i, el) {
            var pose = currentStatus.poseList[i];
            el = $(el);
            if (el.hasClass('dragging')) {
                el = placeholder;
            }
            el.css('left', pose.left + 'px');
            el.css('top', pose.top + 'px');
        });
    }

    function preventDefault(e) {
        e.preventDefault();
    }

    function drag(e, start, dragTo, finish) {
        var ori = {};
        var win = $(document);
        var target = $(e.target);
        var type = e.type;
        var offset = {};

        function readXY(e, dest) {
            try {
                if (type == 'touchstart') {
                    e = e.originalEvent.touches[0];
                }
                dest.x = e.clientX;
                dest.y = e.clientY;
            } catch (ex) {}
        }

        function move(e) {
            var cur = {};
            e.preventDefault();
            readXY(e, cur);
            offset.x = cur.x - ori.x;
            offset.y = cur.y - ori.y;
            if (!draggingFlag) {
                if (Math.abs(offset.x) + Math.abs(offset.y) > 5) {
                    draggingFlag = true;
                    start(offset);
                }
            } else {
                dragTo(offset);
            }
        }

        function end(e) {
            var cur = {};
            var offset = {};
            readXY(e, cur);
            offset.x = cur.x - ori.x;
            offset.y = cur.y - ori.y;
            if (draggingFlag) {
                e.preventDefault();
                finish(offset);
                setTimeout(function() {
                    draggingFlag = false;
                }, 13);
            }
            if (type == 'touchstart') {
                win.unbind('touchmove', move);
                win.unbind('touchend', end);
            } else {
                win.unbind('mousemove', move);
                win.unbind('mouseup', end);
            }
        }
        draggingFlag = false;
        readXY(e, ori);
        if (type == 'touchstart') {
            win.bind('touchmove', move);
            win.bind('touchend', end);
        } else {
            e.preventDefault();
            win.bind('mousemove', move);
            win.bind('mouseup', end);
        }
    }

    function getMatchVertical() {
        var map = [];
        var lastIndex = 0;
        $.each(currentStatus.poseList, function(index, pose) {
            map[pose.top] = index;
        });
        map[currentStatus.bottom] = currentStatus.poseList.length;
        for (var i = 0; i < map.length; i++) {
            var index = map[i];
            if (index === undefined) {
                map[i] = lastIndex;
            } else {
                lastIndex = index;
            }
        }
        return function(x, y) {
            if (y <= 0) {
                return 0;
            }
            if (y >= map.length) {
                return lastIndex - 1;
            }
            return map[y];
        }
    }

    function getMatchHorizontal() {
        var map = [];
        var lastIndex = 0;
        $.each(currentStatus.poseList, function(index, pose) {
            map[pose.left] = index;
        });
        map[currentStatus.right] = currentStatus.poseList.length;
        for (var i = 0; i < map.length; i++) {
            var index = map[i];
            if (index === undefined) {
                map[i] = lastIndex;
            } else {
                lastIndex = index;
            }
        }
        return function(x, y) {
            if (x <= 0) {
                return 0;
            }
            if (x >= map.length) {
                return lastIndex - 1;
            }
            return map[x];
        }
    }

    function getMatchVFlow() {
        var colWidth = elWidth + elMargin.horizontal;
        var rowHeight = elHeight + elMargin.vertical;
        var rowLength = Math.floor(wrapHeight / rowHeight);
        var colLength = Math.floor(currentStatus.right / colWidth);
        var length = currentStatus.poseList.length;
        var maxLength = rowLength * colLength;
        return function(x, y) {
            var col = Math.floor(x / colWidth);
            var row = Math.floor(y / rowHeight);
            var index = col * rowLength + row;
            if (index >= maxLength || index < 0) {
                return -1;
            } else if (index >= length) {
                return length - 1;
            } else {
                return index;
            }
            return 0;
        };
    }

    function getMatchHFlow() {
        var colWidth = elWidth + elMargin.horizontal;
        var rowHeight = elHeight + elMargin.vertical;
        var colLength = Math.floor(wrapWidth / colWidth);
        var rowLength = Math.floor(currentStatus.bottom / rowHeight);
        var length = currentStatus.poseList.length;
        var maxLength = rowLength * colLength;
        return function(x, y) {
            var col = Math.floor(x / colWidth);
            var row = Math.floor(y / rowHeight);
            var index = col + row * colLength;
            if (index >= maxLength || index < 0) {
                return -1;
            } else if (index >= length) {
                return length - 1;
            } else {
                return index;
            }
            return 0;
        };
    }

    function getMatch() {
        switch (flow) {
            case 'vertical':
                return getMatchVertical();
                break;
            case 'horizontal':
                return getMatchHorizontal();
                break;
            case 'v-flow':
                return getMatchVFlow();
                break;
            case 'h-flow':
                return getMatchHFlow();
                break;
        }
    }

    function move(oldIndex, newIndex) {
        var el;
        if (oldIndex > newIndex) {
            for (var i = newIndex; i < oldIndex; i++) {
                el = $(currentElements[i]);
                el.data('current-index', i + 1);
            }
        } else if (oldIndex < newIndex) {
            for (var i = newIndex; i > oldIndex; i--) {
                el = $(currentElements[i]);
                el.data('current-index', i - 1);
            }
        } else {
            return;
        }
        el = $(currentElements[oldIndex]);
        el.data('current-index', newIndex);
        updateCurrentStatus();
    }

    function dragStart(e) {
        var target = $(this);
        var currentIndex;
        var newIndex;
        var match;
        var startPose;
        var wrapPose = container.offset();
        var pageX = e.pageX - Math.floor(wrapPose.left);
        var pageY = e.pageY - Math.floor(wrapPose.top);
        var timer;

        function updateIndex(x, y) {
            var nextIndex;
            if (match) {
                nextIndex = match(x, y);
                if (nextIndex >= 0 && nextIndex !== newIndex) {
                    newIndex = nextIndex;
                    move(currentIndex, newIndex);
                    currentIndex = newIndex;
                }
            }
        }
        drag(e, function start(offset) {
            match = getMatch();
            currentIndex = target.data('current-index');
            startPose = currentStatus.poseList[currentIndex];
            placeholder.css('left', startPose.left + 'px');
            placeholder.css('top', startPose.top + 'px');
            placeholder.css('width', startPose.width + 'px');
            placeholder.css('height', startPose.height + 'px');
            placeholder.show();
            target.addClass('dragging');
        }, function dragTo(offset) {
            if (flow !== 'vertical') {
                target.css('left', startPose.left + offset.x + 'px');
            }
            if (flow !== 'horizontal') {
                target.css('top', startPose.top + offset.y + 'px');
            }
            clearTimeout(timer);
            timer = setTimeout(function() {
                updateIndex(pageX + offset.x, pageY + offset.y);
            }, timeout);
        }, function finish(offset) {
            clearTimeout(timer);
            target.removeClass('dragging');
            placeholder.hide();
            updateIndex(pageX + offset.x, pageY + offset.y);
            updatePose();
        });
    }
    wrapPadding = parseMatrix(opt.wrapPadding);
    elMargin = parseMatrix(opt.elMargin);
    placeholder = createPlaceholder();
    initSize();
    initElements();
    currentElements = oriElements;
    currentStatus = initStatus(currentElements);
    updatePose();
    initContainer();
    oriElements.bind('mousedown', preventDefault);
    oriElements.filter(elementFilter).bind('mousedown', dragStart).bind('touchstart', dragStart);
    setTimeout(function() {
        container.addClass('ready');
    }, 13);
    output.getOrder = function() {
        var result = [];
        $.each(currentElements, function(i, el) {
            result.push($(el).data('ori-index'));
        });
        return result;
    }
    return output;
};