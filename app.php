<?php

// Sætter vores timezone
date_default_timezone_set('Europe/Copenhagen');

// Opsætter vores "locale" til dansk
setlocale(LC_ALL, 'da_DK.UTF-8');

// Opsætter fejlrappotering til alt pånær "notice"-beskeder
error_reporting(E_ALL ^ E_NOTICE);

// Definerer at vi gerne vil vise fejlbeskeder, sæt til 0 for at skjule fejlbeskeder
ini_set("display_errors", 1);

class minAwesomeApp {

    // Database information
    private static $db;
    private $MySQLUsername = 'mitSQLbrugernavn';
    private $MySQLPassword = 'mitSQLpassword';
    private $MySQLHostname = 'localhost';
    private $MySQLDatabase = 'minSQLdatabase';

      // Forbind til vores database
      public function __construct() {
          if (!isset(self::$db)) {
              try {
                  self::$db = new PDO('mysql:host=' . $this->MySQLHostname .
                  ';dbname=' . $this->MySQLDatabase .
                  ';charset=utf8;', $this->MySQLUsername,
                  $this->MySQLPassword);
                  self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              } catch (PDOException $e) {
                  die ( "Kunne ikke logge på databasen, måske har du indtastet forkert MySQL username, password, hostname eller databasenavn. Fejlbeskeden fra systemet er: " . $e->getMessage() );
              }

          }
      }

      // Hent data ud fra vores database (flere linjer)
      // DatabasePrepareQuery( "SELECT * FROM Adressebog", array() )
      // DatabasePrepareQuery( "SELECT * FROM Adressebog WHERE navn = ?", array('Daniel') )
      // DatabasePrepareQuery( "SELECT * FROM Adressebog WHERE navn LIKE ?", array('%Daniel%') )
      public function DatabasePrepareQuery($query,$data_array) {
        try {
          $stmt = self::$db->prepare($query);
          $stmt->execute($data_array);
          return $stmt;
        } catch (PDOException $e) {
            // Catch fejlbesked og echo den ud
            echo 'Der opstod en fejl - fejlbesked: ' . $e->getMessage();
            exit;
        }
      }

      // Hent data ud fra vores database (éen linjer)
      // DatabasePrepareQueryReturnFirstField( "SELECT * FROM Adressebog WHERE id = ?", array(1) )
      public function DatabasePrepareQueryReturnFirstField($query, $data_array) {
        try {
          $stmt = self::$db->prepare($query);
          $stmt->execute($data_array);
          return $stmt->fetch();
        } catch (PDOException $e) {
            // Catch fejlbesked og echo den ud
            echo 'Der opstod en fejl - fejlbesked: ' . $e->getMessage();
            exit;
        }
      }

      // Slet noget fra vores database
      // DatabaseDelete( "Adressebog", "WHERE id = ?", array(1) )
      public function DatabaseDelete($TableName, $WhereField, $WhereValues) {
            if (is_array($WhereValues) && isset($WhereField) && isset($TableName)) {
              try {
                $prepareInsert = self::$db->prepare('DELETE FROM ' . $TableName . ' ' . $WhereField);
                $prepareInsert->execute($WhereValues);
                return $prepareInsert->rowCount();
              } catch (PDOException $e) {
                  // Catch fejlbesked og echo den ud
                  echo 'Der opstod en fejl - fejlbesked: ' . $e->getMessage();
                  exit;
              }
            } else {
                return 0;
            }
        }

        // Sæt noget ind i vores database
        // DatabaseInsert( "Adressebog", array('Navn','Email'), array('Daniel','hej@v5.dk') )
        public function DatabaseInsert($TableName, $Fields, $Values) {
            $buildFields = '';
            if (is_array($Fields)) {
                // Loop igennem alle vores felter
                foreach($Fields as $key => $field) :
                    if ($key == 0) {
                        // Første felt
                        $buildFields .= $field;
                    } else {
                        // Efterfølgende felter starter med ","
                        $buildFields .= ', ' . $field;
                    }
                endforeach;
            } else {
                // Vi indsætter kun éet felt, ingen behov for overstgående loop
                $buildFields .= $Fields;
            }
            $buildValues = '';
            if (is_array($Values)) {
                //  Loop igennem alle vores values
                foreach($Values as $key => $value) :
                    if ($key == 0) {
                        // Første
                        $buildValues .= '?';
                    } else {
                        // Efterfølgende starter med ","
                        $buildValues .= ', ?';
                    }
                endforeach;
            } else {
                // Vi indsætter kun éet felt, ingen behov for overstgående loop
                $buildValues .= ':value';
            }
            try {
                $prepareInsert = self::$db->prepare('INSERT INTO '.$TableName.' ('.$buildFields.') VALUES ('.$buildValues.')');
                if (is_array($Values)) {
                    $prepareInsert->execute($Values);
                } else {
                    $prepareInsert->execute(array(':value' => $Values));
                }
                // Return last Insert ID
                return self::$db->lastInsertId();

            } catch (PDOException $e) {
                // Catch fejlbesked og echo den ud
                echo 'Der opstod en fejl - fejlbesked: ' . $e->getMessage();
                exit;
            }

        }

        // Opdater noget i vores database
        // DatabaseUpdate( "Adressebog", array('email'), array('ny@email.dk', 1), "WHERE id = ?" )
        public function DatabaseUpdate($TableName, $Fields, $Values, $WhereFields) {
            $buildFields = '';
            if (is_array($Fields)) {
                foreach($Fields as $key => $field) :
                    if ($key == 0) {
                        $buildFields .= $field . " = ?";
                    } else {
                        $buildFields .= ', ' . $field . " = ?";
                    }
                endforeach;

            } else {
                $buildFields .= $Fields . " = :value";
            }
            try {
                $prepareInsert = self::$db->prepare('UPDATE '.$TableName.' SET '.$buildFields.' '.$WhereFields);
                if (is_array($Values)) {
                    $prepareInsert->execute($Values);
                } else {
                    $prepareInsert->execute(array(':value' => $Values));
                }
                return self::$db->lastInsertId();
            } catch (PDOException $e) {
              echo 'Der opstod en fejl - fejlbesked: ' . $e->getMessage();
              exit;
            }

        }


}
