<?php
$VERSION = '2.0.0';
$TITLE = 'PYH station';
$AUTHOR = 'MaoHuPi';
$DES = '普一和臨時班網';
$IMAGE = 'image/pyh.png';

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
if($day == 'home' || $day == ''){
    $dayT = '';
    $homeworkInner = '';
    $fileNames = scandir(__DIR__.'/json/');
    $ws = array("日","一","二","三","四","五","六");
    $homeworkLinkNum = 0;
    foreach(array_reverse($fileNames) as $v){
        if(strpos($v, '.json') == 8){
            $homeworkLinkNum++;
            $hwDay = str_replace('.json', '', $v);
            $homeworkInner .= '<h2 onclick="location.href = \'?day='.$hwDay.'\'">'.DtoT($hwDay).'<span style="font-size: 1vw;">（'.$ws[date('w', strtotime(DtoT($hwDay)))].'）</span></h2>';
        }
        if($homeworkLinkNum >= 10){
            break;
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
        header('Location: ?day='.implode('', explode('-', date("Y-m-d"))));
    }
    if(strlen($day) == 8){
        $dayT = DtoT($_GET['day']);
        if(is_file('json/'.$day.'.json')){
            $todayList = fopen('json/'.$day.'.json', 'rb');
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
                    $hasLinkLink = isset($w['link']) && $w['link'] != '';
                    $hasAnswerLink = isset($w['answer']) && $w['answer'] != '';
                    $homeworkElement = in_array(true, [$hasLinkLink, $hasAnswerLink]) ? '<a onclick="alert(\''.$w['name'].'\', ['.
                        ($hasLinkLink ? '{name: \'Link\', link: \''.$w['link'].'\'}, ' : '').
                        ($hasAnswerLink ? '{name: \'Answer\', link: \''.$w['answer'].'\'}, ' : '').
                        '])" target="_blank" class="hWA hasLink">'.$w['name'].'</a>' : '<a class="hWA">'.$w['name'].'</a>';
                    $deadlineElement = isset($w['deadline']) && $w['deadline'] != '' ? '<a class="deadline">'.$w['deadline'].'</a>' : '';
                    $homeworkInner .= $homeworkElement.$deadlineElement.'<input type="checkbox" onchange="checkAll()" data-hw="'.$w['name'].'"><br></hWRow>';
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
            //         $pgU = '?day='.$d;
            //     }
            //     $d = (string)((int)$_GET['day']+$i);
            //     if((int)($_GET['day'][6].$_GET['day'][7]) < $md[$_GET['day'][4].$_GET['day'][5]] && is_file('json/'.$d.'.json')){
            //         $pgD = '?day='.$d;
            //     }
            //     $d = (string)((int)$_GET['day']+$i-$md[$_GET['day'][4].$_GET['day'][5]]+100);
            //     if((int)((string)((int)$_GET['day']+$i)[6].(string)((int)$_GET['day']+$i)[7]) > $md[$_GET['day'][4].$_GET['day'][5]]){
            //     echo $d;
            //     }
            //     if((int)($_GET['day'][6].$_GET['day'][7]) > $md[$_GET['day'][4].$_GET['day'][5]] && is_file('json/'.$d.'.json')){
            //         $pgD = '?day='.$d;
            //     }
            // }
            $fileNames = scandir(__DIR__.'/json/');
            $fileNames = array_filter($fileNames, static function($v){
                return(strpos($v, '.json') == 8);
            });
            $i = array_search($day.'.json', $fileNames);
            if($i > -1){
                if(array_key_exists($i - 1, $fileNames)){
                    $pgU = '?day='.str_replace('.json', '', $fileNames[$i - 1]);
                }
                if(array_key_exists($i + 1, $fileNames)){
                    $pgD = '?day='.str_replace('.json', '', $fileNames[$i + 1]);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta itemprop="name" content="<?=$TITLE?>">
    <meta itemprop="description" content="<?=$DES?>">
    <meta itemprop="image" content="<?=$IMAGE?>">
    <!-- twitter -->
    <meta name="twitter:title" content="<?=$TITLE?>">
    <meta name="twitter:card" content="<?=$TITLE?>">
    <meta name="twitter:creator" content="@maohupi">
    <meta name="twitter:site" content="@maohupi">
    <meta name="twitter:description" content="<?=$DES?>">
    <meta name="twitter:image:src" content="<?=$IMAGE?>">
    <!-- open graph  -->
    <meta property="og:title" content="<?=$TITLE?>"/>
    <meta property="og:site_name" content="<?=$AUTHOR?>"/>
    <meta property="og:description" content="<?=$DES?>"/>
    <meta property="og:type" content="educate"/>
    <meta property="og:image" content="<?=$IMAGE?>"/>
    <meta property="og:url" content="https://maohupi.riarock.com/web/tool/pyhstation/"/>
    <title>::<?=$TITLE?>:: [<?=$webTitle?>]</title>
    <link rel="shortcut icon" href="image/pyh.ico" type="image/x-icon"/>
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
        *::-webkit-scrollbar {
            background-color: transparent;
        }
        *::-webkit-scrollbar-thumb {
            border-radius: 100vw;
            background-color: #5f4323;
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
            overflow-x: hidden;
            overflow-y: auto;
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
        .hasLink {
            text-decoration: underline wavy;
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
            margin-top: 2vw;
            right: 3vw;
            position: absolute;
            opacity: 0.5;
            font-size: 1vw;
            font-weight: bold;
        }
        .box .deadline::before {
            content: '截止時間: ';
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
            margin-top: 1vw;
            right: 1vw;
            position: absolute;
            filter: hue-rotate(195deg);
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
    </style>
    <style>
        @media (max-width: 768px) {
            h1 {
                font-size: 6vw;
            }
            h2 {
                font-size: 5vw;
            }
            h3 {
                font-size: 4vw;
            }
            .hasLink {
                text-decoration: underline;
            }
            .hWA {
                font-size: 4vw;
            }
            .noteImage {
                margin: 0.3vw;
                width: 12vw;
                height: 12vw;
            }
            .new {
                padding: 0.2vw 1vw;
                left: -6vw;
                opacity: 0.5;
                font-size: 3vw;
            }
            .boxBtn {
                height: 8vw;
                width: 15vw;
                top: 0.5vw;
                right: 0.5vw;

                top: -4vw;
                right: -7.5vw;
                border-radius: 1vw;
                font-size: 3vw;
                font-weight: bold;
            }
            .box .deadline {
                font-size: 3vw;
                /* top: -0.5vw; */
                opacity: 0.5;
            }
            .box .deadline::before {
                content: '';
            }
            input[type="checkbox"] {
                margin-top: 1vw;
                right: -2vw;
            }
            #pageTitle {
                padding: 1vw 4vw 1.5vw 4vw;
                top: 3vw;
                font-size: 6vw;
            }
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
            @media (min-width: 500px) {
                input[type="checkbox"] {
                    margin-top: 2vw;
                    right: 0.5vw;
                }
                #pageTitle {
                    font-size: 4vw;
                    top: 0px;
                    left: 0px;
                    padding: 1vw;
                    border-radius: 0px 0px 2vw 0px;
                }
            }
        }
        @media (prefers-color-scheme: dark) {
            html {
                /* filter: hue-rotate(180deg); */
            }
            *::selection {
                background-color: #95c4f933;
                text-shadow: 0px 0px 0.1vw #95c4f966;
            }
            *::-webkit-scrollbar {
            background-color: #95c4f9;
            }
            *::-webkit-scrollbar-thumb {
                border-radius: 100vw;
                background-color: #2e4a6a;
            }
            html, body {
                background-color: #95c4f9;
            }
            .box {
                background-color: #ffffff;
                border-color: #2e4a6a;
            }
            .box * {
                color: #2e4a6a;
            }
            hr {
                background-color: #2e4a6a;
            }
            #pageTitle {
                color: #ffffff;
                background-color: #2e4a6a;
                box-shadow: 0px 0px 0.8vw 0px #000000;
            }
            .new {
                background-color: #2e4a6a;
                color: white;
            }
            #alertMask {
                background-color: #00000088;
            }
            .boxBtn {
                background-color: #95c4f9;
                border-color: #2e4a6a;
            }
            #cCvs {
                filter: hue-rotate(180deg);
            }
            input[type="checkbox"] {
                filter: hue-rotate(345deg);
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
    <div id="pageTitle" onclick="goHome();"><?=$TITLE?></div>
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
            <h1>普一和課表</h1>
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
                <h3 data-href="<?=$pgU?>" title="前往上一篇" data-error="找不到上一頁" id="pgUp" style="position: absolute; left: 1vw; display: inline-block; margin: 0px;">&lt;&lt;上一篇</h3>
                <h3 title="此篇為<?=$dayT?>" style="display: inline-block; margin: 0px; text-align: center; width: 100%;">～<?=$dayT?>～</h3>
                <h3 data-href="<?=$pgD?>" title="前往下一篇" data-error="找不到下一頁" id="pgDown" style="position: absolute; right: 1vw; display: inline-block;  margin: 0px;">下一篇&gt;&gt;</h3>
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
            "彈性": "https://classroom.google.com/c/NTM3NDY5OTY1MDA2", 
            "國文": "https://classroom.google.com/c/NTI2MDA0NzE4NDU0", 
            "數學": "https://classroom.google.com/c/NTM3NTE5NDk0NDc4", 
            "物理": "", 
            "地科": "https://classroom.google.com/c/NDk3Mjc5NTA0ODE2", 
            "歷史": "https://classroom.google.com/c/NTM3NTIwODQ0Njcx", 
            "公民": "", 
            "英文": "https://classroom.google.com/c/NTM3MjcwMDAxMDA0", 
            "英聽": "", 
            "體育": "", 
            "資訊": "https://classroom.google.com/c/NTI2MTU2OTExMjkw", 
            "輔導": "", 
            "生輔": ""
        }
        const meets = {
            "彈性": "https://meet.google.com/jdc-egei-hjc", 
            "國文": "https://meet.google.com/fjg-rsqk-gdo", 
            "數學": "https://meet.google.com/xxa-hqza-oer", 
            "物理": "", 
            "地科": "", 
            "歷史": "", 
            "公民": "", 
            "英文": "https://meet.google.com/vsc-zzas-nok", 
            "英聽": "", 
            "體育": "", 
            "資訊": "https://meet.google.com/vmr-ezij-xpr", 
            "輔導": "", 
            "生輔": ""
        }
        const emails = {
            "彈性": "stsai@gm.nssh.ntpc.edu.tw", 
            "國文": "stanleychiu2022@gm.nssh.ntpc.edu.tw", 
            "數學": "nanii@gm.nssh.ntpc.edu.tw", 
            "物理": "", 
            "地科": "seekingsteven@gm.nssh.ntpc.edu.tw", 
            "歷史": "frog@gm.nssh.ntpc.edu.tw", 
            "公民": "", 
            "英文": "stsai@gm.nssh.ntpc.edu.tw", 
            "英聽": "", 
            "體育": "", 
            "資訊": "johnson@gm.nssh.ntpc.edu.tw", 
            "輔導": "", 
            "生輔": ""
        }
        const resources = {
            "彈性": "", 
            "國文": "", 
            "數學": "", 
            "物理": "", 
            "地科": "", 
            "歷史": "", 
            "公民": "", 
            "英文": "", 
            "英聽": "", 
            "體育": "", 
            "資訊": "https://drive.google.com/drive/folders/10AoHufvgBp1_4gBGiDakmLPZbF7xtNG7?usp=sharing", 
            "輔導": "", 
            "生輔": ""
        }
        let c_src = 'image/c.jpg';
        let xs = [23.2, 37.8, 52.6, 67.6, 82.0, 96.8];
        let ys = [22.017045454545457, 30.255681818181817, 38.79456706281834, 46.6044142614601, 54.58404074702886, 62.12765957446808, 71.34751773049646, 79.14893617021276, 87.65957446808511];
        let cList = {
            "0,0":"數學", "0,1":"國文", "0,2":"資訊", "0,3":"資訊", "0,4":"英文", "0,5":"物理", "0,6":"英聽", "0,7":"彈性", 
            "1,0":"公民", "1,1":"數學", "1,2":"國文", "1,3":"國文", "1,4":"生輔", "1,5":"英文", "1,6":"彈性", "1,7":"彈性", 
            "2,0":"輔導", "2,1":"數學", "2,2":"數學", "2,3":"英文", "2,4":"國文", "2,5":"歷史", "2,6":"體育", "2,7":"彈性", 
            "3,0":"國文", "3,1":"地科", "3,2":"數學", "3,3":"體育", "3,4":"英文", "3,5":"公民", "3,6":"歷史", "3,7":"彈性", 
            "4,0":"物理", "4,1":"數學", "4,2":"英文", "4,3":"英文", "4,4":"地科", "4,5":"國文", "4,6":"彈性", "4,7":"彈性"
        };
        let c_test = false;
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
                e.remove();
            }
            console.log(e.getAttribute('data-href'));
            e.addEventListener('click', function(){
                if(this.getAttribute('data-href') == 'false'){
                    alert(`錯誤！\n(${this.getAttribute('data-error')})`)
                }
                else{
                    location.href = this.getAttribute('data-href')
                }
            })
        }
        pgEL($('#pgUp'));
        pgEL($('#pgDown'));
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
                let xx = false;
                for(let i = 0; i < xs.length; i++){
                    if(x > xs[i] && x < xs[i+1]){
                        xx = i;
                        break;
                    }
                }
                let y = (MY - offset(this)['top'])/offset(this)['height']*100;
                let yy = false;
                for(let i = 0; i < ys.length; i++){
                    if(y > ys[i] && y < ys[i+1]){
                        yy = i;
                        break;
                    }
                }
                if(xx != 'false' && yy != 'false' && cList[`${xx},${yy}`] != undefined && !c_test){
                    alert(cList[`${xx},${yy}`]);
                }
                if(c_test){
                    if('table' in console){
                        console.table({x: x, y: y, xx: xx, yy: yy});
                    }
                    else{
                        console.log({x: x, y: y, xx: xx, yy: yy});
                    }
                }
            });
        }
        document.addEventListener('contextmenu', function(e){
            e.stopPropagation();
            e.preventDefault();
        });
        function goHome(){
            if(page != 'home'){
                location.href = '?day=home';
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
        function alert(c, data = false){
            $('#alertMask').style.opacity = '1';
            $('#alertMask').style.pointerEvents = 'auto';
            $('#alertTitle').innerText = c;
            if(data != false){
                infoHTML = '';
                data.forEach(row => {
                    infoHTML += `<h2>${row['name']}:<a target="_blank" href="${row['link']}">點我</a></h2>`;
                });
                $('#alertInfo').innerHTML = infoHTML;
            }
            else{
                var hasClassroomLink = classrooms[c] != '' && classrooms[c] != undefined;
                var hasMeetLink = meets[c] != '' && meets[c] != undefined;
                var hasEmailLink = emails[c] != '' && emails[c] != undefined;
                var hasResourceLink = resources[c] != '' && resources[c] != undefined;
                let date = new Date();
                $('#alertInfo').innerHTML = `
                    ${hasClassroomLink ? `<h2>GoogleClassroom:<a target="_blank" href="${classrooms[c]}">點我</a></h2>` : ''}
                    ${hasMeetLink ? `<h2>GoogleMeet:<a target="_blank" href="${meets[c]}">點我</a></h2>` : ''}
                    ${hasEmailLink ? `<h2>Email:<a target="_blank" href="mailto:${emails[c]}?subject=${encodeURIComponent(`${c}老師您好！`)}&body=${encodeURIComponent(`敬愛的${c}老師：\n\n  您好！祝\nxxxx\n\n學生 xxx敬上\n${date.getFullYear()}年${date.getMonth()+1}月${date.getDate()}日`)}">點我</a></h2>` : ''}
                    ${hasResourceLink ? `<h2>Resource:<a target="_blank" href="${resources[c]}">點我</a></h2>` : ''}
                    `
            }
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
        // if(!confirm('PYHstation已不再更新，\n確認繼續訪問PYHstation？')){
        //     location.href = 'http://school1.nssh.ntpc.edu.tw/modules/tad_web/index.php?WebID=95';
        // }
    </script>
</body>
</html>
