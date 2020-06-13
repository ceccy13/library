<?php
namespace App;

use Illuminate\Support\Facades\DB;

class Authors
{
    private $authors=array();
    private $author_name;
    private $errorMessage=null;
    private $insertedAuthor=null;
    private $message=null;
    
//С този метод добавяме нов автор
    public function setAuthor($author)
    {
//Ако имам POST заявка, изпъняваме кода 
        if($_POST){
            $error=false;
//Правим нормализация на входящите данни
//Премахваме празните полета в началото и края на низа, ако има такива   
            $this->author_name = trim($author);
//Премахваме празните полета повече от едно между думите на низа, ако има такива   
            $this->author_name = preg_replace('#[\s]+#', ' ', $this->author_name);
//Преобразува всяка дума да започва с главна буква,останалите са малки (за кирилица и латиница)
            $this->author_name = mb_convert_case($this->author_name, MB_CASE_TITLE, 'UTF-8');
		
//Правим валидация на входящите данни
            $isAuthorExists=DB::select('SELECT * FROM authors
                                        WHERE author_name=?
                                        LIMIT 1'
                                       ,array($this->author_name)
                            );
/* Проверяваме дали автора съществува,
  (ако съществува записваме съобщение за грешка 
   в масива $this->errorMessage) */
            if(count($isAuthorExists)>0){
                $this->errorMessage='Този автор съществува!!!';
                $error=true;
            }
/* Дължината на името на автора трябва да е между 3 и 20 символа, и да не съдържа специални символи,
  (ако условията не са изпълнени, записваме съобщения за грешка в масива $this->errorMessage) */
            if(mb_strlen($this->author_name)<3 || mb_strlen($this->author_name)>20 || !preg_match('/^[\d\s\p{Cyrillic}a-zA-Z]+$/u',$this->author_name)){
                $this->errorMessage='Името на автора трябва да е между 3 и 20 символа, и да не съдържа специални символи!!!';
                $error=true;
            }
/* Ако условията са изпълнени правим запис на името на новия автор в БД
   и правим запис на съобщение */
            if(!$error){
                 $this->insertedAuthor=$this->author_name;
                 $this->message='Успешно добавихте нов автор "'.$this->insertedAuthor.'"';

                 return DB::insert('INSERT INTO authors (author_name)
                                    VALUES (?)'
                                   ,array($this->author_name)
                        );
            }
        }
    }
//С този метод извеждаме имената на всички автори,които са в БД
    public function getAuthors()
    {
        $q=DB::select('SELECT * FROM authors');
        foreach($q as $author){
            $this->authors[$author->author_id]=$author->author_id;
            $this->authors[$author->author_id]=$author->author_name;
        }
        return $this->authors;
    }
//С този метод извеждаме съобщенията за грешка
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
//С този метод извеждаме съобщения
    public function getMessage()
    {
        return $this->message;
    }

}

?>
