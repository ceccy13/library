<?php include 'include/header-logon.php';

?>
   
<div id="footer">
    
<div id="pageTitle-addBook">Регистрация на нова книга</div> 

<div id="errorMessageBookAndAuthor"><?= $errorMessage['registerBook']; ?></div>
<div id="messageBookAndAuthor"><?= $message['registerBook']; ?></div>

<div id="book-data">
    
<div id="bookForm">    
    
<form method="POST">
    
<div>Име на нова книгата:</div>
<div><input type="text" name="book" value='<?= $insertedData['book_name']; ?>' /></div>
 
<div id="authorList">
 
<div>Избери автор:</div>
<div>
<select multiple name="author[]"  size="10">
<?php    

foreach($authors as $key=>$author){
    
    echo '<option value='.$key.'>'.$author.'</option>';
    
}
?>
</select>
</div>

</div>

<div><input type="submit" value="Добави" /></div>

</form>

    
    
 </div>   

 <div id="info-free">
     
     <b>В тази страница може да добавите нова книга и автори към нея от списъка с налични автори(може да изберете повече от един автор като задържите бутона CTRL и селектирате с мишката)</b>
     <b>Ако желания от вас автор не фигурира в списъка с автори може да бъде добавен от страницата Регистрация на нов автор през линка</b>
     <name>Добави Автор</name>
     <b>в менюто по-горе</b>
     
 </div>  
    
</div>     

<?php include 'include/footer.php';

?>     
