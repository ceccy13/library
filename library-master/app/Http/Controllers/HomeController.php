<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Authors;
use App\Books;
use App\Comments;
use App\Users;

class HomeController extends BaseController{

	
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function index(){
		
		try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            die("Could not connect to the database! <br><br> Check settings and if DB is imported successfully! ");
        }
      
/* Ако потребителя има валидна сесия го пренасочваме към страница books 
  (Returning A Redirect To A Controller Action books) */
        //echo '<pre>'.print_r(Session::all(),true).'<pre>';
        if(Session::get('islogged')==true){
            
          //return Redirect::to('books');
		    return Redirect::action('HomeController@books');
            
        }
//Взимаме ст-стите предадени чрез глобалната POST,ако има такива
        $username=Input::get('username');
        $password=Input::get('password');
//Създаваме инстанция $user на класа Users,с която ще достъпим методите от класа
        $user=new Users;
//Извикваме метода loginUser
        $login=$user->loginUser($username,$password);
/* Ако получените данните при вход(username,password) за потребителя 
   изпълняват условията на метода loginUser, потребителя получава сесия и се пренасочва 
   към страница books (Returning A Redirect To A Controller Action books) */
        if($login){
            
          //return Redirect::to('books');
		    return Redirect::action('HomeController@books');
           
        }
//Извикваме метода,който ще изведе съобщенията за грешки при логин, ако има такива
            $errorMessageUser=$user->getErrorMessageUser();
/* Взимаме ст-стите предадени чрез глобалната GET,ако има такива
  (по подразбиране са празни, sort е нула, page е 1) */  
            $search=Input::get('search','');
            $author_id=Input::get('author_id','');
            $page=Input::get('page',1);
            $sort=Input::get('sort',0);
            
//Създаваме инстанция $book_model на класа Books,с която ще достъпим методите от класа
            $book_model=new Books;
/* Извикваме метода getBooks,на който му предаваме ст-сти(критерии),
  ($sort,$author_id,$page,$search), ако има такива, 
   по които ще изведе генерираната инф-я */

