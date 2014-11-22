<?php
/**
 * Licensed under The MIT License
 * Redistributions of files must retain the this copyright notice.
 *
 * @author Weichuan Chen <stream_26@sina.com>
 * @version 1.0
 */

class CssAnimationRobber
{
    const USER_AGENT_CHROME = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36';
    const USER_AGENT_IOS    = 'Mozilla/5.0 (iOS; U; zh-Hans) AppleWebKit/533.19.4 (KHTML, like Gecko) AdobeAIR/4.0';

    public function getAnimationCss($url = NULL, $css_links = NULL, $html = NULL)
    {
        $url = (string) $url;
        $css_links = array_filter(is_array($css_links) ? $css_links : explode(',', preg_replace('/\s/', '', (string) $css_links)));
        $html = (string) $html;

        $html .= $url ? self::getRemoteContents($url, self::USER_AGENT_IOS) : '';
        $style_areas = array();
        if ($html) {
            preg_match_all('/<link[^<>]+href=[\'"]([^<>\'"]+)[\'"][^<>]*>/i', $html, $css_links_match, PREG_PATTERN_ORDER);
            $css_links = array_merge($css_links, $css_links_match[1]);
        }

        // 聚合所有含有css 的资源
        $css = $html;
        foreach ($style_areas as $s_a_v) {
            $css .= $s_a_v;
        }
        foreach ($css_links as $c_l_v) {
            $_url = $c_l_v;
            if (0 === strpos($_url, 'http://')) {
                // do nothing
            } else if (0 === strpos($_url, '/')) {
                $_url = substr($url, 0, strpos($url, '/', 7)) . $_url;
            } else {
                $_prefix = explode('/', $url);
                array_pop($_prefix);
                $_prefix = implode('/', $_prefix) . '/';
                $_url = $_prefix . $_url;
            }
            if ($_url) {
                // 抓取css
                $_css = strip_tags(self::getRemoteContents($_url, self::USER_AGENT_IOS));
                // 图片地址替换
                $_img_url_prefix = explode('/', $_url);
                array_pop($_img_url_prefix);
                $_img_url_prefix = implode('/', $_img_url_prefix) . '/';
                $_css = preg_replace('/url\s*\(([\'"]?)([^\(\)\:]+)([\'"]?)\)/', 'url($1' . $_img_url_prefix . '$2$3)', $_css);
                // 拼接
                $css .= $_css;
            }
        }

        // 去除换行
        $css = preg_replace("/[\n\r]/", '', $css);
        // 去除注释
        $css = preg_replace('/\/\*.*?\*\//', '', $css);
        $css = preg_replace('/\/\*|\*\//', '', $css);

        // 提取keyframes
        $keyframes = array();
        preg_match_all('/(@[^@\{\}\s]*keyframes\s+([^\{\}\s]+)\s*\{(?:[^\{\}]+\{[^\{\}]*\}\s*)+\})/', $css, $keyframes_match, PREG_PATTERN_ORDER);
        foreach ($keyframes_match[1] as $k_m_k => $k_m_v) {
            $keyframes[$keyframes_match[2][$k_m_k]][] = self::formatCssBlock($k_m_v);
        }

        // 提取animation
        $animations = array();
        preg_match_all('/[\.#][^\{\}]+\s*(\{[^\{\}]*animation\s*\:\s*([^\{\}\s]+)\s+[\{\}]*[^\{\}]+\})/', $css, $animations_match, PREG_PATTERN_ORDER);
        foreach ($animations_match[1] as $a_m_k => $a_m_v) {
            // 格式化代码
            $animations[$animations_match[2][$a_m_k]][] = self::formatCssBlock($a_m_v);
        }

        // 没找到匹配的class 的keyframes 使用默认的class
        foreach ($keyframes as $k_k => $k_v) {
            empty($animations[$k_k]) && ($animations[$k_k][] = self::getDefaultCssAnimationBlock($k_k, $k_v));
        }
        

        return array(
            'keyframes'  => $keyframes,
            'animations' => $animations,
        );
    }

    static public function getDefaultCssAnimationBlock($keyframes_name, $keyframes_blocks)
    {
        $css = "";
        $css .= "{\n";
        $css .= strpos($keyframes_blocks[0], 'opacity') ? "    opacity: 0;\n" : "";
        $css .= "    -webkit-animation: {$keyframes_name} 1s infinite 0.3s ease-in-out;\n";
        $css .= "    -moz-animation: {$keyframes_name} 1s infinite 0.3s ease-in-out;\n";
        $css .= "    -moz-animation: {$keyframes_name} 1s infinite 0.3s ease-in-out;\n";
        $css .= "    -o-animation: {$keyframes_name} 1s infinite 0.3s ease-in-out;\n";
        $css .= "    animation: {$keyframes_name} 1s infinite 0.3s ease-in-out;\n";
        $css .= "}\n\n";

        return $css;
    }

    static public function formatCssBlock($css, $level = 0)
    {
        $css = (string) $css;

        // 处理开始花括号
        $css = preg_replace("/^(\n?)\s*/", '$1' . str_pad('', $level * 4, ' '), preg_replace('/\s+$/', '', $css));
        // 处理换行缩进
        $css = preg_replace('/\s*([\{])\s*/', " $1\n" . str_pad('', ($level + 1) * 4, ' '), $css);
        $css = preg_replace('/\s*([;])\s*/', "$1\n" . str_pad('', ($level + 1) * 4, ' '), $css);
        // 处理闭合花括号
        $css = preg_replace('/\s*\}\s*/', "\n" . str_pad('', $level * 4, ' ') . "}\n" . ($level > 0 ? "" : "\n"), $css);
        // 处理二层嵌套的缩进
        preg_match_all('/([^\{\}]+\{)((?:[^\{\}]+\{[^\{\}]*\}\s*)+)(\}\s*)/', $css, $sub_blocks, PREG_PATTERN_ORDER);
        if (! empty($sub_blocks[2][0])) {
            $css_start = $sub_blocks[1][0];
            $css_end   = $sub_blocks[3][0];
            preg_match_all('/([^\{\}]+\{[^\{\}]*\}\s*)/', $sub_blocks[2][0], $sub_blocks, PREG_PATTERN_ORDER);
            $css = '';
            $css .= $css_start;
            foreach ($sub_blocks[1] as $s_b_v) {
                $css .= self::formatCssBlock($s_b_v, $level + 1);
            }
            $css .= $css_end;
        }

        return $css;
    }

    static public function getRemoteContents($url, $user_agent = NULL)
    {
        $url = trim((string) $url);
        if (empty($url)) {
            return '';
        }

        for ( $i = 0, $l = 3; $i < $l; ++ $i ) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            $user_agent && curl_setopt($curl, CURLOPT_USERAGENT, $user_agent ? $user_agent : self::USER_AGENT_CHROME);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $html = trim(curl_exec($curl));
            curl_close($curl);
            if ($html) {
                break;
            }
        }

        return $html;
    }
}
