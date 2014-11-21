<?php
include_once './dependents/simple_html_dom.php';

class CssAnimationRobber
{
    const USER_AGENT_CHROME = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36';
    const USER_AGENT_IOS    = 'Mozilla/5.0 (iOS; U; zh-Hans) AppleWebKit/533.19.4 (KHTML, like Gecko) AdobeAIR/4.0';

    public function getCss($url)
    {
        $html = self::getRemoteContents($url, self::USER_AGENT_IOS);
        $dom  = self::buildDom($html);

        $style_dom = $dom->find('style');
        $link_dom  = $dom->find('link');

        // 提取css
        $css = '';
        foreach ($style_dom as $s_d_v) {
            $css .= $s_d_v->plaintext;
        }
        foreach ($link_dom as $l_d_v) {
            $_url = $l_d_v->href;
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
                $_css = self::getRemoteContents($_url, self::USER_AGENT_IOS);
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

        // 提取keyframes
        $keyframes = array();
        preg_match_all('/(@[^@\{\}]*keyframes\s+([^\{\}\s]+)\s*\{(?:[^\{\}]+\{[^\{\}]*\})+\})/', $css, $keyframes_match, PREG_PATTERN_ORDER);
        foreach ($keyframes_match[1] as $k_m_k => $k_m_v) {
            $keyframes[$keyframes_match[2][$k_m_k]][] = $k_m_v;
        }

        // 提取animation
        $animations = array();
        $css = preg_replace('/position\s*\:\s*[a-z]+\s*;/', '', $css);
        preg_match_all('/[\.#][^\{\}]+\s*(\{[^\{\}]*animation\s*\:\s*([^\{\}\s]+)\s+[\{\}]*[^\{\}]+\})/', $css, $animations_match, PREG_PATTERN_ORDER);
        foreach ($animations_match[1] as $a_m_k => $a_m_v) {
            $animations[$animations_match[2][$a_m_k]][] = $a_m_v;
        }

        return array(
            'keyframes'  => $keyframes,
            'animations' => $animations,
        );
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

    static public function buildDom($html)
    {
        $html = (string) $html;

        $dom = new simple_html_dom();
        $dom->load($html, true);

        return $dom;
    }
}
