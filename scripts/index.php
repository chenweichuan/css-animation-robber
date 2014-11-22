<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>CSS动效提取器</title>
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
        .pull-left {
            float: left;
        }
        .hide {
            display: none;
        }
        .well > div {
            margin:10px;
        }
        .preview-form {
            width: 100%;
            height: 20px;
            position: relative;
            z-index: 999;
        }
        .preview-form .preview-field-url {
            height: 100%;
        }
        .preview-form .preview-input-url {
            width: 60%;
            height: 100%;
        }
        .preview-form .preview-input-url input {
            width: 100%;
            height: 100%;
            padding:0 5px;
            margin:0 0 0 10px;
            outline: none;
        }
        .preview-form .preview-submit-btn {
            height: 100%;
        }
        .preview-form .preview-submit-btn input {
            height: 100%;
            padding:0;
            margin:0 0 0 30px;
            outline: none;
        }
        .preview-animation-list {
            position: relative;
            z-index: 99;
        }
        .preview-animation-item {
            margin:10px;
            float: left;
        }
        .preview-animation-item .preview-animation-area {
            width: 200px;
            height: 170px;
            position: relative;
        }
        .preview-animation-item .preview-animation-area .preview-animation-element {
            display: inline-block;
            width: 170px;
            height: 170px;
            line-height: 170px;
            font-size: 1em;
            text-align: center;
            white-space:nowrap;
            padding:0;
            margin:0 auto;
            background-position: center center;
            position: absolute;
            top:auto;
            right: auto;
            bottom: auto;
            left: auto;
            z-index: auto;
        }
        .preview-animation-item .preview-animation-code {
            width: 196px;
            height:68px;
            margin:0 2px;
            padding:0;
            border:none;
            overflow: hidden;
        }
        .preview-refresh-btn {
            color: #dd0000;
            font-size:30px;
            position: fixed;
            top:50px;
            right:20px;
            z-index: 9999;
        }
    </style>

    <script src="./dependents/jquery-1.10.2.js"></script>
  </head>
  
  <body>

    <div class="well">
        <legend>CSS动效提取器</legend>

        <br/>

        <div class="clearfix preview-form">
            <label class="pull-left preview-field-url"><span style="color:red;">*</span>&nbsp;HTML5地址</label>
            <div class="pull-left preview-input-url">
                <input id="url" type="text" name="url" value="http://m.jobtong.com/e/1024/power" placeholder="请输入HTML5 URL" autocomplete="on"/>
            </div>
            <div class="pull-left preview-submit-btn">
                <input id="submit" type="button" value="提取CSS"/>
            </div>
        </div>

        <br/>

        <div id="preview" class="clearfix preview-animation-list"></div>
    </div>

    <input class="preview-refresh-btn js-refresh-btn" type="button" value="刷新动画"/>

    <script>
        ! function() {
            // set url to hash
            function setUrlToHash( url ) {
                location.hash = encodeURIComponent( url );
            }
            // get url from hash
            function getUrlFromHash() {
                var href = location.href;
                return -1 === href.indexOf( "#" ) ? "" : decodeURIComponent( href.split( "#" )[1] );
            }
            // get animations
            function getHtml( url ) {
                var $btn = $( "#submit" ),
                    btnText = $btn.val(),
                    loadingText = "分析提取ing...";
                if ( $btn.attr( "disabled" ) ) {
                    return ;
                }
                $btn.attr( "disabled", true ).val( loadingText );
                url && $.getJSON( "rob.php", { url: url }, function( res ) {
                    if ( res.status ) {
                        $( "#preview" ).html( res.info );
                    } else {
                        alert( "抓取失败，请稍后重试~" );
                    }
                    $btn.attr( "disabled", false ).val( btnText );
                } );
            }
            // manual btn
            $( "#submit" ).click( function() {
                var url = $( "#url" ).val();
                url ? ( getHtml( url ), setUrlToHash( url ) ) : alert( "请输入HTML5地址" );
            } );
            // hashchange
            $( window ).bind( "hashchange", function() {
                var url = getUrlFromHash();
                url && ( getHtml( url ), $( "#url" ).val( url ) );
            } ).trigger( "hashchange" );

            // auto refresh not-infinite animation
            var autoRefreshTimer,
                fRefresh = function() {
                    stopAutoRefresh();
                    $( "#preview" ).find( ".preview-animation-area > .not-infinite" ).css( "display", "none" );
                    setTimeout( function() {
                        $( "#preview" ).find( ".preview-animation-area > .not-infinite" ).css( "display", "inline-block" );
                        startAutoRefresh();
                    }, 100 );
                },
                startAutoRefresh = function() {
                    autoRefreshTimer = setTimeout( function() {
                        fRefresh();
                    }, 8000 );
                },
                stopAutoRefresh = function() {
                    clearTimeout( autoRefreshTimer );
                };
            // manual refresh
            $( ".js-refresh-btn" ).click( function() {
                var $btn  = $( this );
                $btn.attr( "disabled", true );
                fRefresh();
                setTimeout( function() {
                    $btn.attr( "disabled", false );
                }, 1000 );
            } );
            startAutoRefresh();
        } ();
    </script>
  </body>
</html>