<?php

namespace src\controller;

use src\service\StudentService;

class StudentController
{
    public function __construct(private StudentService $service){}

    public function displayStudents(){
        $students = $this->service->getStudents();
        include __DIR__ . "/../view/home.php";
    }

    public function saveStudent(): void
    {
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $date_of_birth = $_POST['date_of_birth'] ?? '';
        $email = $_POST['email'] ?? '';

        // Création d'un nouvel étudiant sans ID
        $student = new \src\model\Student(
            null, // pas d'ID à l'ajout
            $firstname,
            $lastname,
            $date_of_birth,
            $email
        );

        $this->service->createStudent($student);

        header("Location: index.php");
        exit;
    }


}