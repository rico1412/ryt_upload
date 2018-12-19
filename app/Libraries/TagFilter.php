<?php

namespace App\Libraries;
use Exception;

/**
 * 过滤文本里的标签
 *
 * @author
 */
class TagFilter implements LibrariesInterface
{

    /**
     * 文本
     * @var string
     */
    protected $_content;

    /**
     * 文本内所有标签数组
     * @var array
     */
    protected $_Tags = array();

    /**
     * 文本内不允许的标签数组
     * @var array
     */
    protected $_denyTags = array();

    /**
     * 文本内允许的标签数组
     * @var array
     */
    protected $_allowTags = array();

    /**
     * 允许的标签数组设置
     * @var array
     */
    protected $_iniAllowTags = array();

    /**
     * 允许的属性数组设置
     * @var array
     */
    protected $_iniAllowAttrs = array();

    /**
     * 文本内不允许的标属性数组
     * @var array
     */
    protected $_denyAttrs = array();

    /**
     * 文本内所有标签内的属性
     * @var array
     */
    protected $_attrs = array();

    /**
     * 不可以删除的标签
     * @var array
     */
    protected $_iniNotDelTags = array();

    /**
     * 构造方法
     *
     * @param string
     */
    public function __construct($content)
    {
        $this->_content = $content;

        $this->init();
        $this->filter();
        $this->validate(false);
    }

    /**
     * 初始化配置
     */
    public function init()
    {
        $this->_iniAllowTags = array('a', 'table', 'tbody', 'th', 'thead', 'tfoot'
        , 'td', 'tr', 'div', 'img', 'i', 'b', 'strong', 'p', 'P', 'ul'
        , 'br/', 'br', 'font', 'hr', 'embed', 'h', 'blockquote', 'li',
            'ol', 'caption'
        );
        $this->_iniAllowAttrs = array('href', 'target', 'width', 'height', 'bordercolor', 'title',
            'background', 'align', 'rowspan', 'colspan', 'cellspacing',
            'cellpadding', 'border', 'src', 'color', 'alt', 'quality',
            'type', 'valign', 'loop', 'play', 'pluginspage', 'bgcolor',
            'wmode', 'menu', 'allowscriptaccess', 'allowfullscreen', 'rel'
        );
        $this->_iniNotDelTags = array('td', 'th', 'dt', 'dd', 'embed');
    }

    /**
     * 校验内容
     *
     * @param bool $throw
     * @throws Exception
     */
    public function validate($throw = true)
    {
        $denyTags = $this->getDenyTags();
        $denyAttrs = $this->getDenyAttrs();
        $msg = array();
        if ($denyTags) {
            $msg[] = "内容含有非法标签。非法标签如下：" . implode(',', $denyTags);
        }
        if ($denyAttrs) {
            $msg[] = "内容含有非法属性。非法属性如下：" . implode(',', $denyAttrs);
        }

        if ($msg && $throw) {
            throw new Exception(implode("\n", $msg), 7101);
        }
    }

    /**
     * 标签过滤
     */
    public function filter()
    {
        $this->delEmptyTag();
        $this->replace();
        $this->delAttr('style');
        $this->delAttr('class');
    }

