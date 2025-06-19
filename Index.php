<?php

require './vendor/autoload.php';

use src\controller\StudentController;
use src\repository\LogRepository;
use src\repository\StudentRepository;
use src\service\LogService;
use src\service\StudentService;

// CONTROLLER
$studentRepo = new StudentRepository();
$logRepository = new LogRepository();
$logService = new LogService($logRepository);
$studentService = new StudentService($studentRepo, $logRepository);
$studentController = new StudentController($studentService);

/// VIEW
//while (true) {
//    menu();
//    match ($_GET["page"]) {
//        "1" => $studentController->displayStudents(),
//
//        "2" => $studentService->createStudent(),
//        "3" => $studentService->editStudent(),
//        "4" => $studentService->deleteStudent(),
//        "5" => $studentService->searchStudentsByIdentity(),
//        "6" => $logService->getTenLastLogs(),
//        "7" => $logService->clearLogs(),
//        "8" => exit(),
//        default => print("saisie invalide"),
//    };
//
//    echo "\n---Press enter to continue---\n";
//    readline();
//}

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'saveStudent':
        $studentController->saveStudent();
        break;

    default:
        $studentController->displayStudents();
        break;
}