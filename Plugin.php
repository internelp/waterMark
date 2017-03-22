<?php
/**
 * Typecho 图片水印插件。
 * 
 * @package waterMark
 * @author 应用侠
 * @version 1.0.0
 * @link https://www.appgao.com
 * @dependence 14.10.10
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
	    // $waterMarkOpacity = new Typecho_Widget_Helper_Form_Element_Text('waterMarkOpacity', NULL, '80', _t('水印透明度（0-100）'));
	    $waterMarkPos = new Typecho_Widget_Helper_Form_Element_Text('waterMarkPos', NULL, '0', _t('水印位置（九宫格位置填写0-9，1-9为位置，0为随机。）'));
	    $waterMarkAllow = new Typecho_Widget_Helper_Form_Element_Text('waterMarkAllow', NULL, 'jpg,png', _t('水印图片格式，直接填写格式以半角逗号分隔，gif加水印后会失去动画效果。'));
	    $waterMarkquality = new Typecho_Widget_Helper_Form_Element_Text('waterMarkquality', NULL, '100', _t('图片质量（0-100）'));
	    $form->addInput($waterMarkImg);
	    // $form->addInput($waterMarkOpacity);
	    $form->addInput($waterMarkPos);
	    $form->addInput($waterMarkAllow);
	    $form->addInput($waterMarkquality);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    public static function render($img){

    	if ($img->attachment->isImage){

    		$waterMarkImg = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkImg;
    		// $waterMarkOpacity = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkOpacity;
    		$waterMarkPos = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkPos;
			$waterMarkAllow = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkAllow;
			$waterMarkquality = Typecho_Widget::widget('Widget_Options')->plugin('waterMark')->waterMarkquality;
    		$type = $img->attachment->type;
    		

    		if ($type == 'jpeg') {
    			$type = 'jpg';
    		}

    		if (stripos($waterMarkAllow,$type) !== false){
    			$srcImg = __TYPECHO_ROOT_DIR__ . $img->attachment->path;
    			$waterImg = __TYPECHO_ROOT_DIR__ . $waterMarkImg;
    			if (rename($srcImg,$srcImg.'.tmp')){
    				$destImg = $srcImg;
    				$srcImg = $srcImg.'.tmp';
    				require_once("imgfunc.php");
    				ImgWaterMark("", 1,$srcImg,$waterMarkPos,$waterImg, '', 12, '#FF0000',  100 , $waterMarkquality, $destImg);
                    unlink($srcImg);
    			}
    		}
    	}
	}
}
?>