    /**
     * 替换一些客户端编辑器产生无用的字符
     */
    public function replace()
    {
        //去除&nbsp;
        $this->_content = str_replace("&nbsp;", " ", $this->_content);
        //去除 nowrap
        $this->_content = str_replace("nowrap=\"nowrap\"", " ", $this->_content);
        //替换strong为b
        //$this->_content = str_ireplace(array('<strong', '</strong>'), array('<b', '</b>'), $this->_content);
        //将style中的text-align:center替换成属性的align=center
        $this->_content = str_ireplace(array("style=\"text-align:center\">", "style=\"text-align: center;\">", "style=\"text-align:center;\">", "style=\"TEXT-ALIGN: center\">", "style=\"TEXT-ALIGN: center;\">"), "align=\"center\">", $this->_content);
        //将单引号的属性替换成双引号
        $this->_content = preg_replace('/(\w+)=(\'?)([^\'\"<>]*?)\2(?=(( *\w+=)|( *>)))/', '$1="$3"', $this->_content);
        //替换分页符
        $this->_content = str_ireplace(array('<hr>', '<HR>'), '<hr />', $this->_content);
        //替换H标签,非h3
        $this->_content = str_ireplace(array('<h1>', '<h2>', '<h4>', '<h5>', '<h6>', '<H1>', '<H2>', '<H4>', '<H5>', '<H6>'), '<p>', $this->_content);
        $this->_content = str_ireplace(array('</h1>', '</h2>', '</h4>', '</h5>', '</h6>', '</H1>', '</H2>', '</H4>', '</H5>', '</H6>'), '</p>', $this->_content);
        //将img标签中样式width和height改成属性的width和height
        $this->replaceImg();
        $this->replaceEmbed();
        //将p标签后跟的空格字符去掉
        $this->_content = preg_replace('/<p>\s+/i', '<p>', $this->_content);
        //替换style中的color样式为font的color属性
        $this->replaceStyleColor();
    }

    /**
     * 替换font标签style中的color样式为font的color属性
     */
    public function replaceStyleColor()
    {
        preg_match_all('/<font\s+[^>]*?>/i', $this->_content, $match);
        if ($match) {
            $replace = array();
            $beReplace = array();

            $fonts = array_unique($match[0]);
            foreach ($fonts as $font) {
                //提取style中的字符串，并获取color样式的值
                $colorValue = '';
                preg_match('/style\s*=\s*(\'|\")(.*?)\1/', $font, $fontMatch);
                if ($fontMatch) {
                    $styleAttrs = array_filter(explode(';', $fontMatch[2]));
                    foreach ($styleAttrs as $styleAttr) {
                        $tmp = explode(':', $styleAttr);
                        if (count($tmp) < 2) {
                            continue;
                        }
                        $attrKey = strtolower(trim($tmp[0]));
                        if ($attrKey == 'color') {
                            $colorValue = strtolower(trim($tmp[1]));
                            break;
                        }
                    }
                }

                if ($colorValue) {
                    $replace[] = $font;
                    $beReplace[] = str_replace('<font', '<font color="' . $colorValue . '"', $font);
                }
            }

            if ($replace) {
                $this->_content = str_replace($replace, $beReplace, $this->_content);
            }
        }
    }

    /**
     * 删除空标签
     *
     * @return string
     */
    public function delEmptyTag()
    {
        //去除空标签
        preg_match_all('/<([a-zA-Z]+)[^>]*?>\s*<\/\1>/', $this->_content, $matchs);
        if ($matchs[1]) {
            $iniNotDelTags = $this->getIniNotDelTags();
            $needDelTags = array();
            foreach ($matchs[1] as $match) {
                if (array_search(strtolower($match), $iniNotDelTags) === false) {
                    $needDelTags[] = $match;
                }
            }
            if (!$needDelTags) {
                return $this->_content;
            }
            $patterns = array();
            $replacements = array();
            foreach ($needDelTags as $needDelTag) {
                $patterns[] = '/<' . $needDelTag . '[^>]*>\s*<\/' . $needDelTag . '>/mi';
                $replacements[] = '';
            }
            $this->_content = preg_replace($patterns, $replacements, $this->_content);
            $this->delEmptyTag();
        }
        return $this->_content;
    }

    /**
     * 获取文本内出现的不被允许的标签
     * @return array
     */
    public function getDenyTags()
    {
        if (!$this->_denyTags) {
            $tags = $this->getTags();
            $allowTags = $this->getIniAllowTags();
            foreach ($tags as $tag) {
                $tag = strtolower($tag);
                if (array_search($tag, $allowTags) === false) {
                    $this->_denyTags[] = $tag;
                }
            }
        }
        return $this->_denyTags;
    }

