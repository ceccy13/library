<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo asset('css/style.css')?>" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="<?php echo asset('js/code.js')?>">
</script>
</head>
<body>
    
<div id="container">
        
 <div id="personal-info">
        
      <div id="welcome"> 
        
          Добре дошъл, <name><?=$user; ?></name> 
        
      </div>
        
    </div>
    
<div id="header">

    <div id="links">
      <div><a href="<?= action('HomeController@index'); ?>">Списък книги</a></div>
      <div><a href="<?= action('HomeController@addBook'); ?>">Добави Книга</a></div>
      <div><a href="<?= action('HomeController@addAuthor'); ?>">Добави Автор</a></div>
      <div><a href="<?= action('HomeController@logout'); ?>">Изход</a></div>
    </div>
</div>