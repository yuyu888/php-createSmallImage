<?php
/**
 * 缩略图生成类
 *
 * @version 1.0 2008-06-16
 * @author  yurong
 */
Class CreatSmallImage
{
    /**
     * 测定图像文件的大小并返回图像的尺寸以及文件类型和一个可以用于普通 HTML 文件中 <IMG> 标记中的 height/width 文本字符串
     *
     * @param str $imagePath  源图像的路径
     * @return array
     */
    public function getImageInfo($imagePath)
    {
        //取原始图片信息
        $imageInfo = getimagesize($imagePath);
        
        return $imageInfo;
    }
    
    /**
     * 根据给定尺寸创建一块画布，并使其背景色为白色
     *
     * @param int $width       画布的宽
     * @param int $height      画布的高
     * @return unknown         图像标识符
     */
    public function creatTempImage($width, $height)
    {
        //根据给定的高和宽建立一块真彩新画布
        $tempImage = imagecreatetruecolor($width, $height);
        
        //设定图片背景色， 默认为白色
        $white = ImageColorAllocate($tempImage, 255, 255, 255);
        imagefilledrectangle ($tempImage, 0, 0, $width, $height, $white );        

        return $tempImage;
    }
    
    /**
     * 根据给定文件创建图像
     *
     * @param string $imagePath 给定图像的路径
     * @return unknown          图像标示符
     */
    public function creatOldImage($imagePath)
    {
        //取原始图片信息
        $imageInfo = $this->getImageInfo($imagePath);


        //打开图片并创建画布
        if ($imageInfo[2] == 1 )         // $imageinfo[2] == 1 则图片格式为gif；
        {
            $image = imagecreatefromgif($imagePath);
        }
        else if ( $imageInfo[2] == 2 )   // $imageinfo[2] == 2 则图片格式为jpg；
        {
            $image = imagecreatefromjpeg($imagePath);
        }
        else if ( $imageInfo[2] == 3 )   //$imageinfo[2] == 3  则图片格式为png；
        {
            $image = imagecreatefrompng($imagePath);
        }
        else
        {
            die("请选用gif,jpg,png格式图片");
        }
        
        return $image;
    }
    
    /**
     * 根据给定图像，和指定的高，宽生成相应的缩略图
     *
     * @param int $newWidth        缩略图的宽
     * @param int $newHeight       缩略图的高
     * @param string $imagePath    给定的图文件路径
     * @return unknown
     */
    public function creatNewImage($newWidth, $newHeight, $imagePath)
    {
        //取原始图片信息
        $imageInfo = $this->getImageInfo($imagePath);        
        
        $oldWidth  = $imageInfo[0];//取得原始图片宽
        $oldHeight = $imageInfo[1];//取得原始图片高
        
        if( $oldWidth < $newWidth && $oldHeight < $newHeight )//如果原始图片小于缩略图
        {
            $tempWidth  = $oldWidth;
            $tempHeight = $oldHeight;

            $dstx = floor(( $newWidth - $oldWidth )/2 );     //取最终目标缩略图的起始横坐标$dstx
            $dsty = floor(( $newHeight - $oldHeight )/2 );   //取最终目标缩略图的起始纵坐标$dsty
            //$tempimage=$img;
        }
        else //当原始图片高大于宽时
        {
            if( $oldWidth > $oldHeight )
            {
                $tempWidth  = $newWidth; //取临时缩略图的宽
                $tempHeight = floor($oldHeight*$newWidth/$oldWidth);//取临时缩略图的高

                $dstx = 0;//取最终目标缩略图的起始横坐标$dstx
                $dsty = floor(($newHeight - $tempHeight)/2);//取最终目标缩略图的起始纵坐标$dsty
            }
            else
            {
                $tempWidth  = floor($newHeight*$oldWidth/$oldHeight);//取临时缩略图的宽
                $tempHeight = $newHeight;//取临时缩略图的高

                $dstx = floor(($newWidth - $tempWidth)/2);//取最终目标缩略图的起始横坐标$dstx
                $dsty = 0;//取最终目标缩略图的起始纵坐标$dsty
            }
        }
        
        $newImage = $this->creatTempImage($newWidth,$newHeight);
        $oldImage  = $this->creatOldImage($imagePath);
        
        
        //生成最终缩略图
        imagecopyresized($newImage, $oldImage , $dstx , $dsty, 0, 0, $tempWidth, $tempHeight, $oldWidth, $oldHeight);
        
        return $newImage;
    }    

    /**
     * 把生成的图片在浏览器上显示出来
     *
     * @param unknown_type $image         图像标示符
     * @param unknown_type $imagePath     源图的路径
     */
    public function showImage($image,$imagePath)
    {
        //取原始图片信息
        $imageInfo = $this->getImageInfo($imagePath);    
                
        switch($imageInfo[2])
        {
            case 1:    imagegif($image);  break;
            case 2: imagejpeg($image); break;
            case 3:    imagepng($image);  break;
        }    
        
    }
    
    /**
     * 将生成的图像保存到源文件目录下，覆盖原图（可自行扩展）
     *
     * @param unknown_type $image         图像标示符
     * @param unknown_type $imagePath     源图的路径
     */
    public function saveImage($image,$imagePath)
    {
        //取原始图片信息
        $imageInfo = $this->getImageInfo($imagePath);    
                
        switch($imageInfo[2])
        {
            case 1:    imagegif($image,$imagePath);  break;
            case 2: imagejpeg($image,$imagePath); break;
            case 3:    imagepng($image,$imagePath);  break;
        }                
    }
    
    /**
     * 销毁一图像
     *
     * @param unknown_type $image 图像标示符
     */
    public function destoryImage($image)
    {
        imagedestroy($image);
    }
}

//============================================
//for example
//============================================
$imagePath = "image/test.jpg";
$image = new CreatSmallImage();
$newImage = $image->creatNewImage(200, 300, $imagePath);
$image->showImage($newImage,$imagePath);
$image->saveImage($newImage,$imagePath);
$image->destoryImage($image);
?>