    /**
     * 获取文本内出现的不被允许的属性
     * @return array
     */
    public function getDenyAttrs()
    {
        if (!$this->_denyAttrs) {
            $attrs = $this->getAttrs();
            $iniAllowAttrs = $this->getIniAllowAttrs();
            foreach ($attrs as $attr) {
                $tag = strtolower($attr);
                if (array_search($tag, $iniAllowAttrs) === false) {
                    $this->_denyAttrs[] = $attr;
                }
            }
        }
        return $this->_denyAttrs;
    }

    /**
     * 获取文本内出现的所有标签
     * @return array
     */
    public function getTags()
    {
        if (!$this->_Tags) {
            preg_match_all('/<[a-zA-Z]+[^>]*>/', $this->_content, $matchs);
            foreach ($matchs[0] as $match) {
                preg_match_all('/<([a-zA-Z]+)[^>]*>/', $match, $tags);
                if (array_search($tags[1][0], $this->_Tags) === false) {
                    $this->_Tags[] = $tags[1][0];
                }
            }
        }
        return $this->_Tags;
    }

    /**
     * 获取文本内出现的所有属性
     * @return array
     */
    public function getAttrs()
    {
        if (!$this->_attrs) {
            $matchs = array();
            preg_match_all('/<[a-zA-Z]+[^>]*>/', $this->_content, $matchs);
            foreach ($matchs[0] as $match) {
                $patterns = array();
                $patterns[0] = '/=\s*[^"\']+\s+/i';
                $patterns[1] = '/=\s*"[^"]*"/i';
                $patterns[2] = '/=\s*\'[^\']*\'/i';
                $patterns[3] = '/^<\w+[>|\s+|\/]/i';
                $patterns[4] = '/[\/>]|>$/i';
                $replacements = array(' ', ' ', ' ', ' ', ' ');
                $match = preg_replace($patterns, $replacements, $match);
                //var_dump($match);
                if (trim($match) != '') {
                    $attrs = explode(' ', trim($match));
                    foreach ($attrs as $attr) {
                        if (array_search($attr, $this->_attrs) === false && $attr) {
                            $this->_attrs[] = $attr;
                        }
                    }
                }
            }
        }

        return $this->_attrs;
    }

    /**
     * 删除标签内的属性
     *
     * @param string $attrName 属性名称
     * @return string
     */
    public function delAttr($name)
    {
        preg_match_all('/<[a-zA-Z]+\s+.*?(' . $name . '\s*=\s*(\"|\')?.*?\2)/i', $this->_content, $matchs);
        if (isset($matchs[1])) {
            $this->_content = str_replace($matchs[1], '', $this->_content);
            $this->_content = preg_replace('/\s+>/', '>', $this->_content);
        }
    }

