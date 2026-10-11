<!DOCTYPE html>

<html lang="en">
	<head>
    		<meta charset="utf-8">
    		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   		<meta name="viewport" content="width=device-width, initial-scale=1">
    		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="description" content="">
		<meta name="keywords" content="">
		<title>DBMM <?=lang("title_Rankings")?></title>
		<link rel="STYLESHEET" type="text/css" href="<?=base_url()?>css/main.css">
		<link rel="STYLESHEET" type="text/css" href="<?=base_url()?>css/popup.css">
		<link rel="STYLESHEET" type="text/css" href="<?=base_url()?>css/addmatchpopup.css">
		<link rel="STYLESHEET" type="text/css" href="<?=base_url()?>css/red_bootstrap.css">
		<link rel="stylesheet" type="text/css" media="all" href="<?=base_url()?>css/jsDatePick_ltr.min.css" />
		<link rel="stylesheet" href="<?=base_url()?>font-awesome/css/font-awesome.min.css">
    		<!-- Bootstrap -->
    		<link href="<?=base_url()?>css/bootstrap.min.css" rel="stylesheet">
    		<!-- Bootstrap theme -->
    		<link href="<?=base_url()?>css/bootstrap-theme.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?=base_url()?>/js/jsDatePick.min.1.3.js"></script>
		<script type="text/JavaScript" src="<?=base_url()?>/js/util.js"></script>
		<script type="text/JavaScript">var baseURL = "<?=site_url()?>";</script>
	   	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    	<!-- Include all compiled plugins (below), or include individual files as needed -->
    	<script src="<?=base_url()?>js/bootstrap.min.js"></script>
		<script type="text/JavaScript" src="<?=base_url()?>/js/md5.min.js"></script>
	</head>
	<body class="body">
		<?php include_once("analyticstracking.php") ?>
    		<div class="container-fluid">
		<div class="row banner">
  			<div style="float: right; width:100px;">
  				<a href="<?=site_url('langswitch/switchLanguage/english')?>">
  					<img src="<?=base_url()?>images/gb.gif">
  				</a>
				<a href="<?=site_url('langswitch/switchLanguage/italian')?>">
					<img src="<?=base_url()?>images/it.gif">
				</a>
			</div>
			<div class="inner_banner" style="margin-right:100px">
				DBMM <?=lang("title_Rankings")?> <?=$header?>
  			</div>
		</div>