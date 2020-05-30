<?php include 'include/header-logon.php';

?>

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

</div>

<div id="messageSearchBookAndAuthor"><?= $message['searchBookAndAuthor']; ?></div>   

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
	  
<?php	
echo '<td width="13%"><a id="comments_link" href="'
.action('HomeController@comments',array('book_id'=>$book['book_id'])).'">виж / добави</a>
</td><td>';
              
foreach($book['author'] as $author_id=>$author_name){
    echo     '<a href='.action('HomeController@books',array('author_id'=>$author_id ,'sort'=>$selectedSort['selectedSort'])).'>'.$author_name.'</a>'.'  ';

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
                       
                       echo '<a '.$page['isActivePage'].' href='.action('HomeController@books',array('page' => $key,'author_id' => $selectedSort['author_id'],'sort' => $selectedSort['selectedSort'],'search' => $selectedSort['search'])).'>'.$page['start_from'].'-'.$page['end'].'</a> ';
                       
                   }
          
	  ?>
            
        </div>
	
</div>

<?php include 'include/footer.php';

?>   