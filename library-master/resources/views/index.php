<?php include 'include/header.php';

?>
 
      <div><a href="<?= action('HomeController@index'); ?>">Начaло</a></div>
      <div><a href="<?= action('HomeController@registration'); ?>">Регистрация</a></div>
      <div><a onclick="document.getElementById('errorMessageBookAuthor').style.display='block';return false;" href="" >Добави Книга</a></div>
      <div><a onclick="document.getElementById('errorMessageBookAuthor').style.display='block';return false;" href="" >Добави Автор</a></div>

    </div>
 
</div>
    
<div id="errorMessageBookAuthor">За да добавите книга/автор трябва да влезете в системата!!!</div>    
    
<div id="login">
    
   <div id="loginForm"> 
       
      <form method="POST">
        
        <div>Потребителско име:</div>   
        <div><input type="text" name="username" /></div>
        <div>Парола:</div>
        <div><input type="password" name="password" /></div>
    
        <div><input type="submit" value="Вход" /></div>
    
      </form>
       
   </div>


      <div id="errorMessageLogin">

        <?=$errorMessageUser['login']; ?>

      </div>
    
    <div id="info-free"><b>За да влезете в системата 
       без регистрация,може да използвате:</b>
        <b>Потребителско име: <name>user</name></b> 
        <b>Парола: <name>Pass99?</name></b>
    </div>
    
</div>    

<div id="footer"> 
    
    <div id="pageTitle-books">
      Списък книги
    </div>
	
    <div id="menu-criteria">
    
    <div id="footer-search">

      <div>Заглавие:</div>
	
      <div>
          
      <form method="GET">

        <input type="text" name="search" value='<?= $selectedSort['search'] ?>'/>
        <input type="hidden" name="author_id" value='<?= $selectedSort['author_id'] ?>'/>
        <input type="submit" value="Търси"/>
        
      </form>
          
      </div>
        
    </div>   

<div id="footer-filter"> 

    <div>Сортирай по:</div>
    
    <div>
    <form name="filter" method="GET">

    <select name="sort" size=1 onChange="filter.submit();">
            
            <option <?=$selectedSort[0]; ?> value=0>дата->низходящо</option>
            <option <?=$selectedSort[1]; ?> value=1>дата->възходящо</option>
            <option <?=$selectedSort[2]; ?> value=2>книга->низходящо</option>
            <option <?=$selectedSort[3]; ?> value=3>книга->възходящо</option>
            <option <?=$selectedSort[4]; ?> value=4>автор->низходящо</option>
            <option <?=$selectedSort[5]; ?> value=5>автор->възходящо</option>
            
            <input type="hidden" name="author_id" value='<?= $selectedSort['author_id'] ?>'/>
            <input type="hidden" name="page" value='<?= $selectedSort['selectedPage'] ?>'/>
            <input type="hidden" name="search" value='<?= $selectedSort['search'] ?>'/>
            
            
    </select>

    </form>
    </div>  
    
</div>

<div id="messageSearchBookAndAuthor"><?= $message['searchBookAndAuthor']; ?></div>

<div id="errorMessageComments">За да видите/добавите коментари трябва да влезете в системата!!!</div>

<div id="footer-data">
    
   <table>
          <tr>
          <th width="5%">N</th>
          <th width="17%">Дата и час на публикуване</th>
          <th width="29%">Книга</th>
          <th colspan="2" width="16%">Коментари</th>
          <th>Автор</th>
          </tr>

<?php

foreach($books as $row=>$book){
    
    echo '<tr>
          <td>'.$row.'</td>
          <td>'.$book['date_publish'].'</td>
          <td>'.$book['book_name'].'</td>
          <td width="3%" align="right">'.$book['comments_sum'].'</td>';
    
    ?>
		  
<td width="13%"><a id="comments_link" onclick="document.getElementById('errorMessageComments').style.display='block';return false;" href="">виж / добави</a></td>
	  
<?php	
	  
echo  '<td>';
              
foreach($book['author'] as $author_id=>$author_name){
    echo     '<a href='.action('HomeController@index',array('author_id'=>$author_id ,'sort'=>$selectedSort['selectedSort'])).'>'.$author_name.'</a>'.'  ';

}
    echo '</td></tr>';
}

?>
          <tr>
              <td colspan="6" align="center">
               
                   <?=$errorMessage['booksList'] ?>  
                  
              </td>
          </tr>
           
   </table>

	</div>
	
<div id="footer-pages">    
    
	<div id="pages">
        
            <?php
            
                  foreach($pages as $key=>$page){
                       
                       echo '<a '.$page['isActivePage'].' href='.action('HomeController@index',array('page' => $key,'author_id' => $selectedSort['author_id'],'sort' => $selectedSort['selectedSort'],'search' => $selectedSort['search'])).'>'.$page['start_from'].'-'.$page['end'].'</a> ';
                       
                   }
          
	    ?>
            
        </div>
    
</div>    
	
</div>

<?php include 'include/footer.php';

?>       




