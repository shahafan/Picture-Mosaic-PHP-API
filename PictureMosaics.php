<?php
/* PictureMosaics.php
 * Author: Shahaf Antwarg
 *
 * Picture Mosaic library file
 */
class PictureMosaics
{

  protected $token = '';
  protected $pid = '';
  protected $apiURL = 'https://picturemosaics.com/photo-mosaic-tool/api/';

  function __construct($token)
  {
    $this->token = $token;
  }

  public function setProjectId($id)
  {
    $this->pid = $id;
  }
  public function getProjectId()
  {
    return $this->pid;
  }


  /**
   * Get a Project ID
   *
   * @return {"pid" : "XXXXXXXX" , "success" : 1}
   */
  public function createProject()
  {
    $post_data = array(
      'token' => $this->token
    );
    $result = $this->process($post_data,$this->apiURL.'getPid.php');
    return $result;
  }

  /**
   * Submitting and Updating Source Image
   *
   * @param {type : "jpg" , name : "source" , "file" : "source.jpg"}
   * @return {"file" : "source.jpg" , "success" : 1}
   */
  public function setSourceImage($data)
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid,
      'type' => $data['type'],
      'name'  => $data['name'], // A project can have only 1 source, should always be called source
      'file'  => $data['file'] // only send a file that is 1200px on the longest side
    );
    $result = $this->process($post_data,$this->apiURL.'postSource.php');
    return $result;
  }

  /**
   * Adding and Updating Cell Image
   *
   * @param {type : "jpg" , name : "0000001" , "file" : "0000001.jpg"}
   * @return {"file" : "0000001.jpg" , "success" : 1}
   */
  public function setCell($data)
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid,
      'type' => $data['type'],
      'name' => $data['name'], // A project can 1000’s of cells this should be unique for each cell
      'file' => $data['file'] // only send a file that is 600px on the longest side
    );
    $result = $this->process($post_data,$this->apiURL.'addCell.php');
    return $result;
  }

  /**
   * Removing Single Cell Image
   *
   * @param {name : "0000001.jpg"}
   * @return {"file" : "0000001.jpg" , "success" : 1} - SUCCESS​­ file removed
   * @return {"file" : "0000001.jpg" , "success" : 0} - FAILURE​­ could not find file
   */
  public function removeCell($data)
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid,
      'name' => $data['name']
    );
    $result = $this->process($post_data,$this->apiURL.'removeCell.php');
    return $result;
  }

  /**
   * Removing All Cell Images
   *
   * @return {"success" : 1}
   */
  public function removeAllCells()
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid
    );
    $result = $this->process($post_data,$this->apiURL.'removeAll.php');
    return $result;
  }

  /**
   * Generate Mosaic Preview ​(Estimated time 10-45s)
   *
   * @param {mosaicSize : "medium" , cellSize : "medium" , cellRatio : "1:1" , colorOverlay : "25" , sourceOverlay : "20"}
   * @return
   * {
   * "grid" : "2500", // total grid size columns * rows
   * "cellSizeX" : "0.49", // individual cell width size in inches
   * "cellSizeY" : "0.49", // individual cell width size in inches
   * "printSize" : "24x18", // recommended print size in inches
   * "success" : 1
   * }
   */
  public function generatePreview($data)
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid,
      'mosaicSize' => $data['mosaicSize'], // defaults to medium ­ sizes: small, medium, large, x­large
      'cellSize' => $data['cellSize'], // defaults to medium ­ sizes: small, medium, large, x­large
      'cellRatio' => $data['cellRatio'], // Supported cell ratios width:height ­ 1:1, 3:2, 4:3, 3:4, 2:3
      'colorOverlay' => $data['colorOverlay'], // default 25
      'sourceOverlay' => $data['sourceOverlay'], // default 20
    );
    $result = $this->process($post_data,$this->apiURL.'buildPreview.php');
    return $result;
  }

  /**
   * Generate Mosaic Zoom ​(Estimated time 1-3 minutes)
   * Will use settings from latest preview build
   *
   * @return {"success" : 1}
   */
  public function generateZoom()
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid
    );
    $result = $this->process($post_data,$this->apiURL.'buildZoom.php');
    return $result;
  }

  /**
   * Generate Final File ​(Estimated time 5-8 minutes)
   * Will use settings from latest preview build
   *
   * @return {"success" : 1}
   */
  public function generateFinal()
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid
    );
    $result = $this->process($post_data,$this->apiURL.'buildFinal.php');
    return $result;
  }

  /**
   * Duplicate Project ​(Estimated time 10-45s)
   * Will duplicate what ever pid you send and send back a new pid to reference the duplicate project.
   *
   * @return {"success" : 1 , "duplicate_pid" : "XXXXXXXX"}
   */
  public function duplicateProject()
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid
    );
    $result = $this->process($post_data,$this->apiURL.'duplicateProject.php');
    return $result;
  }

  /**
   * Checking Mosaic Preview Progress ​(Estimated time 10-45s)
   * Checking Mosaic Zoom Progress:​(Estimated time 1-3 minutes)
   * Checking Mosaic File Progress:​(Estimated time 5-8 minutes)
   * Checking Duplicate Progress:​(Estimated time 10-45s)
   * @param {type : "preview"}
   * @return ​(URL will only be return when percentage is at 100%)
   * {
   * "percentage" : "xxx",
   * "url" : "https://URL-to-preview-image"
   * }
   */
  public function checkProgress($data)
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid,
      'type' => $data['type'] // preview | zoom | file | duplicate
    );
    $result = $this->process($post_data,$this->apiURL.'checkProgress.php');
    return $result;
  }

  /**
   * Deleting Project ​(Estimated time 10-45s)
   * Will delete an entire project, so be careful
   *
   * @return {"success" : 1}
   */
  public function deleteProject()
  {
    $post_data = array(
      'token' => $this->token,
      'pid'   => $this->pid
    );
    $result = $this->process($post_data,$this->apiURL.'deleteProject.php');
    return $result;
  }






  public function process($post_data,$url)
  {
    echo json_encode($post_data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, 1 );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $postResult = curl_exec($ch);
    if (curl_errno($ch)) {
      //print curl_error($ch);
    }
    curl_close($ch);
    return $postResult;
  }

}
