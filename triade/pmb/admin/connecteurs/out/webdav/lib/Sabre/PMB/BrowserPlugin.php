<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: BrowserPlugin.php,v 1.6 2019/07/01 12:08:41 btafforeau Exp $
namespace Sabre\PMB;

use Sabre\DAV;
use function Sabre\Uri\split;
use function Sabre\HTTP\encodePath;

class BrowserPlugin extends DAV\Browser\Plugin {
	
    public function httpPOSTHandler($method, $uri) {
        if ($method!='POST') return true;
        if (isset($_POST['sabreAction'])) switch($_POST['sabreAction']) {
            case 'mkcol' :
                if (isset($_POST['name']) && trim($_POST['name'])) {
                    // Using basename() because we won't allow slashes
                    list(, $folderName) = split(trim($_POST['name']));
                    $this->server->createDirectory($uri . '/' . $folderName);
                }
                break;
            case 'put' :
                if ($_FILES){
                	$file = $_FILES['file'];
                }else{
                	break;
                }
                $newName = trim($file['name']);
                list(, $newName) = split(trim($file['name']));
               
                if(isset($_POST['name']) && trim($_POST['name'])){
                	$newName2 = trim($_POST['name']);
                	list(, $newName2) = split($newName2);
                	$newName=$newName2.".".extension_fichier($newName);
                }
                
                // Making sure we only have a 'basename' component
                if (is_uploaded_file($file['tmp_name'])) {
                    $this->server->createFile($uri . '/' . $newName, fopen($file['tmp_name'],'r'));
                }
        }
        $this->server->httpResponse->setHeader('Location',$this->server->httpRequest->getUri());
        return false;
    }
    
	public function generateDirectoryIndex($path) {

        $html = "<html>
<head>
  <title>Index for " . $this->escapeHTML($path) . "/ - SabreDAV " . DAV\Version::VERSION . "</title>
  <style type=\"text/css\"> body { Font-family: arial}</style>
</head>
<body>
  <h1>Index for " . $this->escapeHTML($path) . "/</h1>
  <table>
    <tr><th>Name</th><th>Type</th><th>Size</th><th>Last modified</th></tr>
    <tr><td colspan=\"4\"><hr /></td></tr>";
    
    $files = $this->server->getPropertiesIteratorForPath($path,array(
        '{DAV:}displayname',
        '{DAV:}resourcetype',
        '{DAV:}getcontenttype',
        '{DAV:}getcontentlength',
        '{DAV:}getlastmodified',
    ),1);

    $parent = $this->server->tree->getNodeForPath($path);


    if ($path) {

        list($parentUri) = split($path);
        $fullPath = encodePath($this->server->getBaseUri() . $parentUri);

        $html.= "<tr>
<td><a href=\"{$fullPath}\">..</a></td>
<td>[parent]</td>
<td></td>
<td></td>
</tr>";

    }

    foreach($files as $k=>$file) {

        // This is the current directory, we can skip it
        if (rtrim($file['href'],'/')==$path) continue;

        list(, $name) = split($file['href']);

        $type = null;


        if (isset($file[200]['{DAV:}resourcetype'])) {
            $type = $file[200]['{DAV:}resourcetype']->getValue();

            // resourcetype can have multiple values
            if (!is_array($type)) $type = array($type);

            foreach($type as $k=>$v) { 

                // Some name mapping is preferred 
                switch($v) {
                    case '{DAV:}collection' :
                        $type[$k] = 'Collection';
                        break;
                    case '{DAV:}principal' :
                        $type[$k] = 'Principal';
                        break;
                    case '{urn:ietf:params:xml:ns:carddav}addressbook' :
                        $type[$k] = 'Addressbook';
                        break;
                    case '{urn:ietf:params:xml:ns:caldav}calendar' :
                        $type[$k] = 'Calendar';
                        break;
                }

            }
            $content_type = implode(', ', $type);
        }

        // If no resourcetype was found, we attempt to use
        // the contenttype property
        if (empty($content_type) && isset($file[200]['{DAV:}getcontenttype'])) {
            $content_type = $file[200]['{DAV:}getcontenttype'];
        }
        if (empty($content_type)) $content_type = 'Unknown';

        $size = isset($file[200]['{DAV:}getcontentlength'])?(int)$file[200]['{DAV:}getcontentlength']:'';
        $lastmodified = isset($file[200]['{DAV:}getlastmodified'])?$file[200]['{DAV:}getlastmodified']->getTime()->format(DATE_ATOM):'';

        $fullPath = encodePath('/' . trim($this->server->getBaseUri() . ($path?$path . '/':'') . $name,'/'));

        $displayName = isset($file[200]['{DAV:}displayname'])?$file[200]['{DAV:}displayname']:$name;

        $name = $this->escapeHTML($name);
        $displayName = $this->escapeHTML($displayName);
        $content_type = $this->escapeHTML($content_type);

        $html.= "<tr>
<td><a href=\"{$fullPath}\">{$displayName}</a></td>
<td>{$content_type}</td>
<td>{$size}</td>
<td>{$lastmodified}</td>
</tr>";

    }

  $html.= "<tr><td colspan=\"4\"><hr /></td></tr>";

  if ($this->enablePost && $parent instanceof DAV\ICollection) {
      $html.= '<tr><td>
            <form method="post" action="" enctype="multipart/form-data">
            <h3>Upload file</h3>
            <input type="hidden" name="sabreAction" value="put" />
            Name (optional): <input type="text" name="name" /><br />
            File: <input type="file" name="file" /><br />
            <input type="submit" value="upload" />
            </form>
       </td></tr>';
  }

  $html.= "</table>
  <address>Generated by SabreDAV " . DAV\Version::VERSION . " (c)2007-2012 <a href=\"http://code.google.com/p/sabredav/\">http://code.google.com/p/sabredav/</a></address>
</body>
</html>";

        return $html; 

    }    
    
}