<?php include 'include/header-logon.php';

?>

<div id="addCommentLink">
    
<a href="" onclick="document.getElementById('commentForm').style.display='block';return false;" >Добави коментар</a>
<div id="messageAddComment"><?= $message; ?></div>
<div id="errorMessageAddComment"><?= $errorMessage['addComment']; ?></div>
 <div id="info-free">
     
     <b>През този линк може да добавите нов коментар</b>

 </div>  
</div>

<div id="commentForm">

<form name="comment" method="POST" >

<textarea name="limitedtextarea" rows="10" cols="40"
onKeyDown="limitText(this.form.limitedtextarea,this.form.countdown,500);" 
onKeyUp="limitText(this.form.limitedtextarea,this.form.countdown,500);">
</textarea>

<br>

<font>Оставащи символи</font><input readonly type="text" name="countdown" size="3" value="500">

<input type="submit" value="Публикувай"/>

</form>

</div>

<div id="footer">
    
    <div id="menu-criteria">
        
        <div id="pageTitle-comments">
        Коментари за книга "<?= $bookName; ?>"
        </div>
        
        <div id="comments-filter"> 

          <div>Сортирай по:</div>
    
          <div>
        
          <form name="filter" method="GET">
              
            <input type="hidden" name="book_id" value='<?= $selectedSort['selectedBook'] ?>'/>
            <input type="hidden" name="page" value='<?= $selectedSort['selectedPage'] ?>'/>
              
            <select name="sort" size=1 onChange="filter.submit();">

              <option <?=$selectedSort[0]; ?> value=0>дата->низходящо</option>
              <option <?=$selectedSort[1]; ?> value=1>дата->възходящо</option>
            
            </select>

          </form>
        
          </div>
    
        </div>
        
    </div>
      
<?php 

foreach($comments as $comment){

  echo '<div id="comment-data">

<div id="row">

  <div id="row-user">
    <div># '.$comment['username'].'(потребител)</div>
  </div>

  <div id="row-date-publish">
    <div>Публикувано: '.$comment['comment_date'].'</div>
  </div>
    
</div>

<div id="row">

<div id="column">

<div id="column-left"><b>мнения</b></div> <div id="column-right">'.$comment['user_comments_sum'].'</div>
<div id="column-left"><b>регистриран</b></div> <div id="column-right">'.$comment['date_reg'].'</div>
<div id="column-left"><b>години</b></div> <div id="column-right">'.$comment['age'].'</div>
<div id="column-left"><b>пол</b></div> <div id="column-right">'.$comment['sex'].'</div>
<div id="column-left"><b>град</b></div> <div id="column-right">'.$comment['city'].'</div>

</div>

<div id="comment-content">

<div>'.$comment['comment_text'].'</div>

</div>

</div>

</div>';
} 
        
        
?>        

<?='<div id="errorMessageCommentsList">'.$errorMessage['commentsList'].'</div>' ?>

    <div id="footer-pages">
        
        <div id="pages">
            
       <?php
            foreach($pages as $key=>$page){
                
                echo '<a '.$page['isActivePage'].' href='.action('HomeController@comments',array('book_id'=>$page['book_id'],'page' => $key,'sort'=>$selectedSort['selectedSort'])).'>'.$page['start_from'].'-'.$page['end'].'</a> ';
                
            }

       ?>
            
        </div> 
    </div>     

<?php include 'include/footer.php';

?>   