$.fn.fileForm = function(options) {
    var defaultOptions = {
        uploadLimit: 1024 * 1024 * 20, // 20M
        extentions: [ 'hwp', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'gif', 'png', 'txt', 'zip', 'pdf', 'egg' ]
    };

    var that = this;
    var $this = $(this);
    var $fileEl = $this.find("input[type=file]");
    var $fileZone = $this.closest(".input_filezone");

    var settings = $.extend({}, defaultOptions, options);

    var fileKey = 0;
    var _info = {totalFileSize: 0, fileKeys: [], files: {}};
    
    var $tbody = $fileZone.find('.ul_files');

    // 업로드 파일 목록 생성
    function addFileToList(fileKey, fileName, fileSizeStr) {
        var html = '';
        html += '<li id="li_file_' + fileKey + '">';
        html += '  <div class="filename">' + fileName + '</div>';
        html += '  <button type="button" class="fileForm_deleteFile" data-file-key="' + fileKey + '"><i class="fa fa-minus-circle"></i></button>';
        html += '</li>';
        $tbody.append(html);
    }

    // 업로드 파일 삭제
    function deleteFile(fileKey) {
        console.log('deleteFile', fileKey);
        // 전체 파일 사이즈 수정
        _info.totalFileSize -= _info.files[fileKey].fileSize;

        // 파일 정보 삭제
        _info.fileKeys.splice(_info.fileKeys.indexOf(fileKey), 1);
        delete _info.files[fileKey];

        // 업로드 파일 테이블 목록에서 삭제
        $fileZone.find('#li_file_' + fileKey).remove();

        if (_info.fileKeys.length > 0) {
            $fileZone.find('.ul_files').show();
        } else {
            $fileZone.find('.ul_files').hide();
        }
    }

    function addFiles(files) {
        if (files == null) return

        var fileName;
        var fileNameArr;
        var ext;
        var fileSize;

        // 파일 검사
        for (var i = 0; i < files.length; i++) {
            // 파일 이름
            fileName = files[i].name;
            fileNameArr = fileName.split('\.');
            // 확장자
            ext = fileNameArr[fileNameArr.length - 1];
            ext = ext.toLowerCase();

            fileSize = files[i].size; // 파일 사이즈(단위 :byte)
            if ($.inArray(ext, settings.extentions) < 0) {
                // 확장자 체크
                jsModal("alert", '등록이 불가능한 파일 입니다.('+fileName+')').then(function(r) {});
                return false;
                return;
            } else if (fileSizeMb > settings.uploadLimit) {
                // 파일 사이즈 체크
                jsModal("alert", '용량 초과\n업로드 가능 용량 : ' + (settings.uploadLimit / 1024 / 1024) + ' MB').then(function(r) {});
                return false;
            }
        }

        for (var i = 0; i < files.length; i++) {
            // 파일 이름
            fileName = files[i].name;
            fileNameArr = fileName.split('\.');
            // 확장자
            ext = fileNameArr[fileNameArr.length - 1];

            fileSize = files[i].size; // 파일 사이즈(단위 :byte)
            if (fileSize <= 0) {
                console.log(fileName + ' :: 0kb file return');
                continue;
            }

            var fileSizeKb = fileSize / 1024;   // 파일 사이즈(단위 :kb)
            var fileSizeMb = fileSizeKb / 1024; // 파일 사이즈(단위 :Mb)
            var fileSizeStr = '';
            if ((1024*1024) <= fileSize) {  // 파일 용량이 1메가 이상인 경우
                fileSizeStr = fileSizeMb.toFixed(2) + ' Mb';
            } else if ((1024) <= fileSize) {
                fileSizeStr = parseInt(fileSizeKb) + ' kb';
            } else {
                fileSizeStr = parseInt(fileSize) + ' byte';
            }

            // 전체 파일 사이즈
            _info.totalFileSize += fileSizeMb;

            // 파일정보
            _info.fileKeys.push(fileKey);
            _info.files[fileKey] = files[i];
            _info.files[fileKey].fileSize = fileSizeMb;

            // 업로드 파일 목록 생성
            addFileToList(fileKey, fileName, fileSizeStr);

            // 파일 키 증가
            fileKey++;
        }
        
        $tbody.show();
    };
    
    $(document).on('change', $fileEl, function(e) {
        addFiles(e.target.files);
    });
    
    $(document).on('click', '.fileForm_deleteFile', function() {
        var fileKey = $(this).data('fileKey');
        deleteFile(fileKey);
    });

    this.getCheckedFiles = function () {
        var checkedFiles = [];
        $fileZone.find("input:checkbox[name^='fileItemCheck']:checked").each(function(input){
            var fileKey = parseInt($(this).val());
            checkedFiles.push(_info.files[fileKey]);
        });
        return checkedFiles;
    }
    
    this.getFiles = function() {
        return _info.files;
    };

    this.getInfo = function() {
        return _info;
    };

    this.clear = function () {
        fileKey = 0;
        _info = {
            totalFileSize: 0,
            fileKeys: [],
            files: {}
        };
        $tbody.empty().hide();
    }
    $(that).data('fileForm', this);
    return this;
};