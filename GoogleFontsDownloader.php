<?php
/**
* GoogleFontsDownloader
* Easy way to download any google fonts.
* @author     Shohrab Hossain <sourav.diubd@gmail.com>
* @version    1.0.0 
*/
class GoogleFontsDownloader
{
	private $url      = '';
	private $dir      = 'dist/';
	private $fontsDir = 'fonts/';
	private $cssDir   = 'css/';
	private $fileName = 'fonts.css';
	private $content  = '';
	private $errors   = '';
	private $success  = '';
	public  $is_downloadable  = false;

	public function __construct()
	{
		ini_set('allow_url_fopen', 'on');
		ini_set('allow_url_include', 'on');
	}
 
	public function generate($url = null)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) 
		{
			$this->errors .= "<li><strong>Invalid url!</strong> $url</li>";
		}
		else
		{
			$this->url = $url;
			// delete previous files
			$this->_destroy();
			// write font.css
			$this->_css();
			// write fonts
			$this->_fonts();
			// archive files
			$this->_archive();
		}  
		// show all messages
		$this->_message();
	}
 
	public function download()
	{ 
		// Download the created zip file
		$zipFileName = trim($this->dir, '/').'.zip';
		if (file_exists($zipFileName))
		{
			header("Content-type: application/zip");
			header("Content-Disposition: attachment; filename = $zipFileName");
			header("Pragma: no-cache");
			header("Expires: 0");
			readfile("$zipFileName");
 
			// delete file 
			unlink($zipFileName);
			array_map('unlink', glob("$this->dir/*.*"));
			rmdir($this->dir);

		} 
	}	
 
	private function _archive()
	{
		if (is_dir($this->dir))
		{
			$zipFileName = trim($this->dir, '/').'.zip';
		    $zip = new \ZipArchive(); 
		    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) 
		    {
				$zip->addGlob($this->dir. "*.*");
				$zip->addGlob($this->dir. "*/*.*");
				if ($zip->status == ZIPARCHIVE::ER_OK)
				{
		        	$this->success .= '<li>Zip create successful!</li>';
		        	$this->is_downloadable = true;
				}
			    else 
			    {
			        $this->errors .= '<li>Failed to create to zip</li>';
			    } 
		    } 
		    else 
		    {
		        $this->errors .= '<li>ZipArchive not found!</li>';
		    }  
    		$zip->close(); 
		}
		else
		{
			$this->errors .= "<li><strong>File</strong> not exists!</li>";
		} 
	}	
  
	private function _css()
	{  
		$filePath = $this->dir.$this->cssDir.$this->fileName;
		$content  = $this->_request($this->url);
		if (!empty($content))
		{
			if (file_put_contents($filePath, $content))
			{
				$this->success .= "<li>$this->fileName generated successful!</li>";
				$this->content = $content; 
			}
			else
			{
				$this->errors .= '<li>Permission errro in $this->fileName! Unable to write $filePath.</li>';
			}
		}
		else
		{
			$this->errors .= '<li>Unable to create fonts.css file!</li>';
		}
	}

	private function _fonts()
	{
		if (!empty($this->content))
		{
			preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $this->content, $match);
			$gFontPaths = $match[0];
			if (!empty($gFontPaths) && is_array($gFontPaths) && sizeof($gFontPaths)>0)
			{
				$count = 0;
				foreach ($gFontPaths as $url) 
				{
					$name     = basename($url);
					$filePath = $this->dir.$this->fontsDir.$name;
					$this->content = str_replace($url, '../'.$this->fontsDir.$name, $this->content);

					$fontContent  = $this->_request($url);
					if (!empty($fontContent))
					{
						file_put_contents($filePath, $fontContent);
						$count++;
						$this->success .= "<li>The font $name downloaded!</li>";
					}
					else
					{
						$this->errors .= "<li>Unable to download the font $name!</li>";
					} 
				}

				file_put_contents($this->dir.$this->cssDir.$this->fileName, $this->content);
				$this->success .= "<li>Total $count font(s) downloaded!</li>";
			}
		}
	}

	private function _request($url)
	{
		$ch = curl_init(); 
        curl_setopt_array($ch, array(
        	CURLOPT_SSL_VERIFYPEER => FALSE,
        	CURLOPT_HEADER         => FALSE,
        	CURLOPT_FOLLOWLOCATION => TRUE,
        	CURLOPT_URL            => $url,
        	CURLOPT_REFERER        => $url,
        	CURLOPT_RETURNTRANSFER => TRUE,
        ));
        $result = curl_exec($ch);
        curl_close($ch);

		if (!empty($result))
		{
			return $result;
		} 
		return false;
	}

	private function _destroy()
	{
		$cssPath = $this->dir.$this->cssDir.$this->fileName;
		if (file_exists($cssPath) && is_file($cssPath))
		{
			unlink($cssPath);
		} 
		else
		{
			mkdir($this->dir.$this->cssDir, 0777, true);
		}

		$fontsPath = $this->dir.$this->fontsDir;
		if (!is_dir($fontsPath))
		{
			mkdir($fontsPath, 0777, true);
		}
		else
		{
			array_map(function($font) use($fontsPath) {
				if (file_exists($fontsPath.$font) && is_file($fontsPath.$font))
				{
					unlink($fontsPath.$font);
				}
			}, glob($fontsPath.'*.*')); 
		}
	}

	private function _message()
	{
		if (strlen($this->errors)>0)
		{
			echo "<div class='alert alert-danger'><ul>$this->errors</ul></div>";
		}  
		if (strlen($this->success)>0)
		{
			echo "<div class='alert alert-success'><ul>$this->success</ul></div>";
		} 
	}

}
 
