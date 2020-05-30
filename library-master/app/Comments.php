<?php
namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Comments {
    
    private $book_id;
    private $comments=array();
    private $errorMessage=array('addComment'=>'',
                                'commentsList'=>''
        
                                );
    private $page;
    private $pages=array();
    private $sort;
    private $comment_text;
    private $user_id;
    private $username;
    private $message=null;
    private $bookName;
	
//Метод за добавяне на коментар за книга
    public function setComment($book_id,$limitedtextarea){
//Ако има POST заявка се изпълнява кода    
    if($_POST){
        
        $error=false;
		
//Правим нормализация на входящите данни
        
//Премахване на празните полета в началото и края на низа
        $this->comment_text=trim($limitedtextarea);
//Премахваме празните полета,където са повече от едно между думите в низа, ако има такива
        $this->comment_text = preg_replace('#[\s]+#', ' ', $this->comment_text);  
        
//Правим валидация на входящите данни
       
/* Дължината на коментара трябва да между 1 и 500 символа,
  (ако условията не са изпълнени се записва съобщение за грешка
   в масива $this->errorMessage) */
        if((mb_strlen($this->comment_text)<1 || mb_strlen($this->comment_text)>500)){ 

            $this->errorMessage['addComment'] = 'Не сте въвели текст';
            
            $error=true;
        
        }
//Ако условията са изпълнени, правим запис на данните в БД
        if(!$error){

            $comment_date=date('Y-m-d H:i:s');
//Взимаме името на потребителя от сесията 
            $this->username=Session::get('user');
//Взимаме id-то за потребителя с това име 
            $user_id=DB::select('SELECT  user_id FROM users 
                                 WHERE username=?
                                 LIMIT 1'
                                ,array($this->username));
            
            $this->user_id=$user_id[0]->user_id;
            
            $this->book_id=(int)$book_id;
//Правим запис на коментар
            DB::insert('INSERT INTO comments (comment_text,comment_date) 
                        VALUES (?,?)'
                       ,array($this->comment_text,$comment_date));
//Взимаме id-то на коментара, който записахме
            $lastInsertedId=DB::connection('mysql')->getPdo()->lastInsertId();
/* Ако имаме запис на коментар и получено id за въведения коментар,
   правим запис на id-то на потребителя,коментара и книгата 
   в таблица users_comments_books */      
            if($lastInsertedId){

            DB::insert('INSERT INTO users_comments_books (user_id,comment_id,book_id) 
                        VALUES (?,?,?)'
                       ,array($this->user_id,$lastInsertedId,$this->book_id));
//Записваме съобщение в масива $this->message
            $this->message='Успешно добавихте коментар';
            
            return true;
            
            }
            
        }
        
     }
    
   }
//Метод за извеждане на коментари към книга
    public function getComments($book_id,$page,$sort) {
//Нормализация на входящите данни
        $this->book_id=(int)$book_id;
        $this->page=(int)$page;
        $this->sort=(int)$sort;
        
        switch ($this->sort) {
    case 0:
        $sort='comment_date DESC';
        break;
    case 1:
        $sort='comment_date ASC';
        break;

    default:
       $sort='comment_date DESC';
}
//Ako имаме id на книга полечно от глобалната GET по-голямо от 0 изпълняваме кода
        if($this->book_id>0){
      
        $q = DB::select('SELECT  comment_id FROM users_comments_books 
                         WHERE book_id=?' 
                        ,array($this->book_id));
//Всички записи на коментари към книгата
        $total_records=count($q);
//Всичките страници към коментарите
        $total_pages = ceil($total_records / 5);
//Ако има коментари и страницата за тях съществува изпълняваме кода
        if($total_records>0 && $this->page>0 && $this->page<=$total_pages){
            
           for ($page=1; $page<=$total_pages; $page++){
//Първи номер на запис за страница
           $start_from = (($page*5) - 4);   
//Последен номер на запис за страница
           $end=$page*5;
/* Ако последния номер на запис за последната страницата е по-голям от броя 
   на всички записите, последен номер на запис за последната страница 
   ще е броя на всички записи */  
           if(($total_records-$end<0)){

           $end=$total_records;
        
            }
    
	   $isActivePage='';
//На избраната страница ще и предадем този стил от style.css 
	   if($this->page==$page){
            
	   $isActivePage='id="activePage"';
        
	    }
//Текущите резултати за линк на страницата се записват в масива $this->pages
           $this->pages[$page]['isActivePage'] = $isActivePage;
           $this->pages[$page]['start_from'] = $start_from;
           $this->pages[$page]['end'] = $end;
           $this->pages[$page]['book_id']=$this->book_id;

          }
//Първи номер на запис за заявката за коментари 
        $start_from = (($this->page - 1) * 5);
//Броя на коментарите,кото ще бъдат изведени за страницата
        $interval = 5;
  
        $q = DB::select('SELECT ucb.book_id, book_name, date_publish,
                         username, date_reg, age, sex, city, 
                         ucb.comment_id, comment_text,comment_date,
                        (SELECT count(comment_id) FROM users_comments_books 
                         WHERE user_id=ucb.user_id) as user_comments_sum
                         FROM users_comments_books as ucb
                         LEFT JOIN books as b ON b.book_id=ucb.book_id
                         LEFT JOIN comments as c ON c.comment_id = ucb.comment_id
                         LEFT JOIN users as u ON u.user_id = ucb.user_id
                         WHERE ucb.book_id=?
                         ORDER BY '.$sort.'
                         LIMIT '.$start_from.','.$interval 
                        ,array($this->book_id));
/* Всички данни от заявката, които искаме да визуализираме 
   за коментари, записваме в масив $this->comments */
        foreach($q as $key=>$comment){
                
        $this->comments[$key]['book_id']=$comment->book_id;
        $this->comments[$key]['book_name']=$comment->book_name;
        $this->comments[$key]['date_publish']=$comment->date_publish;
        $this->comments[$key]['username']=$comment->username;
        $this->comments[$key]['date_reg']=$comment->date_reg;
        $this->comments[$key]['age']=$comment->age;
        $this->comments[$key]['sex']=$comment->sex;
        $this->comments[$key]['city']=$comment->city;
        $this->comments[$key]['comment_text']=$comment->comment_text;
        $this->comments[$key]['comment_date']=$comment->comment_date;
        $this->comments[$key]['user_comments_sum']=$comment->user_comments_sum;
       
            }
//Връщаме този масив при извикване на метода
            return $this->comments;

        }
/* Ако няма коментари или страницата за тях не съществува,
   записваме съобщение за грешка по търсения резултат */
        else{
             $this->errorMessage['commentsList']='Няма коментари за тази книга!!!';
             return $this->comments;
  
        }
        
        }

/* Ако полученото id за книга към стрницата за коментари не съществува 
   извеждаме съобщение за  грешка по търсения резултат */    
        else{
            
             $this->errorMessage='Няма коментари за тази книга!!!';
             return $this->comments;
            
        }
        
}
//Метод за извеждане на последно избраните критерии във view-то
   public function getSelectedSort(){
            
                return $this->insertedData=array(
                
                       '0'=>'',
                       '1'=>'',
                       $this->sort=>'selected',
                       'selectedSort'=>$this->sort, 
                       'selectedPage'=>$this->page,
                       'selectedBook'=>$this->book_id
 
                       );

    }
//Метод за извеждане броя на страниците за коментарите
    public function getPages(){
    
                return $this->pages;
            
    }
//Метод за извеждане на съобщения за грешка
    public function getErrorMessage(){
    
                return $this->errorMessage;
            
    }
//Метод за извеждане на съобщения
    public function getMessage(){
    
                return $this->message;
            
    }
//Метод за извеждане името на коментираната книга 
    public function getBookName(){
    
        $bookName = DB::select('SELECT book_name FROM books
                                WHERE book_id=?
                                LIMIT 1'
                               ,array($this->book_id));
        
               $this->bookName=$bookName[0]->book_name;
               
               return $this->bookName;
        
    }

}

?>