    public function replaceImg()
    {
        preg_match_all('/<img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $this->_content, $match);
        foreach ($match[0] as $mac) {
            $newImg = $mac;
            //获取图片宽
            preg_match('/<img\s*.+?width=[^\d]*(\d+)/mi', $mac, $tmp);
            $width = $tmp ? $tmp[1] : '';
            if (!$width) {
                preg_match('/<img\s+.*style=[\"|\'].*?(;\s*width\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                if (!$tmp) {
                    preg_match('/<img\s+.*style=[\"|\'](\s*width\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                }
                if ($tmp) {
                    $beReplace = array($tmp[1], '<img');
                    $replace = array(strpos($tmp[1], ';') === 0 ? ';' : '', '<img width="' . $tmp[2] . '"');
                    $newImg = str_replace($beReplace, $replace, $newImg);
                }
            }
            //获取图片高
            preg_match('/<img\s*.+?height=[^\d]*(\d+)/mi', $mac, $tmp);
            $height = !empty($tmp) ? $tmp[1] : '';
            if (!$height) {
                preg_match('/<img\s+.*style=[\"|\'].*?(;\s*[^-]height\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                if (!$tmp) {
                    preg_match('/<img\s+.*style=[\"|\'](\s*height\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                }
                if ($tmp) {
                    $beReplace = array($tmp[1], '<img');
                    $replace = array(strpos($tmp[1], ';') === 0 ? ';' : '', '<img height="' . $tmp[2] . '"');
                    $newImg = str_replace($beReplace, $replace, $newImg);
                }
            }
            $this->_content = str_replace($mac, $newImg, $this->_content);
        }
    }

    public function replaceEmbed()
    {
        preg_match_all('/<embed\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $this->_content, $match);
        foreach ($match[0] as $mac) {
            $newImg = $mac;
            //获取图片宽
            preg_match('/<embed\s*.+?width=[^\d]*(\d+)/mi', $mac, $tmp);
            $width = $tmp ? $tmp[1] : '';
            if (!$width) {
                preg_match('/<embed\s+.*style=[\"|\'].*?(;\s*width\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                if (!$tmp) {
                    preg_match('/<embed\s+.*style=[\"|\'](\s*width\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                }
                if ($tmp) {
                    $beReplace = array($tmp[1], '<embed');
                    $replace = array(strpos($tmp[1], ';') === 0 ? ';' : '', '<embed width="' . $tmp[2] . '"');
                    $newImg = str_replace($beReplace, $replace, $newImg);
                }
            }
            //获取图片高
            preg_match('/<embed\s*.+?height=[^\d]*(\d+)/mi', $mac, $tmp);
            $height = !empty($tmp) ? $tmp[1] : '';
            if (!$height) {
                preg_match('/<embed\s+.*style=[\"|\'].*?(;\s*[^-]height\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                if (!$tmp) {
                    preg_match('/<embed\s+.*style=[\"|\'](\s*height\s*:\s*(\d+)?px\s*;?)/i', $mac, $tmp);
                }
                if ($tmp) {
                    $beReplace = array($tmp[1], '<embed');
                    $replace = array(strpos($tmp[1], ';') === 0 ? ';' : '', '<embed height="' . $tmp[2] . '"');
                    $newImg = str_replace($beReplace, $replace, $newImg);
                }
            }
            $this->_content = str_replace($mac, $newImg, $this->_content);
        }
    }

    /**
     * 获取内容
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * 设置过滤的内容
     * @return Application_Model_Business_Content_TagFilter
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * 获取允许的标签配置
     * @return array
     */
    public function getIniAllowTags()
    {
        return $this->_iniAllowTags;
    }

    /**
     * 根据需求设置允许的属性配置
     * @return Application_Model_Business_Content_TagFilter
     */
    public function setIniAllowTags($AllowTags)
    {
        $this->_iniAllowTags = $AllowTags;
        return $this;
    }

    /**
     * 获取允许的属性配置
     * @return array
     */
    public function getIniAllowAttrs()
    {
        return $this->_iniAllowAttrs;
    }

    /**
     * 根据需求设置允许的属性配置
     * @return Application_Model_Business_Content_TagFilter
     */
    public function setIniAllowAttrs($AllowAttrs)
    {
        $this->_iniAllowAttrs = $AllowAttrs;
        return $this;
    }

    /**
     * 添加允许html属性
     * @param string $attr
     * @return Application_Model_Business_Content_TagFilter
     */
    public function addIniAllowAttr($attr)
    {
        $this->_iniAllowAttrs[] = trim($attr);
        return $this;
    }

    /**
     * 添加允许html标签值
     * @param string $tag
     * @return Application_Model_Business_Content_TagFilter
     */
    public function addIniAllowTags($tag)
    {
        $this->_iniAllowTags[] = trim($tag);
        return $this;
    }

    /**
     * 获取不需要删除的标签
     * @return array
     */
    public function getIniNotDelTags()
    {
        return $this->_iniNotDelTags;
    }

    public static function usage()
    {
        // TODO: Implement usage() method.
    }
}