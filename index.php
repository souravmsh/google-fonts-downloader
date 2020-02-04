<?php
require_once './GoogleFontsDownloader.php';
$obj = new GoogleFontsDownloader;
?> 
<!DOCTYPE html>
<html>
<head>
	<title>Google Fonts Downloader</title>
	<link href="asset/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
	        <div class="col-12 col-md-10 col-lg-8">
	        	<img src="asset/logo.png" class="img-fluid">
	        </div>
	        <div class="col-12 col-md-10 col-lg-8">
	            <form class="card" action="index.php">
	                <div class="card-body row no-gutters">
	                    <div class="col-auto">
	                        <i class="fas fa-search h4 text-body"></i>
	                    </div>
	                    <!--end of col-->
	                    <div class="col">
	                        <input name="url" class="form-control form-control-lg form-control-borderless rounded-0" type="search" placeholder="eg:- https://fonts.googleapis.com/css?family=Lato:300,400,400i,700" value="<?php echo (!empty($_GET['url'])?$_GET['url']:'') ?>">
	                    </div>
	                    <!--end of col-->
	                    <div class="col-auto">
	                        <button class="btn btn-lg btn-success rounded-0" type="submit">Generate</button>
	                    </div>
	                    <!--end of col-->
	                </div>
	            </form>
	        </div>
	        <div class="col-12 col-md-10 col-lg-8">
					<?php
					if(isset($_GET['url']) && !empty($_GET['url']))
					{
						$obj->generate($_GET['url']);
					}

					if(isset($_GET['download']) && !empty($_GET['download']) && $_GET['download']=='true')
					{
						$obj->download();
					}
					?>
	                        <?php if ($obj->is_downloadable) { ?>
	                        <a class="btn btn-lg btn-warning rounded-0" href="index.php?download=true">Download</a>
	                    	<?php } ?>
	        </div>
	        <!--end of col-->
	    </div> 
	</div> 
	<p class="mt-5 mb-3 text-muted text-center">
		<a href="http://codekernel.net">CodeKernel.Net</a>
	</p> 
</body>
</html> 
 