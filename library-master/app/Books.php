<?php
namespace App;

use Illuminate\Support\Facades\DB;

class Books
{
    private $book_id;
    private $book_name;
    private $date_publish;
    private $sort;
    private $author_id;
    private $selectedSort=array();
    private $page;
    private $pages=array();
    private $search;
    private $total_records;
    private $booksPage=array();
    private $errorMessage=array('booksList'=>'',
                                'registerBook'=>''
        
                                );
    private $authors_ids=array();
    private $insertedBook=null;
    private $insertedAuthors=array(0=>'',
                                   1=>''
        
                                   );
    private $message=array('registerBook'=>'',
                           'searchBookAndAuthor'=>''
       
                           );
    
//С този метод добавяме нова книга с автори към нея
    public function setBook($book_name,$authors_ids)
    {
//Ако има POST заявка, кодът се изпълнява        
        if($_POST){
              
            $error=false;
			
//Правим нормализация на входящите данни

//Премахваме празните полета в началото и края на символния низ, ако има такива
            $this->book_name = trim($book_name);
//Премахваме празните полета,където са повече от едно между думите в низа, ако има такива
            $this->book_name = preg_replace('#[\s]+#', ' ', $this->book_name); 
//Преобразува само първата буква от низа на главна (за кирилица и латиница)
            $this->book_name = mb_strtoupper(mb_substr($this->book_name, 0, 1)).mb_substr($this->book_name, 1);

//Правим валидация на входящите данни
  			
            $isBookExists=DB::select('SELECT * FROM books
                                      WHERE book_name=?
                                      LIMIT 1'
                                     ,array($this->book_name));
/* Ако няма получени id-та за въведени автори,
   записваме съобщение за грешка в масив $this->errorMessage */
        if(empty($authors_ids)){
           
            $this->errorMessage['registerBook'] = 'Не сте избрали автор!!!';
            $error=true;
           
         }
/* Ако получените id-та за автори са повече от 2, 
   записваме съобщение за грешка в масив $this->errorMessage */
        if(!empty($authors_ids) && count($authors_ids)>2){
           
          $this->errorMessage['registerBook'] = 'Може да изберете максимум два автора за една книга!!!';
          $error=true;
           
        }
/* Дължината на името на книгата трябва да е между 3 и 30 символа, и да не съдържа специални символи
  (ако условието не е изпълнено,записваме съобщение за грешка в масив $this->errorMessage) */
        if(mb_strlen($this->book_name)<3 || mb_strlen($this->book_name)>30 || !preg_match('/^[\d\s\p{Cyrillic}a-zA-Z]+$/u',$this->book_name)){
            
           $this->errorMessage['registerBook'] = 'името на книгата трябва да е между 3 и 30 символа, и да не съдържа специални символи!!!';
           $error=true;
	       $this->book_name='';
        }
/* Ако книгата съществува, записваме съобщение за грешка
   в масив $this->errorMessage */   
	    if(count($isBookExists)>0){
                
           $this->errorMessage['registerBook'] = 'тази книга вече е регистрирана!!!';
           $error=true; 
           $this->book_name='';    
        }
//Ако условията за регистриране на книга са изпълнени, записваме данните в БД	   
        if(!$error){
  
            $date_publish=date('Y-m-d H:i:s');
            
            $this->insertedBook=$this->book_name;
//Записваме данните за книгата
            $query_newBook=DB::insert('INSERT INTO books (book_name,date_publish) 
                                       VALUES (?,?)'
                                      ,array($this->book_name,$date_publish));  
//Взимаме id-то,което получава при записа             
	    $lastInsertedId=DB::connection('mysql')->getPdo()->lastInsertId();
/* Ако книгата	е записана успешно и сме взели нейното id, 
   записваме id-то на книгата и id-тата на авторите към нея 
   в таблица  books_authors */
            echo var_dump($query_newBook);

       if($query_newBook == true){
	 
                  $this->authors_ids=$authors_ids;
          
	             foreach($this->authors_ids as $author_id){
             
                          DB::insert('INSERT INTO books_authors (book_id,author_id) 
                                      VALUES (?,?)'
                                     ,array($lastInsertedId,$author_id)); 

                     }

       }
/* Изикваме метода, който ще запище книгата и авторите към нея,
   за извеждане на съобщение за успешен запис */
            $this->setMessage();
            return true;
        
          }
     }
}

//Метод за извеждане на списъка за книги
    public function getBooks($sort,$author_id,$page,$search)
    {
//Правим нормализация на входящите данни(критерии) от глобалната GET 
        $this->sort=(int)$sort;
        $this->author_id=(int)$author_id;
        $this->page=(int)$page;
        $this->search=trim($search);
//Извикваме метода, който ще запище критериите, по които търсим  към съобщение 
        $this->setMessage();
        
        switch ($this->sort) {
    case 0:
        $sort='date_publish DESC';
        break;
    case 1:
        $sort='date_publish ASC';
        break;
    case 2:
        $sort='book_name DESC';
        break;
    case 3:
        $sort='book_name ASC';
        break;
    case 4:
        $sort='author_name DESC';
        break;
    case 5:
        $sort='author_name ASC';
        break;
    default:
       $sort='date_publish DESC';
}
/* Ако има критерии за търсене по автор(id на автор) се изпълнява първата заявка
   за извеждане на резултатите за списък книги */ 
if($this->author_id>0){
    
$q = DB::select('SELECT *,(SELECT count(book_id) FROM users_comments_books 
                WHERE book_id=b.book_id) as comments_sum FROM books as b 
                LEFT JOIN books_authors as ba ON b.book_id = ba.book_id 
                LEFT JOIN authors as a ON ba.author_id=a.author_id
                WHERE a.author_id=? AND b.book_name LIKE "%'.$this->search.'%"
                ORDER BY '.$sort
               ,array($this->author_id));

}
//Ако няма търсене по автор се изпълнява тази заявка
//Ще групираме по уникално име на книга,за да получим реалния брой записи на книги
else{
    
$q = DB::select('SELECT book_name FROM books as b 
                 LEFT JOIN books_authors as ba ON b.book_id = ba.book_id 
                 LEFT JOIN authors as a ON ba.author_id=a.author_id
                 WHERE b.book_name LIKE "%'.$this->search.'%" 
                 GROUP BY b.book_name');   
    
}
//Всички записи за книги по търсените критерии,ако има такива
$total_records=count($q);
//Всички страници за книги по търсените критерии,ако има такива
$total_pages = ceil($total_records / 10); 
//Ако има записи и страницата съществува за тези записи се изпълнява кода
if($total_records>0 && $this->page>0 && $this->page<=$total_pages){
    
    for ($page=1; $page<=$total_pages; $page++){
//Първи номер на запис за страница     
    $start_from = (($page*10) - 9);   
//Последен номер на запис за страница  
    $end=$page*10;
/* Ако последния номер на запис за страницата е по-голям от броя на всичките записи,
   последен номер на запис за тази страница ще е броя всички записи */
    if(($total_records-$end<0)){

        $end=$total_records;
        
    }
    
	$isActivePage='';
//На избраната страница ще и предадем този стил от style.css
	if($this->page==$page){
            
	$isActivePage='id="activePage"';
        
	}
//Текущите резултати за линк на страница се записват в масива $this->pages
    $this->pages[$page]['isActivePage'] = $isActivePage;
    $this->pages[$page]['start_from'] = $start_from;
    $this->pages[$page]['end'] = $end;
}
/* Ako не търсим по автор ще се изпълни тази заявка,
   за извеждане на резултатите за списък книги */ 
if(!$this->author_id>0){
        $q=DB::select('SELECT *,(SELECT count(book_id) FROM users_comments_books 
                       WHERE book_id=b.book_id) as comments_sum FROM books as b 
                       LEFT JOIN books_authors as ba ON b.book_id = ba.book_id 
                       LEFT JOIN authors as a ON ba.author_id=a.author_id
                       WHERE b.book_name LIKE "%'.$this->search.'%"
                       ORDER BY '.$sort);
 
}
 
$books=array();
/* Всички данни от заявката, които искаме да визуализираме за книги, записваме в масив $books,
  (като повтаряща се книга с различен автор ще се запище с един ключ в масива(на един ред)) */ 
    foreach($q as $book){

        $books[$book->book_id]['book_id']=$book->book_id;
        $books[$book->book_id]['book_name']=$book->book_name;
        $books[$book->book_id]['date_publish']=$book->date_publish;
        $books[$book->book_id]['author'][$book->author_id]=$book->author_name;
        $books[$book->book_id]['comments_sum']=$book->comments_sum;
       
    }

    $row=0;
//Обхождаме масива $books като извеждаме резултатите по редове
    foreach($books as $key=>$book){
        
        $row++;
/* Редовете с резултати от масива $books(до 10 записа), 
   които са за съответната избрана страница се записват в масива
   $this->booksPage и се връщат като резултат */        
       if(!($row>=(($this->page*10)-9) && $row<=$this->page*10)){
            
            continue;
            
        }
//Ключовете на масива $this->booksPage ще бъдат с поредни номера за записите
        $this->booksPage[$row]=$books[$key];
        
        }
       
        return $this->booksPage;

}
/* Ако няма записи или въведената страницата не съществува 
   за търсените записи, се записва грешка в масива $this->errorMessage */
else{
      
      $this->errorMessage['booksList']='Няма намерени резултати';
      return $this->booksPage;
      
     }
  
}
//Метод за извеждане на последно избраните критерии във view-то
    public function getSelectedSort()
    {
        return $this->insertedData=array(
                '0'=>'',
                '1'=>'',
                '2'=>'',
                '3'=>'',
                '4'=>'',
                '5'=>'',
                $this->sort=>'selected',
                'selectedSort'=>$this->sort,
                'selectedPage'=>$this->page,
                'author_id'=>$this->author_id,
                'search'=>$this->search
                );
    }
//Метод за извеждане броя на страниците за коментарите
    public function getPages()
    {
    return $this->pages;
    }
//Метод за извеждане на съобщения за грешка
    public function getErrorMessage()
    {
    return $this->errorMessage;
    }
//Метод за записване на съобщения 
    public function setMessage()
    {
//Ако е въведено име на книга
        if($this->insertedBook){
//За всяко въведено id на автор към книгата, записваме името на автора в масива $this->insertedAuthors
        foreach($this->authors_ids as $key=>$author_id){
            $insertedAuthor=DB::select('SELECT author_name FROM authors
                                        WHERE author_id=?
                                        LIMIT 1'
                                       ,array($author_id)
                            );

            $this->insertedAuthors[$key]=$insertedAuthor[0]->author_name;
        }
/* Записваме съобщение в масива $this->message за успешно добвяне на книга
(с нейното име и имената на автори към нея) */
        $this->message['registerBook']='Успешно добавихте новa книга " '.
                                     $this->insertedBook.' " с автор/автори " '.
                                     $this->insertedAuthors[0].' '.
                                     $this->insertedAuthors[1].' "';
        }
/* Aко има въведен критерии за търсене по заглавие на книга и
   няма въведен критерии (id) за търсене по автор,
   записваме в масива $this->message съобщение за намерени резултати с търсения резултат  */
        if($this->search && $this->author_id==0){
            $this->message['searchBookAndAuthor']='Намерени резултати за "'.$this->search.'"';
        }
//Aко има въведен критерии за търсене по автор(id)    
        if($this->author_id>0){
    //Взимаме името на автора за съответното id
        $author=DB::select('SELECT author_name FROM authors
                         WHERE author_id=?
                         LIMIT 1'
                        ,array($this->author_id)
                );
    /* Ако имаме търсене по автор(id) и търсене по заглавие на книга
    записваме съобщение в масива $this->message за намерени резултати
    с критериите на търсене по заглавие и име на автор */
             if(count($author)>0 && $this->search!=''){
                $this->message['searchBookAndAuthor']='Намерени резултати за "'.$this->search.'" и автор "'.$author[0]->author_name.'"';
             }
    /* Ако имаме търсене по автор(id) и нямаме търсене по заглавие на книга
    записваме съобщение в масива $this->message за намерени резултати
    само с критерии на търсене по име на автор */
            if(count($author)>0 && $this->search==''){
            $this->message['searchBookAndAuthor']='Намерени резултати за автор "'.$author[0]->author_name.'"';
            }
        }
    }
//Метод за извеждане на съобщения  
    public function getMessage()
    {
        return $this->message;
    }
//Чрез този метод връщаме към полетата във view-то въведените данни отговарящи на усл-ята
    public function getInsertedData()
    {
            return array('book_name'=>$this->book_name

                         );
    }

}

?>
