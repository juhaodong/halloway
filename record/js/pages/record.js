function getDate() {
    var today = new Date();
    var date = today.getFullYear() + " " + twoDigits(today.getMonth() + 1) + " " + twoDigits(today.getDate());
    var week = "";
    var time = twoDigits(today.getHours()) + ":" + twoDigits(today.getMinutes()) ;
    $(".date").html("当前时间：<br>"+ date + week+"<br>" + time);
}
function twoDigits(val) {
    if (val < 10) return "0" + val; return val;
}
$(function () {
    getDate();
    setInterval(getDate, 10000);
});
let Fid=0;
function startLog() {
    var today = new Date();
    var time = twoDigits(today.getHours()) + ":" + twoDigits(today.getMinutes()) ;
    document.getElementById("welcome").style.display="none";
    document.getElementById("log").style.display="";
    document.getElementById("startTime").innerText="开始时间:" +time;
    $.ajax({
        url:"php/record.php",
        data:{
            q:"setLog"
        },
        method:"GET",
        success:function (res) {
           let id=JSON.parse(res);
        Fid=  Object.values(id[0])[0];

        }

    })
}
function endLog(e) {
    e.preventDefault();
    document.getElementById("welcome").style.display="";
    document.getElementById("log").style.display="none";
    $.ajax({
        url:"php/record.php?q=finishLog",
        data:{
            Do:document.getElementById("do").value,
            idRecord:Fid
        },
        method:"POST",
        success:function (res) {
            console.log(res);
        }
    })
}