<?php
/**
 * Typecho 图片水印（觉得好用去我的网站点广告支持一下 O(∩_∩)O谢谢）。
 * 
 * @package waterMark
 * @author 应用侠
 * @version 1.0.2
 * @link https://www.appgao.com
 */

class waterMark_Plugin implements Typecho_Plugin_Interface {

    public static function activate() {
        Typecho_Plugin::factory('Widget_Upload')->upload = array('waterMark_Plugin', 'render');
        // Typecho_Plugin::factory('Widget_Upload')->modify = array('waterMark_Plugin', 'render');
    }

    public static function deactivate(){
    }

    public static function config(Typecho_Widget_Helper_Form $form) {

        /** 配置欢迎话语 */
        $waterMarkImg = new Typecho_Widget_Helper_Form_Element_Text('waterMarkImg', NULL, '/usr/plugins/waterMark/watermark.png', _t('水印路径（png，相对路径）'));
        $waterMarkImg2x = new Typecho_Widget_Helper_Form_Element_Text('waterMarkImg2x', NULL, '/usr/plugins/waterMark/watermark2x.png', _t('针对大图片设定的水印，建议尺寸为默认水印的2倍'));
        $minPixels = new Typecho_Widget_Helper_Form_Element_Text('minPixels', NULL, '50000', _t('使用水印最低像素数（默认为250*200=50000，即 长*宽>50000 的图片将使用水印）'));
        $waterMarkImg2xMinPixels = new Typecho_Widget_Helper_Form_Element_Text('waterMarkImg2xMinPixels', NULL, '786432', _t('使用2x 水印最低像素数（默认为1024*768=786432）'));
        $waterMarkPos = new Typecho_Widget_Helper_Form_Element_Text('waterMarkPos', NULL, '0', _t('水印位置（九宫格位置填写0-9，1-9为位置，0为随机。）'));
        $waterMarkAllow = new Typecho_Widget_Helper_Form_Element_Text('waterMarkAllow', NULL, 'jpg,png', _t('水印图片格式，直接填写格式以半角逗号分隔，gif加水印后会失去动画效果。'));
        $waterMarkquality = new Typecho_Widget_Helper_Form_Element_Text('waterMarkquality', NULL, '100', _t('图片质量（0-100）'));
        $form->addInput($waterMarkImg);
        $form->addInput($waterMarkImg2x);
        $form->addInput($minPixels);
        $form->addInput($waterMarkImg2xMinPixels);
        $form->addInput($waterMarkPos);
        $form->addInput($waterMarkAllow);
        $form->addInput($waterMarkquality);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    public static function render($img){

        if ($img->attachment->isImage){

            $srcImg = __TYPECHO_ROOT_DIR__ . $img->attachment->path;
            $minPixels = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->minPixels;
            $waterMarkImg2xMinPixels = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkImg2xMinPixels;
            
            list($srcData['width'], $srcData['height'], $srcData['type'] ,$srcData['attr']) = getimagesize($srcImg);

            $pixelCount = $srcData['width'] * $srcData['height'];

            // 判断尺寸是否达到添加水印条件，并选择是否是2x 水印
            if ( $pixelCount > $minPixels ) {
                // 有水印
                $waterMarkImg = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkImg;

                if ( $pixelCount > $waterMarkImg2xMinPixels ) {
                    // 2x水印
                    $waterMarkImg = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkImg2x;
                } 
                
                $waterMarkPos = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkPos;
                $waterMarkAllow = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkAllow;
                $waterMarkquality = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkquality;
                $type = $img->attachment->type;
                
                if ($type == 'jpeg') {
                    $type = 'jpg';
                }

                if (stripos($waterMarkAllow,$type) !== false){
                    $waterImg = __TYPECHO_ROOT_DIR__ . $waterMarkImg;
                    if (rename($srcImg,$srcImg.'.tmp')){
                        $destImg = $srcImg;
                        $srcImg = $srcImg.'.tmp';
                        require_once("imgfunc.php");
                        ImgWaterMark($waterMarkquality, 100, "", 1,$srcImg,$waterMarkPos,$waterImg, '', 12, '#FF0000', $destImg);
                        unlink($srcImg);
                    }
                }
            }
        }
    }
}

