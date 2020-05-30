<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Описваме всички връзки към HomeController за нашата система
Route::any('/','HomeController@index')->where('author_id', '[0-9]+');
Route::any('registration','HomeController@registration');
Route::any('welcome','HomeController@welcome');
Route::any('books','HomeController@books')->where('author_id', '[0-9]+');
Route::any('books/comments','HomeController@comments');
Route::any('books/addBook','HomeController@addBook');
Route::any('books/addAuthor','HomeController@addAuthor');
Route::any('logout','HomeController@logout');
