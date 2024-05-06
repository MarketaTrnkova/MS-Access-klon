<?php

class Explorer{
    private ?PDO $instanceDB = null;

    public function __construct(
        private string $dsn  = "mysql:host=mysql80.r3.websupport.cz;dbname=sprava_schuzek;charset=utf8mb4",
        private string $user = "maky94",
        private string $password  = "Sl2design",
    ) {
    }
    
    public function pripojSeDoDb(){
        try {
            $this->instanceDB = new PDO(
                $this->dsn, 
                $this->user,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE =>PDO:: FETCH_ASSOC
                )
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        } 
    }
    
    public function vlozZaznam($jmeno, $prijmeni, $telefon, $termin, $poznamka, $id_obchodnika = null){
        try{
            $query = "INSERT INTO schuzky (jmeno, prijmeni, telefon, termin_schuzky, poznamka, id_obchodnika) VALUES (:jmeno,:prijmeni,:telefon,:termin,:poznamka,:id_obchodnika)";
            $statement = $this->instanceDB->prepare($query);
            $statement->bindParam(":jmeno", $jmeno);
            $statement->bindParam(":prijmeni", $prijmeni);
            $statement->bindParam(":telefon", $telefon);
            $statement->bindParam(":termin", $termin);
            $statement->bindParam(":poznamka", $poznamka);
            if($id_obchodnika == "" || $id_obchodnika == null){
                $statement->bindParam(":id_obchodnika", $id_obchodnika, PDO::PARAM_NULL);
            }else{
                $statement->bindParam(":id_obchodnika", $id_obchodnika, PDO::PARAM_INT);
            }
          
            $statement->execute();
            return true;
        }catch(PDOException $e){
            return false;
        }
      
    }

    public function vypisVsechnySchuzky(?string $terminOd = null, ?string $terminDo = null){
        try {
            if ($terminOd == null || $terminDo == null || $terminOd == "" || $terminDo == "" ) {
                $query = "SELECT id_schuzky, jmeno, prijmeni, telefon, termin_schuzky, poznamka, id_obchodnika FROM schuzky ORDER BY termin_schuzky";
            } else {
                $query = "SELECT id_schuzky, jmeno, prijmeni, telefon, termin_schuzky, poznamka, id_obchodnika FROM schuzky WHERE termin_schuzky >= ? AND termin_schuzky <= ? ORDER BY termin_schuzky";
                $terminOd = DateTime::createFromFormat('Y-m-d', $terminOd);
                $terminDo = DateTime::createFromFormat('Y-m-d', $terminDo);
    
                // Zkontroluje, že objekty DateTime byly úspěšně vytvořeny
                if (!$terminOd || !$terminDo) {
                    throw new Exception("Nevalidni formát data");
                }
                $terminOd = $terminOd->format('Y-m-d'); // Převede objekt DateTime na řetězec
                $terminDo = $terminDo->format('Y-m-d'); // Převede objekt DateTime na řetězec
            }
        
            $statement = $this->instanceDB->prepare($query);
            if ($terminOd != "" && $terminDo != "") {
                $statement->execute([$terminOd, $terminDo]);
            } else {
                $statement->execute();
            }
            return $statement->fetchAll();
        } catch(PDOException $e) {
            return array();
        } 
    }

    public function zmenIdObchodnika($idSchuzky, $idObchodnika){
        try {
            $query = "UPDATE schuzky SET id_obchodnika = ? WHERE id_schuzky = ? ";
            $statement = $this->instanceDB->prepare($query);
            $statement->execute([$idObchodnika, $idSchuzky]);
            return true;
        } catch(PDOException $e) {
            return false;
        } 
    }

    public function vypisSchuzkyObchodniku($id){
        if (!is_numeric($id)){
            return false;
        }
        try {
                $query = "SELECT jmeno, prijmeni, telefon, termin_schuzky, poznamka, id_obchodnika FROM schuzky WHERE id_obchodnika = ? ORDER BY termin_schuzky";
                $statement = $this->instanceDB->prepare($query);
                $statement->execute([$id]);
                return $statement->fetchAll();
            } catch(PDOException $e) {
                return array();
            } 
    }
}

