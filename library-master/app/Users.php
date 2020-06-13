<?php
namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class Users
{
    private $username; 
    private $password;
    private $password2;
    private $email;
    private $sex='choice';
    private $age;
    private $city;
    private $date_reg;
    private $insertedData=array();
    private $errorMessageUser=array(
                                'login'=>'',
                                'username'=>'',
                                'password'=>'',
                                'email'=>'',
                                'sex'=>'',
                                'age'=>'',
                                'city'=>''
                                );
								
//Метода за регистрация в системата
    public function registerUser($username,$password,$password2,$email,$sex,$age,$city)
    {
//Ако има POST заявка се изпълнява кода 
        if($_POST){
            $error=false;
//Правим нормализация на входящите данни
            $this->username=$username;
            $this->password=$password;
            $this->password2=$password2;
//Премахваме празните полета в началото и в края на низа, ако има такива		
            $this->email=trim($email);
            $this->sex=trim($sex);
            $this->age=trim($age);
            $this->city=trim($city);
//Премахваме празните полета,където са повече от едно между думите в низа, ако има такива
            $this->city = preg_replace('#[\s]+#', ' ', $this->city);
//Преобразува всяка дума да започва с главна буква, останалите са малки (за кирилица и латиница)
            $this->city = mb_convert_case($this->city, MB_CASE_TITLE, 'UTF-8');
//Правим валидация на входящите данни
            $verifyUsername = DB::select('SELECT * FROM users
                                          WHERE username = ?
                                          LIMIT 1'
                                         ,array($this->username)
                            );
        
            $verifyEmail = DB::select('SELECT * FROM users
                                       WHERE email = ?
                                       LIMIT 1'
                                      ,array($this->email)
                        );
/* Проверка дали потребитеското име съществува,
  (ако съществува се записва съобщение за грешка 
   масив $this->errorMessageUser) */
            if(count($verifyUsername)>0){
                //$this->username='';
                $this->errorMessageUser['username']= 'това потребителско име вече е заето';
                $error=true;
            }
/* Дължина на потребителското име трябва да е между 4 и 20 символа,
   да започва с цифра или буква, може да съдържа само латиница,малки букви,числа и символите .-_ 
  (ако не са изпълнени условията се записва съобщение за грешка
   в масива $this->errorMessageUser) */
            if((mb_strlen($this->username)<4 || mb_strlen($this->username)>20)
                || !preg_match('/^[a-z0-9][a-z0-9._-]+$/',$username)){

                $this->username='';
                $this->errorMessageUser['username']= 'името трябва да е между 4 и 20 символа, и да започва с цифра или буква <br> ( може да съдържа само латиница,малки букви,числа и символите .-_ )';
                $error=true;
            }
/* Проверка дали паролите съвпадат
  (ако съвпадат се записва съобщение за грешка 
   в масив $this->errorMessageUser) */
            if($this->password != $this->password2){
                $this->password2='';
                $this->errorMessageUser['password']= 'паролите трябва да съвпадат';
                $error=true;
            }
/* Дължина на паролата трябва да е между 6 и 20 символа, 
   може да съдържа само латиница,задължително поне една малка и голяма буква,
   число и специален символ,не може съдържа празни полета,  
  (ако не са изпълнени условията се записва съобщение за грешка
   в масива $this->errorMessageUser) */
            if(!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{6,20}$/',$this->password)
                || preg_match('/[\s\p{Cyrillic}]+/u',$this->password)){

                $this->password='';
                $this->password2='';
                $this->errorMessageUser['password']= 'паролата трябва да е между 6 и 20 символа, и да е на латиница <br> ( трябва да съдържа задължително поне една малка и голяма буква, число и <br> специален символ, не може да съдържа празни полета )';
                $error=true;
            }

/* email-а не можеда е повече от 30 и трябва да отговаря на проверката за валидност,
  (ако не са изпълнени условията се записва съобщение за грешка
   в масива $this->errorMessageUser) */
            if(mb_strlen($this->email)>30 || !filter_var($this->email, FILTER_VALIDATE_EMAIL)){
             //|| !preg_match('/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/',$this->email)){

                $this->email='';
                $this->errorMessageUser['email']= 'невалиден email';
                $error=true;
            }
/* Правим проверка дали email-a съществува,
  (ако съществува се записва съобщение за грешка 
   в масива $this->errorMessageUser) */ 
            if(count($verifyEmail)>0){
                $this->email='';
                $this->errorMessageUser['email']= 'този email вече е регистриран';
                $error=true;
            }
/* Възрастта трябва да е число и да е между 16 и 80,
  (ако не са изпълнени условията се записва съобщение за грешка 
   в масива $this->errorMessageUser) */
            if(($this->age<16 || $this->age>80) || !preg_match('/^[0-9]+$/',$this->age)){
                $this->age='';
                $this->errorMessageUser['age']= 'трябва да сте на възраст между 16 и 80 години <br> ( годините трябва да са число )';
                $error=true;
            }
/* Името на града трябва да е между 3 и 20 символа,
   не може да съдържа число или специални символи
  (ако не са изпълнени условията се записва съобщение за грешка 
   в масива $this->errorMessageUser) */
            if((mb_strlen($this->city)<3 || mb_strlen($this->city)>20)
                  || !preg_match('/^[\s\p{Cyrillic}a-zA-Z]+$/u',$this->city)){

                $this->city='';
                $this->errorMessageUser['city']= 'името на града трябва да е между 3 и 20 символа <br> ( не може да съдържа число или специални символи )';
                $error=true;
            }
/* Правим проверка дали е избран пол,(ако не е избран пол се записва
   съобщение за грешка в масива $this->errorMessageUser) */
            if($this->sex=='choice'){
                $this->errorMessageUser['sex']= 'изберете пол';
                $error=true;
            }
//Ако всички условия са изпълнени правим запис на данните в БД
            if(!$error){
                $date_reg=date('Y-m-d H:i:s');
/* Паролата я криптираме с md5 и sha1
                $salt=md5($this->username.$this->password);
                $this->password=sha1(sha1($salt.$this->password));
 */
//Криптираме паролата            
                $this->password = Hash::make($this->password);
//Правим запис
                DB::insert('INSERT INTO users (username,password,email,sex,age,city,date_reg)
                            VALUES (?,?,?,?,?,?,?)'
                           ,array($this->username,$this->password,$this->email,$this->sex,$this->age,$this->city,$date_reg)
                );
            
                return true;
            }
        }
    }
//Чрез този метод извеждаме съобщенията за грешки
    public function getErrorMessageUser()
    {
        return $this->errorMessageUser;
    }
/* Чрез този метод връщаме към полетата във view-то
   въведените данни отговарящи на усл-ята */
        public function getInsertedData()
        {
            return $this->insertedData=array(
                   'username'=>$this->username,
                   'password'=>$this->password,
                   'password2'=>$this->password2,
                   'email'=>$this->email,
                   'age'=>$this->age,
                   'city'=>$this->city,
                   'choice'=>'',
                   'мъж'=>'',
                   'жена'=>'',
                   $this->sex=>'selected'
                   );
        }
//Метод за логване в системата 
        public function loginUser($username,$password)
        {
//Ако има POST заявка се изпълнява кода 
            if($_POST){
            $this->username=$username;
            $this->password=$password;
/* Криптиране на входящите данни преди проверката          
            $salt=md5($this->username.$this->password);
            $this->password=sha1(sha1($salt.$this->password));
 */
//Проверяваме дали въведените потребителско име и парола съществуват в БД
//С тази заявка взимаме паролата от БД за въведеното потребителско име, ако съществува        
            $verifyUser = DB::select('SELECT password FROM users 
                                      WHERE username = ?
                                      LIMIT 1'
                                     ,array($this->username)
                        );
/* Проверяваме дали имаме резултат от заявката,
  (ако няма, това означава, че потребителското име не съществува в БД) */          
            if(count($verifyUser)>0){
//Ако имаме резултат от заявката,взимаме получената парола        
                $password=$verifyUser[0]->password;
/* Проверяваме дали въведената парола съвпада с паролата от БД 
   за това потребителско име */
                if(Hash::check($this->password, $password)){
//Ако и паролата съществува, създава се сесия за потребителя
		            Session::put('islogged', true);
                    Session::put('user', $this->username);

                    return true;
                }
/* Ако паролите не съвпадат се записва съобщение за грешка 
   в масив $this->errorMessageUser */               
                else{
                    $this->errorMessageUser['login']='грешно потребитеско име или парола!!!';
                }
            }
 /* Ако потребителското име не съществува в БД се записва съобщение за грешка 
    в масив $this->errorMessageUser */
            else{
                $this->errorMessageUser['login']='грешно потребитеско име или парола!!!';
            }
        }
    }
//Метод за унищожаване на текуща сесия
    public function logoutUser()
    {
        Session::flush();
        //Auth::logout();
    }
    
}

?>
