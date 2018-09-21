## Typecho 图片水印插件

本插件仅支持 png \ jpg 格式的图片作为水印。

推荐使用 png 格式的图片作为水印，以保证水印的质量。

## 使用方法

将 waterMark 目录上传至网站的 `usr/plugins` 目录，然后在后台启用即可。

保持目录结构如下：

```
waterMark/
├── imgfunc.php
├── Plugin.php
├── watermark.png
└── watermark2x.png
```

## 更新

**2018-9-21**
- 取消了对 typecho 版本的要求
- 增加了 2x 水印的支持
- 支持小图片不加水印

访问：https://www.appgao.com/Programming/typecho-watermark.html
