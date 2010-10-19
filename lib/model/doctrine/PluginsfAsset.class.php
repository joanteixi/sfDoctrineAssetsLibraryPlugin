<?php

/**
 * PluginsfAsset
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginsfAsset extends BasesfAsset
{
  /**
   * Physically creates asset
   *
   * @param string  $assetPath path to the asset original file
   * @param boolean $move      do move or just copy ?
   * @param boolean $move      check duplicate?
   */
  public function create($assetPath, $move = true, $checkDuplicate = true)
  {
    if (!is_file($assetPath))
    {
      throw new sfAssetException('Asset "%asset%" not found', array('%asset%' => $assetPath));
    }
    // calculate asset properties
    if (!$this->getFilename())
    {
      list(, $filename) = sfAssetsLibraryTools::splitPath($assetPath);
      $this->setFilename($filename);
    }
//
//    // check folder
//    if (!$this->getFolder()->existsPhysical())
//    {
//      $this->getFolder()->create();
//    }
//    // check if a file with this name already exists
//    elseif ($checkDuplicate && sfAssetTable::getInstance()->exists($this->getFolder()->getId(), $this->getFilename()))
//    {
//      $this->setFilename(time() . $this->getFilename());
//    }
//
//    $this->setFilesize((int) filesize($assetPath) / 1024);
//    $this->autoSetType();
//    if (sfConfig::get('app_sfAssetsLibrary_check_type', false) && !in_array($this->getType(), sfConfig::get('app_sfAssetsLibrary_types', array('image', 'txt', 'archive', 'pdf', 'xls', 'doc', 'ppt'))))
//    {
//      throw new sfAssetException('Filetype "%type%" not allowed', array('%type%' => $this->getType()));
//    }
//
//    $ok = $move ? @rename($assetPath, $this->getFullPath()) : @copy($assetPath, $this->getFullPath());
//    if (!$ok)
//    {
//      throw new sfAssetException('A problem occurred during while saving "%file%"', array('%file%' =>  $this->getFullPath()));
//    }
//
//    if ($this->supportsThumbnails())
//    {
//      sfAssetsLibraryTools::createThumbnails($this->getFolderPath(), $this->getFilename(), $this->isPdf());
//    }
  }
  
  /**
   * Get folder relative path
   *
   * @return string
   */
  public function getFolderPath()
  {
    $folder = $this->getFolder();
    if (!$folder)
    {
      throw new Exception(sprintf('You must set define the folder for an asset prior to getting its path. Asset %d doesn\'t have a folder yet.', $this->getFilename()));
    }
    return $folder->getRelativePath();
  }

  /**
   * Gives the file relative path
   *
   * @return string
   */
  public function getRelativePath()
  {
    return $this->getFolderPath() . '/' . $this->getFilename();
  }

  /**
   * Gives full filesystem path
   *
   * @param string $thumbnail_type
   * @return string
   */
  public function getFullPath($thumbnail_type = 'full')
  {
    return sfAssetsLibraryTools::getThumbnailPath($this->getFolderPath(), $this->getFilename(), $thumbnail_type);
  }
  
  public function setFilename($filename)
  {
    $filename = sfAssetsLibraryTools::sanitizeName($filename);
    $this->filename = $filename;
//    parent::setFilename($filename);
  }

  /**
   * Gives the URL for the given thumbnail
   *
   * @param  string  $thumbnail_type
   * @param  string  $relative_path
   * @param  boolean $pdf
   * @return string
   */
  public function getUrl($thumbnail_type = 'full', $relative_path = null, $pdf = false)
  {
    if (is_null($relative_path))
    {
      if (!$folder = $this->getFolder())
      {
        throw new Exception(sprintf('You must set define the folder for an asset prior to getting its path. Asset %d doesn\'t have a folder yet.', $this->getFilename()));
      }
      $relative_path = $folder->getRelativePath();
    }
    $url = sfAssetsLibraryTools::getMediaDir();
    if ($thumbnail_type == 'full')
    {
      $url .= $relative_path . '/' . $this->getFilename();
    }
    else
    {
      $url .= sfAssetsLibraryTools::getThumbnailDir($relative_path, '/') . $thumbnail_type . '_' . $this->getFilename();
    }
    if ($pdf)
    {
      $url = substr($url, 0, -3) . 'jpg';
    }

    return $url;
  }

  public function autoSetType()
  {
    $this->setType(sfAssetsLibraryTools::getType($this->getFullPath()));
  }

  /**
   * @return boolean
   */
  public function isImage()
  {
    return $this->getType() === 'image';
  }

  /**
   * @return boolean
   */
  public function isPdf()
  {
    return $this->getType() === 'pdf';
  }

  /**
   * @return boolean
   */
  public function supportsThumbnails()
  {
    return ($this->isImage() || $this->isPdf()) && class_exists('sfThumbnail');
  }
    
  
}