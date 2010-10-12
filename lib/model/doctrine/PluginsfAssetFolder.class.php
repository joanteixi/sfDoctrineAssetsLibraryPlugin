<?php

/**
 * PluginsfAssetFolder
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginsfAssetFolder extends BasesfAssetFolder
{
  /**
   * @return string
   */
  public function getFullPath()
  {
    return sfAssetsLibraryTools::getMediaDir(true) . $this->getRelativePath();
  }

  /**
   * Gives the URL for the given folder
   *
   * @return string
   */
  public function getUrl()
  {
    return sfAssetsLibraryTools::getMediaDir() . $this->getRelativePath();
  }
    /**
     * @param  Doctrine_Connection $con
     * @return integer
     */
    public function save(Doctrine_Connection $con = null)
    {
      $modified = $this->getModified();
      if (!array_key_exists('relative_path', $modified))
      {
        if ($parent = $this->getNode()->getParent())
        {
          $this->setRelativePath($parent->getRelativePath().'/'.$this->getName());
        }
        else
        {
          $this->setRelativePath($this->getName());
        }
      }
      // physical existence
      if (!$this->existsPhysical())
      {
        if (!$this->create())
        {
          throw new sfAssetException('Impossible to create folder "%name%"', array('%name%' => $this->getRelativePath()));
        }
      }
  
      return parent::save($con);
    }

//    /**
//     * @param  PropelPDO     $con
//     * @return sfAssetFolder
//     */
//    public function retrieveParentIgnoringPooling()
//    {
//      return $this->getNode()->getParent();
//    }
    
    /**
     * Folder physically exists
     *
     * @return bool
     */
    public function existsPhysical()
    {
      return is_dir($this->getRelativePath()) && is_writable($this->getRelativePath());
    }
    
    /**
     * Physically creates folder
     *
     * @return bool succes
     */
    public function create()
    {
      list ($base, $name) = sfAssetsLibraryTools::splitPath($this->getRelativePath());
  
      return sfAssetsLibraryTools::mkdir($name, $base);
    }
    
    public function isRoot() {
      return $this->getNode()->isRoot();
    }
    
    public function getParent() {
      return $this->getNode()->getParent();
    }

  /**
   * Recursively move assets and folders from $old_path to $new_path
   *
   * @param string $old_path
   * @param string $new_path
   * @return bool success
   */
  static public function movePhysically($old_path, $new_path)
  {
    if (!is_dir($new_path) || !is_writable($new_path))
    {
      $old = umask(0);
      mkdir($new_path, 0770);
      umask($old);
    }

    $files = sfFinder::type('file')->maxdepth(0)->in($old_path);
    $success = true;
    foreach ($files as $file)
    {
      $success = rename($file, $new_path . '/' . basename($file)) && $success;
    }
    if ($success)
    {
      $folders = sfFinder::type('dir')->maxdepth(0)->in($old_path);
      foreach ($folders as $folder)
      {
        $new_name = substr($folder, strlen(realpath($old_path)));
        $success = self::movePhysically($folder, $new_path . '/' . $new_name) && $success;
      }
    }
    $success = @rmdir($old_path) && $success;

    return $success;
  }
    
  /**
   * Move under a new parent
   *
   * @param sfAssetFolder $new_parent
   */
  public function move(sfAssetFolder $new_parent)
  {
    // controls
    if ($this->isRoot())
    {
      throw new sfAssetException('The root folder cannot be moved');
    }
    else if ($new_parent->hasSubFolder($this->getName()))
    {
      throw new sfAssetException('The target folder "%folder%" already contains a folder named "%name%". The folder has not been moved.', array('%folder%' => $new_parent, '%name%' => $this->getName()));
    }
    else if ($new_parent->getNode()->isDescendantOf($this))
    {
      throw new sfAssetException('The target folder cannot be a subfolder of moved folder. The folder has not been moved.');
    }
    else if ($this->getParent() !== $new_parent->getId())
    {
      $descendants = $this->getNode()->getDescendants();
      $old_path = $this->getFullPath();

      $this->getNode()->moveAsLastChildOf($new_parent);
      // Update relative path
      $this->save();

      // move its assets
      self::movePhysically($old_path, $this->getFullPath());

      foreach ($descendants as $descendant)
      {
        // Update relative path
        $descendant->save();
      }
    }
    // else: nothing to do
  }
  
  /**
   * Checks if a name already exists in the list of subfolders to a folder
   *
   * @param string $name A folder name
   * @return bool
   */
  public function hasSubFolder($name)
  {
    if ($this->getNode()->getChildren()) {
      foreach ($this->getNode()->getChildren() as $subfolder)
      {
        if ($subfolder->getName() == $name)
        {
          return true;
        }
      }
    }
    return false;
  }
  
  /**
   * Gets ancestor for the given node if it exists
   *
   * @param      PropelPDO $con Connection to use.
   * @return     mixed    Propel object if exists else false
   */
  public function retrieveParent()
  {
    return $this->getParent()->getNode();
  }

}