<?php

namespace src\model;

class Student{
    public function __construct(
        public ?int $id, 
        public string $firstname, 
        public string $lastname, 
        public string $date_of_birth, 
        public string $email 
    ){}

    public function __toString(){
        return "Student n°$this->id : $this->firstname $this->lastname, née le $this->date_of_birth, email : $this->email.";
    }
}

