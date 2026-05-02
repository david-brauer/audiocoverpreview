<?php
use OCA\AudioCoverPreview\SystemCapabilities\AbstractCapability;
use OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\AbstractImageMagickCapability;

$capabilities =[
    'ffmpeg' =>new OCA\AudioCoverPreview\SystemCapabilities\FfmpegCapability,
    'im7' =>new OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Imagemagick7Capability,
    'im6' => new OCA\AudioCoverPreview\SystemCapabilities\Imagemagick\Imagemagick6Capability,
];
?>
<div style="margin:35px">
    <h2>System Compatibility</h2>
    <p>
        This App relies on some 3rd Party Tools that need to be installed on the server. Even having all checks green does not always
        mean that there is no problem. However if you have any problems and see failed checks here you might investigate those first.
    </p>
    <h3>ffmpeg</h3>
    <p>ffmpeg must be installed and usable by the app. If you get an error here the app probably will not work.</p>
    
    <?php echo getFfmpegSection($capabilities['ffmpeg']);?>

    <h3>Imagemagick</h3>
    <p>
      Imagemagick is used to fix the some image files that ffmpeg output. Sometimes the files are not readible by the PHP GD Library.
      This only happens sometimes. If you don't have any issues you can ignore the warning. Otherwise you might have a problem that
      could be solved by processing the file with imagemagick after ffmpeg to fix marker issues.
    </p>

    <?php
      if($capabilities['im7']->hasCapability() === true || $capabilities['im6']->hasCapability() === true){
      }
      echo getImagemagicSection($capabilities,$imageFormat);
    ?>
    <h2>Advanced Usage</h2>
    <?php
     echo getSkipChecksSection($skipChecks);
    ?>

</div>

<?php
 function getFfmpegSection(AbstractCapability $capability) :string
 {
  if($capability->hasCapability() === true){
    return renderBox('ffmpeg is installed and detected<br/>'.$capability->getVersionString(),'#D8F3DA');
  }
  return renderBox('ffmpeg is not installed or not found. This will cause errors','#8A0000');
 }

 function getImagemagicSection(array $capabilities,string $currentFormat):string
 {
  if($capabilities['im7']->hasCapability() === true){
    return renderBox('imagemagick 7 is installed and detected<br/>'.$capabilities['im7']->getVersionString(),'#D8F3DA')
      . getImageMagickFormatSupport($capabilities['im7'], $currentFormat);
  }
  if($capabilities['im6']->hasCapability() === true){
    return renderBox('imagemagick 6 is installed and detected <br/>'.$capabilities['im6']->getVersionString(),'#D8F3DA')
      . getImageMagickFormatSupport($capabilities['im6'],$currentFormat);
  }
  return renderBox('imagemagick is not installed or detected. This might cause errors on some album covers','#FFEEC5');
 }

 function getImageMagickFormatSupport(AbstractImageMagickCapability $capability,string $currentFormat)
 {
          $html= '
          <h3>Supported Imagemagick Formats</h3>
          <p> As Imagemagick is installed we can re-encode the image to use a different format before it gets loaded into gd.
              Your Imagemagick has to support reading of jpg and writing of your desired format for that. Also the
              App and php-gd have to support the format Below are the supported formats. If your desired format is not listed or
              is listed as not supported by your Imagemagick it might not work and the App will try to fall back to jpg.
          </p>
          <br/>
          <p> Your current re-encode format is: <strong>'.$currentFormat. '</strong>
          </p>
          <br/>
          <p>    
              The command for changing that is below (replace "format" with one of the supported formats below:)<br/>
              <code>occ config:app:set --value="format" audiocoverpreview image_format</code>
          </p>
          <p><strong> After changing this you need to reset generated previews to force re-generation in the new format</strong> 
          ';
        $html.= '
          <h4>JPG (Default format)</h4>
          <p><strong>The default format that needs to be supported</strong></p>
        ';
        $html.=getSpecificFormatCapabilities('JPG', $capability,true,true);
        $html.='
        <h4>PNG</h4>
        <p> If supported you can re-encode your previews to the png format</p>
        ';
        $html.=getSpecificFormatCapabilities('PNG', $capability,false,true);
        $html.='
        <h4>WEBP</h4>
        <p> If supported you can re-encode your previews to the webp format. This has the benefit of smaller image sizes.</p>
        ';
        $html.=getSpecificFormatCapabilities('WEBP', $capability,false,true);

  return $html;
 }

 function getSpecificFormatCapabilities(
  string  $format, 
  AbstractImageMagickCapability $capability,
  bool $needsRead = false,
  bool $needsWrite = false
  ):string
  {
    $formatSupport = $capability->getFormatByName($format);
    // No support in installed IM
    if($formatSupport === null){
        return renderBox($format.' is not supported. This likely will not work.','#8A0000');
    }
    // Full support
    if($formatSupport->canRead() && $formatSupport->canWrite()){
      return renderBox($formatSupport->getName().' is fully supported ('.$formatSupport->getDescription().')','#D8F3DA');
    }
    // No read/write support
    if(!$formatSupport->canRead() && !$formatSupport->canWrite()){
      return renderBox($formatSupport->getName().' has no read and no write support. This likely will not work','#8A0000');
    }
    // Check if the missing one is read and we need read
    if($formatSupport->canRead() && $needsRead){
      return renderBox($formatSupport->getName().' has no read support. This likely will not work ('.$formatSupport->getDescription().')','#8A0000');
    }
    // Check if the missing one is write and we need write
    if($formatSupport->canWrite() && $needsWrite){
      return renderBox($formatSupport->getName().' has no write support. This likely will not work ('.$formatSupport->getDescription().')','#8A0000');
    }

    // The one we need is supported. Still show this with warning status
    return renderBox($formatSupport->getName().' has no read or write support. It might work but is not optimal','#FFEEC5');
 }

  function getSkipChecksSection(bool $skipChecks){
   $html = '<p>If you have set up the dependencies correctly and everything is working.
               You may consider setting this setting to true. This settings skips most environment checks for ffmpeg and imagemagick.
               This will disable the checks as long as the setting is set. The main reason for this is to get a bit more performance by
               not checking everything with every image that is fetched for isAvailable() and also every generation.
            </p>
            <br/>
            <p>Your current skipChecks setting is: <strong>'.($skipChecks ?'true':'false'). '</strong>
            </p>
            <br/>
            </p>
               The command for changing that is below (replace "skipChecks" with true to enable or false to disable<br/>
              <code>occ config:app:set --value=skipChecks audiocoverpreview skip_checks</code>
            </p>';
   return $html;         
 }

 function renderBox(string $message, string $color):string
 {
  return '<div style="background:'.$color.';border-radius:5px;margin-top:5px;margin-left:5px">
    <div style="padding: 0 5px 0 5px;font-weight: bold;">'.$message.
      '</div>      
    </div><br/>';
 }
 ?>
