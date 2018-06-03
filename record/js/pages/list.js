$(document).ready(function () {

    $.ajax({
        url:"php/record.php?q=getAllRecord",
        success:function (res) {
            let item=JSON.parse(res);
            console.log(item);
            let totalHours=0;
            for(let i in item){
                document.getElementById("c").appendChild(newRecords(item[i]));
                totalHours+= (((new Date(item[i].EndTime)).getTime()
                    -(new Date(item[i].StartTime)).getTime()) /(1000*60*60));

            }
            let node=document.createElement("div");
            node.className="wideCard demo-card-wide mdl-card mdl-shadow--2dp";
            node.innerHTML=" <div class=\"mdl-card__title\">\n" +
                "    <h2 class=\"mdl-card__title-text\">总计时间</h2>\n" +
                "  </div>\n" +
                "  <div class=\"mdl-card__supporting-text\">\n" +
                "    从开始halloway项目开始，我们已经共同度过了"+totalHours.toFixed(2)+"个小时,\n" +
                "希望我们可以继续努力\n" +
                "  </div>\n" +
                "  <div class=\"mdl-card__actions mdl-card--border\">\n" +
                "    <a class=\"mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect\">\n" +
                "     加油！\n" +
                "    </a>\n" +
                "  </div>\n" +
                "  <div class=\"mdl-card__menu\">\n" +
                "    <button class=\"mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect\">\n" +
                "      <i class=\"material-icons\">share</i>\n" +
                "    </button>\n" +
                "  </div>"
            document.getElementById("c").appendChild(node);
        }
    })




});
let colorIndex=0;
let  colors=[
    "#E74C3C  ",
    "#8E44AD  ",
    "#2E86C1  ",
    "#1ABC9C  ",
    "#28B463  ",
    "#D4AC0D  ",
    "#CA6F1E",
    "#839192  ",
    "#283747  ",
];
function newRecords(item) {
    let node=document.createElement("div");
    node.className="demo-card-event mdl-card mdl-shadow--2dp";
    node.style="background:"+colors[(colorIndex++)%(colors.length-1)];
    let total=(((new Date(item.EndTime)).getTime()-(new Date(item.StartTime)).getTime()) /(1000*60*60)).toFixed(2);
    node.innerHTML=" <div class=\"mdl-card__title mdl-card--expand\">\n" +
        "    <h4>\n" +
        "      "+item.Do+"<br>\n" +
        "      From:"+item.StartTime+"<br>\n" +
        "      To:"+item.EndTime+"<br>\n" +

        "    </h4>\n" +
        "  </div>\n" +
        "  <div class=\"mdl-card__actions mdl-card--border\">\n" +
        "    <a class=\"mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect\">\n" +
        "      Total:"+total+"\n" +
        "    </a>\n" +
        "    <div class=\"mdl-layout-spacer\"></div>\n" +
        "    <i class=\"material-icons\">event</i>\n" +
        "  </div>"
    return node;

}