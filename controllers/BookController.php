<?php

function library() {
    $books = Book::getBooks();
    require_once('views/book/library.php');
}