<?php

class CharacterController extends AbstractController
{


    public function index()
    {
        $this->render('user/character');
    }
    public function characterName() {
        $this->render('user/character-name');
    }
}





























