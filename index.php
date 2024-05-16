<?php
    require_once("./Explorer.php");
    session_start();
    $explorer = new Explorer();
    $explorer->pripojSeDoDb();
    if(!array_key_exists("vsechny-schuzky", $_GET) ){
        $_SESSION["vsechnySchuzky"] = $explorer->vypisVsechnySchuzky();
    }elseif(array_key_exists("vsechny-schuzky", $_GET) && array_key_exists("idSchuzkyZmenenehoRadku", $_SESSION)){
        $idZmeneneSchuzky = $_SESSION["idSchuzkyZmenenehoRadku"];
        if (array_key_exists("vsechnySchuzkyterminOd", $_SESSION) && array_key_exists("vsechnySchuzkyterminDo", $_SESSION)){
            if(($_SESSION["vsechnySchuzkyterminOd"] !== "") && ($_SESSION["vsechnySchuzkyterminOd"] !== "") )
            $_SESSION["vsechnySchuzky"] = $explorer->vypisVsechnySchuzky($_SESSION["vsechnySchuzkyterminOd"], $_SESSION["vsechnySchuzkyterminDo"]);
        }else{
            $_SESSION["vsechnySchuzky"] = $explorer->vypisVsechnySchuzky();
        }

        unset($_SESSION["idSchuzkyZmenenehoRadku"]);
    }
    if  (array_key_exists("vytvorenaSchuzka",$_SESSION)){
        if ($_SESSION["vytvorenaSchuzka"] == true){
            $vytvorenaSchuzka = true;
        } else{
            $vytvorenaSchuzka = false;
        }
        unset($_SESSION["vytvorenaSchuzka"]);
    }
    if(!array_key_exists("schuzky-obchodniku-combobox", $_GET) ){
        $schuzkyObchodniku = array();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if(array_key_exists("submit-vytvorit_schuzku", $_POST)){
            $jmeno = htmlspecialchars($_POST["vytvorit_schuzku-jmeno"]);
            $prijmeni = htmlspecialchars($_POST["vytvorit_schuzku-prijmeni"]);
            $telefon= htmlspecialchars($_POST["vytvorit_schuzku-tel"]);
            $termin = htmlspecialchars($_POST["vytvorit_schuzku-termin"]);
            $poznamka = htmlspecialchars($_POST["vytvorit_schuzku-poznamka"]);
            $idObchodnika = htmlspecialchars($_POST["vytvorit_schuzku-combobox"]);
            $vytvorilaSeSchuzka = $explorer->vlozZaznam( $jmeno, $prijmeni, $telefon, $termin, $poznamka, $idObchodnika);
            if ( $vytvorilaSeSchuzka ){
                $_SESSION["vytvorenaSchuzka"] = true;
            }else{
                $_SESSION["vytvorenaSchuzka"] = false;
            }
            header('Location: index.php?vytvorit-schuzku=1');
            exit();
        }elseif(array_key_exists("noveIdSubmit", $_POST)){
            $explorer->zmenIdObchodnika( $_POST["idSchuzky"], $_POST["noveIdObchodnika"] );
            $_SESSION["idSchuzkyZmenenehoRadku"] = $_POST["idSchuzky"];
            if (array_key_exists("vytvorenaSchuzka",$_SESSION)) unset($_SESSION ["vytvorenaSchuzka"]);
            header('Location: index.php?vsechny-schuzky=1');
            exit();
        }
        }else{
            unset($_SESSION["vytvorenaSchuzka"]);
        }

    if(array_key_exists("submit-vypsat-vsechny-schuzky", $_GET)){
        //aktualizace dat o vsech schuzkach po kliknuti na tlacitko vypsat vsechny schuzky
        
        if($_GET["vsechny-schuzky-terminOd"]!= "" && $_GET["vsechny-schuzky-terminDo"]!= ""){
            $vsechnySchuzky = $explorer->vypisVsechnySchuzky($_GET["vsechny-schuzky-terminOd"], $_GET["vsechny-schuzky-terminDo"]);
            $_SESSION["vsechnySchuzkyterminOd"] = $_GET["vsechny-schuzky-terminOd"];
            $_SESSION["vsechnySchuzkyterminDo"] = $_GET["vsechny-schuzky-terminDo"];
            $_SESSION["vsechnySchuzky"] = $vsechnySchuzky;
            header("Location: index.php?vsechny-schuzky=1");
            exit();
        }else{
            if(array_key_exists("vsechnySchuzkyterminOd", $_SESSION)){
                unset($_SESSION["vsechnySchuzkyterminOd"]);
                unset($_SESSION["vsechnySchuzkyterminDo"]);
            }
        } 
    }
    
    if(array_key_exists("zobrazit-schuzky-obchodnika", $_GET)){
        $schuzkyObchodniku = $explorer->vypisSchuzkyObchodniku($_GET["schuzky-obchodniku-combobox"]);
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a class="menu-item vytvorit-schuzku_btn">Vytvořit schůzku</a></li>
                <li><a class="menu-item vsechny-schuzky_btn">Všechny schůzky</a></li>
                <li><a class="menu-item schuzky-obchodniku_btn">Schůzky obchodníků</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="flashMessage">
            <?php 
            if ( isset($vytvorenaSchuzka)){
                if($vytvorenaSchuzka)echo "Schůzka byla úspěšně vytvořená &#9989;";else echo "Schůzku se nepodařilo vytvořit &#10060";}
            ?>
            </div>
           <div class="infoBox">
            <p>Tato stránka slouží k zobrazení a odeslání formulářů, které jsem původně vytvořila v MS Access a přenesla je do MySQL databáze.  </p>
            <div class="stahnoutDatabazi"><img src="img/Microsoft-Access-logo-pink.png" width="50px" MS-Access Databáze ke stažení"><div class="link-wrapper"><a href="./organizer-schuzek.accdb" download="organizer-schuzek.accdb">Stáhnout MS Access databázi</a></div></div>
           </div>
            <dialog class="dialog-vytvorit_schuzku">
                <div class="close-dialog-wrapper"><span class="close-dialog-icon close-vytvorit-schuzku">&#10006;</span></div>
                <div class="form-heading-wrapper">
                    <h2 class="form-heading">Vytvořit schůzku</h2>
                </div>
                <div class="dialog-content">
                    <form id="form-vytvorit_schuzku" method="post" action="#">
                        <label for="vytvorit_schuzku-jmeno">Jméno *</label><input type="text" name="vytvorit_schuzku-jmeno" id="vytvorit_schuzku-jmeno" required>
                        <span class="error-hlaska error-jmeno"></span>
                        <label for="vytvorit_schuzku-prijmeni">Příjmení *</label>
                        <input type="text" name="vytvorit_schuzku-prijmeni" id="vytvorit_schuzku-prijmeni" required>
                        <span class="error-hlaska error-prijmeni"></span>
                        <label for="vytvorit_schuzku-tel">Telefon *</label>
                        <input type="tel" name="vytvorit_schuzku-tel" id="vytvorit_schuzku-tel" required>
                        <span class="error-hlaska error-tel"></span>
                        <label for="vytvorit_schuzku-termin">Termín *</label>
                        <input type="date" name="vytvorit_schuzku-termin" id="vytvorit_schuzku-termin" required>
                        <span class="error-hlaska error-termin"></span>
                        <label for="vytvorit_schuzku-poznamka">Poznámka</label>
                        <textarea name="vytvorit_schuzku-poznamka" id="vytvorit_schuzku-poznamka"></textarea>
                        <span class="error-hlaska error-poznamka"></span>
                        <label for="vytvorit_schuzku-combobox">ID obchodníka</label>
                        <select name="vytvorit_schuzku-combobox" id="vytvorit_schuzku-combobox">
                            <option value=""></option>
                            <option value="1">1 - Marek Pospíšil</option>
                            <option value="2">2 - Alice Novotná</option>
                            <option value="3">3 - Martin Novák</option>
                            <option value="4">4 - Jiří Smetana</option>
                        </select>

                        <span class="error-hlaska error-idObchodnika"></span>
                        <input type="submit" name="submit-vytvorit_schuzku" id="submit-vytvorit_schuzku" class="submit-btn btn" value="Vytvořit schůzku">
                    </form>
                </div>
            </dialog>

            <dialog class="dialog-schuzky-obchodniku" id="dialog-schuzky-obchodniku">
                <div class="close-dialog-wrapper"><span class="close-dialog-icon close-schuzky-obchodniku">&#10006;</span></div>
                <div class="form-heading-wrapper">
                    <h2 class="form-heading">Schůzky obchodníků</h2>
                </div>
                <div class="dialog-content">
                    <form id="form-schuzky-obchodniku" method="get" action="#">
                        <label for="schuzky-obchodniku-combobox">Vyberte obchodníka</label> 
                        <select name="schuzky-obchodniku-combobox" id="schuzky-obchodniku-combobox">
                            <option value="0"><?php if (array_key_exists("schuzky-obchodniku-combobox", $_GET)) echo $_GET["schuzky-obchodniku-combobox"] ?></option>
                            <option value="1">1 - Marek Pospíšil</option>
                            <option value="2">2 - Alice Novotná</option>
                            <option value="3">3 - Martin Novák</option>
                            <option value="4">4 - Jiří Smetana</option>
                        </select>
                        <div class="form-btns-wrapper"><input type="submit" class="btn" name="zobrazit-schuzky-obchodnika" value="Zobrazit schůzky obchodníka"><button type="button" class="btn vytvorit-schuzku_btn">Vytvořit novou schůzku</button></div>
                    </form>
                    <div class="table-wrapper">
                        <table>
                            <thead class="table-head">
                                <tr class="table-row">
                                    <th>Jméno <img src="./img/filtr.png" width="20px" title="Filtr zatím funguje jen v MS Access" alt="filtr dat"></th>
                                    <th>Příjmení <img src="./img/filtr.png" width="20px" title="Filtr zatím funguje jen v MS Access" alt="filtr dat"></th>
                                    <th>Telefon</th>
                                    <th>Termín schůzky <img src="./img/filtr.png" width="20px" title="Filtr zatím funguje jen v MS Access" alt="filtr dat"></th>
                                    <th>Poznámka</th>
                                    <th>ID_obchodníka</th>
                                </tr>
                            </thead <tbody>
                            <?php foreach($schuzkyObchodniku AS $index => $hodnota): ?>
                                <tr>
                                        <td><?= htmlspecialchars($hodnota['jmeno']) ?></td>
                                        <td><?= htmlspecialchars($hodnota['prijmeni']) ?></td>
                                        <td><?= htmlspecialchars($hodnota['telefon']) ?></td>
                                        <td><?= htmlspecialchars($hodnota['termin_schuzky']) ?></td>
                                        <td><?php if ($hodnota['poznamka'] !== null && $hodnota['poznamka'] !== "") echo htmlspecialchars($hodnota['poznamka']); else echo "-"; ?></td>
                                        <td> <?php if ($hodnota['id_obchodnika'] !== null && $hodnota['id_obchodnika'] !== "") echo htmlspecialchars($hodnota['id_obchodnika']); else echo "-"; ?>
                                        </td>
                                            
                                        </td>
                                </tr>
                            <?php  endforeach; ?> 
                            </tbody>

                        </table>
                    </div>
                </div>
            </dialog>


            <dialog class="dialog-vsechny-schuzky" id="dialog-vsechny-schuzky">
                <div class="close-dialog-wrapper"><span class="close-dialog-icon close-vsechny-schuzky">&#10006;</span></div>
                <div class="form-heading-wrapper">
                    <h2 class="form-heading">Všechny schůzky</h2>
                </div>
                <div class="dialog-content">
                    <form id="form-vsechny-schuzky" action="#" method="GET">
                        <div class="terminOd">  <label for="vsechny-schuzky-terminOd">Termín od</label> <input type="date" name="vsechny-schuzky-terminOd" id="vsechny-schuzky-terminOd" value="<?php if(array_key_exists('vsechnySchuzkyterminOd', $_SESSION)) echo $_SESSION['vsechnySchuzkyterminOd'] ?>"></div>
                      <div class="terminDo"> <label for="vsechny-schuzky-terminDo">Termín do</label> <input type="date" name="vsechny-schuzky-terminDo" id="vsechny-schuzky-terminDo" value="<?php if(array_key_exists('vsechnySchuzkyterminDo', $_SESSION)) echo $_SESSION['vsechnySchuzkyterminDo'] ?>"></div>
                       
                    <div class="form-btns-wrapper"><input type="submit" name="submit-vypsat-vsechny-schuzky" id="submit-vypsat-vsechny-schuzky"  class="btn" value="Vypsat schůzky"></div>
                    </form>
                    <div class="table-wrapper">
                        <table>
                            <thead class="table-head">
                                <tr class="table-row">
                                    <th>Jméno <img src="./img/filtr.png" width="20px" title="Filtr zatím funguje jen v MS Access" alt="filtr dat"></th>
                                    <th>Příjmení <img src="./img/filtr.png" width="20px" title="Filtr zatím funguje jen v MS Access" alt="filtr dat"></th>
                                    <th>Telefon</th>
                                    <th>Termín schůzky <img src="./img/filtr.png" width="20px" title="Filtr zatím funguje jen v MS Access" alt="filtr dat"></th>
                                    <th>Poznámka</th>
                                    <th>ID_obchodníka</th>
                                </tr>
                            </thead 
                            <tbody>
                                
                                <?php if(!array_key_exists("vsechnySchuzky", $_SESSION))$_SESSION["vsechnySchuzky"] = $explorer->vypisVsechnySchuzky(); foreach($_SESSION["vsechnySchuzky"] AS $index => $hodnota): ?>
                                    <tr> 
                                        <?php $zmenenyRadek = isset($idZmeneneSchuzky) && $hodnota['id_schuzky'] == $idZmeneneSchuzky ? "class='zmeneny-radek'" : "";?>
                                  
                                        <td <?= $zmenenyRadek ?>> <?= htmlspecialchars($hodnota['jmeno']) ?></td>
                                        <td <?= $zmenenyRadek ?>> <?= htmlspecialchars($hodnota['prijmeni']) ?></td>
                                        <td <?= $zmenenyRadek ?>> <?= htmlspecialchars($hodnota['telefon']) ?></td>
                                        <td <?= $zmenenyRadek ?>> <?= htmlspecialchars($hodnota['termin_schuzky'])?></td>
                                        <td <?= $zmenenyRadek ?>> <?php if ($hodnota['poznamka'] !== null && $hodnota['poznamka'] !== "") echo htmlspecialchars($hodnota['poznamka']); else echo "-"; ?></td>
                                        <td><select name="schuzky-obchodniku-id" class="schuzky-obchodniku-id">
                                                <option value=""><?php echo(htmlspecialchars($hodnota['id_obchodnika'])); ?></option>
                                                <option value="1">1 - Marek Pospíšil</option>
                                                <option value="2">2 - Alice Novotná</option>
                                                <option value="3">3 - Martin Novák</option>
                                                <option value="4">4 - Jiří Smetana</option>
                                        </select>
                                        <input type="hidden" class="hidden" name="id_schuzky" value="<?= $hodnota['id_schuzky'] ?>" />
                                        </td>
                                    </tr>
                                <?php  endforeach; ?> 
                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="hidden">
                    <form action="#" method="post">
                        <input type="text" name="idSchuzky" id="idSchuzky">
                        <input type="text" name="noveIdObchodnika" id="noveIdObchodnika">
                        <input type="submit" name="noveIdSubmit" id="noveIdSubmit"></input>
                    </form>
                </div>
            </dialog>

        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js" integrity="sha256-sw0iNNXmOJbQhYFuC9OF2kOlD5KQKe1y5lfBn4C9Sjg=" crossorigin="anonymous"></script>
    <script src="./script.js"></script>

    <?php if (array_key_exists("vsechny-schuzky",$_GET) && $_GET["vsechny-schuzky"] == 1): ?> 
        <script defer>
        $(document).ready(function() {
            $(".dialog-vsechny-schuzky").dialog("open").parent().draggable();
        });
        </script>
    <?php endif; ?>
    <?php if (array_key_exists("schuzky-obchodniku-combobox",$_GET)): ?> 
        <script defer>
        $(document).ready(function() {
            $(".dialog-schuzky-obchodniku").dialog("open").parent().draggable();
        });
        </script>
    <?php endif; ?>
    <?php if (array_key_exists("vytvorit-schuzku",$_GET)): ?> 
        <script defer>
        $(document).ready(function() {
            $(".dialog-vytvorit_schuzku").dialog("open").parent().draggable();
        });
        </script>
    <?php endif; ?>
</body>

</html>