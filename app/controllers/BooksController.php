<?php

class BooksController
{
    public function searchBook($slim)
    {
        $slim->responseBody = $slim->request->get();
    }

    public function reportBook()
    {
        $slim->responseBody = $slim->request->post();
        $slim->responseCode = 200;
    }
}