            $books=$book_model->getBooks($sort,$author_id,$page,$search);
//Извикваме метода getPages,който ще върне страниците за търсените резултати
            $pages=$book_model->getPages();
//Извикваме метода getSelectedSort,който ще върне ст-стите за избраните критерии за търсене
            $selectedSort=$book_model->getSelectedSort();
//Извикваме метода getErrorMessage, който ще върне съобщенията за грешки,ако има такива
            $errorMessage=$book_model->getErrorMessage();
//Извикваме метода getMessage,който ще върне съобщение,ако има такова
            $message=$book_model->getMessage();
//Извиква view-то index,на което предаваме данните получени от методите като масив
            return View::make('index',array('books'=>$books,'pages'=>$pages,'selectedSort'=>$selectedSort,'errorMessageUser'=>$errorMessageUser,'errorMessage'=>$errorMessage,'message'=>$message));
           
    }
    
    
    public function registration(){
	
/* Ако потребителя има валидна сесия го пренасочваме към глваната страница 
  (Returning A Redirect To A Controller Action index) */
	 if(Session::get('islogged')==true){
            
          //return Redirect::to('index');
		    return Redirect::action('HomeController@index');
            
        }
//Взимаме ст-стите изпратени с глобалната POST,ако има такива
        $username=Input::get('username');
        $password=Input::get('password');
        $password2=Input::get('password2');
        $email=Input::get('email');
        $sex=Input::get('sex');
        $age=Input::get('age');
        $city=Input::get('city');
//Създаваме истанция user на класа Users,чрез която ще достъпваме методите на класа
        $user=new Users;
/* Извикваме метода registerUser, като му предаваме ст-стите
   изпратени от глоабалната POST, ако има такива */
        $registration=$user->registerUser($username,$password,$password2,$email,$sex,$age,$city);        
/* Аko входящите данни изпълняват изискванията за регистрация на метода registerUser,
   потребителя се пренасочва към страница welcome (Returning A Redirect To A Controller Action welcome) */
        if($registration){
            
          //return Redirect::to('welcome');
		    return Redirect::action('HomeController@welcome');
            
        }
/* Извикваме метода getInsertedData, чрез който получаваме
   данните, които са предадени през глобалната POST */
        $insertedData=$user->getInsertedData();
//Извикваме метода за извеждане на съобщения за грешки, ако има такива
        $errorMessageUser=$user->getErrorMessageUser();
//Извикваме view-то registration, като му предаваме получните данни с масив 
            return View::make('registration',array('insertedData'=>$insertedData,'errorMessageUser'=>$errorMessageUser));
    
    }
    
    
    public function welcome(){
	
/* Ако потребителя има валидна сесия го пренасочваме към главната страница
  (Returning A Redirect To A Controller Action index) */
	 if(Session::get('islogged')==true){
            
          //return Redirect::to('index');
		    return Redirect::action('HomeController@index');
            
        }
//Извикваме view-то welcome
            return View::make('welcome');
            
    }
    
    
    public function books(){
	
/* Ако потребителят няма валидна сесия се пренасочва към главната страница
  (Returning A Redirect To A Controller Action index)*/
        if(Session::get('islogged')!=true){
		
         //return Redirect::to('/');
		   return Redirect::action('HomeController@index');
		   
		}
//Взимаме данните за потребителското име от текущата сесията
            $user=Session::get('user');
/* Взимаме данните предадени с глобалната GET
   по подразбиране са празни,само page e 1) */
            $search=Input::get('search','');
            $author_id=Input::get('author_id','');
            $page=Input::get('page',1);
            $sort=Input::get('sort',0);
            
/* Създаваме инстанция $book_model на класа Books,
   чрез която ще достъпваме методите на класа  */
            $book_model=new Books;
/* Извикваме метода getBooks,на който му предаваме ст-сти(критерии),
  ($sort,$author_id,$page,$search), ако има такива, 
   по които ще изведе генерираната инф-я */
            $books=$book_model->getBooks($sort,$author_id,$page,$search);
//Извикваме метода getPages,който ще върне страниците за търсените резултати
            $pages=$book_model->getPages();
//Извикваме метода getErrorMessage, който ще върне съобщенията за грешки,ако има такива
            $errorMessage=$book_model->getErrorMessage();
//Извикваме метода getSelectedSort,който ще върне ст-стите за избраните критерии за търсене
            $selectedSort=$book_model->getSelectedSort();
//Извикваме метода getMessage,който ше върне съобщение,ако има такова
            $message=$book_model->getMessage();
//Извикваме view-то books, като му предаваме получените данните с масив
            return View::make('books',array('user'=>$user,'books'=>$books,'pages'=>$pages,'selectedSort'=>$selectedSort,'errorMessage'=>$errorMessage,'message'=>$message));
   
    }
    
    
    public function comments(){
	
/* Ако потребителя няма валидна сесия се пренасочва към главната страница 
  (Returning A Redirect To A Controller Action index) */
        if(Session::get('islogged')!=true){
            
          //return Redirect::to('/');
		    return Redirect::action('HomeController@index');
            
        }
//Взимаме потребителското име и съобщение от сесията(ако има такова)
            $user=Session::get('user');
            $message=Session::get('message');
/* Взимаме данните предадени с глобалната POST, ако има такива
  (по подразбиране е прзана) */
            $limitedtextarea=Input::get('limitedtextarea',null);
            
/* Взимаме данните предадени с глобалната GET, ако има такива
  (по подразбиране са book_id празна, page e 1, sort e 0) */
            $book_id=Input::get('book_id','');
            $page=Input::get('page',1);
            $sort=Input::get('sort',0);
/* Създаваме инстанция $comment_model на класа Comments,
   чрез която ще достъпваме методите му */
            $comment_model=new Comments;
//Извикваме метода setComment за публикуване на коментар, като му предаваме параметри
            $comment_record=$comment_model->setComment($book_id,$limitedtextarea);
/* Ако публикацията е успешна пренасочваме потребителя към сртаница books/comments
  (Returning A Redirect To A Controller Action comments) със ст-ст id-то на книгата предадено чрез масив
   и съобщение предадено през текущата сесията  */
             if($comment_record){

                 $message=$comment_model->getMessage();
                 return Redirect::action('HomeController@comments', array('book_id' => $book_id))->with('message', $message);
            
			}
/* Извикваме метода getComments, като му предаваме ст-сти(критерии),
   чрез който показваме коментарите за книга/книги */
            $comments=$comment_model->getComments($book_id,$page,$sort);
/* Извикваме метода getBookName, чрез който ще получим името са книгата
   за която търсим коментари или ще коментираме */
            $bookName=$comment_model->getBookName();
//Извикваме метода getPages,който ще върне страниците за търсените резултати
            $pages=$comment_model->getPages();
//Извикваме метода getSelectedSort,който ще върне ст-стите за избраните критерии за търсене
            $selectedSort=$comment_model->getSelectedSort();
//Извикваме метода getErrorMessage, който ще върне съобщенията за грешки,ако има такива
            $errorMessage=$comment_model->getErrorMessage();
//Извикваме view-to comments, като му предаваме получените данни чрез масив
             return View::make('comments',array('user'=>$user,'comments'=>$comments,'bookName'=>$bookName,'pages'=>$pages,'selectedSort'=>$selectedSort,'errorMessage'=>$errorMessage,'message'=>$message));
 
    }
    
    
    public function addBook($message=null){
	
/* Ако потребителя няма валидна сесия се пренасочва към главната страница 
  (Returning A Redirect To A Controller Action index) */
        if(Session::get('islogged')!=true){

          //return Redirect::to('/');
		    return Redirect::action('HomeController@index');

        }
//Взимаме потребителското име и съобщение от сесията(ако има такова)
            $user=Session::get('user');
            $message=Session::get('message');
//Взимаме ст-стите(за името на книгата и id-тата на авторите) от глобалната POST, ако има такива   
            $book_name=Input::get('book');
            $authors_ids=Input::get('author');
//Създаваме инстация на класа Authors, чрез която ще достъпваме методите му   
            $author_model=new Authors;
//Извикваме метода getAuthors, чрез който получаваме списъка с наличните автори   
            $authors=$author_model->getAuthors();
//Правим инстанция $book_model на класа Books, чрез която ще достъпваме методите му    
            $book_model=new Books;
/* Извикваме метода setBook, като му предаваме името на новата книга 
   и id-тата на избраните автори към нея и ги записаваме ако условията са изпълнени */
            $book_record=$book_model->setBook($book_name,$authors_ids);
/* Ако записът на книгата и авторите към нея е успешен пренасочваме потребителя към текущата страница books/addBook
  (Returning A Redirect To A Controller Action addBook) със съобщение предадено през текущата сесията */
             if($book_record){
                 
                $message=$book_model->getMessage();

              //return Redirect::to('books/addBook')->with('message', $message);
                return Redirect::action('HomeController@addBook')->with('message', $message);
            
			}
/* Извикваме метода getInsertedData, чрез който получаваме
   данните, които са предадени през глобалната POST */   
            $insertedData=$book_model->getInsertedData();
//Извикваме метода getErrorMessage, който ще върне съобщенията за грешки,ако има такива
            $errorMessage=$book_model->getErrorMessage();
            
//Извикваме view-to addBook, като му предаваме получените данни чрез масив
            return View::make('addBook',array('user'=>$user,'authors'=>$authors,'insertedData'=>$insertedData,'errorMessage'=>$errorMessage,'message'=>$message));
   
    }
    
    
    public function addAuthor(){
	
/* Ако потребителя няма валидна сесия се пренасочва към главната страница 
  (Returning A Redirect To A Controller Action index) */
        if(Session::get('islogged')!=true){
   
          //return Redirect::to('/');
            return Redirect::action('HomeController@index');
        
		}
//Взимаме потребителското име и съобщение от сесията(ако има такова)
            $user=Session::get('user');
            $message=Session::get('message');
//Взимаме ст-ста(въведеното име на автора) предадена през глобалната POST, ако има такава    
            $author=Input::get('author','');
//Създаваме инстанция $author_model на класа Authors,чрез която ще достъпваме методите му     
            $author_model=new Authors;
            
//Извикваме метода getAuthors,чрез който получаваме списък със всички налични автори    
            $authors=$author_model->getAuthors();      
/* Извикваме метода setAuthor, като му предаваме ст-ст
  (въведеното името на нов автор, получено с глобалната POST)
   с който регистрираме автора */
            $author_record=$author_model->setAuthor($author);
 /* Ако записът за нов автор е успешен, пренасочваме потребителя към текущата страница books/addAuthor
   (Returning A Redirect To A Controller Action addAuthor ) със съобщение предадено през текущата сесията */  
            if($author_record){
                
               $message=$author_model->getMessage();
             //return Redirect::to('books/addAuthor')->with('message',$message);
               return Redirect::action('HomeController@addAuthor')->with('message', $message);
            
			}
        
//Извикваме метода getErrorMessage, който ще върне съобщенията за грешки,ако има такива
            $errorMessage=$author_model->getErrorMessage();
        
//Извикваме view-то addAuthor, като му предаваме получените данни с масив  
            return View::make('addAuthor',array('user'=>$user,'authors'=>$authors,'errorMessage'=>$errorMessage,'message'=>$message));
           
        }
        

   public function logout(){ 
   
//Създаваме инстанция $user на класа Users
      $user=new Users;
/* Извикваме метода logoutUser от класа Users
   чрез който ще унищожим текущата сесия на потребителя */
      $user->logoutUser();
//Пренасочваме потребителя към главната страница (Returning A Redirect To A Controller Action index)
    //return Redirect::to('/');
      return Redirect::action('HomeController@index');
  
  }
    
}

?>
