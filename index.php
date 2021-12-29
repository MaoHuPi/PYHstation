<?php
function DtoT($t){
    return($t[0].$t[1].$t[2].$t[3].'/'.$t[4].$t[5].'/'.$t[6].$t[7]);
}
$day = @$_GET['day'] ? $_GET['day'] : '';
if($day == 'dl'){
    header('Location: download.html');
}
$isDay = 'false';
$darkC = @$_COOKIE['darkC'] ? $_COOKIE['darkC'] : 'false';
$showCheckedTF = @$_COOKIE['showCheckedTF'] ? $_COOKIE['showCheckedTF'] : 'true';
if($day == 'home'){
    $dayT = '';
    $homeworkInner = '';
    $fileNames = scandir(__DIR__.'/json/');
    $ws = array("日","一","二","三","四","五","六");
    foreach(array_reverse($fileNames) as $v){
        if(strpos($v, '.json') == 8){
            $hwDay = str_replace('.json', '', $v);
            $homeworkInner .= '<h2 onclick="location.href = \'../318station/?day='.$hwDay.'\'">'.DtoT($hwDay).'<span style="font-size: 1vw;">（'.$ws[date('w', strtotime(DtoT($hwDay)))].'）</span></h2>';
        }
    }
    $noteInner = '';
    $ggInner = '';
    $pgU = 'false';
    $pgD = 'false';
    $isDay = 'home';
    $webTitle = '首頁';
}
else{
    if($day == 'today'){
        header('Location: ../318station/?day='.implode('', explode('-', date("Y-m-d"))));
    }
    if(strlen($day) == 8){
        $dayT = DtoT($_GET['day']);
        if(is_file('json/'.$_GET['day'].'.json')){
            $todayList = fopen('json/'.$_GET['day'].'.json', 'rb');
            $content = "";
            while (!feof($todayList)) {
                $content .= fread($todayList, 10000);
            }
            fclose($todayList);
            $todayList = json_decode($content,true);
            $homeworkInner = '';
            $noteInner = '';
            $ggInner = '';
            foreach($todayList['homework'] as $k => $v){
                $homeworkInner .= '<oRow><h2>'.$k.'</h2>';
                foreach($v as $w){
                    $homeworkInner .= '<hWRow>';
                    if($w['new'] == true){
                        $homeworkInner .= '<div class="new">new</div>';
                    }
                    $homeworkInner .= '<a href="'.$w['link'].'" target="_blank" class="hWA">'.$w['name'].'</a><a class="deadline">截止時間: '.$w['deadline'].'</a><input type="checkbox" onchange="checkAll()" data-hw="'.$w['name'].'"><br></hWRow>';
                }
                $homeworkInner .= '</oRow>';
            }
            foreach($todayList['note'] as $v){
                $noteInner .= '<h3>'.$v['name'].'</h3><div class="nIB">';
                foreach($v['src'] as $w){
                    $noteInner .= '<div style="background-image: url('.$w.'); background-size: cover;" class="noteImage" title="'.$v['name'].'-筆記圖片" onclick="window.open(\''.$w.'\')"></div>';
                }
                $noteInner .= '</div><hr>';
            }
            foreach($todayList['matter'] as $v){
                $ggInner .= '<h3>'.$v.'</h3>';
            }
            $pgU = 'false';
            $pgD = 'false';
            // for($i = 31; $i > 0; $i--){
            //     $d = (string)((int)$_GET['day']-$i);
            //     if(is_file('json/'.$d.'.json')){
            //         $pgU = '../318station/?day='.$d;
            //     }
            //     $d = (string)((int)$_GET['day']+$i);
            //     if((int)($_GET['day'][6].$_GET['day'][7]) < $md[$_GET['day'][4].$_GET['day'][5]] && is_file('json/'.$d.'.json')){
            //         $pgD = '../318station/?day='.$d;
            //     }
            //     $d = (string)((int)$_GET['day']+$i-$md[$_GET['day'][4].$_GET['day'][5]]+100);
            //     if((int)((string)((int)$_GET['day']+$i)[6].(string)((int)$_GET['day']+$i)[7]) > $md[$_GET['day'][4].$_GET['day'][5]]){
            //     echo $d;
            //     }
            //     if((int)($_GET['day'][6].$_GET['day'][7]) > $md[$_GET['day'][4].$_GET['day'][5]] && is_file('json/'.$d.'.json')){
            //         $pgD = '../318station/?day='.$d;
            //     }
            // }
            $fileNames = scandir(__DIR__.'/json/');
            $i = -1;
            foreach($fileNames as $v){
                $i++;
                if(strpos($v, '.json') == 8 && str_replace('.json', '', $v) == $_GET['day']){
                    break;
                };
            }
            if($i > -1){
                if($i > 0){
                    $pgU = '../318station/?day='.str_replace('.json', '', $fileNames[$i - 1]);
                }
                if($i < count($fileNames)-1){
                    $pgD = '../318station/?day='.str_replace('.json', '', $fileNames[$i + 1]);
                }
            }
            $isDay = 'day';
            $webTitle = $dayT.' 聯絡簿';
        }
        else{
            $dayT = '';
            $homeworkInner = '';
            $noteInner = '';
            $ggInner = '';
            $pgU = 'false';
            $pgD = 'false';
            $isDay = 'false';
            $webTitle = '錯誤';
        }
    }
    else{
        $dayT = '';
        $homeworkInner = '';
        $noteInner = '';
        $ggInner = '';
        $pgU = 'false';
        $pgD = 'false';
        $isDay = 'false';
        $webTitle = '錯誤';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>::318station:: [<?=$webTitle?>]</title>
    <link rel="icon" href="image/318.ico">
    <style>
        /* @font-face {
            font-family: PTSans;
            src: url('PTSans-Regular.ttf');
        } */
        * {
            margin: 0px;
            border: none;
            outline: none;
            cursor: default;
            font-family: auto;
        }
        *::selection {
            background-color: #e7b88333;
            text-shadow: 0px 0px 0.1vw #e7b88366;
        }
        a {
            text-decoration: none;
        }
        [href=~] {
            cursor: pointer;
        }
        html, body {
            margin: 0px;
            padding: 0px;
            background-color: #e7b883;
        }
        #pageBody, #pageNotfound, #pageHome {
            display: grid;
            width: 80vw;
            grid-gap: 5vw;
            margin: 10vh 6.5vw;
            box-sizing: border-box;
            z-index: 1;
        }
        h1 {
            text-align: center;
            font-size: 3vw;
            user-select: none;
        }
        h2 {
            margin: 1.5vw 0px 0.5vw 0px;
            font-size: 2.5vw;
            user-select: none;
        }
        h3 {
            margin: 1.5vw 0px 0.5vw 0px;
            font-size: 1.5vw;
        }
        .hWA {
            margin: 0.5vw 0px;
            font-size: 2vw;
        }
        .box {
            background-color: #ffffff;
            border-radius: 1vw;
            border-color: #5f4323;
            border-style: solid;
            box-sizing: border-box;
            padding: 1vw;
            position: relative;
        }
        .box * {
            color: #5f4323;
        }
        .box .deadline {
            right: 3vw;
            position: absolute;
            font-size: 1vw;
        }
        hr {
            background-color: #5f4323;
            border: none;
            border-radius: 100vw;
            height: 0.1vw;
        }
        hr.bold {
            height: 0.5vw;
        }
        input[type="checkbox"] {
            right: 1vw;
            position: absolute;
        }
        .noteImage {
            display: inline-block;
            height: 5.5vw;
            width: 5.5vw;
            margin: 0.1vw;
        }
        #pageTitle {
            position: fixed;
            top: 1vw;
            left: -1vw;
            padding: 1vw 1vw 1vw 2vw;
            font-size: 2vw;
            border-radius: 0px 100vw 100vw 0px;
            color: #ffffff;
            background-color: #5f4323;
            z-index: 2;
            box-shadow: 0px 0px 0.8vw 0px #000000;
            user-select: none;
        }
        .new {
            position: absolute;
            left: -2vw;
            background-color: #5f4323;
            color: white;
            border-radius: 0.5vw;
            padding: 0.2vw;
            font-size: 1vw;
            margin: 0.3vw 0px 0px 0px;
            user-select: none;
        }
        #pGD * {
            user-select: none;
        }
        #pageNotfound {
            grid-auto-rows: auto;
            grid-auto-columns: 85vw;
        }
        #alertMask {
            opacity: 0;
            pointer-events: none;
            position: fixed;
            top: 0px;
            left: 0px;
            height: 100vh;
            width: 100vw;
            z-index: 10;
            background-color: #00000088;
            transition: 0.5s;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #alertBox {
            width: 50vw;
        }
        #cCvs {
            border-radius: 1vw;
            margin: 1vw 0px 0px 0px;
        }
        .boxBtn {
            height: 2vw;
            width: 4vw;
            /* font-size: 1.2vw; */
            position: absolute;
            top: -1vw;
            right: -2vw;
            background-color: #e7b883;
            border-radius: 100vw;
            border-color: #5f4323;
            border-style: solid;
            box-sizing: border-box;
            padding: 0px auto;
        }
        .boxBtn * {
            margin: 0px;
        }
        [hWRHidden="true"] {
            display: none;
        }
    </style>
    <style>
        @media (min-width: 401px) {
            #pageBody {
                grid-auto-rows: auto auto auto;
                grid-auto-columns: 60vw 20vw;
            }
            #pageHome {
                grid-auto-rows: auto;
                grid-auto-columns: 60vw 20vw;
            }
            #hWD, #cD {
                grid-area: 1/1/2/2;
            }
            #gGD {
                grid-area: 2/1/3/2;
            }
            #nD {
                grid-area: 1/2/3/3;
            }
            #hD {
                grid-area: 1/2/2/3;
            }
            #pGD {
                grid-area: 3/1/4/3;
            }
        }
        </style>
    <style>
        @media (max-width: 400px) {
            #pageBody {
                grid-auto-rows: auto auto auto auto;
                grid-auto-columns: 85vw;
            }
            #pageHome {
                grid-auto-rows: auto auto;
                grid-auto-columns: 85vw;
            }
            #hWD, #cD {
                grid-area: 1/1/2/2;
            }
            #gGD {
                grid-area: 3/1/4/2;
            }
            #nD, #hD {
                grid-area: 2/1/3/2;
            }
            #pGD {
                grid-area: 4/1/5/2;
            }
        }
    </style>
