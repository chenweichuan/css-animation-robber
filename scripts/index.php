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
        .clearfix {
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
        .preview-form .preview-field {
            width: 130px;
        }
        .preview-form .preview-input {
            width: 60%;
        }
        .preview-form .preview-input input {
            width: 100%;
            height: 20px;
            padding:0 5px;
            margin:0 0 0 10px;
            outline: none;
        }
        .preview-form .preview-input textarea {
            width: 100%;
            height: 60px;
            padding:0 6px;
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
            line-height: 170px;
            font-size: 1em;
            text-align: center;
            white-space:nowrap;
            padding:0;
            margin:0 auto;
            background-position: center center;
            position: absolute;
            top:auto;
            right: 0;
            bottom: auto;
            left: 0;
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
            <label class="pull-left preview-field preview-field-url">&nbsp;HTML5 URL</label>
            <div class="pull-left preview-input preview-input-url">
                <input id="url" type="text" name="url" value="http://m.jobtong.com/e/1024/power" placeholder="请输入HTML5 URL" autocomplete="on"/>
            </div>
            <div class="clearfix"></div>
            <label class="pull-left preview-field preview-field-css-links">&nbsp;CSS URL</label>
            <div class="pull-left preview-input preview-input-css-links">
                <input id="css-links" type="text" name="css_links" value="" placeholder="请输入CSS URL" autocomplete="on"/>
            </div>
            <div class="clearfix"></div>
            <label class="pull-left preview-field preview-field-html">&nbsp;含CSS 的HTML</label>
            <div class="pull-left preview-input preview-input-html">
                <textarea id="html" name="html" placeholder="请输入HTML" autocomplete="off"></textarea>
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
            function setDataToHash( data ) {
                data = data || {};
                location.hash = "url=" + encodeURIComponent( data.url || "" ) + "&cssLinks=" + encodeURIComponent( data.cssLinks || "" ) + "&html=" + encodeURIComponent( data.html || "" );
            }
            // get url from hash
            function getDataFromHash() {
                var href = location.href,
                    hash = href.split( "#" )[1] || "",
                    data = {
                        url: decodeURIComponent( ( hash.match( /url=([^&]*)/ ) || [] )[1] || "" ),
                        cssLinks: decodeURIComponent( ( hash.match( /cssLinks=([^&]*)/ ) || [] )[1] || "" ),
                        html: decodeURIComponent( ( hash.match( /html=([^&]*)/ ) || [] )[1] || "" )
                    };
                return data;
            }
            // get animations
            function getHtml() {
                var $btn = $( "#submit" ),
                    btnText = $btn.val(),
                    loadingText = "分析提取ing...",
                    data = {
                        url: ( $( "#url" ).val() || "" ).replace( /^\s+|\s+$/, "" ),
                        cssLinks: ( $( "#css-links" ).val() || "" ).replace( /^\s+|\s+$/, "" ),
                        html: ( $( "#html" ).val() || "" ).replace( /^\s+|\s+$/, "" )
                    }
                if ( $btn.attr( "disabled" ) ) {
                    return ;
                }
                $btn.attr( "disabled", true ).val( loadingText );
                if (! data.url && ! data.cssLinks && ! data.html) {
                    alert( "请填写要抓取的内容" );
                    return;
                }
                $.getJSON( "rob.php", data, function( res ) {
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
                var data = {
                        url: $( "#url" ).val(),
                        cssLinks: $( "#css-links" ).val(),
                        html: $( "#html" ).val()
                    };
                ( data.url || data.cssLinks || data.html ) && setDataToHash( data );
                getHtml();
            } );
            // hashchange
            $( window ).bind( "hashchange", function() {
                var data = getDataFromHash();
                ( data.url || data.cssLinks || data.html ) && ( $( "#url" ).val( data.url ), $( "#css-links" ).val( data.cssLinks ), $( "#html" ).val( data.html ), getHtml() );
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
