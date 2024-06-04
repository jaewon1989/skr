const blackListPath = "/interface/internal/blackList";
let isHandlingAction = false,
    initBlackListValue = "",
    blackListUid = "none";

function getBlackListDefaultValue() {

    $.post(blackListPath, {
        mode: "getBlackList",
        botUid: bot
    }, function (response) {
        initBlackListValue = JSON.parse(response).result.blackList;
        JSON.parse(response).result.uid ? blackListUid = JSON.parse(response).result.uid : "none";

        $(".blackListTextArea").val(initBlackListValue);
    }).fail(function (error) {
        const errorResponse = JSON.parse(error.responseText);
        alert(errorResponse.message + " 문제가 발생했습니다.");
    });
}

$(document).on("click", ".blackListSubmitBtn", function () {
    const changedBlackListValue = $(".blackListTextArea").val(),
        mode = blackListUid === "none" ? "insertBlackList" : "updateBlackList",
        postData = {
            mode: mode,
            blackListUid: blackListUid,
            botUid: bot,
            blackList: changedBlackListValue
        };

    if("insertBlackList" === mode && "" === changedBlackListValue) {
        alert("내용을 입력하세요.");
        return;
    }

    if (changedBlackListValue === initBlackListValue) {
        alert("수정된 내용이 없습니다.");
        return;
    }

    if (isHandlingAction) {
        return;
    }

    isHandlingAction = true;
    $.post(blackListPath, postData, function (response) {
        isHandlingAction = false;

        if (response.result) {
            alert("문제가 발생했습니다.");
        } else {
            const alertMsg = {msg: "블랙리스트가 저장되었습니다."};

            showBlackListToast(alertMsg);
            getBlackListDefaultValue();
        }
    }, "json");
});

$(document).ready(function () {
    getBlackListDefaultValue();
});

function showBlackListToast(data) {
    const msg = data.msg,
        hideAfter = 1500,
        hiddenAfter = parseInt(hideAfter) + 5,
        _position = "top-center",
        stack = data.stack;

    $.toast({
        heading: msg,
        position: _position,
        topOffset: 70,
        loaderBg: "#009efb",
        icon: "success",
        hideAfter: hideAfter,
        stack: stack // 중복출력 방지
    });

    setTimeout(function () {
        $.toast().reset("all");
    }, hiddenAfter);
}
