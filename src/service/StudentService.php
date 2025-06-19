<?php

namespace src\service;

use PDOException;
use src\enum\LogType;
use src\model\Log;
use src\model\Student;
use src\repository\LogRepository;
use src\repository\StudentRepository;

class StudentService
{
    // Définition des regex à utiliser sous forme de constantes
    const DATE_PATTERN = "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/";
    const EMAIL_PATTERN = "/^[\w\-\.]+@([\w-]+\.)+[\w-]{2,}$/";

    public function __construct(
        private StudentRepository $studentRepository,
        private LogRepository $logRepository
    ){}

    function insertLog(LogType $logType, string $operation, string $message): void{
        $this->logRepository->insert(new Log(null, $logType, $operation, $message));
    }

    // Permet d'afficher les étudiants
    function getStudents(): array
    {
        $students = [];
        try{
            $students = $this->studentRepository->findAll();
            $this->insertLog(LogType::DEBUG, "Affichage", "Affichage de tout les étudiants");
        } catch (PDOException $e) {
            $this->insertLog(LogType::ERR, "Affichage", "Echec de l'affichage de tout les étudiants");
            print("Erreur lors de findAll : " . $e->getMessage());
        }

        $this->displayStudent($students);
        return $students;
    }

    // Créé un étudiant et effectue des vérifications
    public function createStudent(Student $studentToSave): bool
    {
        try {
            $this->studentRepository->save($studentToSave);
            $this->insertLog(LogType::DEBUG, "Création", "Création d'un étudiant");
            return true;
        } catch (PDOException $e) {
            print("Erreur lors de save : " . $e->getMessage());
            $this->insertLog(LogType::ERR, "Création", "Erreur lors de la création d'un étudiant");
            return false;
        }
    }


    // Permet d'éditer un étudiant
    function editStudent(): void
    {
        $id = $this->askStudentId(); // TODO: A mettre dans le view

        try{
            // On récupère l'étudiant en base de données s'il existe
            $student = $this->studentRepository->findById($id);
            $this->insertLog(LogType::DEBUG, "Rechercher par ID", "Etudiant d'id ($id) trouvé");
        } catch (PDOException $e) {
            print("Erreur lors de findById : " . $e->getMessage());
            $this->insertLog(LogType::ERR, "Rechercher par ID", "Etudiant d'id ($id) introuvable");
            $student = false;
        }

        if(!$student)
            return;

        $this->askStudentUpdateInfo($student); // TODO: Dans le view

        try {
            $this->studentRepository->update($student);
            $this->insertLog(LogType::DEBUG, "Update", "Etudiant d'id ($id) mis à jour");
        } catch (PDOException $e) {
            print("Erreur lors de update : " . $e->getMessage());
            $this->insertLog(LogType::ERR, "Update", "Erreur pour l'étudiant d'id ($id) lors de la mis à jour");
        }
    }

    // Supprime un étudiant par son id
    function deleteStudent(): void
    {
        echo "Saisir l'id de l'étudiant: ";
        $id = (int)readline();

        try{
            $success = $this->studentRepository->deleteById($id);
            $this->insertLog(LogType::DEBUG, "Suppression", "Etudiant d'id ($id) supprimé");
        } catch (PDOException $e) {
            print("Erreur lors de deleteById : " . $e->getMessage());
            $this->insertLog(LogType::ERR, "Suppression", "Impossible de supprimer l'étudiant d'id ($id)");
            $success = false;
        }

        $this->displayDeleteSuccess($success, $id); // TODO: Dans le view
    }

    function searchStudentsByIdentity(): void {
        $input = $this->askStudentName(); // TODO: A mettre dans la view puis en param

        $students = [];
        try{
            $students = $this->studentRepository->findAllByName($input);
            $this->insertLog(LogType::DEBUG, "Rechercher par nom", "Etudiant avec ($input) dans le nom trouvé");
        } catch (PDOException $e) {
            $this->insertLog(LogType::ERR, "Rechercher par nom", "Etudiant avec ($input) dans le nom introuvable");
            print("Erreur lors de findAllByName : " . $e->getMessage());
        }

        $this->displayStudentFoundByName($input, $students); // TODO: A envoyer dans la view
    }

    /*
     *
     *         A METTRE DANS LA VIEW
     *
     *
     */

    public function displayStudent(array $students): void
    {
        echo "=== Affichage des étudiants ===\n";
        if (empty($students))
            echo "Aucun étudiant";

        foreach ($students as $student) {
            // On affiche chaque étudiant récupéré depuis la base de données
            echo $student . PHP_EOL;
        }
    }

    public function askStudentInfos(){
        echo "Saisir le prénom : ";
        $firstname = readline();

        if (empty($firstname)) {
            echo "Prénom incorrect";
            $this->insertLog(LogType::WARN, "Création", "Prénom incorrect");
            return false;
        }

        echo "Saisir le nom : ";
        $lastname = readline();

        if (empty($lastname)) {
            echo "Nom incorrect";
            $this->insertLog(LogType::WARN, "Création", "Nom incorrect");
            return false;
        }

        echo "Saisir date naissance (aaaa-mm-jj): ";
        $dob = readline();

        if (!preg_match(self::DATE_PATTERN, $dob)) {
            echo "Date incorrecte";
            $this->insertLog(LogType::WARN, "Création", "Date incorrect");
            return false;
        }

        echo "Saisir email: ";
        $email = readline();

        if (!preg_match(self::EMAIL_PATTERN, $email)) {
            echo "Email incorrect";
            $this->insertLog(LogType::WARN, "Création", "Email incorrect");
            return false;
        }

        return ['firstname' => $firstname,
            'lastname' => $lastname,
            'dob' => $dob,
            'email' => $email];
    }

    public function askStudentId(){
        echo "Saisir l'id de l'étudiant: ";
        return (int)readline();
    }

    public function askStudentUpdateInfo(Student $student): void
    {
        readline();

        echo "Saisir prénom: ";
        $firstname = readline();

        // Si l'utilisateur ne saisit rien, firstname garde son ancienne valeur
        if (!empty($firstname)) {
            $student->firstname = $firstname;
        }

        echo "Saisir nom: ";
        $lastname = readline();

        if (!empty($lastname)) {
            $student->lastname = $lastname;
        }

        echo "Saisir date naissance: ";
        $dob = readline();

        if (!empty($dob) && preg_match(self::DATE_PATTERN, $dob)) {
            $student->date_of_birth = $dob;
        }

        echo "Saisir email: ";
        $email = readline();

        if (!empty($email) && preg_match(self::EMAIL_PATTERN, $email)) {
            $student->email = $email;
        }
    }

    public function displayDeleteSuccess(bool $success, int $id): void
    {
        if ($success)
            echo "L'étudiant avec l'ID $id a été supprimé.\n";
        else
            echo "L'id est incorrecte.\n";
    }

    public function askStudentName(): string
    {
        // On prépare le paramètre pour le like
        echo "Saisir le nom ou prénom de l'étudiant: ";
        $input = '%' . readline() . '%';
        return $input;
    }

    public function displayStudentFoundByName(string $input, array $students): void
    {
        echo "=== Affichage de tout étudiants ayant $input dans leur nom ou prénom === \n";
        foreach ($students as $student) {
            // On affiche chaque étudiant récupéré depuis la base de données
            echo $student . PHP_EOL;
        }
    }
}