<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>CSS效果提取器</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        .clearfix {
            *zoom: 1;
        }
        .clearfix:before,
        .clearfix:after {
            display: table;
            line-height: 0;
        }
        .clearfix:after {
            clear: both;
        }
        .well > div {
            margin:10px;
        }
        .preview-animation-item {
            width: 200px;
            height: 240px;
            margin:10px;
            float: left;
            overflow: hidden;
        }
        .preview-animation-item .preview-animation-element {
            height: 170px;
            line-height: 170px;
            text-align: center;
            white-space:nowrap;
        }
        .preview-animation-item .preview-animation-code {
            width: 100%;
            height:50px;
            overflow: hidden;
        }
        .refresh-btn {
            position: fixed;
            top:50px;
            right:20px;
            font-size:30px;
            color: #dd0000;
        }
    </style>

    <script src="./dependents/jquery-1.10.2.js"></script>
  </head>
  
  <body>

    <div class="well">
        <legend>CSS动效提取器</legend>

        <br/>

        <div class="clearfix">
            <label style="float:left;"><span style="color:red;">*</span>&nbsp;HTML5地址&nbsp;</label>
            <div style="float:left;">
                <input id="url" type="text" name="url" value="http://m.jobtong.com/e/1024/power" placeholder="请输入HTML5 URL" style="width:800px;"/>&nbsp;
            </div>
            <div style="float:left;">
                <input class="js-submit-btn" type="button" value="提取CSS"/>&nbsp;
            </div>
        </div>

        <br/>

        <div id="preview" class="clearfix preview"></div>
    </div>

    <input class="refresh-btn js-refresh-btn" type="button" value="重播动画"/>

    <script>
        $( ".js-submit-btn" ).click( function() {
            var $btn  = $( this ),
                $btns = $( ".js-submit-btn" ),
                btnText = $btn.val(),
                loadingText = "分析提取ing...",
                url = $( "#url" ).val();
            $btn.attr( "disabled", true ).val( loadingText );
            url ? $.getJSON( "rob.php", { url: url }, function( res ) {
                if ( res.status ) {
                    $( "#preview" ).html( res.info );
                } else {
                    alert( "抓取失败，请稍后重试~" );
                }
                $btn.attr( "disabled", false ).val( btnText );
            } ) : ( alert( "请输入HTML5地址" ), $btn.attr( "disabled", false ).val( btnText ) );
        } );
        var autoRefreshTimer,
            initAutoRefresh = function() {
                autoTimer = setInterval( function() {
                    $( "#preview" ).find( ".preview-animation-element > .not-infinite" ).hide();
                    setTimeout( function() {
                        $( "#preview" ).find( ".preview-animation-element > .not-infinite" ).show();
                    }, 100 );
                }, 8000 );
            };
        $( ".js-refresh-btn" ).click( function() {
            var $btn  = $( this );
            clearInterval( autoRefreshTimer );
            $btn.attr( "disabled", true );
            $( "#preview" ).find( ".preview-animation-element > .not-infinite" ).hide();
            setTimeout( function() {
                $( "#preview" ).find( ".preview-animation-element > .not-infinite" ).show();
            }, 100 );
            setTimeout( function() {
                $btn.attr( "disabled", false );
            }, 2000 );
        } );
        initAutoRefresh();
    </script>
  </body>
</html>