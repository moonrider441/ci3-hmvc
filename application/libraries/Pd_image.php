<?php

class Pd_image
{
    public $pci;
    public $image;
    public $image_type;
    private $base_path;
    private $upload_path;
    private $default_address;

    function __construct()
    {
        $this->pci =& get_instance();
        $this->base_path = FCPATH . 'uploads/';
        $this->upload_path = $this->base_path . 'temp/';
        $this->default_address = $this->base_path;
    }

    function init($params)
    {
        if (isset($params['path']))
            $this->base_path = $params['path'];
        if (isset($params['default_address']))
            $this->default_address = $params['default_address'];
    }

    function getWidth()
    {
        return imagesx($this->image);
    }

    function getHeight()
    {
        return imagesy($this->image);
    }

    function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    function resize($width, $height)
    {

        $current_width = $this->getWidth();
        $current_height = $this->getHeight();


        switch ($this->image_type) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_PNG:
                $new_image = $this->imagePngCreate($width, $height);
                break;
            default:
                //$new_image = $this->imageColorFill($dest['width'], $dest['height']);
                $new_image = imagecreatetruecolor($width, $height);
                break;
        }

        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $current_width, $current_height);
        $this->image = $new_image;
    }

    function load($filename)
    {
        $image_info = $this->isPicture($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
        return $image_info;
    }

    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 90, $permissions = null)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
        imagedestroy($this->image);
    }

    /** Check image upload */
    function checkSize($file, $maxFileSize)
    {
        if ($file['size'] > $maxFileSize) {
            return false;
            //Tools::displayError('image is too large').' ('.($file['size'] / 1024).Tools::displayError('KB').'). '.Tools::displayError('Maximum allowed:').' '.($maxFileSize / 1048576).Tools::displayError('MB');
        }
        return true;
    }

    function isPicture($file, $types = NULL)
    {
        $image_info = getimagesize($file);
        return $image_info;
    }

    function imageUpload($fieldName, $newfilename)
    {
        if (isset($_FILES[$fieldName]) &&
            file_exists($_FILES[$fieldName]['tmp_name']) &&
            (isset($_FILES[$fieldName]['error']) && !$_FILES[$fieldName]['error']) ||
            (!empty($_FILES[$fieldName]['tmp_name']) AND $_FILES[$fieldName]['tmp_name'] != 'none')) {
            $filename = $_FILES[$fieldName]['name'];
            $file = $_FILES[$fieldName]['tmp_name'];

            if ($this->checkSize($_FILES[$fieldName], _UPLOAD_SIZE_)) {// MAX UPLOAD FILE 2MB (2000KB)
                if ($this->isPicture($file)) {

                    $ext = Tools::getFileExtention($_FILES[$fieldName]['name']);

                    if (!copy($file, $newfilename . '.' . $ext))
                        return 'ERROR_PERMISION';
                    @unlink($file);

                    return 'UPLOADED';
                } else
                    return 'ERROR_NOT_IMAGE';
            } else
                return 'ERROR_SIZE';
        } else
            return 'ERROR_SYSTEM';
    }

    /** Resize to specific size */
    function image_resize_to($sourceFile, $destFile, $width = NULL, $height = NULL)
    {
        if ($this->load($sourceFile) === false)
            return false;
        $this->resize($width, $height);
        $this->save($destFile, $this->image_type);
    }

    /** Scale in percent */
    function imageScale($sourceFile, $destFile, $scale)
    {
        if ($this->load($sourceFile) === false)
            return false;

        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);

        $this->save($destFile);
    }

    /** Resize, cut and optimize image */
    function image_resize_fit_to($sourceFile, $destFile, $frameWidth, $frameHeight)
    {
        if ($this->load($sourceFile) === false)
            return false;

//        pr($sourceFile);
//        pre($destFile);
        $imgW = $this->getWidth();
        $imgH = $this->getHeight();

        $ratio = min($frameWidth / $imgW, $frameHeight / $imgH);
        $width = $this->getWidth() * $ratio;
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);

        $this->save($destFile);
    }

    function imageToHeightWidth($sourceFile, $destFile, $size, $type)
    {
        if ($this->load($sourceFile) === false)
            return false;

        if (strtoupper($type) == 'HEIGHT') {
            $this->resizeToHeight($size);
        } elseif (strtoupper($type) == 'WIDTH') {
            $this->resizeToWidth($size);
        }

        $this->save($destFile);
    }

    function imageWatermark($sourceFile, $frmFile, $destFile, $frmLeftOfset, $frmTopOfset)
    {
        if ($this->load($sourceFile) === false)
            return false;


        $top_image = imagecreatefrompng($frmFile);
        imagesavealpha($top_image, false);
        imagealphablending($top_image, false);


        list($frmWidth, $frmHeight, $frmtype, $frmattr) = getimagesize($frmFile);

        if (!$frmTopOfset) {
            list($srcwidth, $srcheight, $srctype, $srcattr) = getimagesize($destFile);
            $frmTopOfset = $srcheight - $frmHeight - 0;
        }
        if (!$frmLeftOfset) {
            $frmLeftOfset = 0;
        }
        imagecopy($this->image, $top_image, $frmLeftOfset, $frmTopOfset, 0, 0, $frmWidth, $frmHeight);
        //	imagepng($bottom_image, $desFile);
        if (!$destFile) {
            $destFile = $sourceFile;
        }

        $this->save($destFile);
    }

    function imageColorFill($width, $height, $color = '#000000')
    {
        $rgb = Tools::hexTorgb($color);
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, $rgb['red'], $rgb['green'], $rgb['blue']);
        imagefill($image, 0, 0, $white);
        return $image;
    }

    /**
     * image create png
     */
    function imagePngCreate($width, $height)
    {
        $newImg = imagecreatetruecolor($width, $height);//$this->image;
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
        return $newImg;
    }

    /**
     * Cut image
     */
    function imageCut($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg', $destX = 0, $destY = 0)
    {
        $srcInfos = $this->load($sourceFile);
        if ($srcInfos === false)
            return false;

        // Source infos
        $src['width'] = $srcInfos[0];
        $src['height'] = $srcInfos[1];
        $src['ressource'] = $this->image;

        // Destination infos
        $dest['x'] = $destX;
        $dest['y'] = $destY;
        $dest['width'] = $destWidth != NULL ? $destWidth : $src['width'];
        $dest['height'] = $destHeight != NULL ? $destHeight : $src['height'];

        switch (strtolower($fileType)) {
            case 'gif':
            case 'png':
                $dest['ressource'] = $this->imagePngCreate($dest['width'], $dest['height']);
                break;
            default:
                $dest['ressource'] = $this->imageColorFill($dest['width'], $dest['height']);
                break;
        }

        imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
        /*$white = imagecolorallocate($dest['ressource'], 255, 255, 255);
        imagecolortransparent($dest['ressource'], $white);*/

        $this->image = $dest['ressource'];
        $this->save($destFile, $this->image_type);
    }

    /*
     * show image by set header
     */
    function imageDisplayHeader($file)
    {
        $filename = basename($file);
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));

        switch ($file_extension) {
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            default:
        }

        header('Content-type:' . $ctype);
        readfile(realpath($file));

        exit();
    }

    /*
     * Resize To
     */
    function ResizeTo($filename, $nowadd, $destadd, $width, $height, $isWatermark = FALSE, $wateradd = FALSE)
    {
        $destnormal = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $destadd . '/' . $filename;
        $watermarkdest = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $wateradd . '/' . $filename;
        $framfile = $_SERVER['DOCUMENT_ROOT'] . '/uploads/watermark/' . $width . '.png';
//        if(!is_dir($destnormal))
//        {
//            $oldmask = umask(0);
//            mkdir($_SERVER['DOCUMENT_ROOT'] . '/uploads/'. $destadd, 0777);
//            umask($oldmask);
//        }
        $this->image_resize_to($nowadd, $destnormal, $width, $height);
        if ($isWatermark)
            $this->imageWatermark($destnormal, $framfile, $watermarkdest, 0, 0);
    }

    /*
     * Resize Fit To
     */
    function resize_fit_to($filename, $nowadd, $destadd, $width, $height)
    {
        $dest = FCPATH . 'media/' . $destadd . '/' . $filename;
        pr($dest);
        $this->image_resize_fit_to($nowadd, $dest, $width, $height);
    }

    function get_image_address($params = array())
    {
        if (!isset($params['ident']))
            return false;

        $ident = $params['ident'];
        $id = isset($params['id']) ? $params['id'] : 0;
        $default = isset($params['default']) ? $params['default'] : false;
        $ftp = isset($params['ftp']) ? $params['ftp'] : 0;
        $default = $default ? $default : '/asset/upload/default/default-700x700.jpg';
        $source = isset($params['source']) ? $params['source'] : '';
        $address = isset($params['address']) ? $params['address'] : '';
        $html = isset($params['html']) ? $params['html'] : false;
        $html_type = isset($params['html_type']) ? $params['html_type'] : 'default';
        $title = isset($params['title']) ? $params['title'] : '';
        $attr = isset($params['attr']) ? $params['attr'] : '';
        $no_image_action = isset($params['no_image_action']) ? true : false;


        if($ftp) {
            $base_url = '';
            $image_size = isset($params['size']) ? '-'.$params['size'] : '';
            $image_info = get_file_info_pd($ident, 'all');
            $pic = _CDN_BASE_URL_ . '/'.$source.'/'. $image_info['filename'].$image_size.'.'.$image_info['extension'];
        }
        else {
            $base_url = base_url('asset/');
            $pic = glob(FCPATH . '/' . $source . '/' . $ident . '.*');
            if (isset($pic[0]) && file_exists($pic[0])) {
                if ($address != '')
                    $pic = $address . '/' . basename($pic[0]);
                else
                    $pic = $source . '/' . basename($pic[0]);
            } else {
                if ($no_image_action)
                    return false;
                $pic = $default;
            }
        }


        $title = $title ? $title : basename($pic);
        if ($html) {
            switch ($html_type) {
                case 'default':
                default:
                    $result = sprintf('<img src="%s" title="%s" alt="%s"%s />', $base_url . $pic, $title, $title, $attr);
                    break;
                case 'admin':
                    $img_html = sprintf('<img src="%s" title="%s" alt="%s"%s />', $base_url . $pic, $title, $title, $attr);
                    $a_class = isset($params['a_class']) ? ' '.$params['a_class'] : '';
                    $a_attr = isset($params['a_attr']) ? ' '.$params['a_attr'] : '';
                    $path = isset($params['path']) ? $params['path'] : $source;
                    if($ftp)
                        $a_attr .= ' data-ftp="1"';
                    $full_delete = '';
                    if(isset($params['full_delete'])){
                        $full_delete = sprintf('<div class="col-md-12 margin-top-10"><a href="%s" data-path="%s" data-full="1" class="btn btn-warning btn-lg col-md-12 delete-img%s"%s title="حذف برای همیشه"><i class="fa fa-trash"></i>&nbsp;حذف برای همیشه</a></div>',
                            encrypt_it($id), encrypt_it($path), $a_class, $a_attr);
                    }
                    $result = sprintf('
                                    <div class="col-md-3 img-box">
                                        <div class="col-md-12">%s</div>
                                        <div class="col-md-12 margin-top-10"><a href="%s" data-path="%s" class="btn btn-danger btn-lg col-md-12 delete-img%s"%s title="حذف تصویر"><i class="fa fa-trash"></i></a></div>
                                        %s
                                    </div>',
                        $img_html, encrypt_it($id), encrypt_it($path), $a_class, $a_attr, $full_delete);
                    //' data-full="1"'
                    break;
            }
        } else
            $result = base_url($pic);
        return $result;
    }

    function save_image($params = array())
    {
        if (!is_array($params) || (is_array($params) && empty($params)))
            return false;

        $remove_mines_size = isset($params['remove_mines_size']) ? $params['remove_mines_size'] : false;
        $files = isset($params['files']) ? $params['files'] : array();
        $timestamp = isset($params['timestamp']) ? $params['timestamp'] : '';
        $file_ident = isset($params['id']) ? $params['id'] : rand(1000000, 99999999);
        $save_address = isset($params['address']) ? $params['address'] : $this->default_address;
        $sizes = isset($params['size']) ? $params['size'] : array(array('50', '50'), array('100', '100'), array('500', '500'));;


        if (!empty($files)) {
            $this->pci->load->helper('file');
            foreach ($files as $indx => $file) {
                $path = $this->base_path;
                $upload_path = $this->upload_path;
                $phFile = $upload_path . $timestamp . $file;

                $filename = basename($file);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $filename = $file_ident . '.' . $ext;

                if (file_exists($phFile)) {
                    $org_dest = $save_address;

                    if (!is_dir($org_dest)) {
                        $oldmask = umask(0);
                        mkdir($org_dest, 0777);
                        umask($oldmask);
                    }
                    $dest = $org_dest . $filename;
                    $old = glob($save_address . $file_ident . '.*');
//                    $old_normal = glob($save_address . $file_ident . '*.*');
                    if (!empty($old)) {
//                        $path = $path . '../product/';
                        foreach ($old as $item) {
                            $basename = basename($item);
                            unlink($save_address . $basename);
                        }
                    }
                    rename($phFile, $dest);

                    $youraddress = $dest;
                    $newaddress = $save_address;
                    foreach ($sizes as $size) {
                        $width_size = $size[0];
                        $height_size = $size[1];
                        $new_fileaddress = $file_ident . '-' . $width_size . 'x' . $height_size;
                        $new_file_ext = '.' . $ext;

                        list($width, $height) = getimagesize($youraddress);

                        $old = glob($save_address . $new_fileaddress . '.*');
                        if (!empty($old)) {
                            foreach ($old as $item) {
                                $basename = basename($item);
                                unlink($save_address . $basename);
                            }
                        }
                        if (($remove_mines_size && ($width >= $width_size || $height >= $height_size)) || !$remove_mines_size) {
                            if (!is_dir($newaddress)) {
                                $oldmask = umask(0);
                                mkdir($newaddress, 0777);
                                umask($oldmask);
                            }
                            $this->image_resize_fit_to($youraddress, $newaddress . $new_fileaddress . $new_file_ext, $width_size, $height_size);
                        }
                    }
                } else
                    return false;
            }
        }
        return true;
    }

    function save_image_ftp($params = array())
    {
        if (!is_array($params) || (is_array($params) && empty($params)))
            return false;

        $ftp = isset($params['ftp']) ? $params['ftp'] : false;
        $remove_mines_size = isset($params['remove_mines_size']) ? $params['remove_mines_size'] : false;
        $files = isset($params['files']) ? $params['files'] : array();
        $timestamp = isset($params['timestamp']) ? $params['timestamp'] : '';
        $file_ident = isset($params['id']) ? $params['id'] : rand(1000000, 99999999);
        $save_address = isset($params['address']) ? $params['address'] : $this->default_address;
        $sizes = isset($params['size']) ? $params['size'] : array(array('50', '50'), array('100', '100'), array('500', '500'));;


        if($ftp) {
            global $CFG;
            $config =& $CFG->config;
            $hostname = isset($params['hostname']) ? $params['hostname'] : _CDN_BASE_URL_;
            $username = isset($params['username']) ? $params['username'] : $config['cdn_ftp_username'];
            $password = isset($params['password']) ? $params['password'] : $config['cdn_ftp_password'];
            if($hostname===null || $username===null || $password===null)
                return false;
            //////////////////////////////////////////////////
            /////////////// FTP Connect to server/////////////
            //////////////////////////////////////////////////
            if (!is_lib('ftp'))
                $this->pci->load->library('ftp');
            $configs = [];
            $configs['hostname'] = $hostname;
            $configs['username'] = $username;
            $configs['password'] = $password;
            $configs['debug'] = TRUE;
            $connect_to_ftp = $this->pci->ftp->connect($configs);
            if(!$connect_to_ftp)
                return false;
        }

        if (!empty($files)) {

//        $this->ftp->upload(FCPATH.'/favicon.ico', 'favicon.ico', 'ascii', 0775);
//        $this->ftp->upload(FCPATH.'/favicon.ico', 'favicon.ico', 'ascii', 0775);

            $this->pci->load->helper('file');
            foreach ($files as $indx => $file) {
                $upload_path = $this->upload_path;
                $phFile = $upload_path . $timestamp . $file;

                $filename = basename($file);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $filename = $file_ident . '.' . $ext;

                if (file_exists($phFile)) {
                    $save_address_full_path = FCPATH.$save_address;
                    $dest = $save_address . $filename;
                    $dest_full_path = FCPATH.$dest;

                    if (!is_dir($save_address_full_path)) {
                        $oldmask = umask(0);
                        mkdir($save_address_full_path, 0777);
                        umask($oldmask);
                    }
                    if($ftp){
                        $ftp_params = [];
                        $ftp_params['action'] = 'checkdir';
                        $ftp_params['auth'] = $config['cdn_default_auth_string'];
                        $ftp_params['address'] = $save_address;
                        post_curl(_CDN_BASE_URL_.'/cdn/server/index',['body'=>array_compress($ftp_params)], $config['cdn_default_auth_string']);
                    }
//                    else
//                    }

                    $old = glob($save_address_full_path . $file_ident . '.*');
                    if (!empty($old)) {
                        foreach ($old as $item) {
                            $basename = basename($item);
                            unlink($save_address_full_path . $basename);
                        }
                    }
                    if($ftp){
                        $ftp_params = [];
                        $ftp_params['action'] = 'remove';
                        $ftp_params['auth'] = $config['cdn_default_auth_string'];
                        $ftp_params['address'] = $save_address;
                        $ftp_params['ident'] = $file_ident;
                        post_curl(_CDN_BASE_URL_.'/cdn/server/index',['body'=>array_compress($ftp_params)], $config['cdn_default_auth_string']);
                    }

                    checkdir($save_address);
                    rename($phFile, $dest_full_path);
                    if($ftp) {
                        $this->pci->ftp->upload($dest_full_path, $dest, 'auto', 0775);
                    }

                    $old = glob($save_address_full_path . $file_ident . '-*.*');
                    if (!empty($old)) {
                        foreach ($old as $item) {
                            $basename = basename($item);
                            unlink($save_address_full_path . $basename);
                        }
                    }
                    if($ftp){
                        $ftp_params = [];
                        $ftp_params['action'] = 'remove';
                        $ftp_params['auth'] = $config['cdn_default_auth_string'];
                        $ftp_params['address'] = $save_address;
                        $ftp_params['ident'] = $file_ident . '-*';
                        post_curl(_CDN_BASE_URL_.'/cdn/server/index',['body'=>array_compress($ftp_params)], $config['cdn_default_auth_string']);
                    }

                    foreach ($sizes as $size_indx=>$size) {
                        $width_size = $size[0];
                        $height_size = $size[1];
                        $new_file_ident = $file_ident . '-' . $width_size . 'x' . $height_size;
                        $new_file_ext = '.' . $ext;

                        list($width, $height) = getimagesize($save_address_full_path);
                        if (($remove_mines_size && ($width >= $width_size || $height >= $height_size)) || !$remove_mines_size) {
                            $this->image_resize_fit_to($dest_full_path, $save_address_full_path . $new_file_ident . $new_file_ext, $width_size, $height_size);
                            if($ftp) {
                                $this->pci->ftp->upload($save_address_full_path.$new_file_ident.$new_file_ext, $save_address.$new_file_ident.$new_file_ext, 'auto', 0775);
//                                unlink($save_address_full_path.$new_file_ident.$new_file_ext);
//                                if(sizeof($sizes)==($size_indx+1)){
//                                    unlink($dest_full_path);
//                                    $this->pci->ftp->close();
//                                }
                            }
                        }
                    }
                } else
                    return false;
            }
        }
        else
            return false;
        return true;
    }
}
?>
