# Picture Mosaic #

### simple usage ###
```php
<?php
$pictureMosaics = new PictureMosaics('xxxxxxxxxxxxxx');
$project = json_decode($pictureMosaics->createProject(),true);
$data = array(
  'type' => 'jpg',
  'name' => 'source',
  'file' => '@/uploads/products/mosaic/'.$file
);
echo $pictureMosaics->setSourceImage($data);
?>
```
