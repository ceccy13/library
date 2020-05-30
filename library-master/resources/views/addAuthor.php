<?php include 'include/header-logon.php';

?>
   
<div id="footer">
   
<div id="pageTitle-addAuthor">Регистрация на нов автор</div>     
<div id="errorMessageBookAndAuthor"><?= $errorMessage; ?></div>
<div id="messageBookAndAuthor"><?= $message; ?></div>
<div id="author-data">

<div id="authorForm">    
    
<form method="POST">

    <div>Нов автор:</div><div><input type="text" name="author" /></div>
    
    <div><input type="submit" value="Добави" /></div>

</form>
  
</div>    

<div id="authorList">
    
<div>Списък налични автори:</div>
<div>
<select multiple name="author"  size="10">
<?php    

foreach($authors as $author){
    
    echo '<option>'.$author.'</option>';
    
}
?>
</select>
</div>
    
</div>    
 
<div id="info-free">
    
    <b>В тази страница може да разгледате наличния списък с автори и да добавите нов автор,ако желаният от вас автор не фигурира в него</b>
    <b>За да добавите нова книга с автори отидете на страницата Регистрация на нова книга през линка</b>    
    <name>Добави книга</name>
    <b>в менюто по-горе</b>
    
</div>    
    
</div>        

<?php include 'include/footer.php';

?>       
   