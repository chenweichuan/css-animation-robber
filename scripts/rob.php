<?php

set_time_limit(60);

include_once './dependents/simple_html_dom.php';
include_once './CssAnimationRobber.php';

$url  = isset($_REQUEST['url']) ? (string) $_REQUEST['url'] : NULL;
$css_links = isset($_REQUEST['css_links']) ? (string) $_REQUEST['css_links'] : NULL;
$html  = isset($_REQUEST['html']) ? (string) $_REQUEST['html'] : NULL;

$css_animation_robber = new CssAnimationRobber();

$animations_css = $css_animation_robber->getAnimationCss($url, $css_links, $html);
$preview_html = fetch('./_preview.tpl.php', $animations_css);

$return = array();
$return['status'] = 1;
$return['info']   = array(
    'note' => $animations_css['note'],
    'html' => $preview_html,
);
echo json_encode($return);

function fetch($tpl, $params = array())
{
    // 页面缓存
    ob_start();
    ob_implicit_flush(0);
    // 导入变量
    extract($params, EXTR_OVERWRITE);
    // 载入模版文件
    include $tpl;

    // 输入
    return ob_get_clean();
}