</head>
<body>
    <div id="alertMask">
        <div id="alertBox" class="box">
            <h1 id="alertTitle"></h1>
            <hr class="bold">
            <div id="alertInfo">
            </div>
        </div>
    </div>
    <div id="pageTitle" onclick="goHome();">318 station</div>
    <div id="pageNotfound">
        <div class="box">
            <h1>找不到頁面</h1>
            <hr class="bold">
            <div>
                <h2>請確認您的參數［<?=$day?>］無誤！</h2>
            </div>
        </div>
    </div>
    <div id="pageHome">
        <div class="box" id="cD">
            <h1>318課表</h1>
            <button class="boxBtn" onclick="DandN()" title="對課表進行顏色反向處理">切換</button>
            <hr class="bold">
            <div>
                <canvas id="cCvs" style="width: 100%;"></canvas>
            </div>
        </div>
        <div class="box" id="hD">
            <h1>聯絡簿</h1>
            <hr class="bold">
            <div>
                <?=$homeworkInner?>
            </div>
        </div>
    </div>
    <div id="pageBody">
        <div class="box" id="hWD">
            <h1><?=$dayT?> 聯絡簿</h1>
            <button class="boxBtn" onclick="showChecked()" title="顯示/隱藏已打勾的項目">切換</button>
            <hr class="bold">
            <div id="hWRBox">
                <?=$homeworkInner?>
            </div>
        </div>
        <div class="box" id="gGD">
            <h1>公告事項</h1>
            <hr class="bold">
            <div>
                <?=$ggInner?>
            </div>
        </div>
        <div class="box" id="nD">
            <h1>課堂筆記</h1>
            <hr class="bold">
            <div>
                <?=$noteInner?>
            </div>
        </div>
        <div class="box" id="pGD">
            <div>
                <h3 data-href="<?=$pgU?>" title="前往上一篇" data-error="找不到上一頁" id="pgu" style="position: absolute; left: 1vw; display: inline-block; margin: 0px;">&lt;&lt;上一篇</h3>
                <h3 title="此篇為<?=$dayT?>" style="display: inline-block; margin: 0px; text-align: center; width: 100%;">～<?=$dayT?>～</h3>
                <h3 data-href="<?=$pgD?>" title="前往下一篇" data-error="找不到下一頁" id="pgd" style="position: absolute; right: 1vw; display: inline-block;  margin: 0px;">下一篇&gt;&gt;</h3>
            </div>
        </div>
    </div>
    <script>
        function $(e, f = document){return(f.querySelector(e));}
        function $$(e, f = document){return(f.querySelectorAll(e));}
        var c = {}
        try{
            c = JSON.parse(`<?=json_encode($_COOKIE, true)?>`);
        }
        catch(e){}
        let darkC = '<?=$darkC?>'
        let showCheckedTF = '<?=$showCheckedTF?>'
        const classrooms = {
            "彈性": "https://classroom.google.com/c/MzY3MzQ2ODQ3OTA5", 
            "國文": "https://classroom.google.com/c/MzczMDY0MzMwNjY0", 
            "數學": "https://classroom.google.com/c/MzcyNzU4OTMwMjc4", 
            "生物": "https://classroom.google.com/c/MzcyOTMxOTcxMzk2", 
            "理化": "https://classroom.google.com/c/MzczMDg4Mjk1MTA5", 
            "地科": "https://classroom.google.com/c/MzcxMDg2OTc5Mzg1", 
            "歷史": "https://classroom.google.com/c/MzcxMDIwMzM4NDYx", 
            "地理": "https://classroom.google.com/c/MzczMDQ0MTEyMzAy", 
            "公民": "https://classroom.google.com/c/MzY0NzU4NDYwNzc2", 
            "英語": "https://classroom.google.com/c/MzczMDY2MjUzMDE4", 
            "體育": "https://classroom.google.com/c/MzczMjc2NDU5Njc1", 
            "資訊": "https://classroom.google.com/c/MzcyODU1MDQ5MzIy"
        }
        const meets = {
            "彈性": "https://meet.google.com/lookup/grzyjlw3mm", 
            "國文": "https://meet.google.com/lookup/gzpvabymzy", 
            "數學": "https://meet.google.com/lookup/errt54od4d", 
            "生物": "https://meet.google.com/lookup/bawdxm62do", 
            "理化": "https://meet.google.com/bzm-qdsb-ywf", 
            "地科": "https://meet.google.com/lookup/e27wcvt5eo", 
            "歷史": "https://meet.google.com/lookup/akyp7wwacc", 
            "地理": "https://meet.google.com/lookup/aviijnah5o", 
            "公民": "https://meet.google.com/lookup/gspo3agpyx", 
            "英語": "https://meet.google.com/lookup/hljdl5agzj", 
            "體育": "https://meet.google.com/lookup/f5lkr73zud", 
            "資訊": "https://meet.google.com/lookup/aybxerh5jl"
        }
        let c_src = 'image/c.jpg';
        let xs = [17.966101694915253, 33.898305084745760, 49.830508474576280, 65.762711864406780, 81.58192090395481, 97.51412429378531];
        let ys = [23.089983022071305, 30.89983022071307, 38.79456706281834, 46.6044142614601, 54.58404074702886, 56.02716468590832, 63.83701188455009, 71.81663837011885, 79.71137521222411];
        let cList = {
            "00":"彈性", "01":"英語", "02":"地理", "03":"歷史", "05":"國文", "06":"數學", "07":"地科", 
            "10":"彈性", "11":"英語", "12":"公民", "13":"地科", "15":"資訊", "16":"歷史", "17":"數學", 
            "20":"彈性", "21":"公民", "22":"英語", "23":"英語", "25":"國文", "26":"理化", "27":"數學", 
            "30":"彈性", "31":"英語", "32":"生物", "33":"地理", "35":"體育", "36":"數學", "37":"國文", 
            "40":"彈性", "41":"理化", "42":"理化", "43":"生物", "45":"數學", "46":"國文", "47":"國文"
        };
        let c_test = false;
        if(true){
            c_src = 'image/nc.jpg';
            xs = [23.26388888888889, 35.76388888888889, 48.4375, 61.111111111111114, 73.61111111111111, 85.9375];
            ys = [21.58365261813538, 29.374201787994892, 37.03703703703704, 44.827586206896555, 52.61813537675607, 53.63984674329502, 63.601532567049816, 71.51979565772669, 79.18263090676884];
            cList = {
                "00":"彈性", "01":"英語", "02":"地理", "03":"歷史", "05":"國文", "06":"數學", "07":"地科", 
                "10":"彈性", "11":"英語", "12":"公民", "13":"地科", "15":"資訊", "16":"歷史", "17":"數學", 
                "20":"彈性", "21":"公民", "22":"英語", "23":"英語", "25":"國文", "26":"理化", "27":"國文", 
                "30":"彈性", "31":"英語", "32":"生物", "33":"地理", "35":"體育", "36":"數學", "37":"數學", 
                "40":"彈性", "41":"理化", "42":"理化", "43":"生物", "45":"數學", "46":"國文", "47":"國文"
            };
        }
        const $_COOKIE = c;
        const $_GET = {};
        const page = '<?=$isDay?>';
        let url = location.href;
        let value = "";
        var cvs = ctx = false;
        var MX = 0;
        var MY = 0;
        if(url.indexOf('?') != -1){
            let ary = url.split('?')[1].split('&');
            for(let i = 0; i < ary.length; i++){
                let k = ary[i].split('=')[0];
                let v = ary[i].split('=')[1];
                $_GET[k] = v;
            }
        }
        function checkAll(){
            let checkboxValue = {};
            for(i = 0; i < $$('input[type="checkbox"]').length; i++){
                checkboxValue[$$('input[type="checkbox"]')[i].getAttribute('data-hw')] = $$('input[type="checkbox"]')[i].checked;
            }
            document.cookie = `${$_GET['day']}checkboxValue = ${JSON.stringify(checkboxValue).split('"').join('<ii>')}`
        }
        try{
            var jspc = JSON.parse($_COOKIE[`${$_GET['day']}checkboxValue`].split('<ii>').join('"'))
        }
        catch(e){}
        for(i = 0; i < $$('input[type="checkbox"]').length; i++){
            try{
                if($$('input[type="checkbox"]')[i].getAttribute('data-hw') in jspc && jspc[$$('input[type="checkbox"]')[i].getAttribute('data-hw')] == true){
                    $$('input[type="checkbox"]')[i].setAttribute('checked', '');
                }
            }
            catch(e){}
        }
        function pgEL(e){
            if(e.getAttribute('data-href') == 'false'){
                $('#pGD > div').removeChild(e);
            }
            e.addEventListener('click', function(){
                if(this.getAttribute('data-href') == 'false'){
                    alert(`錯誤！\n(${this.getAttribute('data-error')})`)
                }
                else{
                    location.href = this.getAttribute('data-href')
                }
            })
        }
        pgEL($('#pgu'));
        pgEL($('#pgd'));
        if(page == 'home'){
            document.body.removeChild($('#pageBody'));
            document.body.removeChild($('#pageNotfound'));
            cvsInit();

        }
        else if(page == 'day'){
            document.body.removeChild($('#pageHome'));
            document.body.removeChild($('#pageNotfound'));
        }
        else{
            document.body.removeChild($('#pageHome'));
            document.body.removeChild($('#pageBody'));
        }
        function cvsInit(){
            cvs = $('#cCvs');
            ctx = cvs.getContext('2d');
            var c = new Image;
            c.src = c_src;
            c.onload = () => {
                cvs.width = c.width;
                cvs.height = c.height;
                ctx.drawImage(c, 0, 0, cvs.width, cvs.height);
                if(darkC == 'true'){
                    let p = ctx.getImageData(0, 0, cvs.width, cvs.height);
                    let d = p.data;
                    for(i = 0; i < d.length; i += 4){
                        d[i] = 255-d[i];
                        d[i+1] = 255-d[i+1];
                        d[i+2] = 255-d[i+2];
                    }
                    ctx.putImageData(p, 0, 0);
                }
            }
            cvs.addEventListener('click', function(e){
                let x = (MX - offset(this)['left'])/offset(this)['width']*100;
                let xx = 'false';
                for(let i = 0; i < xs.length; i++){
                    if(x > xs[i] && x < xs[i+1]){
                        xx = i;
                        break;
                    }
                }
                let y = (MY - offset(this)['top'])/offset(this)['height']*100;
                let yy = 'false'
                for(let i = 0; i < ys.length; i++){
                    if(y > ys[i] && y < ys[i+1]){
                        yy = i;
                        break;
                    }
                }
                if(xx != 'false' && yy != 'false' && yy != 4 && !c_test){
                    alert(cList[xx+''+yy]);
                }
                else if(c_test){
                    console.log(x, y);
                }
            });
        }
        document.addEventListener('contextmenu', function(e){
            e.stopPropagation();
            e.preventDefault();
        });
        function goHome(){
            if(page != 'home'){
                location.href = '../318station/?day=home';
            }
        }
        document.addEventListener('mousemove', (e) => {
            MX = e.pageX;
            MY = e.pageY;
        });
        function offset(e){
            var l = 0;
            var t = 0;
            var ef = e;
            while(ef != document.body){
                l += ef.offsetLeft;
                t += ef.offsetTop;
                ef = ef.offsetParent;
            }
            return({'left':l, 'top':t, 'width':e.offsetWidth, 'height':e.offsetHeight});
        }
        function alert(c){
            $('#alertMask').style.opacity = '1';
            $('#alertMask').style.pointerEvents = 'auto';
            $('#alertTitle').innerText = c;
            $('#alertInfo').innerHTML = `
                <h2>GoogleClassroom:<a href="${classrooms[c]}">點我</a></h2>
                <h2>GoogleMeet:<a href="${meets[c]}">點我</a></h2>
            `
        }
        $('#alertMask').addEventListener('click', () => {
            $('#alertMask').style.opacity = '0';
            $('#alertMask').style.pointerEvents = 'none';
        });
        $('#alertBox').addEventListener('click', (e) => {
            e.stopPropagation();
        });
        function DandN(){
            let p = ctx.getImageData(0, 0, cvs.width, cvs.height);
            let d = p.data;
            for(i = 0; i < d.length; i += 4){
                d[i] = 255-d[i];
                d[i+1] = 255-d[i+1];
                d[i+2] = 255-d[i+2];
            }
            ctx.putImageData(p, 0, 0);
            if(darkC == 'true'){
                darkC = 'false';
            }
            else{
                darkC = 'true';
            }
            document.cookie = 'darkC = '+darkC;
        }
        function showChecked(){
            if(showCheckedTF == 'false'){
                for(let r of $$('#hWRBox *')){
                    r.removeAttribute('hWRHidden');
                }
                showCheckedTF = 'true';
                document.cookie = 'showCheckedTF = true';
            }
            else{
                for(let r of $$('hWRow')){
                    if($('input[type="checkbox"]', r).checked == true){
                        r.setAttribute('hWRHidden', 'true');
                    }
                }
                for(let r of $$('oRow')){
                    let rrH = 0;
                    for(rr of $$('hWRow', r)){
                        if(rr.getAttribute('hWRHidden') == 'true'){
                            rrH++;
                        }
                    }
                    if(rrH == $$('hWRow', r).length){
                        r.setAttribute('hWRHidden', 'true');
                    }
                }
                showCheckedTF = 'false';
                document.cookie = 'showCheckedTF = false';
                checkedHiddenner();
            }
        }
        function checkedHiddenner(){
            if(showCheckedTF == 'false'){
                for(let r of $$('hWRow')){
                    if($('input[type="checkbox"]', r).checked == true){
                        r.setAttribute('hWRHidden', 'true');
                    }
                }
                for(let r of $$('oRow')){
                    let rrH = 0;
                    for(rr of $$('hWRow', r)){
                        if(rr.getAttribute('hWRHidden') == 'true'){
                            rrH++;
                        }
                    }
                    if(rrH == $$('hWRow', r).length){
                        r.setAttribute('hWRHidden', 'true');
                    }
                }
                setTimeout(() => {
                    checkedHiddenner();
                }, 30);
            }
        }
        checkedHiddenner();
        // if(!confirm('318station已不再更新，\n確認繼續訪問318station？')){
        //     location.href = 'http://school1.nssh.ntpc.edu.tw/modules/tad_web/index.php?WebID=95';
        // }
    </script>
</body>
</html>